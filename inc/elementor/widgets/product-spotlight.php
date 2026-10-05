<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Product_Spotlight extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-product-spotlight'; }
    public function get_title() { return __('ولنتو | معرفی محصول ویژه', 'velento-shop'); }
    public function get_icon() { return 'eicon-product-featured-image'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('product_id', [
            'label'       => __('شناسه (ID) محصول', 'velento-shop'),
            'type'        => \Elementor\Controls_Manager::NUMBER,
            'description' => __('شناسه عددی محصول را از پیشخوان وردپرس > محصولات کپی کنید.', 'velento-shop'),
        ]);
        $this->add_control('desc_override', ['label' => __('توضیح دلخواه (اختیاری)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'description' => __('خالی بگذارید تا توضیح کوتاه محصول نمایش داده شود.', 'velento-shop')]);
        $this->add_control('cta_text', ['label' => __('متن دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مشاهده و خرید', 'velento-shop')]);
        $this->end_controls_section();

        $this->register_velento_color_controls();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $product = !empty($s['product_id']) ? wc_get_product((int) $s['product_id']) : null;

        if (!$product instanceof WC_Product) {
            echo '<p>' . esc_html__('شناسه محصول معتبر وارد کنید.', 'velento-shop') . '</p>';
            return;
        }

        $link = get_permalink($product->get_id());
        $image_id = $product->get_image_id();
        $image = $image_id ? wp_get_attachment_image_url($image_id, 'large') : wc_placeholder_img_src('woocommerce_thumbnail');
        $brand_terms = get_the_terms($product->get_id(), 'velento_brand');
        $brand = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0]->name : '';
        $desc = $s['desc_override'] ?: wp_strip_all_tags($product->get_short_description() ?: $product->get_description());
        $desc = wp_trim_words($desc, 30, '…');
        ?>
        <div class="velento-el-widget velento-el-spotlight" id="<?php echo esc_attr($this->get_id()); ?>">
            <div class="velento-el-spotlight__media">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
            </div>
            <div class="velento-el-spotlight__body">
                <?php if ($brand) : ?><span class="velento-el-spotlight__brand"><?php echo esc_html($brand); ?></span><?php endif; ?>
                <h2 class="velento-el-spotlight__title"><?php echo esc_html($product->get_name()); ?></h2>
                <?php if ($desc) : ?><p class="velento-el-spotlight__desc"><?php echo esc_html($desc); ?></p><?php endif; ?>
                <span class="velento-el-spotlight__price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                <a class="velento-el-btn velento-el-btn--primary" href="<?php echo esc_url($link); ?>"><?php echo esc_html($s['cta_text']); ?></a>
            </div>
        </div>
        <?php
    }
}
