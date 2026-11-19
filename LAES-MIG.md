# Kundens materiale - Kæde & Krank

Det her har kunden sendt. Læs `kundens-mail.md` og `designguide.pdf` først.

| Mappe / fil | Indhold |
| --- | --- |
| `kundens-mail.md` | Kundens ønsker, kontaktoplysninger og alle tekster til sitet |
| `designguide.pdf` | Logo, farver, fonte, knapper, ikoner og tone (2 sider) |
| `logo/` | `logo.svg` (lys baggrund), `logo-negativ.svg` (mørk baggrund), `logo-mark.svg` (kun mærket), `favicon.svg` |
| `fonte/` | Bricolage Grotesque (400, 600, 800) og Atkinson Hyperlegible (400, 400 kursiv, 700) som woff2 + licens |
| `ikoner/` | 19 SVG-ikoner, 24 × 24, `stroke="currentColor"` |
| `grafik/hjul.svg` | Cykelhjul til forsidens hero - gruppen med klassen `wheel` kan animeres |
| `grafik/kort.svg` | Kort til kontaktsiden. Markeringen har id'et `vaerksted-markering` |
| `billeder/hero-vaerksted.jpg` | Hero-billede, 2400 × 1200 px |
| `billeder/cykler/` | 12 billeder af de brugte cykler, 2400 × 1600 px |
| `data/ydelser.csv` | Prislisten: kategori, navn, beskrivelse, pris, varighed i minutter, fremhævet (ja/nej) |
| `data/cykler.csv` | De brugte cykler. Kolonnen `billede` passer med filnavnene i `billeder/cykler/` |
| `data/aabningstider.json` | Åbningstider og antal mekanikere |

**Bemærk:**

- Billederne er i fuld størrelse (ca. 3 MB hver). Du skal selv skalere og komprimere dem (krav T-07)
- CSV-filerne bruger semikolon som skilletegn og UTF-8. I phpMyAdmin: Import > CSV > "Columns separated with" = `;`
- Virksomheden, adressen, telefonnummeret og CVR-nummeret er fiktive
