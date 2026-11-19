<?php
// Felterne til ydelser og cykler som en formular i redigeringsskærmen

defined('ABSPATH') || exit;

// Faste valgmuligheder. Bruges både i formularen og til at tjekke det, der gemmes
function kk_valg() {
    return [
        'kategori'   => ['Service', 'Reparation', 'Hjul og dæk', 'Elcykel'],
        'type'       => ['Citybike', 'Racer', 'Mountainbike', 'Gravel', 'Elcykel', 'Børnecykel'],
        'stoerrelse' => ['XS', 'S', 'M', 'L', 'XL'],
        'status'     => ['Til salg', 'Reserveret', 'Solgt'],
    ];
}

// Klassisk redigeringsskærm for ydelser og cykler: titel, beskrivelse og felterne under hinanden
add_filter('use_block_editor_for_post_type', function ($brug, $post_type) {
    return in_array($post_type, ['kk_ydelse', 'kk_cykel'], true) ? false : $brug;
}, 10, 2);

add_action('add_meta_boxes', function () {
    add_meta_box('kk_ydelse_felter', 'Ydelse', 'kk_ydelse_boks', 'kk_ydelse', 'normal', 'high');
    add_meta_box('kk_cykel_felter', 'Cykel', 'kk_cykel_boks', 'kk_cykel', 'normal', 'high');

    // WordPress' egen "Custom Fields"-boks ville vise de samme felter en gang til
    remove_meta_box('postcustom', 'kk_ydelse', 'normal');
    remove_meta_box('postcustom', 'kk_cykel', 'normal');
});

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
function kk_ydelse_boks($post) {
    wp_nonce_field('kk_gem_felter', 'kk_nonce');

    kk_felt_valg('kategori', 'Kategori', get_post_meta($post->ID, 'kategori', true));
    kk_felt_tal('pris', 'Pris (kr.)', get_post_meta($post->ID, 'pris', true), 0, 1);
    kk_felt_tal('varighed_min', 'Varighed (minutter, hele halve timer)', get_post_meta($post->ID, 'varighed_min', true), 30, 30);
    kk_felt_flueben('fremhaevet', 'Vis på forsiden', get_post_meta($post->ID, 'fremhaevet', true));
}

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
function kk_cykel_boks($post) {
    wp_nonce_field('kk_gem_felter', 'kk_nonce');

    kk_felt_tekst('maerke', 'Mærke', get_post_meta($post->ID, 'maerke', true));
    kk_felt_valg('type', 'Type', get_post_meta($post->ID, 'type', true));
    kk_felt_valg('stoerrelse', 'Stelstørrelse', get_post_meta($post->ID, 'stoerrelse', true));
    kk_felt_tal('gear', 'Antal gear', get_post_meta($post->ID, 'gear', true), 1, 1);
    kk_felt_tal('aargang', 'Årgang', get_post_meta($post->ID, 'aargang', true), 1900, 1);
    kk_felt_tal('pris', 'Pris (kr.)', get_post_meta($post->ID, 'pris', true), 0, 1);
    kk_felt_valg('status', 'Status', get_post_meta($post->ID, 'status', true));
}

// --- Små hjælpefunktioner, der skriver ét felt hver ---

function kk_felt_tekst($navn, $label, $vaerdi) {
    printf(
        '<p><label for="kk_%1$s"><strong>%2$s</strong></label><br><input type="text" class="regular-text" id="kk_%1$s" name="kk[%1$s]" value="%3$s"></p>',
        esc_attr($navn), esc_html($label), esc_attr($vaerdi)
    );
}

function kk_felt_tal($navn, $label, $vaerdi, $min, $step) {
    printf(
        '<p><label for="kk_%1$s"><strong>%2$s</strong></label><br><input type="number" class="small-text" id="kk_%1$s" name="kk[%1$s]" value="%3$s" min="%4$d" step="%5$d" required></p>',
        esc_attr($navn), esc_html($label), esc_attr($vaerdi), absint($min), absint($step)
    );
}

function kk_felt_flueben($navn, $label, $vaerdi) {
    printf(
        '<p><label><input type="checkbox" name="kk[%1$s]" value="1" %2$s> %3$s</label></p>',
        esc_attr($navn), checked((bool) $vaerdi, true, false), esc_html($label)
    );
}

function kk_felt_valg($navn, $label, $vaerdi) {
    printf('<p><label for="kk_%1$s"><strong>%2$s</strong></label><br><select id="kk_%1$s" name="kk[%1$s]">', esc_attr($navn), esc_html($label));
    foreach (kk_valg()[$navn] as $valg) {
        printf('<option value="%1$s" %2$s>%3$s</option>', esc_attr($valg), selected($vaerdi, $valg, false), esc_html($valg));
    }
    echo '</select></p>';
}

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
add_action('save_post', 'kk_gem_felter', 10, 2);

function kk_gem_felter($post_id, $post) {
    if (!isset(kk_felter()[$post->post_type])) {
        return;
    }
    if (!isset($_POST['kk_nonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['kk_nonce'])), 'kk_gem_felter')) {
        return; // ikke gemt fra formularen (fx import eller autosave)
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $input = isset($_POST['kk']) ? wp_unslash((array) $_POST['kk']) : [];
    $valg  = kk_valg();

    foreach (kk_felter()[$post->post_type] as $navn => $type) {
        $vaerdi = $input[$navn] ?? '';

        if ($type === 'boolean') {
            $vaerdi = !empty($vaerdi);
        } elseif ($type === 'integer') {
            $vaerdi = max(0, (int) $vaerdi); // ingen negative tal
        } else {
            $vaerdi = sanitize_text_field($vaerdi);
            if (isset($valg[$navn]) && !in_array($vaerdi, $valg[$navn], true)) {
                continue; // ukendt værdi i en dropdown gemmes ikke
            }
        }

        // Bookinger starter hver halve time, så varigheden rundes op til hele halve timer
        if ($navn === 'varighed_min') {
            $vaerdi = max(30, (int) ceil($vaerdi / 30) * 30);
        }

        update_post_meta($post_id, $navn, $vaerdi);
    }
}
