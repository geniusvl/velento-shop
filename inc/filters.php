<?php
/**
 * Velento Shop — Product discovery filters.
 *
 * Keeps all shop filtering in one module. Values are resolved from real
 * WooCommerce taxonomies/attributes; no product values are hardcoded.
 * The same query builder powers the normal archive (progressive enhancement)
 * and the AJAX filter endpoint.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return the canonical filter configuration. Attribute taxonomies are
 * resolved at runtime, so a filter only appears when the store actually
 * has the corresponding WooCommerce attribute registered.
 */
function velento_filter_attribute_taxonomy($keys)
{
    if (!function_exists('wc_get_attribute_taxonomies')) {
        return '';
    }

    $taxonomies = wc_get_attribute_taxonomies();
    if (empty($taxonomies)) {
        return '';
    }

    $normalized_keys = array_map('sanitize_title', (array) $keys);

    foreach ($taxonomies as $attribute) {
        $slug = sanitize_title($attribute->attribute_name);
        $label = sanitize_title($attribute->attribute_label);

        if (in_array($slug, $normalized_keys, true) || in_array($label, $normalized_keys, true)) {
            return wc_attribute_taxonomy_name($attribute->attribute_name);
        }
    }

    return '';
}

function velento_shop_filter_definitions()
{
    $definitions = [
        'brand' => [
            'label'    => __('برند', 'velento-shop'),
            'taxonomy' => taxonomy_exists('velento_brand') ? 'velento_brand' : (taxonomy_exists('product_brand') ? 'product_brand' : ''),
        ],
        'category' => [
            'label'    => __('دسته‌بندی', 'velento-shop'),
            'taxonomy' => 'product_cat',
        ],
        'strap' => [
            'label'    => __('نوع بند', 'velento-shop'),
            'taxonomy' => velento_filter_attribute_taxonomy(['strap', 'band', 'نوع-بند']),
        ],
        'movement' => [
            'label'    => __('نوع موتور', 'velento-shop'),
            'taxonomy' => velento_filter_attribute_taxonomy(['movement', 'movement-type', 'mechanism', 'نوع-موتور']),
        ],
        'movement_count' => [
            'label'    => __('تعداد موتور', 'velento-shop'),
            'taxonomy' => velento_filter_attribute_taxonomy(['movement-count', 'movement_count', 'calibers', 'تعداد-موتور']),
        ],
        'gender' => [
            'label'    => __('جنسیت', 'velento-shop'),
            'taxonomy' => velento_filter_attribute_taxonomy(['gender', 'جنسیت']),
        ],
        'material' => [
            'label'    => __('جنس بدنه', 'velento-shop'),
            'taxonomy' => velento_filter_attribute_taxonomy(['material', 'case-material', 'جنس-بدنه']),
        ],
        'color' => [
            'label'    => __('رنگ', 'velento-shop'),
            'taxonomy' => velento_filter_attribute_taxonomy(['color', 'colour', 'رنگ']),
        ],
        'water_resistance' => [
            'label'    => __('مقاومت در برابر آب', 'velento-shop'),
            'taxonomy' => velento_filter_attribute_taxonomy(['water-resistance', 'water_resistance', 'waterproof', 'مقاومت-در-برابر-آب']),
        ],
    ];

    return $definitions;
}

function velento_shop_filter_values($key)
{
    $definitions = velento_shop_filter_definitions();

    if (!isset($definitions[$key]) || empty($definitions[$key]['taxonomy'])) {
        return [];
    }

    $terms = get_terms([
        'taxonomy'   => $definitions[$key]['taxonomy'],
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'number'     => 100,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return [];
    }

    return $terms;
}

function velento_filter_slug_list($value)
{
    $values = is_array($value) ? $value : [$value];
    $slugs = [];

    foreach ($values as $item) {
        if (!is_scalar($item)) {
            continue;
        }

        $slug = sanitize_title((string) $item);
        if ($slug !== '') {
            $slugs[] = $slug;
            if (count($slugs) >= 30) {
                break;
            }
        }
    }

    return array_values(array_unique($slugs));
}

function velento_shop_filter_state()
{
    $state = [
        'brand'            => [],
        'category'         => [],
        'strap'            => [],
        'movement'         => [],
        'movement_count'   => [],
        'gender'           => [],
        'material'         => [],
        'color'            => [],
        'water_resistance' => [],
        'stock'            => '',
        'sale'             => false,
        'orderby'          => 'menu_order',
        'paged'            => 1,
    ];

    foreach (array_keys($state) as $key) {
        if (in_array($key, ['stock', 'orderby', 'paged'], true)) {
            continue;
        }

        if ($key === 'sale') {
            $state[$key] = !empty($_GET[$key]);
            continue;
        }

        $value = isset($_GET[$key]) ? wp_unslash($_GET[$key]) : [];
        $state[$key] = velento_filter_slug_list($value);
    }

    $state['stock'] = isset($_GET['stock']) && is_scalar($_GET['stock']) ? sanitize_key(wp_unslash($_GET['stock'])) : '';
    if (!in_array($state['stock'], ['instock', 'outofstock', 'onbackorder'], true)) {
        $state['stock'] = '';
    }

    $state['sale'] = !empty($_GET['sale']);


    if (isset($_GET['orderby']) && is_scalar($_GET['orderby']) && $_GET['orderby'] !== '') {
        $state['orderby'] = sanitize_key(wp_unslash($_GET['orderby']));
    }

    $allowed_orderby = ['menu_order', 'date', 'popularity', 'price', 'price-desc', 'discount'];
    if (!in_array($state['orderby'], $allowed_orderby, true)) {
        $state['orderby'] = 'menu_order';
    }


    $state['paged'] = max(1, isset($_GET['paged']) && is_scalar($_GET['paged']) ? absint($_GET['paged']) : 1);

    return $state;
}

function velento_shop_tax_query_from_state($state)
{
    $tax_query = [];

    foreach (velento_shop_filter_definitions() as $key => $definition) {
        if (empty($definition['taxonomy']) || empty($state[$key])) {
            continue;
        }

        $term_ids = [];
        foreach ((array) $state[$key] as $slug) {
            $term = get_term_by('slug', $slug, $definition['taxonomy']);
            if ($term && !is_wp_error($term)) {
                $term_ids[] = (int) $term->term_id;
            }
        }

        if ($term_ids) {
            $tax_query[] = [
                'taxonomy' => $definition['taxonomy'],
                'field'    => 'term_id',
                'terms'    => $term_ids,
                'operator' => 'IN',
            ];
        }
    }

    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    return $tax_query;
}

function velento_shop_meta_query_from_state($state)
{
    $meta_query = [];


    if ($state['stock'] !== '') {
        $meta_query[] = [
            'key'     => '_stock_status',
            'value'   => $state['stock'],
            'compare' => '=',
        ];
    }

    if ($state['sale']) {
        $meta_query[] = [
            'key'     => '_sale_price',
            'value'   => '',
            'compare' => '!=',
        ];
    }


    return $meta_query;
}

function velento_shop_discount_sorted_ids()
{
    $cache_key = 'velento_discount_product_ids_v1';
    $cached = get_transient($cache_key);

    if (is_array($cached)) {
        return array_map('absint', $cached);
    }

    if (!function_exists('wc_get_products')) {
        return [];
    }

    $products = wc_get_products([
        'status'  => 'publish',
        'limit'   => -1,
        'return'  => 'objects',
        'on_sale' => true,
    ]);

    $ranked = [];
    foreach ($products as $product) {
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();

        if ($regular <= 0 || $sale <= 0 || $sale >= $regular) {
            continue;
        }

        $ranked[] = [
            'id'       => (int) $product->get_id(),
            'discount' => ($regular - $sale) / $regular,
        ];
    }

    usort($ranked, static function ($a, $b) {
        if ($a['discount'] === $b['discount']) {
            return $a['id'] <=> $b['id'];
        }
        return $a['discount'] < $b['discount'] ? 1 : -1;
    });

    $ids = array_column($ranked, 'id');
    set_transient($cache_key, $ids, 10 * MINUTE_IN_SECONDS);

    return $ids;
}

function velento_clear_discount_sort_cache($post_id = 0)
{
    if ($post_id && get_post_type($post_id) !== 'product') {
        return;
    }
    delete_transient('velento_discount_product_ids_v1');
}
add_action('save_post_product', 'velento_clear_discount_sort_cache');
add_action('woocommerce_update_product', 'velento_clear_discount_sort_cache');

function velento_shop_query_args($state, $paged = 1)
{
    $args = [
        'post_type'              => 'product',
        'post_status'            => 'publish',
        'posts_per_page'         => (int) get_option('posts_per_page', 12),
        'paged'                  => max(1, absint($paged)),
        'tax_query'              => velento_shop_tax_query_from_state($state),
        'meta_query'             => velento_shop_meta_query_from_state($state),
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => false,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    ];

    switch ($state['orderby']) {
        case 'date':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'discount':
            $ranked_ids = velento_shop_discount_sorted_ids();
            $args['post__in'] = $ranked_ids ?: [0];
            $args['orderby'] = 'post__in';
            break;
        default:
            $args['orderby'] = ['menu_order' => 'ASC', 'date' => 'DESC'];
            break;
    }

    return $args;
}

function velento_shop_apply_filters_to_main_query($query)
{
    if (is_admin() || !$query->is_main_query() || !function_exists('is_shop')) {
        return;
    }

    if (!(is_shop() || is_product_category() || is_product_tag())) {
        return;
    }

    $state = velento_shop_filter_state();
    $filter_tax_query = velento_shop_tax_query_from_state($state);
    $filter_meta_query = velento_shop_meta_query_from_state($state);

    // Preserve WooCommerce's own archive constraints (current category/tag,
    // visibility, catalog exclusions, etc.) and append Velento filters.
    $existing_tax_query = $query->get('tax_query');
    if ($existing_tax_query && is_array($existing_tax_query)) {
        $merged_tax_query = ['relation' => 'AND', $existing_tax_query];
        foreach ($filter_tax_query as $tax_query) {
            if (is_array($tax_query) && isset($tax_query['taxonomy'])) {
                $merged_tax_query[] = $tax_query;
            }
        }
        if (count($merged_tax_query) > 1) {
            $query->set('tax_query', $merged_tax_query);
        }
    } elseif ($filter_tax_query) {
        $query->set('tax_query', $filter_tax_query);
    }

    if ($filter_meta_query) {
        $existing_meta_query = $query->get('meta_query');
        if ($existing_meta_query && is_array($existing_meta_query)) {
            $query->set('meta_query', ['relation' => 'AND', $existing_meta_query, ...$filter_meta_query]);
        } else {
            $query->set('meta_query', $filter_meta_query);
        }
    }

    if ($state['orderby'] === 'discount') {
        $query->set('post__in', velento_shop_discount_sorted_ids() ?: [0]);
        $query->set('orderby', 'post__in');
        return;
    }

    $args = velento_shop_query_args($state, $query->get('paged') ?: 1);
    foreach (['orderby', 'order', 'meta_key'] as $key) {
        if (isset($args[$key])) {
            $query->set($key, $args[$key]);
        }
    }
}
add_action('pre_get_posts', 'velento_shop_apply_filters_to_main_query', 30);

function velento_render_shop_filters($state)
{
    $definitions = velento_shop_filter_definitions();
    $has_attribute_filters = false;

    foreach ($definitions as $key => $definition) {
        if (!empty($definition['taxonomy']) && velento_shop_filter_values($key)) {
            $has_attribute_filters = true;
            break;
        }
    }

    ?>
    <aside class="velento-shop-filters" data-shop-filters aria-label="<?php echo esc_attr__('فیلترهای فروشگاه', 'velento-shop'); ?>">
        <div class="velento-shop-filters__head">
            <div>
                <span class="v-shop-filter-kicker"><?php esc_html_e('جستجوی دقیق', 'velento-shop'); ?></span>
                <h2><?php esc_html_e('فیلترها', 'velento-shop'); ?></h2>
            </div>
            <button type="button" class="velento-shop-filters__clear" data-filter-clear><?php esc_html_e('پاک کردن همه', 'velento-shop'); ?></button>
        </div>

        <?php foreach ($definitions as $key => $definition) :
            $terms = !empty($definition['taxonomy']) ? velento_shop_filter_values($key) : [];
            if (!$terms) {
                continue;
            }
        ?>
            <fieldset class="velento-shop-filter-group">
                <legend><?php echo esc_html($definition['label']); ?></legend>
                <div class="velento-filter-options">
                    <?php foreach ($terms as $term) :
                        $checked = in_array($term->slug, (array) $state[$key], true);
                    ?>
                        <label class="velento-filter-option">
                            <input type="checkbox" name="<?php echo esc_attr($key); ?>[]" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked); ?>>
                            <span><?php echo esc_html($term->name); ?></span>
                            <small><?php echo esc_html($term->count); ?></small>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>

        <fieldset class="velento-shop-filter-group">
            <legend><?php esc_html_e('موجودی', 'velento-shop'); ?></legend>
            <div class="velento-filter-options">
                <?php
                $stock_options = [
                    'instock'     => __('موجود', 'velento-shop'),
                    'outofstock'  => __('ناموجود', 'velento-shop'),
                    'onbackorder' => __('پیش‌خرید', 'velento-shop'),
                ];
                foreach ($stock_options as $value => $label) :
                ?>
                    <label class="velento-filter-option">
                        <input type="radio" name="stock" value="<?php echo esc_attr($value); ?>" <?php checked($state['stock'], $value); ?>>
                        <span><?php echo esc_html($label); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <label class="velento-filter-sale">
            <input type="checkbox" name="sale" value="1" <?php checked($state['sale']); ?>>
            <span><?php esc_html_e('فقط محصولات فروش ویژه', 'velento-shop'); ?></span>
        </label>
    </aside>
    <?php
}

function velento_shop_active_filter_labels($state)
{
    $chips = [];
    $definitions = velento_shop_filter_definitions();

    foreach ($definitions as $key => $definition) {
        if (empty($definition['taxonomy'])) {
            continue;
        }
        foreach ((array) $state[$key] as $slug) {
            $term = get_term_by('slug', $slug, $definition['taxonomy']);
            if ($term && !is_wp_error($term)) {
                $chips[] = [
                    'key'   => $key,
                    'value' => $slug,
                    'label' => $definition['label'] . ': ' . $term->name,
                ];
            }
        }
    }

    if ($state['stock'] !== '') {
        $stock_labels = [
            'instock' => __('موجود', 'velento-shop'),
            'outofstock' => __('ناموجود', 'velento-shop'),
            'onbackorder' => __('پیش‌خرید', 'velento-shop'),
        ];
        $chips[] = [
            'key' => 'stock',
            'value' => $state['stock'],
            'label' => __('موجودی: ', 'velento-shop') . ($stock_labels[$state['stock']] ?? $state['stock']),
        ];
    }

    if ($state['sale']) {
        $chips[] = [
            'key' => 'sale',
            'value' => '1',
            'label' => __('فروش ویژه', 'velento-shop'),
        ];
    }


    return $chips;
}

function velento_ajax_shop_filters()
{
    check_ajax_referer('velento_shop_filters', 'nonce');

    if (!function_exists('wc_get_product')) {
        wp_send_json_error(['message' => __('ووکامرس فعال نیست.', 'velento-shop')], 400);
    }

    if (function_exists('velento_rate_limit') && !velento_rate_limit('shop_filters', 30, 60)) {
        wp_send_json_error(['message' => __('تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop')], 429);
    }

    $raw_filters = isset($_POST['filters']) ? (string) wp_unslash($_POST['filters']) : '';
    if (strlen($raw_filters) > 20000) {
        wp_send_json_error(['message' => __('درخواست فیلتر معتبر نیست.', 'velento-shop')], 400);
    }
    $raw_state = $raw_filters !== '' ? json_decode($raw_filters, true) : [];
    $raw_state = is_array($raw_state) ? $raw_state : [];

    // Reuse the same sanitization rules as URL filters, but never trust
    // arbitrary keys or values from the browser.
    $state = [
        'brand'            => [],
        'category'         => [],
        'strap'            => [],
        'movement'         => [],
        'movement_count'   => [],
        'gender'           => [],
        'material'         => [],
        'color'            => [],
        'water_resistance' => [],
        'stock'            => '',
        'sale'             => false,
        'orderby'          => 'menu_order',
        'paged'            => 1,
    ];

    foreach (array_keys($state) as $key) {
        if (in_array($key, ['stock', 'orderby', 'paged'], true)) {
            continue;
        }
        $state[$key] = velento_filter_slug_list($raw_state[$key] ?? []);
    }

    $state['stock'] = isset($raw_state['stock']) && is_scalar($raw_state['stock']) ? sanitize_key((string) $raw_state['stock']) : '';
    if (!in_array($state['stock'], ['instock', 'outofstock', 'onbackorder'], true)) {
        $state['stock'] = '';
    }

    $state['sale'] = !empty($raw_state['sale']);


    $state['orderby'] = isset($raw_state['orderby']) && is_scalar($raw_state['orderby']) ? sanitize_key((string) $raw_state['orderby']) : 'menu_order';
    if (!in_array($state['orderby'], ['menu_order', 'date', 'popularity', 'price', 'price-desc', 'discount'], true)) {
        $state['orderby'] = 'menu_order';
    }

    $state['paged'] = max(1, isset($raw_state['paged']) && is_scalar($raw_state['paged']) ? absint($raw_state['paged']) : 1);

    $query = new WP_Query(velento_shop_query_args($state, $state['paged']));

    ob_start();
    $template = get_template_directory() . '/template-parts/shop/product-results.php';
    if (file_exists($template)) {
        $args = [
            'query'        => $query,
            'state'        => $state,
            'show_filters' => false,
            'show_brand_filter' => false,
            'ajax_mode'   => true,
        ];
        include $template;
    }
    $html = ob_get_clean();

    wp_send_json_success([
        'html'  => $html,
        'count' => (int) $query->found_posts,
        'paged' => (int) $state['paged'],
        'max_pages' => (int) $query->max_num_pages,
    ]);
}
add_action('wp_ajax_velento_shop_filters', 'velento_ajax_shop_filters');
add_action('wp_ajax_nopriv_velento_shop_filters', 'velento_ajax_shop_filters');

function velento_shop_filter_localize_data()
{
    return [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('velento_shop_filters'),
    ];
}
