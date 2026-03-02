# 🌬️ Amsterdam Geurtracker

A single-file progressive web app (PWA) that helps residents of neighbourhoods surrounding Amsterdam's Westpoort area identify the likely source of industrial smells based on live wind data.

## What it does

The app cross-references real-time wind direction and speed with the locations of known industrial sources to show which facility is most likely upwind of the user's location. A directional plume cone is projected from each source on the map. If the user's location falls inside a cone, the app identifies that source as the likely cause and offers a direct link to file a complaint.

Users can also scrub back through the past 3 hours of wind data in 30-minute increments to see how conditions have changed.

## Live demo

Hosted on GitHub Pages: [https://vredeling.github.io/smell-tracker](https://vredeling.github.io/smell-tracker)

---

## Smell sources

| Source | Typical smell |
|---|---|
| ICL Fertilizers BV | Scherp chemisch / verbrand rubber / uitlaatgassen |
| Bunge | Weeïg zoet / gekookte pasta / geroosterde soja |
| Afval Energie Bedrijf | Verbrand afval / zware organische lucht |
| Benzine Terminal | Benzine / nafta / scherp aromatisch, prikt in keel |

---

## Features

- 🗺️ OpenStreetMap via Leaflet
- 📍 GPS geolocation or manual address entry (via Nominatim)
- 🌬️ Live wind data from Open-Meteo (no API key required)
- 📐 Wind-speed-dependent plume cones — narrow at high speed, wider at low speed
- ⏱️ Time slider: scrub back 3 hours in 30-minute steps with animated cone transitions
- 🧭 Wind rose widget showing direction, m/s and Beaufort scale
- 🇳🇱 Dutch by default, English toggle — persisted in localStorage
- 📢 Direct link to complaint portal [overlast.odnzkg.nl](https://overlast.odnzkg.nl) on positive match
- 📱 PWA — installable on iPhone via Safari → Share → Add to Home Screen

---

## External services

| Service | Purpose | Cost | Rate limit |
|---|---|---|---|
| [OpenStreetMap](https://tile.openstreetmap.org) | Map tiles | Free | Fair use, attribution required |
| [Open-Meteo](https://open-meteo.com) | Live + historical wind data | Free | 10,000 req/day |
| [Nominatim](https://nominatim.openstreetmap.org) | Address geocoding | Free | 1 req/sec |

No API keys required. For high traffic, consider replacing Nominatim with a paid geocoder.

---

## Usage

The app is a single HTML file — no build step, no dependencies to install.

**Run locally:**
```bash
python3 -m http.server 8080
# open http://localhost:8080
```

**Deploy:**
Upload `index.html` to any static host. GitHub Pages is recommended (free HTTPS, required for GPS on iOS Safari).

---

## Install as PWA on iPhone

1. Open the GitHub Pages URL in **Safari**
2. Tap the **Share** button → **Zet op beginscherm / Add to Home Screen**
3. Tap **Add** — the app appears as "Geurtracker" with a 🌬️ icon

---

## Disclaimer

The plume cones are a simplified indicative model, not a certified dispersion calculation. Cone width is derived from the Pasquill-Gifford atmospheric dispersion model (Briggs 1973 rural σy formula) using wind speed as a proxy for atmospheric stability. This is a known simplification: a full model would also account for time of day, cloud cover, surface roughness, and source stack height.

In practice this means:

- The cone shows where smell is *plausible*, not where it is *certain*
- Stability class is estimated from wind speed alone — calm sunny afternoons and calm clear nights produce very different real-world dispersion but are treated identically here
- No concentration gradient is shown — inside the cone is treated as a uniform zone
- Source coordinates are approximate public-domain locations, not surveyed emission points

This app is intended as a residents' orientation tool and a starting point for filing complaints. It is not a substitute for professional air quality monitoring or official source attribution.

---

## Complaint portal

Smell complaints can be filed at:
**[https://overlast.odnzkg.nl](https://overlast.odnzkg.nl)**

This is operated by the Omgevingsdienst Noordzeekanaalgebied (ODNZKG), the environmental authority responsible for the Westpoort area.