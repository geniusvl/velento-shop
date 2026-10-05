<?php if (!defined('ABSPATH')) exit; ?>

<?php
$cart_count = 0;
if (function_exists('WC') && WC()->cart) {
    $cart_count = WC()->cart->get_cart_contents_count();
}
// Only relevant when logged in — a link to the account page. When
// logged out, the icon is a <button> below that opens the login
// drawer in place; it never needs a URL of its own.
$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account');
?>

<div class="header-actions">

    <button type="button" class="header-icon nav-toggle" aria-label="<?php echo esc_attr__('باز کردن منو', 'velento-shop'); ?>" aria-expanded="false" aria-controls="site-header">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <line x1="3.5" y1="7" x2="20.5" y2="7"></line>
            <line x1="3.5" y1="12.5" x2="20.5" y2="12.5"></line>
            <line x1="3.5" y1="18" x2="20.5" y2="18"></line>
        </svg>
    </button>

    <button type="button" class="header-icon search-toggle" aria-label="<?php echo esc_attr__('جستجو', 'velento-shop'); ?>" aria-expanded="false" aria-controls="velento-search-overlay">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <circle cx="10.5" cy="10.5" r="6.5"></circle>
            <line x1="20" y1="20" x2="15.3" y2="15.3"></line>
        </svg>
    </button>

    <button type="button" class="header-icon theme-toggle" aria-label="<?php echo esc_attr__('تغییر تم روشن/تاریک', 'velento-shop'); ?>">
        <svg class="icon-sun" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <circle cx="12" cy="12" r="4.5"></circle>
            <line x1="12" y1="1.5" x2="12" y2="4.2"></line>
            <line x1="12" y1="19.8" x2="12" y2="22.5"></line>
            <line x1="1.5" y1="12" x2="4.2" y2="12"></line>
            <line x1="19.8" y1="12" x2="22.5" y2="12"></line>
            <line x1="4.4" y1="4.4" x2="6.3" y2="6.3"></line>
            <line x1="17.7" y1="17.7" x2="19.6" y2="19.6"></line>
            <line x1="4.4" y1="19.6" x2="6.3" y2="17.7"></line>
            <line x1="17.7" y1="6.3" x2="19.6" y2="4.4"></line>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5z"></path>
        </svg>
    </button>

    <?php if (is_user_logged_in()) : ?>
        <a
            href="<?php echo esc_url($account_url); ?>"
            class="header-icon account-link"
            aria-label="<?php echo esc_attr__('حساب کاربری', 'velento-shop'); ?>"
        >
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="3.6"></circle>
                <path d="M4.5 20c1.4-3.6 4.4-5.4 7.5-5.4S18.1 16.4 19.5 20"></path>
            </svg>
        </a>
    <?php else : ?>
        <button
            type="button"
            class="header-icon account-link login-toggle"
            aria-label="<?php echo esc_attr__('ورود یا ثبت‌نام', 'velento-shop'); ?>"
            aria-haspopup="dialog" aria-controls="velento-login-drawer" aria-expanded="false"
        >
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="3.6"></circle>
                <path d="M4.5 20c1.4-3.6 4.4-5.4 7.5-5.4S18.1 16.4 19.5 20"></path>
            </svg>
        </button>
    <?php endif; ?>

    <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart')); ?>" class="header-icon cart-link" aria-label="<?php echo esc_attr__('سبد خرید', 'velento-shop'); ?>">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6.5 8h11l-1 11.5a1.5 1.5 0 0 1-1.5 1.4H9a1.5 1.5 0 0 1-1.5-1.4L6.5 8z"></path>
            <path d="M9 8V6.2a3 3 0 0 1 6 0V8"></path>
        </svg>
        <span class="cart-count cart-count-fragment" data-count="<?php echo (int) $cart_count; ?>"><?php echo $cart_count > 0 ? esc_html($cart_count) : ''; ?></span>
    </a>

</div>
