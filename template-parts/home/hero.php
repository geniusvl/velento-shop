<?php
/**
 * Template Part: Home Hero
 *
 * Wide copy + rotating brand slider hero for Velento Shop. The right-hand
 * side auto-rotates between the brands carried in the shop (Rolex,
 * Omega, Audemars Piguet for now), each with its own product photo and
 * name caption — this is the "hero slider" the homepage wireframe calls
 * for. Pure CSS + a small vanilla-JS timer (hero-slider.js), no
 * external library.
 *
 * TODO: the `rolex` slide below uses a placeholder photo
 * (assets/images/products/watch-2.avif) since no real Rolex product
 * photo has been supplied yet — swap it out as soon as one exists.
 * Once real WooCommerce products/brand terms exist, this array should
 * be replaced by a small wc_get_products()/tax_query pull instead of
 * a static list.
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

$velento_shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');

$velento_hero_eyebrow  = get_theme_mod('velento_hero_eyebrow', __('ولنتو شاپ', 'velento-shop'));
$velento_hero_title    = get_theme_mod('velento_hero_title', __('زمان، وقتی با ظرافت طراحی شود', 'velento-shop'));
$velento_hero_subtitle = get_theme_mod('velento_hero_subtitle', __('هر قطعه در ولنتو شاپ، ترکیبی از دقت مکانیکی و طراحی بی‌زمانه است؛ ساعتی که فراتر از اندازه‌گیری زمان، بخشی از هویت شماست.', 'velento-shop'));
$velento_hero_cta_text = get_theme_mod('velento_hero_cta_text', __('مشاهده‌ی کالکشن', 'velento-shop'));

$velento_hero_slides = [
    [
        'brand' => __('رولکس', 'velento-shop'),
        'name'  => __('مدل کلاسیک اویستر', 'velento-shop'),
        'image' => get_template_directory_uri() . '/assets/images/products/watch-2.avif', // placeholder — see TODO above
    ],
    [
        'brand' => __('امگا', 'velento-shop'),
        'name'  => __('اسپیدمستر کرونوگراف', 'velento-shop'),
        'image' => get_template_directory_uri() . '/assets/images/brands/omega-speedmaster.webp',
    ],
    [
        'brand' => __('ای پی', 'velento-shop'),
        'name'  => __('رویال اوک الماس‌نشان', 'velento-shop'),
        'image' => get_template_directory_uri() . '/assets/images/brands/audemars-piguet-royal-oak.webp',
    ],
];
?>

<section class="hero-section" id="hero">
    <div class="hero-bg-ring" aria-hidden="true"></div>

    <div class="container hero-grid">

        <div class="hero-copy">
            <p class="hero-eyebrow">
                <span class="hero-eyebrow-mark" aria-hidden="true">&#10022;</span>
                <?php echo esc_html($velento_hero_eyebrow); ?>
                <span class="hero-eyebrow-mark" aria-hidden="true">&#10022;</span>
            </p>
            <h1 class="hero-title"><?php echo esc_html($velento_hero_title); ?></h1>
            <p class="hero-subtitle">
                <?php echo esc_html($velento_hero_subtitle); ?>
            </p>

            <div class="hero-actions">
                <a href="<?php echo esc_url($velento_shop_url); ?>" class="hero-cta">
                    <?php echo esc_html($velento_hero_cta_text); ?>
                </a>
            </div>

            <ul class="hero-trust-row">
                <li><?php esc_html_e('ضمانت اصالت', 'velento-shop'); ?></li>
                <li><?php esc_html_e('ارسال ایمن به سراسر کشور', 'velento-shop'); ?></li>
                <li><?php esc_html_e('خدمات پس از فروش اختصاصی', 'velento-shop'); ?></li>
            </ul>
        </div>

        <div class="hero-slider" id="hero-slider" aria-roledescription="carousel" aria-label="<?php echo esc_attr__('برندهای منتخب', 'velento-shop'); ?>">
            <div class="hero-image-glow" aria-hidden="true"></div>

            <div class="hero-slider-track">
                <?php foreach ($velento_hero_slides as $i => $slide) : ?>
                    <figure class="hero-slide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo (int) $i; ?>" aria-hidden="<?php echo $i === 0 ? 'false' : 'true'; ?>">
                        <img
                            src="<?php echo esc_url($slide['image']); ?>"
                            alt="<?php echo esc_attr($slide['brand'] . ' — ' . $slide['name']); ?>"
                            width="250" height="305"
                            <?php echo $i === 0 ? '' : 'loading="lazy"'; ?>
                        >
                        <figcaption class="hero-slide-caption">
                            <span class="hero-slide-brand"><?php echo esc_html($slide['brand']); ?></span>
                            <span class="hero-slide-name"><?php echo esc_html($slide['name']); ?></span>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>

            <div class="hero-slider-dots" role="tablist" aria-label="<?php echo esc_attr__('انتخاب برند', 'velento-shop'); ?>">
                <?php foreach ($velento_hero_slides as $i => $slide) : ?>
                    <button
                        type="button"
                        class="hero-slider-dot<?php echo $i === 0 ? ' is-active' : ''; ?>"
                        data-index="<?php echo (int) $i; ?>"
                        role="tab"
                        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        aria-label="<?php echo esc_attr($slide['brand']); ?>"
                    ></button>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>
