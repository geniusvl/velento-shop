<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Product_Grid extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-product-grid'; }
    public function get_title() { return __('ولنتو | گرید محصولات', 'velento-shop'); }
    public function get_icon() { return 'eicon-products'; }
    public function get_script_depends() { return []; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('source', [
            'label'   => __('منبع محصولات', 'velento-shop'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'latest',
            'options' => [
                'latest'    => __('جدیدترین', 'velento-shop'),
                'featured'  => __('ویژه', 'velento-shop'),
                'sale'      => __('حراج', 'velento-shop'),
                'bestsellers' => __('پرفروش‌ترین', 'velento-shop'),
                'category'  => __('یک دسته خاص', 'velento-shop'),
            ],
        ]);
        $this->add_control('category', [
            'label'     => __('اسلاگ دسته‌بندی', 'velento-shop'),
            'type'      => \Elementor\Controls_Manager::TEXT,
            'condition' => ['source' => 'category'],
            'default'   => '',
            'placeholder' => 'men',
        ]);
        $this->add_control('limit', ['label' => __('تعداد محصول', 'velento-shop'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 8, 'min' => 1, 'max' => 24]);
        $this->add_responsive_control('columns', [
            'label'   => __('تعداد ستون', 'velento-shop'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '4',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'],
            'selectors' => ['{{WRAPPER}} .velento-el-products' => '--v-cols: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        $this->register_velento_color_controls();
    }

    protected function query_products($s)
    {
        if (!function_exists('wc_get_products')) {
            return [];
        }
        $args = ['status' => 'publish', 'limit' => (int) $s['limit']];
        switch ($s['source']) {
            case 'featured':
                $args['featured'] = true;
                break;
            case 'sale':
                $ids = function_exists('wc_get_product_ids_on_sale') ? wc_get_product_ids_on_sale() : [];
                $args['include'] = $ids ?: [0];
                break;
            case 'bestsellers':
                $args['orderby'] = 'popularity';
                $args['order'] = 'DESC';
                break;
            case 'category':
                if (!empty($s['category'])) {
                    $args['category'] = [sanitize_title($s['category'])];
                }
                break;
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
        }
        return wc_get_products($args);
    }

    public static function render_card($product)
    {
        if (!($product instanceof WC_Product)) {
            return;
        }
        $link = get_permalink($product->get_id());
        $image_id = $product->get_image_id();
        $image = $image_id ? wp_get_attachment_image_url($image_id, 'medium_large') : wc_placeholder_img_src('woocommerce_thumbnail');
        $brand_terms = get_the_terms($product->get_id(), 'velento_brand');
        $brand = ($brand_terms && !is_wp_error($brand_terms)) ? $brand_terms[0]->name : '';
        ?>
        <li class="velento-el-product">
            <a class="velento-el-product__media" href="<?php echo esc_url($link); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
                <?php if ($product->is_on_sale()) : ?><span class="velento-el-product__badge"><?php esc_html_e('حراج', 'velento-shop'); ?></span><?php endif; ?>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy">
            </a>
            <div class="velento-el-product__body">
                <?php if ($brand) : ?><span class="velento-el-product__brand"><?php echo esc_html($brand); ?></span><?php endif; ?>
                <h3 class="velento-el-product__title"><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
                <div class="velento-el-product__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
            </div>
        </li>
        <?php
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();

        if (!function_exists('wc_get_products')) {
            echo '<p>' . esc_html__('ووکامرس فعال نیست.', 'velento-shop') . '</p>';
            return;
        }

        $products = $this->query_products($s);
        if (!$products) {
            echo '<p>' . esc_html__('محصولی برای نمایش پیدا نشد.', 'velento-shop') . '</p>';
            return;
        }
        ?>
        <ul class="velento-el-widget velento-el-products" id="<?php echo esc_attr($this->get_id()); ?>" style="--v-cols:<?php echo esc_attr($s['columns'] ?: 4); ?>;">
            <?php foreach ($products as $product) { self::render_card($product); } ?>
        </ul>
        <?php
    }
}
