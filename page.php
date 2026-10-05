<?php
/**
 * The default template for generic WordPress Pages.
 *
 * This file was an empty stub in the original theme upload — meaning
 * ANY normal Page (About, Contact, and crucially WooCommerce's "My
 * Account" page, since that's just a Page with the
 * [woocommerce_my_account] shortcode in its content) rendered as a
 * totally blank screen: no header, no content, no footer. That's the
 * root cause of the blank /my-account/ page — not the account
 * templates in woocommerce/myaccount/, which never even got a chance
 * to run because get_header()/the_content()/get_footer() were never
 * called.
 *
 * Kept deliberately simple/generic (unlike page-blog.php or
 * templates/page/page-login.php, which are custom-designed templates
 * for specific pages) since this is the fallback for any Page that
 * doesn't have a dedicated template.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

/**
 * Elementor compatibility: a Page built with Elementor already has its
 * own full-width sections, own hero/title area if the store owner
 * wants one, etc. Wrapping that inside page-default__container (a
 * max-width box) and printing our own <h1> above it is exactly the
 * "double title / content stuck in a narrow column" bug that makes a
 * theme feel un-Elementor-compatible. So for a Page that was built
 * with Elementor, skip straight to the_content() with no wrapper —
 * Elementor's own sections control their own width edge-to-edge.
 * Every other Page (the normal WordPress editor / Gutenberg) keeps the
 * original boxed layout below exactly as before.
 */
$velento_built_with_elementor = did_action('elementor/loaded')
    && get_post_meta(get_the_ID(), '_elementor_edit_mode', true) === 'builder';
?>

<main id="primary" class="site-main page-default<?php echo $velento_built_with_elementor ? ' page-default--elementor' : ''; ?>">
    <?php if ($velento_built_with_elementor) : ?>

        <?php while (have_posts()) : the_post(); ?>
            <?php the_content(); ?>
        <?php endwhile; ?>

    <?php else : ?>

    <div class="container page-default__container">

        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('page-default__article'); ?>>

                <?php if (!is_front_page()) : ?>
                    <header class="page-default__header">
                        <?php the_title('<h1 class="page-default__title">', '</h1>'); ?>
                    </header>
                <?php endif; ?>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="page-default__thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="page-default__content">
                    <?php
                    the_content();

                    wp_link_pages([
                        'before' => '<div class="page-links">' . esc_html__('صفحات:', 'velento-shop'),
                        'after'  => '</div>',
                    ]);
                    ?>
                </div>

            </article>

            <?php
            // Only relevant for pages that actually have comments enabled
            // (most Pages, including My Account, don't).
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>

        <?php endwhile; ?>

    </div>

    <?php endif; ?>
</main>

<?php get_footer(); ?>
