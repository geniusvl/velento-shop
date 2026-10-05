<?php
/**
 * Template Part: Featured Watches Carousel
 *
 * Sample data below — TODO: once real WooCommerce products exist,
 * replace $velento_featured_watches with a WP_Query/wc_get_products() pull
 * (e.g. featured products or a chosen category) instead of this
 * static array. Photos are reused/cycled across categories since only
 * a handful of placeholder images exist right now; swap in real
 * per-model photos as soon as they're available.
 *
 * The JS positions items generically (position = index distance from
 * the active one), so this scales cleanly to any item count without
 * touching markup/CSS.
 */

if (!defined('ABSPATH')) {
    exit;
}

$velento_featured_watches = [
    [
        'image' => get_template_directory_uri() . '/assets/images/products/watch-1.avif',
        'name'  => __('مدل کلاسیک', 'velento-shop'),
        'tag'   => __('دست‌ساز رسمی', 'velento-shop'),
    ],
    [
        'image' => get_template_directory_uri() . '/assets/images/products/watch-2.avif',
        'name'  => __('مدل کرونوگراف', 'velento-shop'),
        'tag'   => __('ورزشی', 'velento-shop'),
    ],
    [
        'image' => get_template_directory_uri() . '/assets/images/products/watch-3.avif',
        'name'  => __('مدل هریتیج', 'velento-shop'),
        'tag'   => __('کلاسیک', 'velento-shop'),
    ],
    [
        'image' => get_template_directory_uri() . '/assets/images/brands/omega-speedmaster.webp',
        'name'  => __('اسپیدمستر', 'velento-shop'),
        'tag'   => __('کرونوگراف', 'velento-shop'),
    ],
    [
        'image' => get_template_directory_uri() . '/assets/images/brands/audemars-piguet-royal-oak.webp',
        'name'  => __('رویال اوک', 'velento-shop'),
        'tag'   => __('الماس‌نشان', 'velento-shop'),
    ],
    [
        'image' => get_template_directory_uri() . '/assets/images/products/watch-1.avif',
        'name'  => __('مدل غواصی', 'velento-shop'),
        'tag'   => __('ضدآب حرفه‌ای', 'velento-shop'),
    ],
    [
        'image' => get_template_directory_uri() . '/assets/images/products/watch-2.avif',
        'name'  => __('مدل اسپرت', 'velento-shop'),
        'tag'   => __('روزمره', 'velento-shop'),
    ],
];
?>

<section class="featured-carousel" aria-label="<?php echo esc_attr__('ساعت‌های منتخب', 'velento-shop'); ?>">
    <div class="container featured-carousel-container">

        <div class="section-heading">
            <p class="section-eyebrow"><?php esc_html_e('کالکشن منتخب', 'velento-shop'); ?></p>
            <h2 class="section-title"><?php esc_html_e('ساعت‌های شاخص', 'velento-shop'); ?></h2>
        </div>

        <div class="carousel-stage" id="watch-carousel">

            <button type="button" class="carousel-arrow carousel-prev" aria-label="<?php echo esc_attr__('قبلی', 'velento-shop'); ?>">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 6 9 12 15 18"></polyline>
                </svg>
            </button>

            <div class="carousel-track" id="carousel-track">
                <?php foreach ($velento_featured_watches as $i => $watch) : ?>
                    <div class="carousel-item" data-index="<?php echo (int) $i; ?>">
                        <div class="carousel-item-photo">
                            <img src="<?php echo esc_url($watch['image']); ?>" alt="<?php echo esc_attr($watch['name']); ?>" loading="lazy">
                        </div>
                        <p class="carousel-item-tag"><?php echo esc_html($watch['tag']); ?></p>
                        <h3 class="carousel-item-name"><?php echo esc_html($watch['name']); ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="carousel-arrow carousel-next" aria-label="<?php echo esc_attr__('بعدی', 'velento-shop'); ?>">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 6 15 12 9 18"></polyline>
                </svg>
            </button>

        </div>

        <div class="carousel-dots" id="carousel-dots" role="tablist" aria-label="<?php echo esc_attr__('انتخاب ساعت', 'velento-shop'); ?>"></div>

    </div>
</section>
