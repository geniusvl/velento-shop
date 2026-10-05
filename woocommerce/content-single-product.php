<?php
/**
 * Velento Shop — Single WooCommerce product content.
 *
 * Product data is sourced from WooCommerce objects and attributes. The
 * surrounding template keeps the standard WooCommerce summary hooks alive
 * where they are useful while providing the theme's own presentation.
 */
if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product instanceof WC_Product) {
    return;
}

$product_id = $product->get_id();
$brand_terms = get_the_terms($product_id, 'velento_brand');
$brand = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0] : null;

$regular = (float) $product->get_regular_price();
$sale = (float) $product->get_sale_price();
$discount = ($regular > 0 && $sale > 0 && $sale < $regular)
    ? round((1 - ($sale / $regular)) * 100)
    : 0;

$stock_quantity = $product->managing_stock() ? $product->get_stock_quantity() : null;
$low_stock_threshold = function_exists('wc_get_low_stock_amount')
    ? wc_get_low_stock_amount($product)
    : 2;

$stock_label = function_exists('velento_get_product_stock_label')
    ? velento_get_product_stock_label($product)
    : ($product->is_in_stock() ? __('موجود', 'velento-shop') : __('ناموجود', 'velento-shop'));

$attributes = $product->get_attributes();
?>
<article id="product-<?php the_ID(); ?>" <?php wc_product_class('v-single-card', $product); ?>>

    <div class="v-single-breadcrumb">
        <?php
        if (function_exists('woocommerce_breadcrumb')) {
            woocommerce_breadcrumb([
                'delimiter'   => '<span class="v-breadcrumb-separator" aria-hidden="true">/</span>',
                'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__('مسیر صفحه', 'velento-shop') . '">',
                'wrap_after'  => '</nav>',
            ]);
        }
        ?>
    </div>

    <div class="v-single-grid">
        <div class="v-single-gallery">
            <?php do_action('woocommerce_before_single_product_summary'); ?>
        </div>

        <div class="v-single-summary">
            <?php if ($brand) : ?>
                <a class="v-single-brand" href="<?php echo esc_url(get_term_link($brand)); ?>"><?php echo esc_html($brand->name); ?></a>
            <?php endif; ?>

            <?php the_title('<h1 class="v-single-title">', '</h1>'); ?>

            <div class="v-single-rating" aria-label="<?php echo esc_attr__('امتیاز محصول', 'velento-shop'); ?>">
                <?php woocommerce_template_single_rating(); ?>
            </div>

            <div class="v-single-price">
                <?php woocommerce_template_single_price(); ?>
                <?php if ($discount) : ?>
                    <span class="v-single-discount"><?php echo esc_html(number_format_i18n($discount)); ?>٪ تخفیف</span>
                <?php endif; ?>
            </div>

            <div class="v-single-stock<?php echo !$product->is_in_stock() ? ' is-out' : ''; ?>">
                <span class="v-single-stock__label"><?php esc_html_e('موجودی', 'velento-shop'); ?></span>
                <strong>
                    <?php
                    if ($product->managing_stock() && null !== $stock_quantity) {
                        printf(
                            /* translators: %s: stock quantity */
                            esc_html__('موجودی: %s عدد', 'velento-shop'),
                            number_format_i18n(max(0, (int) $stock_quantity))
                        );
                    } else {
                        echo esc_html($stock_label);
                    }
                    ?>
                </strong>
                <?php if ($product->is_in_stock() && $product->managing_stock() && null !== $stock_quantity && $stock_quantity > 0 && $stock_quantity <= $low_stock_threshold) : ?>
                    <span class="v-single-stock__hint"><?php esc_html_e('موجودی محدود است', 'velento-shop'); ?></span>
                <?php endif; ?>
            </div>

            <div class="v-single-excerpt">
                <?php woocommerce_template_single_excerpt(); ?>
            </div>

            <div class="v-single-cart">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>

            <?php
            /**
             * Integration point for wishlist plugins. A plugin can attach
             * its button here without the theme owning wishlist data.
             */
            do_action('velento_single_product_wishlist', $product);
            ?>

            <div class="v-single-meta">
                <?php if ($product->get_sku()) : ?>
                    <div><span><?php esc_html_e('کد محصول', 'velento-shop'); ?></span><strong><?php echo esc_html($product->get_sku()); ?></strong></div>
                <?php endif; ?>

                <?php if ($product->get_weight()) : ?>
                    <div><span><?php esc_html_e('وزن', 'velento-shop'); ?></span><strong><?php echo esc_html($product->get_weight() . ' ' . get_option('woocommerce_weight_unit')); ?></strong></div>
                <?php endif; ?>

                <?php
                $category_names = wc_get_product_category_list($product_id, ', ');
                if ($category_names) :
                ?>
                    <div><span><?php esc_html_e('دسته‌بندی', 'velento-shop'); ?></span><strong><?php echo wp_kses_post($category_names); ?></strong></div>
                <?php endif; ?>
            </div>

            <div class="v-single-trust" aria-label="<?php echo esc_attr__('اطمینان از خرید', 'velento-shop'); ?>">
                <span aria-hidden="true">✓</span><?php esc_html_e('ضمانت اصالت', 'velento-shop'); ?>
                <i aria-hidden="true"></i>
                <span aria-hidden="true">✓</span><?php esc_html_e('ارسال امن', 'velento-shop'); ?>
                <i aria-hidden="true"></i>
                <span aria-hidden="true">✓</span><?php esc_html_e('پشتیبانی تخصصی', 'velento-shop'); ?>
            </div>
        </div>
    </div>

    <div class="v-single-details">
        <div class="v-detail-tabs" role="tablist" aria-label="<?php echo esc_attr__('اطلاعات محصول', 'velento-shop'); ?>">
            <button type="button" id="tab-description" class="v-detail-tab is-active" role="tab" aria-selected="true" aria-controls="panel-description" data-product-tab="description">
                <?php esc_html_e('توضیحات', 'velento-shop'); ?>
            </button>
            <?php if ($attributes) : ?>
                <button type="button" id="tab-additional-information" class="v-detail-tab" role="tab" aria-selected="false" aria-controls="panel-additional-information" tabindex="-1" data-product-tab="additional_information">
                    <?php esc_html_e('مشخصات', 'velento-shop'); ?>
                </button>
            <?php endif; ?>
            <button type="button" id="tab-reviews" class="v-detail-tab" role="tab" aria-selected="false" aria-controls="panel-reviews" tabindex="-1" data-product-tab="reviews">
                <?php esc_html_e('نظرات', 'velento-shop'); ?>
            </button>
        </div>

        <section id="panel-description" class="v-tab-panel is-active" role="tabpanel" aria-labelledby="tab-description" data-product-panel="description">
            <h2><?php esc_html_e('توضیحات محصول', 'velento-shop'); ?></h2>
            <div class="v-tab-panel__content">
                <?php echo wp_kses_post(wpautop($product->get_description())); ?>
            </div>
        </section>

        <?php if ($attributes) : ?>
            <section id="panel-additional-information" class="v-tab-panel" role="tabpanel" aria-labelledby="tab-additional-information" data-product-panel="additional_information" hidden>
                <h2><?php esc_html_e('مشخصات محصول', 'velento-shop'); ?></h2>
                <div class="v-product-specifications">
                    <?php wc_display_product_attributes($product); ?>
                </div>
            </section>
        <?php endif; ?>

        <section id="panel-reviews" class="v-tab-panel" role="tabpanel" aria-labelledby="tab-reviews" data-product-panel="reviews" hidden>
            <h2><?php esc_html_e('نظرات مشتریان', 'velento-shop'); ?></h2>
            <?php comments_template(); ?>
        </section>
    </div>

    <?php
    // The default Woo tabs are removed by the theme on single-product
    // requests so this custom description/specification/review presentation
    // is not duplicated. Related/upsell integrations remain hooked below.
    do_action('woocommerce_after_single_product_summary');
    ?>

</article>
