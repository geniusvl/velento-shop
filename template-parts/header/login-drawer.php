<?php
/**
 * Template Part: Header Login / Register Drawer
 *
 * Opens from the left, same treatment as the cart drawer
 * (template-parts/header/cart-drawer.php) — same slide-in, same
 * dim+blur backdrop (.site-overlay), same focus handling.
 *
 * Deliberately NOT wired to WooCommerce: sign-in uses wp_signon()
 * and sign-up uses wp_insert_user(), both plain WordPress core, and
 * both processed over admin-ajax.php (see inc/ajax.php). No page
 * reload, no dependency on WooCommerce being active at all.
 *
 * Skipped entirely when already logged in (see
 * template-parts/header/actions.php, which only renders the trigger
 * button for logged-out visitors in the first place).
 */

if (!defined('ABSPATH')) {
    exit;
}

if (is_user_logged_in()) {
    return;
}
?>

<aside class="velento-login-drawer" id="velento-login-drawer" data-mode="signin" aria-hidden="true" aria-labelledby="velento-login-drawer-title">
    <div class="velento-login-drawer__header">
        <div>
            <span class="velento-login-drawer__eyebrow"><?php bloginfo('name'); ?></span>
            <h2 id="velento-login-drawer-title"><?php esc_html_e('ورود / ثبت‌نام', 'velento-shop'); ?></h2>
        </div>
        <button type="button" class="velento-login-drawer__close" aria-label="<?php esc_attr_e('بستن پنجره ورود', 'velento-shop'); ?>">
            <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <line x1="5" y1="5" x2="19" y2="19"></line>
                <line x1="19" y1="5" x2="5" y2="19"></line>
            </svg>
        </button>
    </div>

    <div class="velento-login-drawer__content">

        <div class="velento-login-drawer__switch" role="tablist" aria-label="<?php esc_attr_e('نوع ورود', 'velento-shop'); ?>">
            <span class="velento-login-drawer__indicator" aria-hidden="true"></span>
            <button type="button" class="is-active" data-login-target="signin" role="tab" aria-selected="true">
                <?php esc_html_e('ورود', 'velento-shop'); ?>
            </button>
            <button type="button" data-login-target="signup" role="tab" aria-selected="false">
                <?php esc_html_e('ثبت‌نام', 'velento-shop'); ?>
            </button>
        </div>

        <p class="velento-login-drawer__notice" data-role="notice" role="alert" hidden></p>

        <!-- Sign in -->
        <form class="velento-login-drawer__panel" data-login-panel="signin" id="velento-login-form" novalidate>

            <div class="form-group">
                <label class="form-label" for="velento-login-username"><?php esc_html_e('ایمیل یا نام کاربری', 'velento-shop'); ?></label>
                <input type="text" maxlength="254" id="velento-login-username" name="username" class="form-input" dir="ltr" autocomplete="username">
            </div>

            <div class="form-group">
                <label class="form-label" for="velento-login-password"><?php esc_html_e('رمز عبور', 'velento-shop'); ?></label>
                <input type="password" maxlength="256" id="velento-login-password" name="password" class="form-input" dir="ltr" autocomplete="current-password">
            </div>

            <div class="velento-login-drawer__row">
                <label class="form-check">
                    <input type="checkbox" id="velento-login-remember" name="remember">
                    <?php esc_html_e('مرا به خاطر بسپار', 'velento-shop'); ?>
                </label>
                <a class="velento-login-drawer__link" href="<?php echo esc_url(wp_lostpassword_url()); ?>">
                    <?php esc_html_e('فراموشی رمز عبور', 'velento-shop'); ?>
                </a>
            </div>

            <button type="submit" class="btn btn--primary btn--block velento-login-drawer__submit">
                <?php esc_html_e('ورود', 'velento-shop'); ?>
            </button>

            <p class="velento-login-drawer__switch-line">
                <?php esc_html_e('هنوز ثبت‌نام نکرده‌اید؟', 'velento-shop'); ?>
                <button type="button" data-login-target="signup"><?php esc_html_e('ثبت‌نام کنید', 'velento-shop'); ?></button>
            </p>
        </form>

        <!-- Sign up -->
        <form class="velento-login-drawer__panel" data-login-panel="signup" id="velento-register-form" novalidate hidden>

            <div class="form-group">
                <label class="form-label" for="velento-register-email"><?php esc_html_e('ایمیل', 'velento-shop'); ?></label>
                <input type="email" maxlength="254" id="velento-register-email" name="email" class="form-input" dir="ltr" autocomplete="email" placeholder="name@gmail.com">
            </div>

            <div class="form-group">
                <label class="form-label" for="velento-register-password"><?php esc_html_e('رمز عبور', 'velento-shop'); ?></label>
                <input type="password" id="velento-register-password" name="password" class="form-input" dir="ltr" autocomplete="new-password" minlength="8" maxlength="20">
            </div>

            <div class="form-group">
                <label class="form-label" for="velento-register-password2"><?php esc_html_e('تکرار رمز عبور', 'velento-shop'); ?></label>
                <input type="password" id="velento-register-password2" name="password2" class="form-input" dir="ltr" autocomplete="new-password" minlength="8" maxlength="20">
            </div>

            <button type="submit" class="btn btn--primary btn--block velento-login-drawer__submit">
                <?php esc_html_e('ساخت حساب کاربری', 'velento-shop'); ?>
            </button>

            <p class="velento-login-drawer__switch-line">
                <?php esc_html_e('از قبل ثبت‌نام کرده‌اید؟', 'velento-shop'); ?>
                <button type="button" data-login-target="signin"><?php esc_html_e('ورود به حساب', 'velento-shop'); ?></button>
            </p>
        </form>

    </div>
</aside>
