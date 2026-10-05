<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Announcement_Bar extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-announcement-bar'; }
    public function get_title() { return __('ولنتو | نوار اعلان', 'velento-shop'); }
    public function get_icon() { return 'eicon-announcement'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('text', ['label' => __('متن', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('ارسال رایگان برای سفارش‌های بالای ۵ میلیون تومان', 'velento-shop')]);
        $this->add_control('link', ['label' => __('لینک (اختیاری)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::URL]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        if (empty($s['text'])) return;
        $url = !empty($s['link']['url']) ? $s['link']['url'] : '';
        ?>
        <div class="velento-el-widget velento-el-announcement" id="<?php echo esc_attr($this->get_id()); ?>">
            <?php if ($url) : ?>
                <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($s['text']); ?></a>
            <?php else : ?>
                <span><?php echo esc_html($s['text']); ?></span>
            <?php endif; ?>
        </div>
        <?php
    }
}
