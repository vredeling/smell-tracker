<?php
// Amsterdam Geurtracker — Open-Meteo wind proxy
//
// Deploy this file to a PHP host. The frontend (https://vredeling.github.io/smell-tracker)
// will hit it before falling back to direct Open-Meteo if the proxy is unavailable.
//
// Configure the paid plan by exposing the key as an env var on the host:
//   OPENMETEO_KEY=xxxxxxxxxxxxxxxx
// If unset, the proxy transparently uses the free public endpoint.
//
// What this file does:
//   1. Restricts requests by Origin/Referer header (best-effort — header-based only).
//   2. Per-IP rate limit (file-based, 30 req/min) as belt-and-suspenders.
//   3. Shared 5-min response cache so a traffic spike collapses to ~12 upstream calls/hour.
//   4. Serves stale cache on upstream failure so the app stays up during outages.

$ALLOWED_ORIGINS = [
    'https://vredeling.github.io',
    'https://ademvrijaanhetij.nl',
    'https://www.ademvrijaanhetij.nl',
];

$origin  = $_SERVER['HTTP_ORIGIN']  ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';

$allowed = in_array($origin, $ALLOWED_ORIGINS, true);
if (!$allowed && $referer !== '') {
    foreach ($ALLOWED_ORIGINS as $o) {
        if (strncmp($referer, $o, strlen($o)) === 0) { $allowed = true; break; }
    }
}
if (!$allowed) {
    http_response_code(403);
    exit('Forbidden');
}

header('Access-Control-Allow-Origin: ' . ($origin !== '' ? $origin : $ALLOWED_ORIGINS[0]));
header('Vary: Origin');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');

$now = time();

// Per-IP rate limit (max 30 req/min). Cache absorbs the bulk of load; this just
// stops a single misbehaving client from hammering the PHP-FPM worker pool.
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ip = trim(explode(',', $ip)[0]);
$rlFile = sys_get_temp_dir() . '/wind-rl-' . md5($ip);
$hits = [];
if (is_file($rlFile)) {
    $decoded = json_decode((string)file_get_contents($rlFile), true);
    if (is_array($decoded)) {
        foreach ($decoded as $t) { if (is_int($t) && $t > $now - 60) $hits[] = $t; }
    }
}
if (count($hits) >= 30) {
    http_response_code(429);
    header('Retry-After: 60');
    exit('{"error":"rate_limited"}');
}
$hits[] = $now;
file_put_contents($rlFile, json_encode($hits), LOCK_EX);

// Shared response cache (5 min). Location is fixed (Westpoort) so one cache key suffices.
$cache = sys_get_temp_dir() . '/wind-westpoort.json';
$ttl   = 300;
if (is_file($cache) && ($now - filemtime($cache)) < $ttl) {
    header('X-Cache: hit');
    readfile($cache);
    exit;
}

$key  = getenv('OPENMETEO_KEY') ?: '';
$host = $key !== '' ? 'customer-api.open-meteo.com' : 'api.open-meteo.com';
$url  = "https://$host/v1/forecast"
      . '?latitude=52.4&longitude=4.9'
      . '&current=wind_speed_10m,wind_direction_10m'
      . '&hourly=wind_speed_10m,wind_direction_10m'
      . '&wind_speed_unit=ms&past_hours=8&forecast_hours=8'
      . ($key !== '' ? '&apikey=' . urlencode($key) : '');

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 5,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_USERAGENT      => 'amsterdam-geurtracker/1.0 (+https://vredeling.github.io/smell-tracker)',
]);
$data = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code === 200 && $data !== false && $data !== '') {
    file_put_contents($cache, $data, LOCK_EX);
    header('X-Cache: miss');
    echo $data;
    exit;
}

// Upstream failure: serve stale cache if we have one, otherwise 502.
if (is_file($cache)) {
    header('X-Cache: stale');
    readfile($cache);
} else {
    http_response_code(502);
    echo '{"error":"upstream","status":' . (int)$code . '}';
}
