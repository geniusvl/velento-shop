<?php
if (!defined('ABSPATH')) exit;

$cart_count = 0;
if (function_exists('WC') && WC()->cart) {
    $cart_count = WC()->cart->get_cart_contents_count();
}
?>

<aside class="velento-cart-drawer" id="velento-cart-drawer" aria-hidden="true" aria-labelledby="velento-cart-title">
    <div class="velento-cart-drawer__header">
        <div>
            <span class="velento-cart-drawer__eyebrow"><?php esc_html_e('ولنتو شاپ', 'velento-shop'); ?></span>
            <h2 id="velento-cart-title"><?php esc_html_e('سبد خرید', 'velento-shop'); ?></h2>
        </div>
        <button type="button" class="velento-cart-drawer__close" aria-label="<?php esc_attr_e('بستن سبد خرید', 'velento-shop'); ?>">
            <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <line x1="5" y1="5" x2="19" y2="19"></line>
                <line x1="19" y1="5" x2="5" y2="19"></line>
            </svg>
        </button>
    </div>

    <div class="velento-cart-drawer__content">
        <?php get_template_part('template-parts/header/cart-drawer-content'); ?>
    </div>
</aside>
