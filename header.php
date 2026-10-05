<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        /* Set the theme before first paint so there's no light/dark flash.
           Priority: saved choice in localStorage -> OS preference -> light. */
        (function () {
            try {
                var saved = localStorage.getItem('velento-theme');
                var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('برو به محتوای اصلی', 'velento-shop'); ?></a>

<?php get_template_part('template-parts/header/announcement'); ?>

<?php
/**
 * Elementor Pro Theme Builder support: if the store owner has built and
 * assigned a custom header via Templates > Theme Builder, render that
 * instead of the theme's own header markup below. Harmless without
 * Elementor Pro — elementor_theme_do_location() only exists (and only
 * returns true) when Pro itself is active and a header template is
 * actually assigned.
 */
if (!(function_exists('elementor_theme_do_location') && elementor_theme_do_location('header'))) :
?>
<header class="site-header" id="site-header">

    <div class="container header-container">

        <?php get_template_part('template-parts/header/actions'); ?>

        <?php get_template_part('template-parts/header/branding'); ?>

        <?php get_template_part('template-parts/header/navigation'); ?>

    </div>

    <?php get_template_part('template-parts/header/search-overlay'); ?>

</header>
<?php endif; ?>

<?php get_template_part('template-parts/header/cart-drawer'); ?>

<?php get_template_part('template-parts/header/login-drawer'); ?>

<div class="site-overlay" id="site-overlay"></div>
