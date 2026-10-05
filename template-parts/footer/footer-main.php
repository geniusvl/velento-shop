<?php
/**
 * Template Part: Site Footer
 *
 * Four-column footer (brand/about, quick links, customer service,
 * contact) + a bottom bar with copyright. Quick-links and
 * customer-service columns use static fallback lists
 * (velento_default_footer_menu / velento_default_service_links,
 * inc/helpers.php) until real pages/menu items exist — same pattern
 * already used for the primary nav's fallback menu.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<footer class="site-footer" id="colophon">
    <div class="container footer-columns">

        <div class="footer-col footer-col-brand">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <img
                    class="logo-img logo-light"
                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/branding/logo-light.png'); ?>"
                    alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                    width="120" height="120"
                >
                <img
                    class="logo-img logo-dark"
                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/branding/logo-dark.png'); ?>"
                    alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                    width="120" height="120"
                >
            </a>
            <p class="footer-about">
                <?php esc_html_e('ولنتو شاپ، مرجع تخصصی خرید ساعت‌های لوکس اورجینال با ضمانت اصالت و خدمات پس از فروش اختصاصی.', 'velento-shop'); ?>
            </p>
            <ul class="footer-socials">
                <li>
                    <a href="#" class="footer-social-link" aria-label="<?php echo esc_attr__('اینستاگرام', 'velento-shop'); ?>">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect><circle cx="12" cy="12" r="4.2"></circle><circle cx="17" cy="7" r="1"></circle></svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="footer-social-link" aria-label="<?php echo esc_attr__('تلگرام', 'velento-shop'); ?>">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path d="m4 11.5 15.3-6.4a.6.6 0 0 1 .8.7l-2.6 13.2a.7.7 0 0 1-1.1.4l-4.4-3.3-2.3 2.2a.4.4 0 0 1-.7-.3l.4-4 8-7.4-9.6 6.3-3.5-1.1a.5.5 0 0 1-.1-.9Z"></path></svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="footer-social-link" aria-label="<?php echo esc_attr__('واتساپ', 'velento-shop'); ?>">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5A8.5 8.5 0 0 0 4.7 16.6L3.5 20.5l4-1.2A8.5 8.5 0 1 0 12 3.5Z"></path><path d="M8.7 8.8c.2-.5.4-.5.6-.5h.5c.2 0 .4 0 .5.4.2.5.6 1.6.7 1.7.1.1.1.3 0 .5-.1.2-.2.3-.3.4-.2.2-.3.3-.1.6.2.3.8 1.2 1.7 1.9 1.1 1 1.1.6 1.6.1.2-.2.4-.5.7-.4.2.1 1.5.7 1.7.8.2.1.4.2.4.3 0 .2 0 1-.4 1.4-.4.4-1.3.7-1.9.6-.5-.1-2-.7-3.3-1.9-1.6-1.4-2.5-3-2.6-3.3-.1-.2-.7-1-.7-1.9 0-.9.5-1.4.7-1.6Z"></path></svg>
                    </a>
                </li>
            </ul>
        </div>

        <nav class="footer-col footer-col-links" aria-label="<?php echo esc_attr__('لینک‌های سریع', 'velento-shop'); ?>">
            <h3 class="footer-col-title"><?php esc_html_e('لینک‌های سریع', 'velento-shop'); ?></h3>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'footer-menu',
                'fallback_cb'    => 'velento_default_footer_menu',
            ]);
            ?>
        </nav>

        <div class="footer-col footer-col-service">
            <h3 class="footer-col-title"><?php esc_html_e('خدمات مشتریان', 'velento-shop'); ?></h3>
            <ul class="footer-menu">
                <?php velento_default_service_links(); ?>
            </ul>
        </div>

        <div class="footer-col footer-col-contact">
            <h3 class="footer-col-title"><?php esc_html_e('تماس با ما', 'velento-shop'); ?></h3>
            <ul class="footer-contact-list">
                <li>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 6.5c0-1.1.9-2 2-2h1.6c.5 0 .9.3 1 .8l.8 3a1.1 1.1 0 0 1-.3 1.1l-1.4 1.3a12 12 0 0 0 5.1 5.1l1.3-1.4c.3-.3.7-.4 1.1-.3l3 .8c.5.1.8.5.8 1v1.6c0 1.1-.9 2-2 2h-1C9.9 19.6 4.4 14.1 4.5 6.5Z"></path></svg>
                    <span><?php esc_html_e('پشتیبانی آنلاین ولنتو', 'velento-shop'); ?></span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5.5" width="17" height="13" rx="2"></rect><path d="m4 7 8 6 8-6"></path></svg>
                    <span dir="ltr"><?php echo esc_html(get_option('admin_email')); ?></span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-6.3-7-11.5A7 7 0 0 1 19 9.5C19 14.7 12 21 12 21Z"></path><circle cx="12" cy="9.5" r="2.4"></circle></svg>
                    <span><?php esc_html_e('ارسال به سراسر ایران', 'velento-shop'); ?></span>
                </li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p class="footer-copyright">
                &copy; <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?> — <?php esc_html_e('تمامی حقوق محفوظ است.', 'velento-shop'); ?>
            </p>
            <p class="footer-payment-note"><?php esc_html_e('پرداخت امن با درگاه بانکی معتبر', 'velento-shop'); ?></p>
        </div>
    </div>
</footer>
