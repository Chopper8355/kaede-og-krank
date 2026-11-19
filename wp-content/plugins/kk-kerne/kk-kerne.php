<?php
/**
 * Plugin Name: Kæde & Krank – kerne
 * Description: Post types og felter til ydelser og brugte cykler.
 * Version:     0.1.0
 * Author:      Jonas Knudsen
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/meta-boxes.php';

// AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11
add_action('init', 'kk_registrer_post_types');

function kk_registrer_post_types() {
    // Ydelser vises kun samlet på prissiden, så de har ingen egne sider
    register_post_type('kk_ydelse', [
        'labels' => [
            'name'          => 'Ydelser',
            'singular_name' => 'Ydelse',
            'add_new_item'  => 'Tilføj ydelse',
            'edit_item'     => 'Rediger ydelse',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-admin-tools',
        'supports'     => ['title', 'editor', 'page-attributes', 'custom-fields'],
    ]);

    // Brugte cykler får egne sider på /cykler/<slug>/ (C-04)
    register_post_type('kk_cykel', [
        'labels' => [
            'name'          => 'Cykler',
            'singular_name' => 'Cykel',
            'add_new_item'  => 'Tilføj cykel',
            'edit_item'     => 'Rediger cykel',
        ],
        'public'       => true,
        'has_archive'  => 'cykler',
        'rewrite'      => ['slug' => 'cykler', 'with_front' => false],
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-tag',
        'supports'     => ['title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields'],
    ]);
}

// Felterne har samme navne som kolonnerne i kundens CSV-filer
function kk_felter() {
    return [
        'kk_ydelse' => [
            'kategori'     => 'string',
            'pris'         => 'integer',
            'varighed_min' => 'integer',
            'fremhaevet'   => 'boolean',
        ],
        'kk_cykel' => [
            'maerke'     => 'string',
            'type'       => 'string',
            'stoerrelse' => 'string',
            'gear'       => 'integer',
            'aargang'    => 'integer',
            'pris'       => 'integer',
            'status'     => 'string', // Til salg, Reserveret eller Solgt
        ],
    ];
}

add_action('init', 'kk_registrer_felter');

function kk_registrer_felter() {
    foreach (kk_felter() as $post_type => $felter) {
        foreach ($felter as $navn => $type) {
            register_post_meta($post_type, $navn, [
                'type'          => $type,
                'single'        => true,
                'show_in_rest'  => true,
                'auth_callback' => fn() => current_user_can('edit_posts'),
            ]);
        }
    }
}

// Permalinks skal genopbygges, når /cykler/ bliver tilføjet eller fjernet
register_activation_hook(__FILE__, function () {
    kk_registrer_post_types();
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
