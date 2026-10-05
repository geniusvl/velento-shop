<?php
if (!defined('ABSPATH')) exit;

get_header();
?>
<main id="primary" class="site-main blog-page">
    <section class="blog-hero">
        <div class="container blog-hero__inner">
            <p class="blog-eyebrow"><?php esc_html_e('ولنتو ژورنال', 'velento-shop'); ?></p>
            <h1 class="blog-title"><?php the_archive_title(); ?></h1>
            <?php if (get_the_archive_description()) : ?>
                <div class="blog-intro"><?php the_archive_description(); ?></div>
            <?php endif; ?>
        </div>
    </section>
    <div class="container blog-content">
        <div class="blog-grid">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article class="blog-card">
                    <a class="blog-card__media" href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large', ['loading' => 'lazy']); else : ?><span class="blog-card__placeholder" aria-hidden="true">✦</span><?php endif; ?>
                    </a>
                    <div class="blog-meta"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time></div>
                    <h2 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="blog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20, '…')); ?></p>
                </article>
            <?php endwhile; else : ?>
                <div class="blog-empty"><h2><?php esc_html_e('موردی پیدا نشد.', 'velento-shop'); ?></h2></div>
            <?php endif; ?>
        </div>
        <?php the_posts_pagination(); ?>
    </div>
</main>
<?php get_footer(); ?>
