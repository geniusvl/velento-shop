<?php
/**
 * Velento Shop — Product discovery/results partial.
 *
 * Used by the main product archive and AJAX filter responses. The product
 * query itself stays in inc/filters.php so the template only owns markup.
 */

if (!defined('ABSPATH')) {
    exit;
}

$show_filters = !isset($args['show_filters']) || $args['show_filters'];
$show_brand_filter = !isset($args['show_brand_filter']) || $args['show_brand_filter'];
$empty_message = isset($args['empty_message']) ? $args['empty_message'] : '';
$ajax_mode = !empty($args['ajax_mode']);
$state = isset($args['state']) && is_array($args['state']) ? $args['state'] : (function_exists('velento_shop_filter_state') ? velento_shop_filter_state() : []);
$query = isset($args['query']) && ($args['query'] instanceof WP_Query) ? $args['query'] : $GLOBALS['wp_query'];

if (!($query instanceof WP_Query)) {
    return;
}

$filter_chips = function_exists('velento_shop_active_filter_labels') ? velento_shop_active_filter_labels($state) : [];
?>
<?php if ($show_filters && !$ajax_mode && function_exists('velento_render_shop_filters')) : ?>
    <div class="velento-shop-discovery">
        <button type="button" class="velento-mobile-filter-trigger" data-filter-open aria-controls="velento-shop-filter-panel" aria-expanded="false">
            <span><?php esc_html_e('فیلترها', 'velento-shop'); ?></span>
            <span aria-hidden="true">☰</span>
        </button>

        <div class="velento-shop-discovery__layout">
            <div class="velento-shop-filter-drawer" id="velento-shop-filter-panel" data-filter-drawer>
                <button type="button" class="velento-filter-drawer-close" data-filter-close aria-label="<?php echo esc_attr__('بستن فیلترها', 'velento-shop'); ?>">×</button>
                <?php velento_render_shop_filters($state); ?>
            </div>

            <div class="velento-shop-results-area">
<?php endif; ?>

<div class="v-shop-toolbar">
    <div class="v-shop-result" data-filter-result-count>
        <?php
        if (function_exists('wc_print_notices') && !$ajax_mode) {
            // Notices are rendered by WooCommerce outside this partial.
        }
        if ($ajax_mode) {
            printf(
                /* translators: %s: total product count */
                esc_html__('%s محصول', 'velento-shop'),
                number_format_i18n((int) $query->found_posts)
            );
        } elseif (function_exists('woocommerce_result_count')) {
            woocommerce_result_count();
        } else {
            printf(
                /* translators: %s: total product count */
                esc_html__('%s محصول', 'velento-shop'),
                number_format_i18n((int) $query->found_posts)
            );
        }
    ?>
    </div>
    <div class="v-shop-toolbar-actions">
        <?php if ($show_filters && !$ajax_mode) : ?>
            <button type="button" class="velento-filter-inline-trigger" data-filter-open aria-controls="velento-shop-filter-panel">
                <?php esc_html_e('فیلترها', 'velento-shop'); ?>
            </button>
        <?php endif; ?>
        <div class="v-shop-sort">
            <?php if ($ajax_mode) : ?>
                <label class="screen-reader-text" for="velento-sort-ajax"><?php esc_html_e('مرتب‌سازی', 'velento-shop'); ?></label>
                <select id="velento-sort-ajax" data-filter-sort>
                    <?php
                    $sorts = [
                        'menu_order' => __('مرتب‌سازی پیش‌فرض', 'velento-shop'),
                        'date'       => __('جدیدترین', 'velento-shop'),
                        'popularity' => __('محبوب‌ترین', 'velento-shop'),
                        'price'      => __('ارزان‌ترین', 'velento-shop'),
                        'price-desc' => __('گران‌ترین', 'velento-shop'),
                        'discount'   => __('بیشترین تخفیف', 'velento-shop'),
                    ];
                    foreach ($sorts as $value => $label) :
                    ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($state['orderby'], $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else : ?>
                <label class="screen-reader-text" for="velento-sort"><?php esc_html_e('مرتب‌سازی', 'velento-shop'); ?></label>
                <select id="velento-sort" data-filter-sort>
                    <?php
                    $sorts = [
                        'menu_order' => __('مرتب‌سازی پیش‌فرض', 'velento-shop'),
                        'date'       => __('جدیدترین', 'velento-shop'),
                        'popularity' => __('محبوب‌ترین', 'velento-shop'),
                        'price'      => __('ارزان‌ترین', 'velento-shop'),
                        'price-desc' => __('گران‌ترین', 'velento-shop'),
                        'discount'   => __('بیشترین تخفیف', 'velento-shop'),
                    ];
                    foreach ($sorts as $value => $label) :
                    ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($state['orderby'], $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($filter_chips) : ?>
    <div class="velento-active-filters" data-active-filters aria-label="<?php echo esc_attr__('فیلترهای فعال', 'velento-shop'); ?>">
        <?php foreach ($filter_chips as $chip) : ?>
            <button type="button" class="velento-filter-chip" data-filter-chip-key="<?php echo esc_attr($chip['key']); ?>" data-filter-chip-value="<?php echo esc_attr($chip['value']); ?>">
                <span><?php echo esc_html($chip['label']); ?></span>
                <b aria-hidden="true">×</b>
            </button>
        <?php endforeach; ?>
        <button type="button" class="velento-filter-clear-link" data-filter-clear><?php esc_html_e('پاک کردن همه', 'velento-shop'); ?></button>
    </div>
<?php endif; ?>

<div class="velento-products-result" data-filter-results>
<?php if ($query->have_posts()) : ?>
    <ul class="products columns-4">
        <?php
        while ($query->have_posts()) :
            $query->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
        ?>
    </ul>

    <nav class="v-shop-pagination" aria-label="<?php echo esc_attr__('صفحه‌بندی محصولات', 'velento-shop'); ?>">
        <?php
        echo paginate_links([
            'base'      => add_query_arg('paged', '%#%', remove_query_arg('paged')),
            'format'    => '',
            'current'   => max(1, (int) $query->get('paged')),
            'total'     => max(1, (int) $query->max_num_pages),
            'type'      => 'list',
            'prev_text' => '→',
            'next_text' => '←',
        ]);
        ?>
    </nav>
<?php elseif ($empty_message) : ?>
    <div class="v-shop-empty">
        <span class="v-shop-empty__mark" aria-hidden="true">✦</span>
        <h2><?php esc_html_e('محصولی پیدا نشد', 'velento-shop'); ?></h2>
        <p><?php echo esc_html($empty_message); ?></p>
        <button type="button" class="btn btn--outline" data-filter-clear><?php esc_html_e('حذف فیلترها', 'velento-shop'); ?></button>
    </div>
<?php else : ?>
    <div class="v-shop-empty">
        <span class="v-shop-empty__mark" aria-hidden="true">✦</span>
        <h2><?php esc_html_e('محصولی پیدا نشد', 'velento-shop'); ?></h2>
        <p><?php esc_html_e('فیلترها یا عبارت جستجو را تغییر دهید.', 'velento-shop'); ?></p>
    </div>
<?php endif; ?>
</div>

<?php
wp_reset_postdata();
?>

<?php if ($show_filters && !$ajax_mode && function_exists('velento_render_shop_filters')) : ?>
            </div>
        </div>
    </div>
<?php endif; ?>
