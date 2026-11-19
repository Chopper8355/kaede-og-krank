-- Kæde & Krank: egne tabeller til bookinger og beskeder
-- Køres i phpMyAdmin under fanen SQL. Tjek tabelpræfikset først (her wp_).
-- Cykler og ydelser ligger som post types i wp_posts / wp_postmeta.

-- AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
CREATE TABLE IF NOT EXISTS wp_kk_bookinger (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, -- bookingnummer KK-00042 = id 42
    ydelse_id    BIGINT UNSIGNED NOT NULL,                -- ID på en kk_ydelse i wp_posts
    start        DATETIME        NOT NULL,
    slut         DATETIME        NOT NULL,                -- start + ydelsens varighed, gemt ved booking
    navn         VARCHAR(100)    NOT NULL,
    email        VARCHAR(190)    NOT NULL,
    telefon      VARCHAR(30)     NOT NULL,
    cykeltype    VARCHAR(50)     NOT NULL,
    bemaerkning  TEXT            NULL,
    status       ENUM('Ny', 'Bekræftet', 'I gang', 'Færdig', 'Annulleret') NOT NULL DEFAULT 'Ny',
    oprettet     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY start (start),
    KEY ydelse_id (ydelse_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
CREATE TABLE IF NOT EXISTS wp_kk_beskeder (
    id        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cykel_id  BIGINT UNSIGNED NULL,              -- udfyldt ved "Jeg er interesseret" (C-06), ellers NULL
    navn      VARCHAR(100)    NOT NULL,
    email     VARCHAR(190)    NOT NULL,
    emne      VARCHAR(150)    NOT NULL,
    besked    TEXT            NOT NULL,
    laest     TINYINT(1)      NOT NULL DEFAULT 0,
    oprettet  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY laest (laest),
    KEY cykel_id (cykel_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
