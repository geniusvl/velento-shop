<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Button extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-button'; }
    public function get_title() { return __('ولنتو | دکمه', 'velento-shop'); }
    public function get_icon() { return 'eicon-button'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('text', ['label' => __('متن دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مشاهده محصولات', 'velento-shop')]);
        $this->add_control('link', ['label' => __('لینک', 'velento-shop'), 'type' => \Elementor\Controls_Manager::URL, 'default' => ['url' => '#']]);
        $this->add_control('style', [
            'label' => __('نوع', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'primary',
            'options' => ['primary' => __('طلایی (اصلی)', 'velento-shop'), 'outline' => __('خط‌دار', 'velento-shop'), 'ghost' => __('بدون پس‌زمینه', 'velento-shop')],
        ]);
        $this->add_control('size', [
            'label' => __('اندازه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'md',
            'options' => ['sm' => __('کوچک', 'velento-shop'), 'md' => __('متوسط', 'velento-shop'), 'lg' => __('بزرگ', 'velento-shop')],
        ]);
        $this->add_control('align', [
            'label' => __('چینش', 'velento-shop'), 'type' => \Elementor\Controls_Manager::CHOOSE, 'default' => 'center',
            'options' => [
                'right'  => ['title' => __('راست', 'velento-shop'), 'icon' => 'eicon-text-align-right'],
                'center' => ['title' => __('وسط', 'velento-shop'), 'icon' => 'eicon-text-align-center'],
                'left'   => ['title' => __('چپ', 'velento-shop'), 'icon' => 'eicon-text-align-left'],
            ],
            'selectors' => ['{{WRAPPER}} .velento-el-button-wrap' => 'text-align: {{VALUE}};'],
        ]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $url = !empty($s['link']['url']) ? $s['link']['url'] : '#';
        $target = !empty($s['link']['is_external']) ? ' target="_blank"' : '';
        $nofollow = !empty($s['link']['nofollow']) ? ' rel="nofollow"' : '';
        $size_class = $s['size'] !== 'md' ? ' velento-el-btn--' . $s['size'] : '';
        ?>
        <div class="velento-el-widget velento-el-button-wrap" id="<?php echo esc_attr($this->get_id()); ?>">
            <a class="velento-el-btn velento-el-btn--<?php echo esc_attr($s['style']); ?><?php echo esc_attr($size_class); ?>" href="<?php echo esc_url($url); ?>"<?php echo $target . $nofollow; ?>>
                <?php echo esc_html($s['text']); ?>
            </a>
        </div>
        <?php
    }
}
