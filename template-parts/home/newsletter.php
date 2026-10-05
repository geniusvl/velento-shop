<?php
/**
 * Template Part: Newsletter Signup
 *
 * Simple email capture band above the footer. Submits via the theme's
 * AJAX endpoint (inc/ajax.php) — TODO: wire up an velento_subscribe
 * handler there (store the address as a CPT/option or forward to an
 * ESP) once a decision is made on where subscriber emails should live;
 * the form degrades to a no-op POST to itself until then.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="newsletter-band" aria-label="<?php echo esc_attr__('عضویت در خبرنامه', 'velento-shop'); ?>">
    <div class="container newsletter-inner">
        <div class="newsletter-copy">
            <h2 class="newsletter-title"><?php esc_html_e('از تازه‌ترین کالکشن‌ها باخبر شوید', 'velento-shop'); ?></h2>
            <p class="newsletter-subtitle"><?php esc_html_e('ایمیل خود را ثبت کنید تا پیش از دیگران از ورود ساعت‌های جدید و پیشنهادهای ویژه مطلع شوید.', 'velento-shop'); ?></p>
        </div>

        <form class="newsletter-form" id="velento-newsletter-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
            <?php wp_nonce_field('velento_subscribe', 'velento_newsletter_nonce'); ?>
            <input type="hidden" name="action" value="velento_subscribe">

            <label class="screen-reader-text" for="velento-newsletter-email"><?php esc_html_e('آدرس ایمیل', 'velento-shop'); ?></label>
            <input
                type="email"
                id="velento-newsletter-email"
                name="email"
                class="newsletter-input"
                placeholder="<?php echo esc_attr__('ایمیل', 'velento-shop'); ?>"
                required
            >

            <button type="submit" class="newsletter-submit"><?php esc_html_e('ثبت', 'velento-shop'); ?></button>
        </form>
    </div>
</section>
