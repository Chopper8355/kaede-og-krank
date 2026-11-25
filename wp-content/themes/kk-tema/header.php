<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo esc_url(get_theme_file_uri('assets/logo/favicon.svg')); ?>" type="image/svg+xml">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <a class="spring-til-indhold" href="#indhold">Spring til indhold</a>

    <div class="page-wrapper">



<?php // AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 23.11 ?>
<header class="site-header">
    <a class="site-header__logo" href="<?php echo esc_url(home_url('/')); ?>">
        <img class="logo" src="<?php echo esc_url(get_theme_file_uri('assets/logo/logo.svg')); ?>"
            alt="Kæde &amp; Krank - forside" width="452" height="104">
    </a>
    <button class="menu-knap" type="button" aria-expanded="false" aria-controls="hovedmenu" aria-label="Menu">
        <span class="menu-knap__streg"></span>
        <span class="menu-knap__streg"></span>
        <span class="menu-knap__streg"></span>
    </button>

    <div class="overlay"></div>

    <nav class="hovedmenu" id="hovedmenu" aria-label="Hovedmenu">
        <?php wp_nav_menu([
            'theme_location' => 'hovedmenu',
            'container'      => false,
            'fallback_cb'    => false,
        ]); ?>
    </nav>
</header>

<main id="indhold">