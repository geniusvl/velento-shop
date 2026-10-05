<?php
/**
 * Widget Areas
 *
 * footer-main.php currently renders its three columns from hardcoded
 * PHP (velento_default_footer_menu / velento_default_service_links in
 * inc/helpers.php) rather than dynamic sidebars — that's intentional
 * for now (see footer-main.php) and left untouched here.
 *
 * The one registered here is new: a shop-sidebar for the WooCommerce
 * archive page (filters, promo banner, etc.) that gets built in the
 * next phase — registering it now means Appearance > Widgets already
 * has a home for that content once woocommerce/archive-product.php
 * exists and calls dynamic_sidebar('shop-sidebar').
 */

if (!defined('ABSPATH')) {
    exit;
}

function velento_register_widget_areas()
{
    register_sidebar([
        'name'          => __('سایدبار فروشگاه', 'velento-shop'),
        'id'            => 'shop-sidebar',
        'description'   => __('نمایش داده می‌شود در ستون کناری صفحه فروشگاه (فیلترها، بنر تبلیغاتی و غیره).', 'velento-shop'),
        'before_widget' => '<div id="%1$s" class="widget shop-sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'velento_register_widget_areas');
