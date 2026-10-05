<?php
/**
 * Velento Shop — single blog post fallback.
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<main id="primary" class="site-main blog-page">
    <div class="container blog-content">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('blog-single'); ?>>
                <header class="blog-single__header">
                    <p class="blog-eyebrow"><?php echo esc_html(get_the_date()); ?></p>
                    <?php the_title('<h1 class="blog-title">', '</h1>'); ?>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="blog-single__media"><?php the_post_thumbnail('large'); ?></div>
                <?php endif; ?>

                <div class="blog-single__content">
                    <?php
                    the_content();
                    wp_link_pages([
                        'before' => '<div class="page-links">' . esc_html__('صفحات:', 'velento-shop'),
                        'after'  => '</div>',
                    ]);
                    ?>
                </div>

                <?php if (comments_open() || get_comments_number()) comments_template(); ?>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
