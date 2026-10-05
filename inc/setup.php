<?php

if (!defined('ABSPATH')) {
    exit;
}

function velento_setup()
{
    // Dynamic <title>
    add_theme_support('title-tag');

    // Featured Images
    add_theme_support('post-thumbnails');

    // Custom Logo
    add_theme_support('custom-logo');

    // RSS Feed
    add_theme_support('automatic-feed-links');

    // HTML5 Support
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // WooCommerce Support
    add_theme_support('woocommerce');

    // Product gallery zoom/lightbox/swipe — important for a watch store
    // where buyers want to inspect dial/case detail closely.
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Responsive Embeds
    add_theme_support('responsive-embeds');

    // Wide Alignment (Gutenberg)
    add_theme_support('align-wide');

    // Block Styles
    add_theme_support('wp-block-styles');

    // Editor Styles
    add_theme_support('editor-styles');

    // Menus
    register_nav_menus([
        'primary' => __('Primary Menu', 'velento-shop'),
        'footer'  => __('Footer Menu', 'velento-shop'),
    ]);
}

add_action('after_setup_theme', 'velento_setup');

/**
 * Fix: WordPress marks both the Home link AND the Blog link as
 * "current-menu-item" whenever Settings > Reading is set to show the
 * latest posts on the homepage (the default) — because on that URL,
 * both is_home() and is_front_page() are true at once. That's exactly
 * why two nav buttons can appear gold/underlined together even though
 * only one page is actually being viewed. Keep the highlight only on
 * the item that truly points to the front page.
 */
function velento_fix_duplicate_current_menu_item($classes, $item)
{
    if (is_home() && is_front_page() && in_array('current-menu-item', $classes, true)) {
        if (untrailingslashit($item->url) !== untrailingslashit(home_url('/'))) {
            $classes = array_diff($classes, ['current-menu-item', 'current_page_item']);
        }
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'velento_fix_duplicate_current_menu_item', 10, 2);
/**
 * Keep the primary header's About link connected to the real About page.
 * If a custom WordPress menu omits it, append it once at render time.
 */
function velento_primary_menu_about_objects($items, $args)
{
    if (!isset($args->theme_location) || 'primary' !== $args->theme_location) {
        return $items;
    }

    $about_url = function_exists('velento_get_about_url') ? velento_get_about_url() : home_url('/about/');
    $about_page = get_page_by_path('about') ?: get_page_by_path('درباره-ما');
    $about_id = $about_page ? (int) $about_page->ID : 0;

    foreach ($items as $item) {
        $title = trim(wp_strip_all_tags((string) ($item->title ?? '')));
        if (in_array($title, ['درباره ما', 'About Us', 'About'], true)) {
            $item->title = __('درباره ما', 'velento-shop');
            $item->url = $about_url;
            $item->type = 'custom';
            $item->object = '';
            $item->object_id = 0;
            $item->classes = array_values(array_diff((array) $item->classes, [
                'current-menu-item','current_page_item','current-menu-parent','current_page_parent',
            ]));
            if ($about_id && is_page($about_id)) {
                $item->classes[] = 'current-menu-item';
            }
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'velento_primary_menu_about_objects', 25, 2);

function velento_ensure_about_menu_item($items, $args)
{
    if (!isset($args->theme_location) || 'primary' !== $args->theme_location) {
        return $items;
    }

    if (preg_match('/درباره\s*ما|About(?:\s*Us)?/u', wp_strip_all_tags($items))) {
        return $items;
    }

    $about_url = function_exists('velento_get_about_url') ? velento_get_about_url() : home_url('/about/');
    $about_page = get_page_by_path('about') ?: get_page_by_path('درباره-ما');
    $current = $about_page && is_page($about_page->ID);

    return $items . sprintf(
        '<li class="menu-item menu-item-about%s"><a href="%s">%s</a></li>',
        $current ? ' current-menu-item' : '',
        esc_url($about_url),
        esc_html__('درباره ما', 'velento-shop')
    );
}
add_filter('wp_nav_menu_items', 'velento_ensure_about_menu_item', 25, 2);

/**
 * Keep the public navigation terminology consistent and force the primary
 * menu's old Journal/Blog item to the real Velento blog URL.
 */
function velento_is_blog_menu_item_object($item)
{
    if (!$item || !is_object($item)) {
        return false;
    }

    $title = trim(wp_strip_all_tags((string) ($item->title ?? '')));
    $url   = untrailingslashit((string) ($item->url ?? ''));

    if (in_array($title, ['ژورنال', 'Journal', 'Blog', 'وبلاگ'], true)) {
        return true;
    }

    $blog_url = untrailingslashit(velento_get_blog_url());
    return $url !== '' && $blog_url !== '' && $url === $blog_url;
}

function velento_primary_menu_objects($items, $args)
{
    if (!isset($args->theme_location) || 'primary' !== $args->theme_location) {
        return $items;
    }

    $blog_url = velento_get_blog_url();

    foreach ($items as $item) {
        if (!velento_is_blog_menu_item_object($item)) {
            continue;
        }

        $item->title = __('وبلاگ', 'velento-shop');
        $item->url = $blog_url;
        $item->type = 'custom';
        $item->object = '';
        $item->object_id = 0;

        $item->classes = array_values(array_diff((array) $item->classes, [
            'current-menu-item',
            'current_page_item',
            'current-menu-parent',
            'current_page_parent',
        ]));

        if (velento_is_blog_page()) {
            $item->classes[] = 'current-menu-item';
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'velento_primary_menu_objects', 20, 2);

/**
 * If the active custom primary menu has no Blog/Journal item, append the
 * link at the HTML stage. This is safer than fabricating a partial menu
 * item object and guarantees compatibility with every WordPress walker.
 */
function velento_ensure_blog_menu_item($items, $args)
{
    if (!isset($args->theme_location) || 'primary' !== $args->theme_location) {
        return $items;
    }

    if (preg_match('/(?:ژورنال|Journal|Blog|وبلاگ)/u', wp_strip_all_tags($items))) {
        return $items;
    }

    $is_current = velento_is_blog_page();

    return $items . sprintf(
        '<li class="menu-item menu-item-blog%s"><a href="%s">%s</a></li>',
        $is_current ? ' current-menu-item' : '',
        esc_url(velento_get_blog_url()),
        esc_html__('وبلاگ', 'velento-shop')
    );
}
add_filter('wp_nav_menu_items', 'velento_ensure_blog_menu_item', 20, 2);

/**
 * Fallback menu: always use the same real blog URL.
 */
function velento_default_menu_blog_current($classes, $item)
{
    if (isset($item->url) && untrailingslashit($item->url) === untrailingslashit(velento_get_blog_url())) {
        if (velento_is_blog_page()) {
            $classes[] = 'current-menu-item';
        }
    }
    return array_unique($classes);
}
add_filter('nav_menu_css_class', 'velento_default_menu_blog_current', 20, 2);

/**
 * Same fix as above, for the Brands nav item — covers the case where a
 * real menu is assigned to the "primary" location (in which case
 * velento_default_menu()'s own 'current' check in inc/helpers.php never
 * runs at all, since that function is only the fallback_cb for when no
 * menu is assigned). Matches by URL like the blog filter does, so it
 * works whether the "برندها" item was added as a Page link or a Custom
 * Link pointing at the same URL.
 */
function velento_brands_menu_current($classes, $item)
{
    if (isset($item->url)) {
        $brands_page = get_page_by_path('brands') ?: get_page_by_path('برندها');
        $brands_url  = $brands_page ? get_permalink($brands_page->ID) : home_url('/brands');
        if (untrailingslashit($item->url) === untrailingslashit($brands_url) && velento_is_brands_page()) {
            $classes[] = 'current-menu-item';
        }
    }
    return array_unique($classes);
}
add_filter('nav_menu_css_class', 'velento_brands_menu_current', 20, 2);

/**
 * While viewing the blog (any of the three routes velento_is_blog_page()
 * covers), forcibly strip current/ancestor classes off the "خانه" item.
 *
 * WordPress core itself — independently of the two filters above — can
 * mark the Home item current/ancestor (e.g. it treats the blog page as
 * a descendant of Home in the page hierarchy, or Home is the configured
 * front page). Those two filters only ever *add* the class to وبلاگ;
 * neither of them touches خانه, so on a page-blog.php template Home was
 * staying gold at the same time as وبلاگ. This runs last and always wins.
 */
function velento_strip_home_current_on_blog($classes, $item)
{
    if (!velento_is_blog_page()) {
        return $classes;
    }

    $home_url  = untrailingslashit(home_url('/'));
    $is_home   = isset($item->url) && untrailingslashit($item->url) === $home_url;
    $front_id  = (int) get_option('page_on_front');
    $is_home   = $is_home || (isset($item->object, $item->object_id) && 'page' === $item->object && $front_id > 0 && (int) $item->object_id === $front_id);

    if (!$is_home) {
        return $classes;
    }

    return array_values(array_diff($classes, [
        'current-menu-item',
        'current_page_item',
        'current-menu-parent',
        'current_page_parent',
        'current-menu-ancestor',
        'current_page_ancestor',
    ]));
}
add_filter('nav_menu_css_class', 'velento_strip_home_current_on_blog', 30, 2);
