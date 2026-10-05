<?php
/**
 * Velento Shop — WooCommerce product archive.
 *
 * The page keeps the theme's editorial hero while using a real WP_Query
 * product loop. Filtering is progressively enhanced by inc/filters.php.
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main velento-shop-page">
    <?php
    /**
     * Elementor Pro Theme Builder support: if an Archive template has
     * been assigned (Templates > Theme Builder > Archive, with a
     * "Product Archive" display condition), render that instead of the
     * theme's own hero + product grid below. Harmless without Elementor
     * Pro — same pattern as header.php/footer.php/single-product.php.
     */
    if (function_exists('elementor_theme_do_location') && elementor_theme_do_location('archive')) :
    ?>
    <?php else : ?>

    <?php do_action('woocommerce_before_main_content'); ?>

    <section class="v-shop-hero v-shop-hero--image" style="background-image:url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/shop/shop-banner.webp'); ?>')">
        <div class="v-shop-hero-overlay" aria-hidden="true"></div>
        <div class="v-shop-wrap v-shop-hero-content">
            <span class="v-shop-overline"><?php esc_html_e('VELENTO / COLLECTION', 'velento-shop'); ?></span>
            <h1><?php esc_html_e('فروشگاه', 'velento-shop'); ?> <em><?php esc_html_e('ولنتو', 'velento-shop'); ?></em></h1>
            <p><?php esc_html_e('انتخابی دقیق از ساعت‌های لوکس، کلاسیک و روزمره؛ با طراحی مینیمال و اصالت در جزئیات.', 'velento-shop'); ?></p>
        </div>
    </section>

    <?php
    get_template_part('template-parts/shop/product-results', null, [
        'show_filters'      => true,
        'show_brand_filter' => false,
        'state'             => function_exists('velento_shop_filter_state') ? velento_shop_filter_state() : [],
        'query'             => $GLOBALS['wp_query'],
    ]);
    ?>

    <?php do_action('woocommerce_after_main_content'); ?>

    <?php endif; ?>
</main>
<?php get_footer(); ?>
