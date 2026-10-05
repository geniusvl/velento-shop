<?php
/**
 * Blog / Posts Page Template
 *
 * Used automatically when WordPress is configured to display latest posts
 * on a dedicated Posts page. The visual language intentionally follows
 * Velento's luxury watch identity instead of the default WordPress archive.
 */
if (!defined('ABSPATH')) exit;

get_header();

$posts_page_id = (int) get_option('page_for_posts');
$posts_page_title = $posts_page_id ? get_the_title($posts_page_id) : __('وبلاگ', 'velento-shop');
$posts_page_title = $posts_page_title ?: __('وبلاگ', 'velento-shop');

$categories = get_categories([
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'number'     => 6,
]);

$blog_query = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 7,
    'ignore_sticky_posts' => false,
]);

$featured_post = null;
if ($blog_query->have_posts()) {
    $featured_post = $blog_query->posts[0];
}
?>

<main id="primary" class="site-main blog-page">
    <section class="blog-hero">
        <div class="container blog-hero__inner">
            <nav class="blog-breadcrumb" aria-label="<?php esc_attr_e('مسیر صفحه', 'velento-shop'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('خانه', 'velento-shop'); ?></a>
                <span class="blog-breadcrumb__sep">/</span>
                <span><?php echo esc_html($posts_page_title); ?></span>
            </nav>

            <p class="blog-eyebrow"><?php esc_html_e('ولنتو ژورنال', 'velento-shop'); ?></p>
            <h1 class="blog-title"><?php esc_html_e('زمان را با ظرافت تجربه کنید', 'velento-shop'); ?></h1>
            <p class="blog-intro">
                <?php esc_html_e('روایت‌های ولنتو درباره ساعت، طراحی، استایل و جزئیاتی که یک انتخاب معمولی را به امضای شخصی شما تبدیل می‌کنند.', 'velento-shop'); ?>
            </p>

            <?php if ($categories) : ?>
                <div class="blog-categories" aria-label="<?php esc_attr_e('دسته‌بندی‌های وبلاگ', 'velento-shop'); ?>">
                    <?php foreach ($categories as $category) : ?>
                        <a class="blog-category" href="<?php echo esc_url(get_category_link($category)); ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="container blog-content">
        <?php if ($featured_post) : setup_postdata($featured_post); ?>
            <section aria-labelledby="blog-featured-title">
                <div class="blog-section-head">
                    <div>
                        <span class="blog-section-kicker"><?php esc_html_e('یادداشت ویژه', 'velento-shop'); ?></span>
                        <h2 class="blog-section-title"><?php esc_html_e('یادداشت منتخب', 'velento-shop'); ?></h2>
                    </div>
                    <?php if ($posts_page_id) : ?>
                        <a class="blog-section-link" href="<?php echo esc_url(get_permalink($posts_page_id)); ?>"><?php esc_html_e('همه یادداشت‌ها', 'velento-shop'); ?> ←</a>
                    <?php endif; ?>
                </div>

                <article class="blog-featured">
                    <a class="blog-featured__media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', ['loading' => 'eager']); ?>
                        <?php else : ?>
                            <span class="blog-featured__placeholder" aria-hidden="true">✦</span>
                        <?php endif; ?>
                    </a>
                    <div class="blog-featured__copy">
                        <div class="blog-meta">
                            <?php $featured_categories = get_the_category(); ?>
                            <?php if (!empty($featured_categories)) : ?>
                                <span class="blog-meta__category"><?php echo esc_html($featured_categories[0]->name); ?></span>
                                <span class="blog-meta__dot" aria-hidden="true"></span>
                            <?php endif; ?>
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                        </div>
                        <h2 class="blog-featured__title" id="blog-featured-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p class="blog-featured__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 34, '…')); ?></p>
                        <a class="blog-read-more" href="<?php the_permalink(); ?>"><?php esc_html_e('ادامه مطلب', 'velento-shop'); ?></a>
                    </div>
                </article>
            </section>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

        <?php if ($blog_query->post_count > 1) : ?>
            <section aria-labelledby="blog-latest-title">
                <div class="blog-section-head">
                    <div>
                        <span class="blog-section-kicker"><?php esc_html_e('تازه‌ترین نوشته‌ها', 'velento-shop'); ?></span>
                        <h2 class="blog-section-title" id="blog-latest-title"><?php esc_html_e('تازه‌ترین نوشته‌ها', 'velento-shop'); ?></h2>
                    </div>
                </div>

                <div class="blog-grid">
                    <?php
                    $index = 0;
                    while ($blog_query->have_posts()) :
                        $blog_query->the_post();
                        $index++;
                        if ($index === 1) continue;
                    ?>
                        <article class="blog-card">
                            <a class="blog-card__media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium_large', ['loading' => 'lazy']); ?>
                                <?php else : ?>
                                    <span class="blog-card__placeholder" aria-hidden="true">✦</span>
                                <?php endif; ?>
                            </a>
                            <div class="blog-meta">
                                <?php $post_categories = get_the_category(); ?>
                                <?php if (!empty($post_categories)) : ?>
                                    <span class="blog-meta__category"><?php echo esc_html($post_categories[0]->name); ?></span>
                                    <span class="blog-meta__dot" aria-hidden="true"></span>
                                <?php endif; ?>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                            </div>
                            <h3 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="blog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20, '…')); ?></p>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
        <?php elseif (!$featured_post) : ?>
            <div class="blog-empty">
                <h2><?php esc_html_e('هنوز نوشته‌ای منتشر نشده است', 'velento-shop'); ?></h2>
                <p><?php esc_html_e('اولین داستان ولنتو را از بخش نوشته‌ها در پیشخوان وردپرس منتشر کنید.', 'velento-shop'); ?></p>
            </div>
        <?php endif; ?>

        <section class="blog-newsletter" aria-labelledby="blog-newsletter-title">
            <div>
                <span class="blog-newsletter__eyebrow"><?php esc_html_e('نامه ولنتو', 'velento-shop'); ?></span>
                <h2 id="blog-newsletter-title"><?php esc_html_e('برای یادداشت‌های بعدی آماده باشید.', 'velento-shop'); ?></h2>
                <p><?php esc_html_e('خبرهای کالکشن‌ها و نوشته‌های تازه را مستقیم دریافت کنید.', 'velento-shop'); ?></p>
            </div>
            <?php
            $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
            ?>
            <a class="hero-cta blog-newsletter__button" href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('کشف کالکشن ساعت‌ها', 'velento-shop'); ?></a>
        </section>
    </div>
</main>

<?php get_footer(); ?>
