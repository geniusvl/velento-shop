<?php
/**
 * Velento WooCommerce product card.
 *
 * The card reads every product value from WooCommerce. Visual badges are
 * derived from product state (sale, publish date, sales, stock) rather than
 * from hardcoded product data.
 */
if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product instanceof WC_Product || !$product->is_visible()) {
    return;
}

$product_id = $product->get_id();
$product_url = get_permalink($product_id);
$brand_terms = get_the_terms($product_id, 'velento_brand');
$brand = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0]->name : '';

$badges = [];

if ($product->is_on_sale()) {
    $regular = (float) $product->get_regular_price();
    $sale = (float) $product->get_sale_price();
    if ($regular > 0 && $sale > 0 && $sale < $regular) {
        $discount = round((1 - ($sale / $regular)) * 100);
        $badges[] = [
            'label' => sprintf(
                /* translators: %s: discount percentage */
                __('%s٪ تخفیف', 'velento-shop'),
                number_format_i18n($discount)
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

if (strtotime($product->get_date_created() ? $product->get_date_created()->date('c') : '') > strtotime('-30 days')) {
    $badges[] = [
        'label' => __('جدید', 'velento-shop'),
        'class' => 'is-new',
    ];
}

if ($product->managing_stock()) {
    $stock_quantity = $product->get_stock_quantity();
    if (null !== $stock_quantity && $stock_quantity > 0 && $stock_quantity <= 5) {
        $badges[] = [
            'label' => __('محدود', 'velento-shop'),
            'class' => 'is-limited',
        ];
    }
}

$image_id = $product->get_image_id();
$image_html = $image_id
    ? wp_get_attachment_image(
        $image_id,
        'woocommerce_thumbnail',
        false,
        [
            'class'    => 'v-product-image',
            'alt'      => $product->get_name(),
            'loading'  => 'lazy',
            'decoding' => 'async',
        ]
    )
    : sprintf(
        '<img class="v-product-image" src="%s" alt="%s" loading="lazy" decoding="async">',
        esc_url(wc_placeholder_img_src('woocommerce_thumbnail')),
        esc_attr($product->get_name())
    );

$stock_label = '';
if ($product->managing_stock() && null !== $product->get_stock_quantity()) {
    $stock_label = sprintf(
        /* translators: %s: stock quantity */
        __('موجودی: %s عدد', 'velento-shop'),
        number_format_i18n(max(0, (int) $product->get_stock_quantity()))
    );
} elseif ($product->is_in_stock()) {
    $stock_label = __('موجود', 'velento-shop');
} else {
    $stock_label = __('ناموجود', 'velento-shop');
}
?>
<li <?php wc_product_class('velento-product-card', $product); ?>>
    <article class="velento-product-card__inner">
        <div class="v-product-media">
            <a class="v-product-media__link" href="<?php echo esc_url($product_url); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
                <?php if ($badges) : ?>
                    <div class="v-product-badges" aria-label="<?php echo esc_attr__('برچسب‌های محصول', 'velento-shop'); ?>">
                        <?php foreach ($badges as $badge) : ?>
                            <span class="v-product-badge <?php echo esc_attr($badge['class']); ?>"><?php echo esc_html($badge['label']); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <span class="v-product-image-frame">
                    <?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
                <span class="v-product-view" aria-hidden="true"><?php esc_html_e('مشاهده محصول', 'velento-shop'); ?> <span>←</span></span>
            </a>
        </div>

        <div class="v-product-info">
            <?php if ($brand) : ?>
                <a class="v-product-brand" href="<?php echo esc_url(get_term_link($brand_terms[0])); ?>"><?php echo esc_html($brand); ?></a>
            <?php endif; ?>

            <h2 class="woocommerce-loop-product__title">
                <a href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product->get_name()); ?></a>
            </h2>

            <div class="v-product-bottom">
                <div class="v-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                <span class="v-stock-dot<?php echo $product->is_in_stock() ? '' : ' out'; ?>">
                    <?php echo esc_html($stock_label); ?>
                </span>
            </div>
        </div>
    </article>
</li>
