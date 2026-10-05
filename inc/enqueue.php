<?php

if (!defined('ABSPATH')) {
    exit;
}

function velento_enqueue_assets()
{
    $theme_version = wp_get_theme()->get('Version');

    // CSS
    wp_enqueue_style(
        'velento-variables',
        get_template_directory_uri() . '/assets/css/base/variables.css',
        [],
        $theme_version
    );

    wp_enqueue_style(
        'velento-reset',
        get_template_directory_uri() . '/assets/css/base/reset.css',
        ['velento-variables'],
        $theme_version
    );

    wp_enqueue_style(
        'velento-typography',
        get_template_directory_uri() . '/assets/css/base/typography.css',
        ['velento-reset'],
        $theme_version
    );

    wp_enqueue_style(
        'velento-grid',
        get_template_directory_uri() . '/assets/css/layout/grid.css',
        ['velento-typography'],
        $theme_version
    );

    // Shared component library (buttons, forms, cards, modal, icons,
    // slider) — used on every page from here on (home today; shop,
    // product, cart, checkout, account as those get built), so it's
    // loaded globally, once, right after the layout grid.
    $components = ['buttons', 'forms', 'cards', 'modal', 'icons', 'slider'];
    $component_deps = ['velento-grid'];
    foreach ($components as $component) {
        $handle = 'velento-' . $component;
        wp_enqueue_style(
            $handle,
            get_template_directory_uri() . '/assets/css/components/' . $component . '.css',
            $component_deps,
            $theme_version
        );
        $component_deps = [$handle];
    }

    wp_enqueue_style(
        'velento-cart-drawer',
        get_template_directory_uri() . '/assets/css/components/cart-drawer.css',
        [$component_deps[0]],
        $theme_version
    );

    wp_enqueue_style(
        'velento-login-drawer',
        get_template_directory_uri() . '/assets/css/components/login-drawer.css',
        ['velento-cart-drawer'],
        $theme_version
    );

    // Search overlay results/recent-searches/recently-viewed UI — lives
    // inside the header markup, so it loads in this same chain, right
    // before layout/header.css.
    wp_enqueue_style(
        'velento-search-results',
        get_template_directory_uri() . '/assets/css/components/search-results.css',
        ['velento-login-drawer'],
        $theme_version
    );

    wp_enqueue_style(
        'velento-header',
        get_template_directory_uri() . '/assets/css/layout/header.css',
        ['velento-search-results'],
        $theme_version
    );

    wp_enqueue_style(
        'velento-footer',
        get_template_directory_uri() . '/assets/css/layout/footer.css',
        ['velento-header'],
        $theme_version
    );

    // Page-specific stylesheets — only loaded on the page they apply to,
    // so we're not shipping shop/product/account CSS on every request.
    // Load the homepage stylesheet globally; every homepage selector is
    // scoped to .velento-home/body.front-page, which also makes the
    // design render correctly inside the WordPress Customizer preview.
    wp_enqueue_style(
        'velento-home',
        get_template_directory_uri() . '/assets/css/pages/home.css',
        ['velento-footer'],
        $theme_version
    );

    if (is_home() || is_category() || is_tag() || is_date() || is_author() || is_page_template('page-blog.php') || is_page('blog') || get_query_var('velento_blog')) {
        wp_enqueue_style(
            'velento-blog',
            get_template_directory_uri() . '/assets/css/pages/blog.css',
            ['velento-footer'],
            $theme_version
        );
    }

    if (is_page_template('page-about.php') || is_page('about')) {
        wp_enqueue_style(
            'velento-about',
            get_template_directory_uri() . '/assets/css/pages/about.css',
            ['velento-footer'],
            $theme_version
        );
    }

    if (is_page_template('page-brands.php') || is_page('brands')) {
        // Self-contained page (own layout, own tokens mirrored from
        // :root, same pattern as about.css) — no longer depends on
        // shop.css since it no longer reuses the product-grid card
        // markup the previous version of this page used.
        wp_enqueue_style(
            'velento-brands',
            get_template_directory_uri() . '/assets/css/pages/brands.css',
            ['velento-footer'],
            $theme_version
        );
    }

    if (function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag() || (is_search() && get_query_var('post_type') === 'product'))) {
        wp_enqueue_style(
            'velento-shop-style',
            get_template_directory_uri() . '/assets/css/pages/shop.css',
            ['velento-footer'],
            $theme_version
        );
    }

    if (function_exists('is_product') && is_product()) {
        wp_enqueue_style(
            'velento-product',
            get_template_directory_uri() . '/assets/css/pages/product.css',
            ['velento-footer'],
            $theme_version
        );
    }

    if (function_exists('is_product') && is_product()) {
        wp_enqueue_script(
            'velento-product',
            get_template_directory_uri() . '/assets/js/modules/product.js',
            [],
            $theme_version,
            ['strategy' => 'defer', 'in_footer' => true]
        );
    }

    if (function_exists('is_account_page') && is_account_page()) {
        wp_enqueue_style(
            'velento-account',
            get_template_directory_uri() . '/assets/css/pages/account.css',
            ['velento-footer'],
            $theme_version
        );
    }

    // Generic fallback (page.php) — any Page without its own dedicated
    // template, including the WooCommerce My Account page, which loads
    // this alongside account.css above.
    if (is_page() && !is_page_template('page-about.php') && !is_page('about')) {
        wp_enqueue_style(
            'velento-page-default',
            get_template_directory_uri() . '/assets/css/pages/page-default.css',
            ['velento-footer'],
            $theme_version
        );
    }

    // responsive.css goes last so its media-query overrides always win.
    wp_enqueue_style(
        'velento-responsive',
        get_template_directory_uri() . '/assets/css/responsive.css',
        ['velento-footer'],
        $theme_version
    );

    // Unified typography is deliberately loaded after every page stylesheet
    // so Home, Shop, Brands, Blog, About, Product, Account and WooCommerce
    // screens share one visual type hierarchy.
    wp_enqueue_style(
        'velento-typography-unified',
        get_template_directory_uri() . '/assets/css/base/typography-unified.css',
        ['velento-responsive'],
        $theme_version
    );

    // JavaScript
    wp_enqueue_script(
        'velento-header',
        get_template_directory_uri() . '/assets/js/header.js',
        [],
        $theme_version,
        true
    );

    wp_localize_script('velento-header', 'velentoCart', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('velento_cart'),
    ]);

    // Search overlay — live AJAX search, recent searches, recently
    // viewed. Self-contained module, no dependency on velento-header,
    // so it can load/execute independently of it.
    wp_enqueue_script(
        'velento-search',
        get_template_directory_uri() . '/assets/js/modules/search.js',
        [],
        $theme_version,
        true
    );

    if (function_exists('velento_search_localize_data')) {
        wp_localize_script('velento-search', 'velentoSearch', velento_search_localize_data());
    }

    // Login/register drawer — plain admin-ajax.php, WordPress-core
    // auth only (wp_signon/wp_insert_user in inc/ajax.php), no
    // WooCommerce dependency. Only needed for logged-out visitors.
    if (!is_user_logged_in()) {
        wp_localize_script('velento-header', 'velentoLogin', [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('velento_login'),
            // Where to send the visitor right after a successful sign-in
            // or registration in the drawer, so they land on their
            // profile instead of just reloading the page they were on.
            'accountUrl' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/'),
        ]);
    }

    // Brands page — scroll-reveal only, no server data needed so no
    // wp_localize_script() call.
    if (is_page_template('page-brands.php') || is_page('brands')) {
        wp_enqueue_script(
            'velento-brands-page',
            get_template_directory_uri() . '/assets/js/pages/brands.js',
            [],
            $theme_version,
            true
        );
    }

    // Shop filters — only ship the filter module where product discovery
    // controls are actually rendered.
    if (function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag())) {
        wp_enqueue_script(
            'velento-filters',
            get_template_directory_uri() . '/assets/js/modules/filters.js',
            [],
            $theme_version,
            ['strategy' => 'defer', 'in_footer' => true]
        );

        if (function_exists('velento_shop_filter_localize_data')) {
            wp_localize_script('velento-filters', 'velentoFilters', velento_shop_filter_localize_data());
        }
    }

    // Front-page-only scripts.
    if (is_front_page()) {
        wp_enqueue_script(
            'velento-home',
            get_template_directory_uri() . '/assets/js/home.js',
            [],
            $theme_version,
            ['strategy' => 'defer', 'in_footer' => true]
        );

        wp_enqueue_script(
            'velento-newsletter',
            get_template_directory_uri() . '/assets/js/modules/newsletter.js',
            [],
            $theme_version,
            ['strategy' => 'defer', 'in_footer' => true]
        );

    }

    wp_enqueue_script(
        'velento-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        $theme_version,
        true
    );
}

add_action('wp_enqueue_scripts', 'velento_enqueue_assets');