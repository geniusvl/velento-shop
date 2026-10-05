<?php
/**
 * Velento Shop — Premium RTL Persian homepage.
 */
if (!defined('ABSPATH')) exit;
get_header();

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$watch_images = [
    get_template_directory_uri() . '/assets/images/home/home-watch-1.webp',
    get_template_directory_uri() . '/assets/images/home/home-watch-2.webp',
    get_template_directory_uri() . '/assets/images/home/home-watch-3.webp',
    get_template_directory_uri() . '/assets/images/home/home-watch-4.webp',
];

// Hero — eyebrow/title/subtitle/CTA stay editable from Customizer
// (Appearance → Customize → Hero), same settings the old hero template
// part used, so nothing breaks for whoever already set these.
$hero_eyebrow  = get_theme_mod('velento_hero_eyebrow', __('ولنتو شاپ', 'velento-shop'));
$hero_title    = get_theme_mod('velento_hero_title', __('زمان، وقتی با ظرافت طراحی شود', 'velento-shop'));
$hero_subtitle = get_theme_mod('velento_hero_subtitle', __('هر قطعه در ولنتو شاپ، ترکیبی از دقت مکانیکی و طراحی بی‌زمانه است؛ ساعتی که فراتر از اندازه‌گیری زمان، بخشی از هویت شماست.', 'velento-shop'));
$hero_cta_text = get_theme_mod('velento_hero_cta_text', __('مشاهده‌ی کالکشن', 'velento-shop'));

$hero_slides = [
    [
        'brand' => __('رولکس', 'velento-shop'),
        'name'  => __('دی-دیت اویستر طلایی', 'velento-shop'),
        'image' => get_template_directory_uri() . '/assets/images/home/hero-rolex-daydate.avif',
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

$categories_main = [
    ['title'=>'مردانه','slug'=>'men','desc'=>'قدرت و شخصیت','num'=>'01','img'=>0],
    ['title'=>'زنانه','slug'=>'women','desc'=>'ظرافت و جزئیات','num'=>'02','img'=>1],
];
$categories_sub = [
    ['title'=>'رسمی','slug'=>'formal','desc'=>'برای لحظه‌های مهم','num'=>'03','img'=>2],
    ['title'=>'اسپرت','slug'=>'sport','desc'=>'برای هر ماجراجویی','num'=>'04','img'=>3],
    ['title'=>'کرنوگراف','slug'=>'chronograph','desc'=>'مهندسی دقیق زمان','num'=>'05','img'=>2],
    ['title'=>'بند چرم','slug'=>'leather-band','desc'=>'کلاسیک و ماندگار','num'=>'06','img'=>0],
];

$brand_logos = [
    ['name' => 'Rolex',           'file' => 'rolex.webp'],
    ['name' => 'Omega',           'file' => 'omega.webp'],
    ['name' => 'Patek Philippe',  'file' => 'patek-philippe.webp'],
    ['name' => 'Audemars Piguet', 'file' => 'audemars-piguet.webp'],
    ['name' => 'Hublot',          'file' => 'hublot.webp'],
    ['name' => 'Citizen',         'file' => 'citizen.webp'],
];

function velento_home_cat_url($slug, $fallback) {
    $term = function_exists('get_term_by') ? get_term_by('slug', $slug, 'product_cat') : false;
    if ($term && !is_wp_error($term)) {
        $url = get_term_link($term);
        if (!is_wp_error($url)) return $url;
    }
    return $fallback;
}

function velento_render_home_product($product, $fallback = '', $index = 0, $sale = false)
{
    if (!($product instanceof WC_Product)) {
        return;
    }

    $product_id = $product->get_id();
    $link = get_permalink($product_id);
    $image_id = $product->get_image_id();
    $image = $image_id
        ? wp_get_attachment_image_url($image_id, 'large')
        : ($fallback ?: wc_placeholder_img_src('woocommerce_thumbnail'));
    $price = $product->get_price_html();
    $brand_terms = get_the_terms($product_id, 'velento_brand');
    $brand = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0]->name : '';
    $badges = function_exists('velento_get_product_badges') ? velento_get_product_badges($product) : [];
    $stock_label = function_exists('velento_get_product_stock_label') ? velento_get_product_stock_label($product) : '';

    ?>
    <article class="v-product-card velento-product-card">
        <a class="v-product-image" href="<?php echo esc_url($link); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
            <?php if ($badges) : ?>
                <span class="v-product-badges" aria-label="<?php echo esc_attr__('برچسب‌های محصول', 'velento-shop'); ?>">
                    <?php foreach ($badges as $badge) : ?>
                        <span class="v-product-badge <?php echo esc_attr($badge['class']); ?>"><?php echo esc_html($badge['label']); ?></span>
                    <?php endforeach; ?>
                </span>
            <?php endif; ?>
            <span class="v-heart" aria-hidden="true">♡</span>
            <span class="v-product-image-frame">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy" decoding="async">
            </span>
            <span class="v-quick" aria-hidden="true"><?php esc_html_e('مشاهده محصول', 'velento-shop'); ?> <b>←</b></span>
        </a>
        <div class="v-product-body">
            <?php if ($brand) : ?>
                <a class="v-product-brand" href="<?php echo esc_url(get_term_link($brand_terms[0])); ?>"><?php echo esc_html($brand); ?></a>
            <?php endif; ?>
            <h3><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
            <div class="v-price">
                <strong><?php echo wp_kses_post($price); ?></strong>
                <?php if ($stock_label) : ?><span class="v-home-stock"><?php echo esc_html($stock_label); ?></span><?php endif; ?>
            </div>
        </div>
    </article>
    <?php
}

/**
 * One full "product rail" section: a golden billboard tile + a horizontal
 * product slider beside it, spanning the shared wide section width. Used
 * for both the bestsellers rail and the special-sale rail so they stay
 * visually identical, as requested.
 */
function velento_render_product_rail($a) {
    $a += [
        'id'               => 'v-products-rail',
        'slider_key'       => 'bestsellers',
        'overline'         => '',
        'title'            => '',
        'billboard_kicker' => 'ولنتو شاپ',
        'billboard_title'  => '',
        'billboard_sub'    => '',
        'cta_text'         => 'مشاهده همه',
        'cta_url'          => '#',
        'products'         => [],
        'sale'             => false,
        'count'            => 5,
        'watch_images'     => [],
    ];
    $products = array_values(array_filter((array) $a['products'], static function ($product) {
        return $product instanceof WC_Product;
    }));
    if (!$products) {
        return;
    }
    ?>
    <section class="v-products" id="<?php echo esc_attr($a['id']); ?>">
        <div class="v-wrap">
            <div class="v-products-layout">
                <div class="v-product-billboard">
                    <span class="v-product-billboard-kicker"><?php echo esc_html($a['billboard_kicker']); ?></span>
                    <h3><?php echo wp_kses_post($a['billboard_title']); ?></h3>
                    <p><?php echo esc_html($a['billboard_sub']); ?></p>
                    <a class="v-product-billboard-cta" href="<?php echo esc_url($a['cta_url']); ?>"><?php echo esc_html($a['cta_text']); ?> <span>←</span></a>
                </div>
                <div class="v-products-main">
                    <div class="v-heading">
                        <div><span class="v-overline"><?php echo esc_html($a['overline']); ?></span><h2><?php echo wp_kses_post($a['title']); ?></h2></div>
                        <div class="v-slider-nav"><button type="button" data-slider-prev="<?php echo esc_attr($a['slider_key']); ?>">→</button><button type="button" data-slider-next="<?php echo esc_attr($a['slider_key']); ?>">←</button></div>
                    </div>
                    <div class="v-product-slider" data-slider="<?php echo esc_attr($a['slider_key']); ?>">
                        <?php foreach (array_slice($products, 0, (int) $a['count']) as $i => $product) velento_render_home_product($product, '', $i, $a['sale']); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}
?>

<main id="primary" class="site-main front-page velento-home">
    <section class="v-hero" id="v-hero">
        <div class="v-wrap v-hero-inner">

            <div class="v-hero-copy">
                <p class="v-hero-eyebrow">
                    <span aria-hidden="true">&#10022;</span>
                    <?php echo esc_html($hero_eyebrow); ?>
                    <span aria-hidden="true">&#10022;</span>
                </p>
                <h2 class="v-hero-title"><?php echo esc_html($hero_title); ?></h2>
                <p class="v-hero-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
                <div class="v-hero-actions">
                    <a class="v-btn v-btn-primary" href="<?php echo esc_url($shop_url); ?>"><?php echo esc_html($hero_cta_text); ?></a>
                </div>
                <ul class="v-hero-trust">
                    <li><?php esc_html_e('ضمانت اصالت', 'velento-shop'); ?></li>
                    <li><?php esc_html_e('ارسال ایمن به سراسر کشور', 'velento-shop'); ?></li>
                    <li><?php esc_html_e('خدمات پس از فروش اختصاصی', 'velento-shop'); ?></li>
                </ul>
            </div>

            <div class="v-hero-showcase" id="hero-slider" aria-roledescription="carousel" aria-label="<?php echo esc_attr__('برندهای منتخب', 'velento-shop'); ?>">
                <div class="v-hero-glow" aria-hidden="true"></div>

                <div class="v-hero-slides">
                    <?php foreach ($hero_slides as $i => $slide) : ?>
                        <figure class="v-hero-slide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo (int) $i; ?>" aria-hidden="<?php echo $i === 0 ? 'false' : 'true'; ?>">
                            <img
                                src="<?php echo esc_url($slide['image']); ?>"
                                alt="<?php echo esc_attr($slide['brand'] . ' — ' . $slide['name']); ?>"
                                width="250" height="305"
                                <?php echo $i === 0 ? '' : 'loading="lazy"'; ?>
                            >
                            <figcaption class="v-hero-caption">
                                <span class="v-hero-caption-brand"><?php echo esc_html($slide['brand']); ?></span>
                                <span class="v-hero-caption-name"><?php echo esc_html($slide['name']); ?></span>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>

                <div class="v-hero-dots" role="tablist" aria-label="<?php echo esc_attr__('انتخاب برند', 'velento-shop'); ?>">
                    <?php foreach ($hero_slides as $i => $slide) : ?>
                        <button
                            type="button"
                            class="v-hero-dot<?php echo $i === 0 ? ' is-active' : ''; ?>"
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

    <section class="v-categories" id="v-categories">
        <div class="v-wrap">
            <div class="v-heading">
                <div><span class="v-overline">انتخاب بر اساس سبک</span><h2>ساعتی برای هر <em>شخصیت</em></h2></div>
                <a href="<?php echo esc_url($shop_url); ?>">مشاهده همه مدل‌ها <span>←</span></a>
            </div>
            <div class="v-cat-hero-grid">
                <?php foreach($categories_main as $cat): $url=velento_home_cat_url($cat['slug'],$shop_url); ?>
                    <a class="v-cat-card v-cat-card--hero" href="<?php echo esc_url($url); ?>">
                        <span class="v-cat-num"><?php echo esc_html($cat['num']); ?></span>
                        <div class="v-cat-image"><img src="<?php echo esc_url($watch_images[$cat['img']]); ?>" alt="" loading="lazy"></div>
                        <div class="v-cat-text"><span><?php echo esc_html($cat['desc']); ?></span><h3><?php echo esc_html($cat['title']); ?></h3></div>
                        <span class="v-cat-arrow">↙</span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="v-cat-grid v-cat-grid--sub">
                <?php foreach($categories_sub as $cat): $url=velento_home_cat_url($cat['slug'],$shop_url); ?>
                    <a class="v-cat-card" href="<?php echo esc_url($url); ?>">
                        <span class="v-cat-num"><?php echo esc_html($cat['num']); ?></span>
                        <div class="v-cat-image"><img src="<?php echo esc_url($watch_images[$cat['img']]); ?>" alt="" loading="lazy"></div>
                        <div class="v-cat-text"><span><?php echo esc_html($cat['desc']); ?></span><h3><?php echo esc_html($cat['title']); ?></h3></div>
                        <span class="v-cat-arrow">↙</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php
    velento_render_product_rail([
        'id'               => 'v-bestsellers',
        'slider_key'       => 'bestsellers',
        'overline'         => 'انتخاب مشتریان ولنتو',
        'title'            => 'پرفروش‌ترین <em>ساعت‌ها</em>',
        'billboard_kicker' => 'ولنتو شاپ',
        'billboard_title'  => 'پرفروش‌ترین‌ها',
        'billboard_sub'    => 'محبوب‌ترین انتخاب مشتریان ولنتو در یک نگاه.',
        'cta_text'         => 'مشاهده همه',
        'cta_url'          => $shop_url,
        'products'         => function_exists('wc_get_products') ? wc_get_products(['status'=>'publish','limit'=>5,'orderby'=>'popularity','order'=>'DESC']) : [],
        'sale'             => false,
        'count'            => 5,
        'watch_images'     => $watch_images,
    ]);
    ?>

    <section class="v-brand-band" id="v-brand-band">
        <div class="v-wrap">
            <div class="v-heading v-heading--center">
                <div><span class="v-overline">همکاری با برترین‌ها</span><h2>برندهای <em>معتبر جهانی</em></h2></div>
            </div>
        </div>
        <div class="v-brand-track" role="list" aria-label="<?php echo esc_attr__('برندهای همکار', 'velento-shop'); ?>">
            <div class="v-brand-track-inner">
                <?php for ($r = 0; $r < 2; $r++) : foreach ($brand_logos as $logo) : ?>
                    <span class="v-brand-logo" role="listitem" aria-hidden="<?php echo $r === 0 ? 'false' : 'true'; ?>">
                        <img
                            src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/brands/logos/' . $logo['file']); ?>"
                            alt="<?php echo $r === 0 ? esc_attr($logo['name']) : ''; ?>"
                            loading="lazy"
                        >
                    </span>
                <?php endforeach; endfor; ?>
            </div>
        </div>
    </section>

    <section class="v-video-feature">
        <video class="v-video-bg" autoplay muted loop playsinline poster="<?php echo esc_url(get_template_directory_uri() . '/assets/videos/pioneer-poster.jpg'); ?>">
            <source src="<?php echo esc_url(get_template_directory_uri() . '/assets/videos/pioneer.mp4'); ?>" type="video/mp4">
        </video>
        <div class="v-video-overlay" aria-hidden="true"></div>
        <div class="v-video-content">
            <span class="v-overline">صنعتگری بی‌نظیر</span>
            <h2>دقتی که در هر <em>جزئیات</em> احساس می‌شود</h2>
            <a class="v-btn v-btn-primary" href="<?php echo esc_url($shop_url); ?>">مشاهده محصولات <span>←</span></a>
        </div>
    </section>

    <section class="v-features">
        <div class="v-wrap">
            <div class="v-feature-grid">
                <div class="v-feature">
                    <span class="v-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 3l7 3v5c0 4.6-3 7.9-7 10-4-2.1-7-5.4-7-10V6l7-3z"/><path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3>تضمین اصالت</h3>
                    <p>اصالت تمام محصولات پیش از ارسال بررسی می‌شود.</p>
                </div>
                <div class="v-feature">
                    <span class="v-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17.5" cy="18" r="1.6"/></svg></span>
                    <h3>ارسال سریع و امن</h3>
                    <p>بسته‌بندی حرفه‌ای و ارسال مطمئن به سراسر کشور.</p>
                </div>
                <div class="v-feature">
                    <span class="v-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M4 12a8 8 0 0116 0v4a2 2 0 01-2 2h-1v-6h3"/><path d="M4 16v-4h3v6H5a1 1 0 01-1-1z"/></svg></span>
                    <h3>مشاوره تخصصی</h3>
                    <p>برای انتخاب مدل مناسب، کارشناسان ما کنار شما هستند.</p>
                </div>
                <div class="v-feature">
                    <span class="v-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="9" r="5.2"/><path d="M9 13.5L7.5 21l4.5-2.4 4.5 2.4-1.5-7.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3>ضمانت خدمات</h3>
                    <p>خدمات پس از خرید بخشی از تجربه لوکس ولنتو است.</p>
                </div>
            </div>
        </div>
    </section>

    <?php
    velento_render_product_rail([
        'id'               => 'v-special-sale',
        'slider_key'       => 'sale',
        'overline'         => 'فرصت محدود',
        'title'            => 'فروش <em>ویژه</em>',
        'billboard_kicker' => 'تا پایان موجودی',
        'billboard_title'  => 'فروش ویژه',
        'billboard_sub'    => 'ساعت‌های منتخب، با تخفیف ویژه و برای مدتی محدود.',
        'cta_text'         => 'مشاهده تخفیف‌ها',
        'cta_url'          => $shop_url,
        'products'         => function_exists('wc_get_products') ? wc_get_products(['status'=>'publish','limit'=>5,'on_sale'=>true]) : [],
        'sale'             => true,
        'count'            => 5,
        'watch_images'     => $watch_images,
    ]);
    ?>

    <section class="v-sale">
        <div class="v-wrap">
            <div class="v-sale-box">
                <div class="v-sale-copy"><span class="v-overline">فقط برای مدت محدود</span><h2>مدل‌های منتخب،<br><em>قیمت‌های جذاب‌تر.</em></h2><p>فرصت خرید ساعت‌های منتخب با تخفیف ویژه را از دست ندهید.</p><a class="v-btn v-btn-primary" href="<?php echo esc_url($shop_url); ?>">مشاهده تخفیف‌ها <span>←</span></a></div>
                <div class="v-sale-product"><span class="v-sale-stamp">SALE</span><img src="<?php echo esc_url($watch_images[1]); ?>" alt="ساعت تخفیف خورده"></div>
            </div>
        </div>
    </section>

    <section class="v-faq" id="v-faq">
        <div class="v-wrap">
            <div class="v-faq-grid">
                <div class="v-faq-intro"><span class="v-overline">راهنمای خرید</span><h2>سؤالاتی که<br><em>زیاد می‌پرسید.</em></h2><p>اگر پاسخ سوالتان را اینجا پیدا نکردید، با ما تماس بگیرید.</p><a href="<?php echo esc_url(home_url('/contact/')); ?>">تماس با ما ←</a></div>
                <div class="v-accordion">
                    <?php $faqs=[['چطور از اصالت ساعت مطمئن شویم؟','تمام محصولات پیش از ارسال توسط تیم ولنتو بررسی می‌شوند و همراه با ضمانت اصالت ارسال خواهند شد.'],['ارسال سفارش چقدر زمان می‌برد؟','سفارش‌ها در کوتاه‌ترین زمان ممکن آماده و با بسته‌بندی امن ارسال می‌شوند. زمان دقیق بر اساس مقصد هنگام ثبت سفارش نمایش داده می‌شود.'],['آیا امکان مشاوره برای انتخاب ساعت وجود دارد؟','بله. قبل از خرید می‌توانید برای انتخاب مدل، سایز، سبک و بودجه مناسب از مشاوره تخصصی ولنتو استفاده کنید.'],['شرایط مرجوعی چگونه است؟','شرایط و بازه مرجوعی در صفحه قوانین فروشگاه توضیح داده شده است و پشتیبانی نیز راهنمایی‌تان می‌کند.']]; foreach($faqs as $i=>$faq): ?>
                        <details <?php echo $i===0?'open':''; ?>><summary><span><?php echo esc_html($faq[0]); ?></span><b>+</b></summary><p><?php echo esc_html($faq[1]); ?></p></details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="v-newsletter">
        <div class="v-wrap"><div class="v-newsletter-box"><div><span class="v-overline">باشگاه ولنتو</span><h2>از کالکشن‌های جدید<br>زودتر باخبر شوید.</h2></div><form class="newsletter-form v-newsletter-form" id="velento-newsletter-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                        <?php wp_nonce_field('velento_subscribe', 'velento_newsletter_nonce'); ?>
                        <input type="hidden" name="action" value="velento_subscribe">
                        <label class="screen-reader-text" for="velento-newsletter-email"><?php esc_html_e('آدرس ایمیل', 'velento-shop'); ?></label>
                        <input type="email" maxlength="254" id="velento-newsletter-email" name="email" placeholder="<?php echo esc_attr__('ایمیل شما', 'velento-shop'); ?>" aria-label="<?php echo esc_attr__('ایمیل شما', 'velento-shop'); ?>" required>
                        <button type="submit" class="newsletter-submit"><?php esc_html_e('عضویت', 'velento-shop'); ?> <span>←</span></button>
                    </form></div></div>
    </section>
</main>
<?php get_footer(); ?>
