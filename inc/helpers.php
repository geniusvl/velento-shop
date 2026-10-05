<?php
/**
 * Always return customers to the homepage after logging out.
 * The login drawer remains the only front-end sign-in entry point.
 */
add_filter('woocommerce_logout_default_redirect_url', function ($redirect) {
    return home_url('/');
});

/**
 * Shared Helper Functions
 * TODO: small reusable utility functions used across template files.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * True whenever the visitor is looking at "the blog" by any of the three
 * possible routes the theme supports:
 *   1) WordPress' native Posts page (is_home())
 *   2) the theme's own /blog/ rewrite fallback (velento_blog query var)
 *   3) a real Page that uses the page-blog.php template
 *
 * Centralized here so every current-menu-item check (inc/setup.php,
 * inc/helpers.php) agrees on the same definition — previously each
 * check only covered #1/#2, so viewing a Page assigned the page-blog.php
 * template never highlighted "وبلاگ" in the nav (bug).
 */
function velento_is_blog_page()
{
    return (is_home() && !is_front_page())
        || get_query_var('velento_blog')
        || is_page_template('page-blog.php');
}

/**
 * Same idea as velento_is_blog_page() above, for the Brands page: covers
 * both a real product_brand taxonomy archive and this install's actual
 * setup (a Page using page-brands.php, which — since its slug is
 * "brands" — WordPress's own template hierarchy will select even when
 * no template was explicitly chosen in Page Attributes, so checking
 * is_page_template() alone is not enough on its own; is_page() against
 * the resolved page covers that case too).
 */
function velento_is_brands_page()
{
    if (function_exists('is_tax') && is_tax('product_brand')) {
        return true;
    }
    $brands_page = get_page_by_path('brands') ?: get_page_by_path('برندها');
    return $brands_page && is_page($brands_page->ID);
}

/**
 * URL of the dedicated OTP login page (templates/page/page-login.php).
 * Looks for whichever Page the store owner assigned that template to
 * in the editor; falls back to the theme's My Account URL if no page
 * has been assigned yet, so the account icon never points to a 404.
 */
function velento_get_login_url()
{
    $pages = get_posts([
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'templates/page/page-login.php',
        'fields'         => 'ids',
    ]);

    if (!empty($pages)) {
        $url = get_permalink($pages[0]);
        if ($url) {
            return $url;
        }
    }

    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/');
}

/**
 * Fallback for the 'primary' nav menu location when no menu has been
 * assigned yet in Appearance > Menus. Renders the four core links the
 * theme is designed around, so the header never looks broken pre-setup.
 */
function velento_get_blog_url()
{
    // Preferred: WordPress' native Posts page.
    $posts_page_id = (int) get_option('page_for_posts');
    if ($posts_page_id > 0) {
        $url = get_permalink($posts_page_id);
        if ($url) {
            return $url;
        }
    }

    // Next: a manually-created Blog page.
    $blog_page = get_page_by_path('blog') ?: get_page_by_path('وبلاگ');
    if ($blog_page) {
        $url = get_permalink($blog_page->ID);
        if ($url) {
            return $url;
        }
    }

    // Final fallback: the theme owns /blog/ and renders its custom blog
    // template there, so the header link can never point to a dead URL.
    return home_url('/blog/');
}

/**
 * Fallback for the 'primary' nav menu location when no menu has been
 * assigned yet in Appearance > Menus. Renders the core links the theme
 * is designed around, including a guaranteed connection to the real
 * WordPress Posts/Blog page.
 */
/**
 * Ensure the About page exists so the header link never lands on a 404.
 * The page is created only once and uses the dedicated page-about.php template.
 */
function velento_ensure_about_page()
{
    $about_page = get_page_by_path('about');

    if (!$about_page) {
        $about_id = wp_insert_post([
            'post_title'   => 'درباره ما',
            'post_name'    => 'about',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
            'meta_input'   => ['_wp_page_template' => 'page-about.php'],
        ], true);

        if (!is_wp_error($about_id)) {
            update_option('velento_about_page_id', (int) $about_id, false);
        }
    } elseif (get_page_template_slug($about_page->ID) !== 'page-about.php') {
        update_post_meta($about_page->ID, '_wp_page_template', 'page-about.php');
    }
}
add_action('init', 'velento_ensure_about_page', 8);

function velento_get_about_url()
{
    $about_page = get_page_by_path('about');
    return $about_page ? get_permalink($about_page->ID) : home_url('/about/');
}

function velento_default_menu()
{
    $shop_url = home_url('/shop');
    if (function_exists('wc_get_page_id')) {
        $shop_page_id = wc_get_page_id('shop');
        if ($shop_page_id && $shop_page_id > 0) {
            $shop_url = get_permalink($shop_page_id);
        }
    }

    $blog_url = velento_get_blog_url();

    $about_page = get_page_by_path('about') ?: get_page_by_path('درباره-ما');
    $about_url = $about_page ? get_permalink($about_page->ID) : velento_get_about_url();

    // Resolved unconditionally (not just inside the taxonomy-archive
    // branch below) because it's also needed to correctly detect when
    // the visitor is currently on the Brands page — this install uses
    // a Page template (page-brands.php), not a product_brand taxonomy
    // archive, so 'current' must be able to match that case too.
    $brands_page = get_page_by_path('brands') ?: get_page_by_path('برندها');

    $brands_url = home_url('/brands');
    if (taxonomy_exists('product_brand')) {
        $brands_archive = get_post_type_archive_link('product_brand');
        if ($brands_archive) {
            $brands_url = $brands_archive;
        }
    } elseif ($brands_page) {
        $brands_url = get_permalink($brands_page->ID);
    }

    $items = [
        ['label' => __('خانه', 'velento-shop'), 'url' => home_url('/'), 'current' => is_front_page() && !velento_is_blog_page()],
        ['label' => __('فروشگاه', 'velento-shop'), 'url' => $shop_url, 'current' => function_exists('is_shop') && is_shop()],
        ['label' => __('درباره ما', 'velento-shop'), 'url' => $about_url, 'current' => $about_page && is_page($about_page->ID)],
        // Was `is_tax('product_brand')` only — always false for this
        // install, since Brands here is a Page (page-brands.php), not a
        // taxonomy archive, so the nav item never lit up.
        ['label' => __('برندها', 'velento-shop'), 'url' => $brands_url, 'current' => velento_is_brands_page()],
        ['label' => __('وبلاگ', 'velento-shop'), 'url' => $blog_url, 'current' => velento_is_blog_page()],
    ];

    echo '<ul class="primary-menu">';
    foreach ($items as $item) {
        printf(
            '<li class="menu-item%s"><a href="%s">%s</a></li>',
            $item['current'] ? ' current-menu-item' : '',
            esc_url($item['url']),
            esc_html($item['label'])
        );
    }
    echo '</ul>';
}

/**
 * Fallback for the 'footer' nav menu location when no menu has been
 * assigned yet in Appearance > Menus. Mirrors velento_default_menu()
 * above but reused inside the footer's "quick links" column
 * (template-parts/footer/footer-main.php).
 */
function velento_default_footer_menu()
{
    $shop_url = home_url('/shop');
    if (function_exists('wc_get_page_id')) {
        $shop_page_id = wc_get_page_id('shop');
        if ($shop_page_id && $shop_page_id > 0) {
            $shop_url = get_permalink($shop_page_id);
        }
    }

    $about_page = get_page_by_path('about') ?: get_page_by_path('درباره-ما');
    $contact_page = get_page_by_path('contact') ?: get_page_by_path('تماس-با-ما');

    $items = [
        ['label' => __('خانه', 'velento-shop'), 'url' => home_url('/')],
        ['label' => __('فروشگاه', 'velento-shop'), 'url' => $shop_url],
        ['label' => __('درباره ما', 'velento-shop'), 'url' => $about_page ? get_permalink($about_page->ID) : home_url('/about')],
        ['label' => __('تماس با ما', 'velento-shop'), 'url' => $contact_page ? get_permalink($contact_page->ID) : home_url('/contact')],
    ];

    echo '<ul class="footer-menu">';
    foreach ($items as $item) {
        printf('<li><a href="%s">%s</a></li>', esc_url($item['url']), esc_html($item['label']));
    }
    echo '</ul>';
}

/**
 * Static customer-service links for the footer's third column
 * (template-parts/footer/footer-main.php). TODO: point these at real
 * pages once they're created (raahnamaa-ye kharid, bazgasht-e kaalaa,
 * hariim-e khosoosi, so'aalaat-e motedaavel).
 */
function velento_default_service_links()
{
    $items = [
        ['label' => __('راهنمای خرید', 'velento-shop'), 'url' => home_url('/raahnamaa-ye-kharid')],
        ['label' => __('شرایط بازگشت کالا', 'velento-shop'), 'url' => home_url('/bazgasht-e-kaalaa')],
        ['label' => __('حریم خصوصی', 'velento-shop'), 'url' => home_url('/hariim-e-khosoosi')],
        ['label' => __('سوالات متداول', 'velento-shop'), 'url' => home_url('/soalaat-e-motedaavel')],
    ];

    foreach ($items as $item) {
        printf('<li><a href="%s">%s</a></li>', esc_url($item['url']), esc_html($item['label']));
    }
}



/**
 * Build consistent product badges from WooCommerce product state.
 */
function velento_get_product_badges($product)
{
    if (!($product instanceof WC_Product)) {
        return [];
    }

    $badges = [];

    if ($product->is_on_sale()) {
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();

        if ($regular > 0 && $sale > 0 && $sale < $regular) {
            $badges[] = [
                'label' => sprintf(
                    /* translators: %s: discount percentage */
                    __('%s٪ تخفیف', 'velento-shop'),
                    number_format_i18n(round((1 - ($sale / $regular)) * 100))
                ),
                'class' => 'is-sale',
            ];
        }
    }

    if ($product->get_total_sales() > 10) {
        $badges[] = [
            'label' => __('پرفروش', 'velento-shop'),
            'class' => 'is-bestseller',
        ];
    }

    $created = $product->get_date_created();
    if ($created && $created->getTimestamp() > strtotime('-30 days')) {
        $badges[] = [
            'label' => __('جدید', 'velento-shop'),
            'class' => 'is-new',
        ];
    }

    if ($product->managing_stock()) {
        $quantity = $product->get_stock_quantity();
        if (null !== $quantity && $quantity > 0 && $quantity <= 5) {
            $badges[] = [
                'label' => __('محدود', 'velento-shop'),
                'class' => 'is-limited',
            ];
        }
    }

    return $badges;
}

/**
 * Human-readable stock label shared by shop/home product cards.
 */
function velento_get_product_stock_label($product)
{
    if (!($product instanceof WC_Product)) {
        return '';
    }

    if ($product->managing_stock() && null !== $product->get_stock_quantity()) {
        return sprintf(
            /* translators: %s: stock quantity */
            __('موجودی: %s عدد', 'velento-shop'),
            number_format_i18n(max(0, (int) $product->get_stock_quantity()))
        );
    }

    return $product->is_in_stock()
        ? __('موجود', 'velento-shop')
        : __('ناموجود', 'velento-shop');
}

/**
 * Small inline SVG icons for the My Account sidebar
 * (woocommerce/myaccount/navigation.php, dashboard.php). Kept as one
 * lookup so every account endpoint — including ones added later by a
 * plugin — gets a consistent icon instead of a missing/broken one.
 */
function velento_account_nav_icon($name)
{
    $icons = [
        'grid' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"></rect><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"></rect><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"></rect><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"></rect></svg>',
        'box' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 7.5 12 3l8.5 4.5V16.5L12 21l-8.5-4.5z"></path><path d="M3.5 7.5 12 12l8.5-4.5"></path><path d="M12 12v9"></path></svg>',
        'download' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"></path><path d="M7.5 10.5 12 15l4.5-4.5"></path><path d="M4.5 19.5h15"></path></svg>',
        'pin' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21z"></path><circle cx="12" cy="9.5" r="2.3"></circle></svg>',
        'card' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5.5" width="18" height="13" rx="2"></rect><path d="M3 10h18"></path></svg>',
        'user' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8.3" r="3.6"></circle><path d="M4.5 20c1.4-3.6 4.4-5.4 7.5-5.4S18.1 16.4 19.5 20"></path></svg>',
        'logout' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 20H5.5A1.5 1.5 0 0 1 4 18.5v-13A1.5 1.5 0 0 1 5.5 4H9"></path><path d="M15.5 16.5 20 12l-4.5-4.5"></path><path d="M20 12H9"></path></svg>',
        'dot' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle></svg>',
    ];

    return $icons[$name] ?? $icons['dot'];
}

/**
 * Maps a WooCommerce My Account endpoint slug to one of the icons above.
 * Unknown endpoints (e.g. from a plugin) fall back to a plain dot rather
 * than breaking the row layout.
 */
function velento_account_nav_icon_for_endpoint($endpoint)
{
    $map = [
        'dashboard'       => 'grid',
        'orders'          => 'box',
        'downloads'       => 'download',
        'edit-address'    => 'pin',
        'payment-methods' => 'card',
        'edit-account'    => 'user',
        'customer-logout' => 'logout',
    ];

    return velento_account_nav_icon($map[$endpoint] ?? 'dot');
}

/**
 * Returns the cart drawer content so WooCommerce AJAX can refresh the drawer
 * without a page reload.
 */
function velento_get_cart_drawer_content()
{
    ob_start();
    get_template_part('template-parts/header/cart-drawer-content');
    return ob_get_clean();
}

/**
 * Register a real /blog/ fallback when the site owner has not configured a
 * Posts page yet. This keeps the header Blog link functional out of the box.
 */
function velento_register_blog_fallback_route()
{
    add_rewrite_rule('^blog/?$', 'index.php?velento_blog=1', 'top');
}
add_action('init', 'velento_register_blog_fallback_route');

function velento_register_blog_query_var($vars)
{
    $vars[] = 'velento_blog';
    return $vars;
}
add_filter('query_vars', 'velento_register_blog_query_var');

function velento_blog_fallback_template($template)
{
    if (get_query_var('velento_blog')) {
        $blog_template = get_template_directory() . '/page-blog.php';
        if (file_exists($blog_template)) {
            return $blog_template;
        }
    }

    return $template;
}
add_filter('template_include', 'velento_blog_fallback_template', 99);

function velento_flush_blog_fallback_rules()
{
    velento_register_blog_fallback_route();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'velento_flush_blog_fallback_rules');
