<?php
defined('ABSPATH') || exit;




/* backend menu oprettese */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    register_nav_menus(['hovedmenu' => 'Hovedmenu']);
});
/* backend menu oprettese */


/* assets/js/menu.js samt CSS stylesheet */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('kk-tema', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_script('kk-menu', get_theme_file_uri('assets/js/menu.js'), [], wp_get_theme()->get('Version'), true);
});
/* assets/js/menu.js samt CSS stylesheet */



/* Preload af fonts */

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 24.11
add_action('wp_head', function () {
    $fonte = [
        'atkinson-hyperlegible-latin-400-normal.woff2',
        'atkinson-hyperlegible-latin-700-normal.woff2',
    ];
    foreach ($fonte as $font) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url(get_theme_file_uri('assets/fonte/' . $font))
        );
    }
}, 1);


/* Preload af fonts */