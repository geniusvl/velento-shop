<?php
/**
 * Generic WordPress loop fallback.
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<main id="primary" class="site-main blog-page">
    <div class="container blog-content">
        <?php if (have_posts()) : ?>
            <div class="blog-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('blog-card'); ?>>
                        <a class="blog-card__media" href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large', ['loading' => 'lazy']); else : ?><span class="blog-card__placeholder" aria-hidden="true">✦</span><?php endif; ?>
                        </a>
                        <div class="blog-meta"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time></div>
                        <h2 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p class="blog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20, '…')); ?></p>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <div class="v-shop-empty"><h2><?php esc_html_e('موردی پیدا نشد.', 'velento-shop'); ?></h2></div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
