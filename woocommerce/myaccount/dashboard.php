<?php
/**
 * My Account dashboard — theme override of
 * woocommerce/myaccount/dashboard.php.
 *
 * This is the content shown at the account page's default endpoint
 * (i.e. what the header's user icon lands on). Shows the username and a
 * clear, always-visible "خروج از حساب" (log out) button up top — in
 * addition to the logout entry already in the sidebar nav
 * (woocommerce/myaccount/navigation.php) — plus a short welcome blurb.
 * woocommerce_account_dashboard is kept so anything a plugin hooks onto
 * the stock dashboard still renders below.
 */

if (!defined('ABSPATH')) {
    exit;
}

$velento_current_user = wp_get_current_user();

// Prefer a real email; OTP-registered accounts (inc/auth.php) have none,
// so fall back to the phone number that was actually collected.
$velento_identity = $velento_current_user->user_email;
if (empty($velento_identity)) {
    $velento_identity = get_user_meta($velento_current_user->ID, 'velento_phone', true);
}
?>

<div class="velento-account-card">

    <div class="velento-account-card__head">
        <div class="velento-account-card__intro">
            <span class="velento-account-card__eyebrow"><?php esc_html_e('پروفایل کاربری', 'velento-shop'); ?></span>
            <h1 class="velento-account-card__title">
                <?php esc_html_e('سلام،', 'velento-shop'); ?>
                <span class="velento-account-card__username"><?php echo esc_html($velento_current_user->display_name); ?></span>
            </h1>
            <?php if (!empty($velento_identity)) : ?>
                <p class="velento-account-card__meta" dir="ltr"><?php echo esc_html($velento_identity); ?></p>
            <?php endif; ?>
        </div>

        <a href="<?php echo esc_url(wc_logout_url(home_url('/'))); ?>" class="btn btn--outline velento-account-card__logout">
            <?php echo velento_account_nav_icon('logout'); ?>
            <?php esc_html_e('خروج از حساب', 'velento-shop'); ?>
        </a>
    </div>

    <p class="velento-account-card__hint">
        <?php esc_html_e('از این‌جا می‌توانید سفارش‌ها، آدرس‌ها و اطلاعات حساب خود را مدیریت کنید.', 'velento-shop'); ?>
    </p>

    <?php do_action('woocommerce_account_dashboard'); ?>

</div>
