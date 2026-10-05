<?php
/**
 * Live Product Search + Recent Searches + Recently Viewed
 *
 * Everything the search overlay (template-parts/header/search-overlay.php)
 * needs on the server side lives here, kept separate from inc/ajax.php's
 * cart/login endpoints so search can grow (facets, synonyms, etc.) without
 * that file turning into a dumping ground.
 *
 * Storage model:
 * - Guests: recent searches + recently viewed products live entirely in
 *   localStorage (assets/js/modules/search.js). No server storage, no
 *   extra requests.
 * - Logged-in users: mirrored into user meta so history follows them
 *   across devices. Recently viewed is written directly on page load
 *   (template_redirect) since a single-product view is already a full
 *   request; recent searches are written via a small fire-and-forget
 *   AJAX call. Server data is sent to the browser once via
 *   wp_localize_script() and merged client-side with localStorage.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('VELENTO_META_RECENT_SEARCHES')) {
    define('VELENTO_META_RECENT_SEARCHES', '_velento_recent_searches');
}
if (!defined('VELENTO_META_RECENTLY_VIEWED')) {
    define('VELENTO_META_RECENTLY_VIEWED', '_velento_recently_viewed');
}

function velento_search_min_chars()
{
    return 2;
}

function velento_search_max_results()
{
    return 8;
}

function velento_search_max_history()
{
    return 8;
}

function velento_search_max_viewed()
{
    return 12;
}

/**
 * WordPress redirects a search that matches exactly one post straight
 * to that post's permalink (redirect_canonical()). For a product
 * search that means a single-match search silently skips the results
 * page and lands the visitor on a single product — but "مشاهده همه
 * نتایج" in the search overlay is meant to always open the shop-style
 * results page (search.php), even when there's only one match. Disable
 * just that one redirect, for product searches only.
 */
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (is_search() && get_query_var('post_type') === 'product') {
        return false;
    }
    return $redirect_url;
}, 10, 2);

/**
 * Build the small, front-end-safe payload used for every product row
 * the search UI can render (live results, recent searches don't need
 * this, recently viewed does).
 */
function velento_search_build_result($product)
{
    if (!($product instanceof WC_Product) || !$product->is_visible()) {
        return null;
    }

    $image_id = $product->get_image_id();
    $image = $image_id
        ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail')
        : wc_placeholder_img_src('woocommerce_thumbnail');

    $brand_terms = get_the_terms($product->get_id(), 'velento_brand');
    $brand = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0]->name : '';

    $stock_text = '';
    $in_stock = $product->is_in_stock();

    if ($product->managing_stock()) {
        $qty = $product->get_stock_quantity();
        if ($qty !== null) {
            $stock_text = $qty > 0
                /* translators: %s: stock quantity */
                ? sprintf(__('موجودی: %s عدد', 'velento-shop'), number_format_i18n($qty))
                : __('ناموجود', 'velento-shop');
        }
    } elseif (!$in_stock) {
        $stock_text = __('ناموجود', 'velento-shop');
    }

    return [
        'id'         => $product->get_id(),
        'name'       => $product->get_name(),
        'url'        => get_permalink($product->get_id()),
        'image'      => $image ?: '',
        'price_html' => wp_kses_post($product->get_price_html()),
        'brand'      => $brand,
        'in_stock'   => $in_stock,
        'stock_text' => $stock_text,
    ];
}

/**
 * Query args shared by the AJAX handler: real WooCommerce products only,
 * respecting the same "exclude from search" visibility WooCommerce's own
 * search uses, so nothing hidden by a store owner leaks into results.
 */
function velento_search_tax_query()
{
    $tax_query = [];

    if (function_exists('wc_get_product_visibility_term_ids')) {
        $visibility_ids = wc_get_product_visibility_term_ids();
        if (!empty($visibility_ids['exclude-from-search'])) {
            $tax_query[] = [
                'taxonomy' => 'product_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => [$visibility_ids['exclude-from-search']],
                'operator' => 'NOT IN',
            ];
        }
    }

    return $tax_query;
}

/**
 * AJAX: live search-as-you-type. Public (nopriv) since it only reads
 * published catalog data.
 */
function velento_ajax_product_search()
{
    check_ajax_referer('velento_search', 'nonce');

    if (!function_exists('velento_rate_limit') || !velento_rate_limit('search', 60, 60)) {
        wp_send_json_error([
            'message' => __('تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop'),
        ], 429);
    }

    $term = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
    $term = trim($term);

    if (mb_strlen($term) > 120) {
        wp_send_json_error(['message' => __('عبارت جستجو بیش از حد طولانی است.', 'velento-shop')], 400);
    }

    if (mb_strlen($term) < velento_search_min_chars() || !class_exists('WooCommerce')) {
        wp_send_json_success(['products' => [], 'term' => $term, 'count' => 0]);
    }

    $query = new WP_Query([
        's'                      => $term,
        'post_type'              => 'product',
        'post_status'            => 'publish',
        'posts_per_page'         => velento_search_max_results(),
        'tax_query'              => velento_search_tax_query(), // phpcs:ignore WordPress.DB.SlowDBQuery
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
        'ignore_sticky_posts'    => true,
    ]);

    $results = [];
    foreach ($query->posts as $post_row) {
        $product = wc_get_product($post_row->ID);
        $row = $product ? velento_search_build_result($product) : null;
        if ($row) {
            $results[] = $row;
        }
    }
    wp_reset_postdata();

    wp_send_json_success([
        'products' => $results,
        'term'     => $term,
        'count'    => count($results),
    ]);
}
add_action('wp_ajax_velento_search', 'velento_ajax_product_search');
add_action('wp_ajax_nopriv_velento_search', 'velento_ajax_product_search');

/**
 * Recent searches — user meta helpers (logged-in users only).
 */
function velento_search_save_recent_term($user_id, $term)
{
    $term = trim((string) $term);
    if ($term === '') {
        return;
    }

    $list = get_user_meta($user_id, VELENTO_META_RECENT_SEARCHES, true);
    $list = is_array($list) ? $list : [];

    $list = array_values(array_filter($list, function ($existing) use ($term) {
        return mb_strtolower((string) $existing) !== mb_strtolower($term);
    }));

    array_unshift($list, $term);
    $list = array_slice($list, 0, velento_search_max_history());

    update_user_meta($user_id, VELENTO_META_RECENT_SEARCHES, $list);
}

/**
 * AJAX: persist one search term for a logged-in user. Guests are
 * skipped server-side (their history is localStorage-only) but still
 * get a success response so the front-end fire-and-forget call never
 * logs a console error.
 */
function velento_ajax_search_recent_save()
{
    check_ajax_referer('velento_search', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_success();
    }

    if (!function_exists('velento_rate_limit') || !velento_rate_limit('search_recent_save', 40, 300)) {
        wp_send_json_error([], 429);
    }

    $term = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
    if (mb_strlen($term) > 120) {
        wp_send_json_error([], 400);
    }
    velento_search_save_recent_term(get_current_user_id(), $term);

    wp_send_json_success();
}
add_action('wp_ajax_velento_search_recent_save', 'velento_ajax_search_recent_save');
add_action('wp_ajax_nopriv_velento_search_recent_save', 'velento_ajax_search_recent_save');

/**
 * AJAX: clear a logged-in user's recent-search history.
 */
function velento_ajax_search_recent_clear()
{
    check_ajax_referer('velento_search', 'nonce');

    if (is_user_logged_in()) {
        delete_user_meta(get_current_user_id(), VELENTO_META_RECENT_SEARCHES);
    }

    wp_send_json_success();
}
add_action('wp_ajax_velento_search_recent_clear', 'velento_ajax_search_recent_clear');
add_action('wp_ajax_nopriv_velento_search_recent_clear', 'velento_ajax_search_recent_clear');

/**
 * Recently viewed — tracked directly on the single-product request for
 * logged-in users (no extra AJAX round-trip needed for this part).
 */
function velento_search_track_recently_viewed()
{
    if (!is_user_logged_in() || !function_exists('is_product') || !is_product()) {
        return;
    }

    $product_id = get_queried_object_id();
    if (!$product_id) {
        return;
    }

    $user_id = get_current_user_id();
    $list = get_user_meta($user_id, VELENTO_META_RECENTLY_VIEWED, true);
    $list = is_array($list) ? $list : [];

    $list = array_values(array_diff($list, [$product_id]));
    array_unshift($list, (int) $product_id);
    $list = array_slice($list, 0, velento_search_max_viewed());

    update_user_meta($user_id, VELENTO_META_RECENTLY_VIEWED, $list);
}
add_action('template_redirect', 'velento_search_track_recently_viewed');

/**
 * Turn a list of product IDs (from user meta) into render-ready rows,
 * silently dropping anything since deleted, unpublished or hidden.
 */
function velento_search_products_from_ids($ids)
{
    $items = [];
    foreach ((array) $ids as $id) {
        $product = wc_get_product($id);
        $row = $product ? velento_search_build_result($product) : null;
        if ($row) {
            $items[] = $row;
        }
        if (count($items) >= velento_search_max_viewed()) {
            break;
        }
    }
    return $items;
}

/**
 * AJAX: clear a logged-in user's recently-viewed history.
 */
function velento_ajax_search_viewed_clear()
{
    check_ajax_referer('velento_search', 'nonce');

    if (is_user_logged_in()) {
        delete_user_meta(get_current_user_id(), VELENTO_META_RECENTLY_VIEWED);
    }

    wp_send_json_success();
}
add_action('wp_ajax_velento_search_viewed_clear', 'velento_ajax_search_viewed_clear');
add_action('wp_ajax_nopriv_velento_search_viewed_clear', 'velento_ajax_search_viewed_clear');

/**
 * Data handed to assets/js/modules/search.js via wp_localize_script().
 * Called from inc/enqueue.php so all script-localization stays in one
 * place; this file only builds the payload.
 */
function velento_search_localize_data()
{
    $data = [
        'ajaxUrl'        => admin_url('admin-ajax.php'),
        'nonce'          => wp_create_nonce('velento_search'),
        'minChars'       => velento_search_min_chars(),
        'isLoggedIn'     => is_user_logged_in(),
        'recentSearches' => [],
        'recentlyViewed' => [],
        'currentProduct' => null,
    ];

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

        $recent = get_user_meta($user_id, VELENTO_META_RECENT_SEARCHES, true);
        $data['recentSearches'] = is_array($recent) ? array_values($recent) : [];

        $viewed_ids = get_user_meta($user_id, VELENTO_META_RECENTLY_VIEWED, true);
        $data['recentlyViewed'] = velento_search_products_from_ids(is_array($viewed_ids) ? $viewed_ids : []);
    }

    if (function_exists('is_product') && is_product()) {
        $product = wc_get_product(get_queried_object_id());
        if ($product) {
            $data['currentProduct'] = velento_search_build_result($product);
        }
    }

    return $data;
}
