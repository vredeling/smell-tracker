# 🌬️ Amsterdam Geurtracker

A single-file progressive web app (PWA) that helps residents of neighbourhoods surrounding Amsterdam's Westpoort area identify the likely source of industrial smells based on live wind data.

## What it does

The app cross-references real-time wind direction and speed with the locations of known industrial sources to show which facility is most likely upwind of the user's location. A directional plume cone is projected from each source on the map. If the user's location falls inside a cone, the app identifies that source as the likely cause and offers a direct link to file a complaint.

Users can also scrub back through the past 3 hours of wind data in 30-minute increments to see how conditions have changed.

## Live demo

Hosted on GitHub Pages: [https://vredeling.github.io/smell-tracker](https://vredeling.github.io/smell-tracker)

---

## Smell sources

| Source | Location | Typical smell |
|---|---|---|
| ICL Fertiliser | Fosfaatweg 48, Westpoort | Scherp chemisch / verbrand rubber / uitlaatgassen |
| Bunge | HoogTij, Westpoort (new plant 2024) | Weeïg zoet / gekookte pasta / geroosterde soja |
| Afval Energie Bedrijf | Westpoort | Verbrand afval / zware organische lucht |
| Benzine Terminal | Westpoort | Benzine / nafta / scherp aromatisch, prikt in keel |

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

## Complaint portal

Smell complaints can be filed at:
**[https://overlast.odnzkg.nl](https://overlast.odnzkg.nl)**

This is operated by the Omgevingsdienst Noordzeekanaalgebied (ODNZKG), the environmental authority responsible for the Westpoort area.