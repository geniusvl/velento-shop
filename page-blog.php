<?php
/**
 * Velento Shop — Blog / Journal
 * Editorial grid inspired by the reference layout, rebuilt in Velento's
 * black / ivory / gold visual language and written for a luxury watch store.
 */
if (!defined('ABSPATH')) exit;

get_header();

$blog_url = function_exists('velento_get_blog_url') ? velento_get_blog_url() : get_permalink();
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');

$categories = get_categories([
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'number'     => 6,
]);

$posts_query = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 8,
    'ignore_sticky_posts' => true,
]);

/*
 * Built-in editorial content keeps the page premium even on a fresh install.
 * Once real WordPress posts are published, they automatically replace these
 * cards. The copy is intentionally written for watches rather than generic
 * lifestyle content.
 */
$editorial_cards = [
    [
        'title'    => 'چطور یک ساعت مردانه را برای استایل روزمره انتخاب کنیم؟',
        'excerpt'  => 'از اندازه قاب تا رنگ صفحه و نوع بند؛ چند قانون ساده برای اینکه ساعت با استایل شما هماهنگ باشد، نه اینکه از آن جلو بزند.',
        'category' => 'راهنمای انتخاب ساعت',
        'day'      => '۱۵',
        'month'    => 'مرداد',
        'folder'   => 'products',
        'image'    => 'watch-1.avif',
    ],
    [
        'title'    => 'ساعت مکانیکی یا کوارتز؛ کدام انتخاب برای شماست؟',
        'excerpt'  => 'دو دنیای متفاوت از دقت و مهندسی را مقایسه می‌کنیم تا انتخاب بعدی‌تان فقط بر اساس ظاهر نباشد.',
        'category' => 'دانش ساعت',
        'day'      => '۱۲',
        'month'    => 'مرداد',
        'folder'   => 'products',
        'image'    => 'watch-2.avif',
    ],
    [
        'title'    => '۵ جزئیات که یک ساعت لوکس را متمایز می‌کند',
        'excerpt'  => 'پرداخت قاب، کیفیت صفحه، عقربه‌ها، بند و حتی صدای مکانیزم؛ جزئیاتی که در نگاه اول شاید دیده نشوند.',
        'category' => 'دنیای ساعت',
        'day'      => '۱۰',
        'month'    => 'مرداد',
        'folder'   => 'products',
        'image'    => 'watch-3.avif',
    ],
    [
        'title'    => 'قاب ساعت را چطور با اندازه مچ خود هماهنگ کنیم؟',
        'excerpt'  => 'یک راهنمای سریع برای انتخاب قطر قاب و فرم ساعت تا روی مچ، متناسب و خوش‌فرم به نظر برسد.',
        'category' => 'راهنمای انتخاب ساعت',
        'day'      => '۸',
        'month'    => 'مرداد',
        'folder'   => 'brands',
        'image'    => 'audemars-piguet-royal-oak.webp',
    ],
    [
        'title'    => 'استیل، چرم یا بند فلزی؛ کدام امضای شماست؟',
        'excerpt'  => 'بند فقط یک جز کاربردی نیست؛ بخش مهمی از شخصیت ساعت و استایل شماست. انتخاب را هوشمندانه‌تر کنید.',
        'category' => 'استایل',
        'day'      => '۵',
        'month'    => 'مرداد',
        'folder'   => 'brands',
        'image'    => 'omega-speedmaster.webp',
    ],
    [
        'title'    => 'چطور از ساعت خود نگهداری کنیم تا سال‌ها سالم بماند؟',
        'excerpt'  => 'از میدان مغناطیسی و رطوبت تا نحوه تمیزکردن بند؛ نکات ساده‌ای که عمر ساعت شما را بیشتر می‌کنند.',
        'category' => 'نگهداری ساعت',
        'day'      => '۲',
        'month'    => 'مرداد',
        'folder'   => 'products',
        'image'    => 'watch-1.avif',
    ],
    [
        'title'    => 'برای استایل رسمی چه ساعتی انتخاب کنیم؟',
        'excerpt'  => 'وقتی کت‌وشلوار می‌پوشید، ساعت باید ظریف، دقیق و هماهنگ باشد. اینجا سراغ اصول انتخاب می‌رویم.',
        'category' => 'استایل',
        'day'      => '۳۰',
        'month'    => 'تیر',
        'folder'   => 'products',
        'image'    => 'watch-2.avif',
    ],
    [
        'title'    => 'چرا بعضی ساعت‌ها هیچ‌وقت از مد نمی‌افتند؟',
        'excerpt'  => 'راز ماندگاری در طراحی ساعت‌های کلاسیک چیست و چرا بعضی مدل‌ها دهه‌ها همچنان جذاب می‌مانند؟',
        'category' => 'دنیای ساعت',
        'day'      => '۲۷',
        'month'    => 'تیر',
        'folder'   => 'products',
        'image'    => 'watch-3.avif',
    ],
];

$theme_images = get_template_directory_uri() . '/assets/images/';
?>

<main id="primary" class="site-main blog-page">
    <section class="blog-hero">
        <div class="container blog-hero__inner">
            <h1 class="blog-title">وبلاگ</h1>
            <nav class="blog-breadcrumb" aria-label="مسیر صفحه">
                <a href="<?php echo esc_url(home_url('/')); ?>">خانه</a>
                <span class="blog-breadcrumb__sep">/</span>
                <span>وبلاگ</span>
            </nav>
        </div>
    </section>

    <div class="container blog-content">
        <?php if ($categories) : ?>
            <div class="blog-categories blog-categories--bar" aria-label="<?php echo esc_attr__('دسته‌بندی‌های وبلاگ', 'velento-shop'); ?>">
                <a class="blog-category is-active" href="<?php echo esc_url($blog_url); ?>">همه مطالب</a>
                <?php foreach ($categories as $category) : ?>
                    <a class="blog-category" href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="blog-section-head">
            <div>
                <span class="blog-section-kicker"><?php esc_html_e('ولنتو ژورنال', 'velento-shop'); ?></span>
                <h2 class="blog-section-title">آخرین مطالب درباره ساعت</h2>
            </div>
            <a class="blog-section-link" href="<?php echo esc_url($shop_url); ?>">مشاهده ساعت‌ها ←</a>
        </div>

        <div class="blog-grid blog-grid--reference">
            <?php if ($posts_query->have_posts()) : ?>
                <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                    <article class="blog-card">
                        <a class="blog-card__media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                            <span class="blog-date-badge"><strong><?php echo esc_html(get_the_date('j')); ?></strong><small><?php echo esc_html(wp_date('F', get_post_timestamp())); ?></small></span>
                            <?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large', ['loading' => 'lazy']); else : ?>
                                <span class="blog-card__placeholder">ولنتو</span>
                            <?php endif; ?>
                        </a>
                        <div class="blog-card__body">
                            <div class="blog-meta">
                                <?php $post_categories = get_the_category(); if (!empty($post_categories)) : ?>
                                    <span class="blog-meta__category"><?php echo esc_html($post_categories[0]->name); ?></span>
                                    <span class="blog-meta__dot"></span>
                                <?php endif; ?>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                            </div>
                            <h3 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="blog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22, '…')); ?></p>
                            <a class="blog-card__read" href="<?php the_permalink(); ?>">ادامه مطلب <span>←</span></a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <?php foreach ($editorial_cards as $card) : ?>
                    <article class="blog-card blog-card--editorial">
                        <a class="blog-card__media" href="<?php echo esc_url($shop_url); ?>" aria-label="<?php echo esc_attr($card['title']); ?>">
                            <span class="blog-date-badge"><strong><?php echo esc_html($card['day']); ?></strong><small><?php echo esc_html($card['month']); ?></small></span>
                            <img src="<?php echo esc_url($theme_images . $card['folder'] . '/' . $card['image']); ?>" alt="<?php echo esc_attr($card['title']); ?>" loading="lazy">
                        </a>
                        <div class="blog-card__body">
                            <div class="blog-meta">
                                <span class="blog-meta__category"><?php echo esc_html($card['category']); ?></span>
                                <span class="blog-meta__dot"></span>
                                <time><?php echo esc_html($card['day'] . ' ' . $card['month']); ?></time>
                            </div>
                            <h3 class="blog-card__title"><a href="<?php echo esc_url($shop_url); ?>"><?php echo esc_html($card['title']); ?></a></h3>
                            <p class="blog-card__excerpt"><?php echo esc_html($card['excerpt']); ?></p>
                            <a class="blog-card__read" href="<?php echo esc_url($shop_url); ?>">ادامه مطلب <span>←</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <section class="blog-bottom-cta">
            <div>
                <span class="blog-newsletter__eyebrow"><?php esc_html_e('ساعت‌های ولنتو', 'velento-shop'); ?></span>
                <h2>حالا که بیشتر می‌دانید، ساعت مناسب خودتان را پیدا کنید.</h2>
                <p>از میان مدل‌های منتخب ولنتو، ساعتی را انتخاب کنید که با سبک شما حرف بزند.</p>
            </div>
            <a class="hero-cta blog-newsletter__button" href="<?php echo esc_url($shop_url); ?>">مشاهده کالکشن ساعت‌ها</a>
        </section>
    </div>
</main>

<?php get_footer(); ?>
