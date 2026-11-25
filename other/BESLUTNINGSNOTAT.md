# Beslutningsnotat: teknologivalg (D02 / T-01)

**Kæde & Krank** · Jonas Knudsen, Pixelværket · 19.11.2026 · Til godkendelse ved Milepæl 1

## Beslutning
**Klassisk WordPress med eget tema** (`kk-tema`, skrevet fra bunden) og **eget plugin** (`kk-kerne`) til data og logik. Ydelser og cykler er post types med felter, bookinger og beskeder ligger i egne tabeller, og JavaScript henter JSON fra egne REST-endpoints.

## Fravalgte alternativer
- **Headless WordPress + vanilla JS** (mit første valg): to dele at drive, og en SPA gør unikke titler, 404 og cykel-links sværere (G-06, G-07, C-04).
- **Vanilla PHP og JS:** login, admin og upload (A-01 til A-06) skal bygges og sikres fra bunden.
- **Færdigt booking-plugin** (fx Bookly): passer ikke præcist til B-03, styrer selv sin HTML (T-06) og er svært at forklare krav for krav.

## De sværeste krav
- **B-03:** én PHP-funktion beregner halvtimes-starttider ud fra åbningstiderne, fjerner tider hvor ydelsen ikke når at blive færdig, og tæller overlap (højst 2). Endpointet (B-02) og gem-bookingen bruger den samme funktion, så reglerne tjekkes på serveren.
- **C-02:** endpointet sender kun cykler til salg eller reserveret (C-05). JS filtrerer på type, størrelse og maks. pris uden reload og viser antal eller tom-besked.
- **A-01 til A-06:** WordPress' login med hashede adgangskoder, wp-admin kræver login, dashboard-widget med dagens bookinger og ulæste beskeder, egne admin-sider til bookinger (dato og status) og beskeder med nonces, og cykler som post type med upload-tjek (JPG/PNG/WebP, højst 2 MB).

## Sådan opdaterer kunden selv
Sofie får egen bruger som Redaktør. Cykler og ydelser/priser rettes som indlæg med felter, og bookinger ses pr. dato og skifter status i en dropdown.

## cPanel og hvad jeg har testet
WordPress kører på min cPanel-server med database fra cPanel; kode uploades med SFTP. **Testet:** 12 cykler og ydelser importeret · REST API'et returnerer cyklerne som JSON · `/cykler/<slug>/` virker · egne tabeller oprettet i phpMyAdmin · `kk-tema` viser en forside.

## Plugins og pakker
Kun egne: `kk-kerne`, `kk-import` (kørt én gang og slettet) og `secure-svg-upload` (renser SVG-uploads). Ingen page builder, ACF eller booking-plugin: det holder HTML'en ren (T-06, G-08), og jeg kan forklare al koden.

## Nyt for mig og ekstra tid
Ledige tider med kapacitet og transaktion (D15–D16) **+3 t** · egne admin-sider på egne tabeller (D21, D23) **+3 t** · fjerne WordPress' inline-scripts/-styles (D09, D24) **+1 t** · validering af billedupload (D22) **+1 t**. I alt ca. **8 t**, lagt ind i P-værdierne.

## Brug af AI
Claude hjælper med at sammenligne løsninger, udkast, boilerplate-kode og fejlfinding. Jeg fører AI-LOG.md undervejs, tagger Toggl med "AI", kommenterer AI-kodeblokke på 10+ linjer og tester AI-kode for SQL injection og XSS (T-02, T-03). Jeg deler aldrig adgangskoder eller `wp-config.php` med AI og afleverer kun kode, jeg kan forklare.
