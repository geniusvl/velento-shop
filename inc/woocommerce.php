<?php
/**
 * WooCommerce Hooks & Template Overrides
 */

if (!defined('ABSPATH')) {
    exit;
}



/**
 * Keep WooCommerce extension points while preventing its stock breadcrumb
 * and sidebar from being printed around Velento's custom archive/product UI.
 */
function velento_remove_default_woo_chrome()
{
    if (function_exists('is_product') && is_product()) {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    }

    if (function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag())) {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
    }
}
add_action('wp', 'velento_remove_default_woo_chrome', 21);

/**
 * The single-product template supplies its own Details/Specifications/
 * Reviews presentation. Remove only WooCommerce's default tab renderer on
 * product pages to avoid duplicate content; upsells/related products and
 * third-party callbacks attached at other priorities remain available.
 */
function velento_remove_default_product_tabs()
{
    if (function_exists('is_product') && is_product()) {
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    }
}
add_action('wp', 'velento_remove_default_product_tabs', 20);

/**
 * Keeps the header cart-count badge (template-parts/header/actions.php)
 * in sync after an AJAX add-to-cart, without a full page reload.
 * WooCommerce core handles the AJAX call itself on any
 * `.add_to_cart_button` / `.ajax_add_to_cart` element; this just tells
 * it which markup fragment to refresh afterward.
 */
function velento_cart_count_fragment($fragments)
{
    $count = 0;
    if (function_exists('WC') && WC()->cart) {
        $count = WC()->cart->get_cart_contents_count();
    }

    ob_start();
    ?>
    <span class="cart-count cart-count-fragment" data-count="<?php echo (int) $count; ?>"><?php echo $count > 0 ? esc_html($count) : ''; ?></span>
    <?php
    $fragments['span.cart-count-fragment'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'velento_cart_count_fragment');

/**
 * Refresh the complete drawer after WooCommerce's native AJAX add-to-cart.
 */
function velento_cart_drawer_fragment($fragments)
{
    if (function_exists('WC') && WC()->cart) {
        $fragments['.velento-cart-drawer__content'] = velento_get_cart_drawer_content();
    }

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'velento_cart_drawer_fragment');

/**
 * Velento demo catalog: registers a lightweight brand taxonomy and creates
 * ten real WooCommerce products on the first admin/front-end request after
 * the theme is installed. Existing products are never touched.
 */
function velento_register_brand_taxonomy()
{
    if (taxonomy_exists('velento_brand')) return;

    register_taxonomy('velento_brand', ['product'], [
        'labels' => [
            'name' => 'برندها',
            'singular_name' => 'برند',
            'search_items' => 'جستجوی برند',
            'all_items' => 'همه برندها',
            'edit_item' => 'ویرایش برند',
            'add_new_item' => 'افزودن برند',
        ],
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'hierarchical' => false,
        'rewrite' => ['slug' => 'brand'],
    ]);
}
add_action('init', 'velento_register_brand_taxonomy', 5);

function velento_get_or_create_demo_image($filename)
{
    $path = get_template_directory() . '/assets/images/home/' . $filename;
    if (!file_exists($path)) return 0;

    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_velento_demo_image',
        'meta_value' => $filename,
        'fields' => 'ids',
        'posts_per_page' => 1,
    ]);
    if ($existing) return (int) $existing[0];

    $filetype = wp_check_filetype(basename($path), null);
    $attachment = [
        'post_mime_type' => $filetype['type'] ?: 'image/webp',
        'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
    ];
    $attachment_id = wp_insert_attachment($attachment, $path);
    if (is_wp_error($attachment_id)) return 0;

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $metadata = wp_generate_attachment_metadata($attachment_id, $path);
    if ($metadata) wp_update_attachment_metadata($attachment_id, $metadata);
    update_post_meta($attachment_id, '_velento_demo_image', $filename);

    return (int) $attachment_id;
}

function velento_create_demo_store()
{
    if (!class_exists('WooCommerce') || !function_exists('wc_get_product')) return;
    if (get_option('velento_demo_store_seeded')) return;

    $brands = ['رولکس', 'امگا', 'تگ هویر', 'لونژین', 'تودور'];
    $brand_terms = [];
    foreach ($brands as $brand_name) {
        $term = term_exists($brand_name, 'velento_brand');
        if (!$term) $term = wp_insert_term($brand_name, 'velento_brand');
        if (!is_wp_error($term)) {
            $brand_terms[$brand_name] = (int) (is_array($term) ? $term['term_id'] : $term);
        }
    }

    $categories = [
        'مردانه' => 'men',
        'زنانه' => 'women',
        'رسمی' => 'formal',
        'اسپرت' => 'sport',
        'کرنوگراف' => 'chronograph',
    ];
    foreach ($categories as $name => $slug) {
        if (!term_exists($slug, 'product_cat')) wp_insert_term($name, 'product_cat', ['slug' => $slug]);
    }

    $images = ['home-watch-1.webp', 'home-watch-2.webp', 'home-watch-3.webp', 'home-watch-4.webp'];
    $image_ids = [];
    foreach ($images as $img) $image_ids[] = velento_get_or_create_demo_image($img);

    $products = [
        ['رولکس دیت‌جاست کلاسیک', 'رولکس', 'رسمی', 128000000, 112000000, 'VL-1001'],
        ['امگا اسپیدمستر مشکی', 'امگا', 'کرنوگراف', 98000000, 89000000, 'VL-1002'],
        ['تگ هویر کاررا اسپرت', 'تگ هویر', 'اسپرت', 76000000, 64900000, 'VL-1003'],
        ['لونژین مستر کالکشن', 'لونژین', 'رسمی', 69000000, 59900000, 'VL-1004'],
        ['تودور بلک‌بی 58', 'تودور', 'مردانه', 87000000, 79500000, 'VL-1005'],
        ['رولکس اویستر زنانه', 'رولکس', 'زنانه', 104000000, 93900000, 'VL-1006'],
        ['امگا کانستلیشن نقره‌ای', 'امگا', 'زنانه', 72000000, 65500000, 'VL-1007'],
        ['تگ هویر موناکو', 'تگ هویر', 'کرنوگراف', 83000000, 74900000, 'VL-1008'],
        ['لونژین هریتیج کلاسیک', 'لونژین', 'مردانه', 61000000, 54900000, 'VL-1009'],
        ['تودور رنجر مشکی', 'تودور', 'اسپرت', 65000000, 57900000, 'VL-1010'],
    ];

    foreach ($products as $i => $row) {
        [$name, $brand, $category, $regular, $sale, $sku] = $row;
        if (wc_get_product_id_by_sku($sku)) continue;

        $product = new WC_Product_Simple();
        $product->set_name($name);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_description('یک انتخاب لوکس از کالکشن ولنتو با تمرکز بر طراحی، جزئیات و تجربه‌ای ماندگار.');
        $product->set_short_description('ساعت لوکس با طراحی مینیمال و جزئیات دقیق؛ مناسب برای استایل روزمره و رسمی.');
        $product->set_regular_price((string) $regular);
        $product->set_sale_price((string) $sale);
        $product->set_price((string) $sale);
        $product->set_sku($sku);
        $product->set_manage_stock(true);
        $product->set_stock_quantity(12 + ($i % 7));
        $product->set_stock_status('instock');
        $product->set_weight((string) (80 + ($i * 7)));
        
        $category_term = get_term_by('slug', $categories[$category], 'product_cat');
        if ($category_term && !is_wp_error($category_term)) {
            $product->set_category_ids([(int) $category_term->term_id]);
        }
        $product_id = $product->save();

        if (!empty($brand_terms[$brand])) wp_set_object_terms($product_id, [$brand_terms[$brand]], 'velento_brand');
        if (!empty($image_ids[$i % count($image_ids)])) set_post_thumbnail($product_id, $image_ids[$i % count($image_ids)]);
    }

    // Create a dedicated Brands page if one doesn't already exist.
    $brand_page = get_page_by_path('brands');
    if (!$brand_page) {
        $brand_page_id = wp_insert_post([
            'post_title' => 'برندها',
            'post_name' => 'brands',
            'post_status' => 'publish',
            'post_type' => 'page',
        ]);
        if (!is_wp_error($brand_page_id)) {
            update_post_meta($brand_page_id, '_wp_page_template', 'page-brands.php');
            update_option('velento_brands_page_id', (int) $brand_page_id);
        }
    }

    update_option('velento_demo_store_seeded', 1);
    update_option('velento_demo_store_seeded_at', current_time('mysql'));
    flush_rewrite_rules(false);
}
add_action('init', 'velento_create_demo_store', 30);
