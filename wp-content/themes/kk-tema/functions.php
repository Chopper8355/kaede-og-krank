<?php
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('kk-tema', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
});
