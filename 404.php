<?php
/**
 * Velento Shop — 404 fallback.
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<main id="primary" class="site-main page-default velento-error-page">
    <div class="container page-default__container">
        <article class="page-default__article">
            <p class="page-default__eyebrow"><?php esc_html_e('صفحه پیدا نشد', 'velento-shop'); ?></p>
            <h1 class="page-default__title"><?php esc_html_e('این صفحه دیگر در دسترس نیست.', 'velento-shop'); ?></h1>
            <p class="page-default__content"><?php esc_html_e('می‌توانید به صفحه اصلی برگردید یا محصولات فروشگاه را ببینید.', 'velento-shop'); ?></p>
            <div class="v-btn-group">
                <a class="btn btn--primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('صفحه اصلی', 'velento-shop'); ?></a>
                <?php if (function_exists('wc_get_page_permalink')) : ?>
                    <a class="btn btn--outline" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('فروشگاه', 'velento-shop'); ?></a>
                <?php endif; ?>
            </div>
        </article>
    </div>
</main>
<?php get_footer(); ?>
