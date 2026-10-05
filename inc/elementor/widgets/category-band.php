<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Category_Band extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-category-band'; }
    public function get_title() { return __('ولنتو | نوار دسته‌بندی‌ها', 'velento-shop'); }
    public function get_icon() { return 'eicon-gallery-grid'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('image', ['label' => __('تصویر', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $repeater->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مردانه', 'velento-shop')]);
        $repeater->add_control('desc', ['label' => __('توضیح کوتاه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('قدرت و شخصیت', 'velento-shop')]);
        $repeater->add_control('link', ['label' => __('لینک', 'velento-shop'), 'type' => \Elementor\Controls_Manager::URL]);
        $this->add_control('items', [
            'label' => __('دسته‌ها', 'velento-shop'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
            'default' => [
                ['title' => __('مردانه', 'velento-shop'), 'desc' => __('قدرت و شخصیت', 'velento-shop')],
                ['title' => __('زنانه', 'velento-shop'), 'desc' => __('ظرافت و جزئیات', 'velento-shop')],
                ['title' => __('اسپرت', 'velento-shop'), 'desc' => __('برای هر ماجراجویی', 'velento-shop')],
                ['title' => __('رسمی', 'velento-shop'), 'desc' => __('برای لحظه‌های مهم', 'velento-shop')],
            ],
            'title_field' => '{{{ title }}}',
        ]);
        $this->add_responsive_control('columns', [
            'label' => __('تعداد ستون', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '4',
            'tablet_default' => '2', 'mobile_default' => '2',
            'options' => ['2' => '2', '3' => '3', '4' => '4'],
            'selectors' => ['{{WRAPPER}} .velento-el-categories' => '--v-cols: {{VALUE}};'],
        ]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $items = $s['items'] ?: [];
        ?>
        <div class="velento-el-widget velento-el-categories" id="<?php echo esc_attr($this->get_id()); ?>" style="--v-cols:<?php echo esc_attr($s['columns'] ?: 4); ?>;">
            <?php foreach ($items as $item) :
                $img = !empty($item['image']['url']) ? $item['image']['url'] : \Elementor\Utils::get_placeholder_image_src();
                $url = !empty($item['link']['url']) ? $item['link']['url'] : '#';
                ?>
                <a class="velento-el-category" href="<?php echo esc_url($url); ?>">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                    <span class="velento-el-category__label"><span><?php echo esc_html($item['desc']); ?></span><b><?php echo esc_html($item['title']); ?></b></span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
