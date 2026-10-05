<?php
if (!defined('ABSPATH')) exit;

$cart = function_exists('WC') ? WC()->cart : null;
$count = $cart ? $cart->get_cart_contents_count() : 0;
?>

<?php if (!$cart || $cart->is_empty()) : ?>
    <div class="velento-cart-empty">
        <span class="velento-cart-empty__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6.5 8h11l-1 11.5a1.5 1.5 0 0 1-1.5 1.4H9a1.5 1.5 0 0 1-1.5-1.4L6.5 8z"></path>
                <path d="M9 8V6.2a3 3 0 0 1 6 0V8"></path>
            </svg>
        </span>
        <h3><?php esc_html_e('سبد خرید شما خالی است', 'velento-shop'); ?></h3>
        <p><?php esc_html_e('یک ساعت انتخاب کنید تا مجموعه‌ی شما اینجا نمایش داده شود.', 'velento-shop'); ?></p>
        <?php
        $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
        ?>
        <a class="velento-cart-drawer__primary" href="<?php echo esc_url($shop_url); ?>">
            <?php esc_html_e('مشاهده فروشگاه', 'velento-shop'); ?>
        </a>
    </div>
<?php else : ?>
    <div class="velento-cart-items" aria-live="polite">
        <?php foreach ($cart->get_cart() as $cart_item_key => $cart_item) :
            $product = $cart_item['data'];
            if (!$product || !$product->exists() || $cart_item['quantity'] < 1) continue;
            $product_permalink = $product->is_visible() ? $product->get_permalink($cart_item) : '';
            $thumbnail = $product->get_image('woocommerce_thumbnail', ['class' => 'velento-cart-item__image']);
        ?>
            <article class="velento-cart-item" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                <a class="velento-cart-item__media" href="<?php echo esc_url($product_permalink ?: '#'); ?>" tabindex="-1">
                    <?php echo wp_kses_post($thumbnail); ?>
                </a>
                <div class="velento-cart-item__body">
                    <div class="velento-cart-item__top">
                        <div>
                            <?php if ($product_permalink) : ?>
                                <a class="velento-cart-item__name" href="<?php echo esc_url($product_permalink); ?>">
                                    <?php echo esc_html($product->get_name()); ?>
                                </a>
                            <?php else : ?>
                                <span class="velento-cart-item__name"><?php echo esc_html($product->get_name()); ?></span>
                            <?php endif; ?>
                            <?php $item_data = wc_get_formatted_cart_item_data($cart_item); ?>
                            <?php if ($item_data) : ?>
                                <div class="velento-cart-item__meta"><?php echo wp_kses_post($item_data); ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="velento-cart-item__remove" data-cart-remove="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php echo esc_attr(sprintf(__('حذف %s از سبد', 'velento-shop'), $product->get_name())); ?>">
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="velento-cart-item__bottom">
                        <div class="velento-cart-qty" aria-label="<?php esc_attr_e('تعداد', 'velento-shop'); ?>">
                            <button type="button" data-cart-qty="decrease" data-cart-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php esc_attr_e('کاهش تعداد', 'velento-shop'); ?>">−</button>
                            <span><?php echo esc_html($cart_item['quantity']); ?></span>
                            <button type="button" data-cart-qty="increase" data-cart-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="<?php esc_attr_e('افزایش تعداد', 'velento-shop'); ?>">+</button>
                        </div>
                        <span class="velento-cart-item__price"><?php echo wp_kses_post($cart->get_product_subtotal($product, $cart_item['quantity'])); ?></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="velento-cart-summary">
        <div class="velento-cart-summary__row">
            <span><?php esc_html_e('جمع سبد', 'velento-shop'); ?></span>
            <strong><?php echo wp_kses_post($cart->get_cart_subtotal()); ?></strong>
        </div>
        <p class="velento-cart-summary__note"><?php esc_html_e('هزینه ارسال در مرحله نهایی سفارش محاسبه می‌شود.', 'velento-shop'); ?></p>
        <div class="velento-cart-drawer__actions">
            <?php $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart'); ?>
            <?php $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout'); ?>
            <a class="velento-cart-drawer__secondary" href="<?php echo esc_url($cart_url); ?>"><?php esc_html_e('مشاهده سبد', 'velento-shop'); ?></a>
            <a class="velento-cart-drawer__primary" href="<?php echo esc_url($checkout_url); ?>"><?php esc_html_e('ادامه و پرداخت', 'velento-shop'); ?></a>
        </div>
    </div>
<?php endif; ?>
