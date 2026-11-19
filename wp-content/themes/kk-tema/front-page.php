<?php // AI-assisteret (Claude), tilpasset og testet af mig - se AI-log 18.11 og 19.11 ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <section class="hero">
        <?php echo file_get_contents(get_theme_file_path('assets/hero-vaerksted.svg')); // Temaets egen fil, ikke en upload ?>
    </section>
    <h1>Hello world</h1>
    <?php wp_footer(); ?>
</body>
</html>
