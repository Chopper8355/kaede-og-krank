<?php
/**
 * Plugin Name: Kæde & Krank – import
 * Description: Engangsimport af ydelser, cykler og åbningstider fra kundens filer (D08). Deaktivér og slet efter brug.
 * Version:     0.1.0
 * Author:      Jonas Knudsen
 */

defined('ABSPATH') || exit;

const KK_IMPORT_DATA = __DIR__ . '/data';

// Siden ligger under Værktøjer > Importér K&K-data
add_action('admin_menu', function () {
    add_management_page('Importér K&K-data', 'Importér K&K-data', 'manage_options', 'kk-import', 'kk_import_side');
});

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
function kk_import_side() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $log = [];
    if (isset($_POST['kk_import'])) {
        check_admin_referer('kk_import');

        if (!post_type_exists('kk_cykel')) {
            $log[] = 'Aktivér pluginet "Kæde & Krank – kerne" først.';
        } else {
            set_time_limit(120); // 12 billeder skal skaleres af WordPress
            $log = array_merge(kk_import_ydelser(), kk_import_cykler(), kk_import_aabningstider());
        }
    }
    ?>
    <div class="wrap">
        <h1>Importér Kæde &amp; Krank-data</h1>
        <p>Opretter ydelser og cykler fra CSV-filerne og gemmer åbningstiderne.
           Poster, der allerede findes, springes over, så importen kan køres igen.</p>
        <form method="post">
            <?php wp_nonce_field('kk_import'); ?>
            <?php submit_button('Kør import', 'primary', 'kk_import'); ?>
        </form>
        <?php if ($log) : ?>
            <ul>
                <?php foreach ($log as $linje) : ?>
                    <li><?php echo esc_html($linje); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
}

// Læser en CSV-fil med semikolon som skilletegn og returnerer rækker som [kolonne => værdi]
function kk_laes_csv($fil) {
    $h = fopen(KK_IMPORT_DATA . '/' . $fil, 'r');
    if (!$h) {
        return [];
    }

    $kolonner = array_map('trim', fgetcsv($h, 0, ';', '"', ''));
    $raekker  = [];
    while (($felter = fgetcsv($h, 0, ';', '"', '')) !== false) {
        if (count($felter) !== count($kolonner)) {
            continue; // tom eller ødelagt linje
        }
        $raekker[] = array_combine($kolonner, array_map('trim', $felter));
    }
    fclose($h);

    return $raekker;
}

function kk_findes($titel, $post_type) {
    return get_page_by_path(sanitize_title($titel), OBJECT, $post_type) !== null;
}

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
function kk_import_ydelser() {
    $log = [];
    foreach (kk_laes_csv('ydelser.csv') as $i => $r) {
        if (kk_findes($r['navn'], 'kk_ydelse')) {
            $log[] = 'Findes allerede: ' . $r['navn'];
            continue;
        }

        $id = wp_insert_post([
            'post_type'    => 'kk_ydelse',
            'post_status'  => 'publish',
            'post_title'   => sanitize_text_field($r['navn']),
            'post_content' => sanitize_textarea_field($r['beskrivelse']),
            'menu_order'   => $i, // bevarer rækkefølgen fra prislisten
            'meta_input'   => [
                'kategori'     => sanitize_text_field($r['kategori']),
                'pris'         => absint($r['pris']),
                'varighed_min' => absint($r['varighed_min']),
                'fremhaevet'   => $r['fremhaevet'] === 'ja',
            ],
        ], true);

        $log[] = is_wp_error($id)
            ? 'Fejl ved ydelse ' . $r['navn'] . ': ' . $id->get_error_message()
            : 'Ydelse oprettet: ' . $r['navn'];
    }
    return $log;
}

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
function kk_import_cykler() {
    $log = [];
    foreach (kk_laes_csv('cykler.csv') as $i => $r) {
        if (kk_findes($r['titel'], 'kk_cykel')) {
            $log[] = 'Findes allerede: ' . $r['titel'];
            continue;
        }

        $id = wp_insert_post([
            'post_type'    => 'kk_cykel',
            'post_status'  => 'publish',
            'post_title'   => sanitize_text_field($r['titel']),
            'post_content' => sanitize_textarea_field($r['beskrivelse']),
            'post_date'    => sanitize_text_field($r['oprettet']) . ' 12:00:00', // bruges til "nyeste"
            'menu_order'   => $i,
            'meta_input'   => [
                'maerke'     => sanitize_text_field($r['maerke']),
                'type'       => sanitize_text_field($r['type']),
                'stoerrelse' => sanitize_text_field($r['stoerrelse']),
                'gear'       => absint($r['gear']),
                'aargang'    => absint($r['aargang']),
                'pris'       => absint($r['pris']),
                'status'     => sanitize_text_field($r['status']),
            ],
        ], true);

        if (is_wp_error($id)) {
            $log[] = 'Fejl ved cykel ' . $r['titel'] . ': ' . $id->get_error_message();
            continue;
        }

        $billede = kk_import_billede($r['billede'], $id);
        $log[]   = 'Cykel oprettet: ' . $r['titel']
            . (is_wp_error($billede) ? ' (billede fejlede: ' . $billede->get_error_message() . ')' : '');
    }
    return $log;
}

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
// Lægger et af de skalerede billeder i mediebiblioteket og sætter det som udvalgt billede
function kk_import_billede($filnavn, $post_id) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $kilde = KK_IMPORT_DATA . '/billeder/' . basename($filnavn);
    if (!is_readable($kilde)) {
        return new WP_Error('kk_mangler', 'filen ' . $filnavn . ' findes ikke');
    }

    // media_handle_sideload flytter filen, så den får en kopi
    $kopi = wp_tempnam($filnavn);
    copy($kilde, $kopi);

    $billede_id = media_handle_sideload(['name' => basename($filnavn), 'tmp_name' => $kopi], $post_id);
    if (is_wp_error($billede_id)) {
        @unlink($kopi);
        return $billede_id;
    }

    update_post_meta($billede_id, '_wp_attachment_image_alt', get_the_title($post_id));
    set_post_thumbnail($post_id, $billede_id);
    return $billede_id;
}

function kk_import_aabningstider() {
    $data = json_decode(file_get_contents(KK_IMPORT_DATA . '/aabningstider.json'), true);
    if (!is_array($data) || empty($data['aabningstider'])) {
        return ['Fejl: aabningstider.json kunne ikke læses'];
    }

    update_option('kk_aabningstider', $data['aabningstider']);
    update_option('kk_mekanikere', absint($data['mekanikere']));
    return ['Åbningstider og antal mekanikere gemt'];
}
