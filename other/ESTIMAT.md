# Estimat (D04)

**Kæde & Krank** · Jonas Knudsen, Pixelværket · 19.11.2026 · Til godkendelse ved Milepæl 1

Bygger på beslutningsnotatet: klassisk WordPress med eget tema (`kk-tema`) og eget plugin (`kk-kerne`). Alle tal er i timer. **E = (O + 4M + P) / 6**, rundet til én decimal.

## Sådan er estimatet regnet

Hvert delmål har fået tre bud på tiden:

- **O (optimistisk):** hvis alt går glat
- **M (mest sandsynlig):** det, jeg realistisk regner med
- **P (pessimistisk):** hvis det, der kan gå galt, går galt

De tre bud bliver til ét estimat (E) med formlen **E = (O + 4M + P) / 6**. M tæller fire gange, fordi det er det mest sandsynlige udfald. O og P tæller én gang hver. Det giver 1 + 4 + 1 = 6 tal i alt, og derfor deles der med 6, ligesom ved et almindeligt gennemsnit. Resultatet ligger tæt på M, men trækkes op, når P ligger langt væk, altså når der er stor risiko.

**Eksempel, D15 Ledige tider** (O 4 / M 6 / P 12): (4 + 4 · 6 + 12) / 6 = 40 / 6 ≈ **6,7 t**

E er regnet ud for hvert delmål og rundet til én decimal, før det hele er lagt sammen.

Metoden hedder **trepunktsestimering** eller **PERT** (Program Evaluation and Review Technique). Den blev udviklet i 1958 af den amerikanske flådes Special Projects Office sammen med Lockheed og konsulentfirmaet Booz Allen Hamilton. Formålet var at holde Polaris-programmet på tidsplanen. Programmet skulle bygge atommissiler til ubåde og havde tusindvis af underleverandører, og ingen kunne sige præcist, hvor lang tid hver opgave ville tage. Derfor fik hver opgave tre bud i stedet for ét. Metoden bruges i dag bredt i projektledelse.

| Delmål | O (t) | M (t) | P (t) | Estimat (t) | Begrundelse |
| --- | ---: | ---: | ---: | ---: | --- |
| D01 Læs og forstå | 1,5 | 2,5 | 4 | 2,6 | 23 siders opgave + kundens materiale (mail, designguide, CSV). Spørgsmål til kunden skrives undervejs. |
| D02 Teknologivalg | 2 | 4 | 7 | 4,2 | Tre løsninger sammenlignet, WordPress testet på cPanel (import, REST, permalinks), notat på én side. Højt P: jeg skiftede fra headless til eget tema undervejs. |
| D03 Værktøjer | 1 | 1,5 | 2,5 | 1,6 | Trello med 27 kort, Toggl-projekt, Git-repo og SFTP-upload. Kendte værktøjer. |
| D04 Estimat og tilbud | 1,5 | 2,5 | 4 | 2,6 | O/M/P og begrundelse for 27 delmål, tilbud og tid til at rette efter feedback. |
| D05 Sitemap og wireframes | 3 | 5 | 8 | 5,2 | Sitemap (inkl. Om os) + forside, booking og cykeloversigt på mobil og desktop = 6 wireframes i lav detalje. |
| D06 Datamodel | 1,5 | 2,5 | 4 | 2,6 | Post types ydelse og cykel med felter + egne tabeller til bookinger og beskeder. Relationen booking → ydelse. |
| D07 Server og projekt | 1,5 | 2,5 | 4 | 2,6 | Én WP-installation på ét subdomæne. Usikkert: `wp-config.php` uden for webroden (T-04), permalinks og upload af tema og plugin. |
| D08 Data | 2 | 3,5 | 6 | 3,7 | Post types og felter i `kk-kerne`, importplugin til to CSV-filer (semikolon, UTF-8), 12 billeder knyttet til cyklerne, admin-bruger. |
| D09 Designgrundlag | 2,5 | 4 | 6,5 | 4,2 | CSS-variabler, fonte (woff2), typografi, knapper og felter efter designguiden. Kendt terræn. +0,5 t i P til at holde WP's inline-styles ude (T-06). |
| D10 Header, nav og footer | 2 | 3 | 5 | 3,2 | `header.php`/`footer.php` (G-02), burger-menu med `aria-expanded` og aktiv side (G-03). Rydder op i WP's `<head>` (emoji, block-styles), så HTML'en validerer. |
| D11 Forside | 3,5 | 5,5 | 8 | 5,6 | Fem dele: hero, animeret hjul (F-05), 3 udvalgte ydelser og 3 nyeste cykler med `WP_Query`, åbent/lukket i JS. Cykelkortet laves som fælles `<template>`. |
| D12 Ydelser og priser | 1,5 | 2,5 | 4 | 2,6 | Én `WP_Query` grupperet i 4 kategorier med book-links (`?ydelse=`). Enkel liste. |
| D13 Kontakt (+ Om os) | 3 | 5 | 7,5 | 5,1 | Kontaktinfo, `kort.svg` med markering, formular til egen tabel med servervalidering og honeypot. +1 t til Om os-siden (ønske fra kunden 18.11). |
| D14 Formular og validering | 3 | 5 | 8 | 5,2 | 8 felter med validering i browser og på serveren. Fejl fra serveren skal vises ved det rigtige felt (B-05). |
| D15 Ledige tider (backend) | 4 | 6 | 12 | 6,7 | Projektets sværeste logik: åbningstider, halve timer, højst 2 overlap, ydelsen skal nå at blive færdig, i morgen til +30 dage. Nyt for mig: læringstid i M, +2 t i P. |
| D16 Ledige tider (frontend) og gem | 4 | 6 | 11 | 6,5 | Hent tider uden reload, gem booking med status "Ny" og kvittering med KK-nummer. B-07 (transaktion/lås i MySQL) er nyt for mig: +1 t i P. |
| D17 JSON og oversigt | 2 | 3 | 5 | 3,2 | Eget REST-endpoint med rene felter. Solgte skjules, reserverede får mærkat. Genbruger kort-`<template>` fra D11. |
| D18 Filter og sortering | 2,5 | 4 | 6 | 4,1 | Filtre og sortering i JS uden reload, antal og tom-besked. C-07 (filtre i URL'en) er småt med `URLSearchParams`. |
| D19 Detaljeside | 2 | 3,5 | 5 | 3,5 | `single-kk_cykel.php` med pæn URL og rigtig 404 for ukendte og solgte cykler. Interesseret-formular gemmes som besked med cykel-id. |
| D20 Login | 1 | 2 | 3 | 2,0 | WordPress' login dækker A-01, A-02 og A-07. Tiden går til Sofies bruger (Redaktør) og en ryddet admin-menu. |
| D21 Overblik og bookinger | 3,5 | 6 | 10 | 6,3 | Dashboard-widget og egen admin-side på egen tabel: liste pr. dato og statusskift med nonce. Ikke gratis fra WP, derfor M hævet. Nyt for mig: +2 t i P. |
| D22 Cykler (CRUD) | 1,5 | 3 | 6 | 3,3 | CRUD og nonces kommer fra WP, A-08 følger med post typen. Tiden går til felter og tjek af billedet (JPG/PNG/WebP, maks. 2 MB): +1 t i P. |
| D23 Beskeder | 2 | 3 | 5 | 3,2 | Egen admin-side på egen tabel: læs, markér som læst, slet + tæller til overblikket. Genbruger mønstret fra D21: +1 t i P. |
| D24 Test og rettelser | 4 | 6 | 10,5 | 6,4 | Mobil, 2 browsere, tastatur, W3C, SQL injection og XSS i alle formularer og endpoints. Omfanget af rettelser er ukendt, derfor højt P (+0,5 t til T-06-tjek). |
| D25 Optimering | 1,5 | 3 | 5 | 3,1 | Billedstørrelser under 200 KB med `width`/`height`/`lazy`, Lighthouse, unik title og meta via hooks, `404.php`. |
| D26 Deploy | 1 | 1,5 | 3 | 1,7 | Én installation at uploade (tema, plugins), test på serveren og SQL-dump fra phpMyAdmin. |
| D27 README og evaluering | 2 | 3 | 5 | 3,2 | README, evaluering på én side, Toggl-rapport og oprydning i AI-loggen. |
| **Sum** | **60,5** | **99** | **165** | **104,2** | |
| **Buffer 15 %** | | | | **15,6** | Møder, stand-ups, milepæle, fejl og ting, jeg ikke har tænkt på. |
| **Total** | | | | **119,8 ≈ 120** | Tilbuddet regnes på **120 timer**. |

## Læringstid

Det, der er nyt for mig, er lagt ind i P (og i M på D15 og D21), som i beslutningsnotatet: ledige tider og transaktion (D15 +2, D16 +1) · egne admin-sider på egne tabeller (D21 +2, D23 +1) · holde WP's inline-scripts/-styles ude (D09 +0,5, D24 +0,5) · validering af billedupload (D22 +1). I alt **8 t**.

## Fordeling og tidsplan

Regnet med ca. **6 effektive timer pr. hverdag** (30 t/uge) og bufferen fordelt forholdsmæssigt.

| Fase | Delmål | Estimat (t) | Med buffer (t) | Færdig |
| --- | --- | ---: | ---: | --- |
| Opstart | D01–D04 | 11,0 | 12,7 | Milepæl 1: fre 20.11.2026 |
| Planlægning til offentlig del | D05–D19 | 64,0 | 73,6 | Milepæl 2: ons 25.11 · Milepæl 3 (demo): tor 10.12.2026 |
| Admin, test og aflevering | D20–D27 | 29,2 | 33,6 | Milepæl 4 (aflevering): fre 18.12.2026 |
| **I alt** | | **104,2** | **119,8** | |

## Med og ikke med

- **Med:** alle Skal- og Bør-krav samt C-07 (filtre i URL'en). T-09 (Lighthouse 90+) er et mål i D25, ikke et løfte.
- **Ikke med:** B-08 (bekræftelse på e-mail). Kan laves som ændringsønske.
- **Åbent:** "Jeg er interesseret" på reserverede cykler (C-06) afgøres senere med kunden.
- Et delmål, der tager mere end 50 % længere end estimatet, meldes til projektlederen samme dag.
