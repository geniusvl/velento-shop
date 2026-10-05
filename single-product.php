<?php
/**
 * Velento Shop — Single WooCommerce product.
 *
 * Uses WooCommerce's standard single-product lifecycle so extensions can
 * continue to hook into before/after product content.
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main velento-single-product">
    <div class="v-product-wrap">
        <?php
        /**
         * Elementor Pro Theme Builder support: if a Single template has
         * been assigned (Templates > Theme Builder > Single, with a
         * "Product" display condition), render that instead of the
         * theme's own WooCommerce product markup below. Harmless without
         * Elementor Pro — same pattern as header.php/footer.php.
         */
        if (function_exists('elementor_theme_do_location') && elementor_theme_do_location('single')) :
        ?>
        <?php else : ?>

        <?php do_action('woocommerce_before_main_content'); ?>

        <?php while (have_posts()) : the_post(); ?>
            <?php do_action('woocommerce_before_single_product'); ?>
            <?php wc_get_template_part('content', 'single-product'); ?>
            <?php do_action('woocommerce_after_single_product'); ?>
        <?php endwhile; ?>

        <?php do_action('woocommerce_after_main_content'); ?>

        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
