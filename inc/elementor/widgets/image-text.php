<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Image_Text extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-image-text'; }
    public function get_title() { return __('ولنتو | تصویر و متن', 'velento-shop'); }
    public function get_icon() { return 'eicon-image-box'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('image', ['label' => __('تصویر', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('image_position', [
            'label' => __('موقعیت تصویر', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'left',
            'options' => ['left' => __('راست بصری (چپ سند)', 'velento-shop'), 'right' => __('چپ بصری (راست سند)', 'velento-shop')],
        ]);
        $this->add_control('eyebrow', ['label' => __('برچسب کوچک', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('درباره ولنتو', 'velento-shop')]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('میراثی از دقت و ظرافت', 'velento-shop')]);
        $this->add_control('desc', ['label' => __('متن', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('توضیح این بخش را اینجا بنویسید.', 'velento-shop')]);
        $this->add_control('cta_text', ['label' => __('متن دکمه (اختیاری)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT]);
        $this->add_control('cta_link', ['label' => __('لینک دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::URL, 'condition' => ['cta_text!' => '']]);
        $this->end_controls_section();
        $this->register_velento_color_controls();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $img = !empty($s['image']['url']) ? $s['image']['url'] : \Elementor\Utils::get_placeholder_image_src();
        $url = !empty($s['cta_link']['url']) ? $s['cta_link']['url'] : '';
        $class = 'velento-el-widget velento-el-imgtext' . ($s['image_position'] === 'right' ? ' is-image-right' : '');
        ?>
        <div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($this->get_id()); ?>">
            <div class="velento-el-imgtext__media"><img src="<?php echo esc_url($img); ?>" alt="" loading="lazy"></div>
            <div class="velento-el-imgtext__body">
                <?php if ($s['eyebrow']) : ?><span class="velento-el-imgtext__eyebrow"><?php echo esc_html($s['eyebrow']); ?></span><?php endif; ?>
                <?php if ($s['title']) : ?><h2 class="velento-el-imgtext__title"><?php echo esc_html($s['title']); ?></h2><?php endif; ?>
                <?php if ($s['desc']) : ?><p class="velento-el-imgtext__desc"><?php echo esc_html($s['desc']); ?></p><?php endif; ?>
                <?php if ($s['cta_text']) : ?><a class="velento-el-btn velento-el-btn--outline" href="<?php echo esc_url($url); ?>"><?php echo esc_html($s['cta_text']); ?></a><?php endif; ?>
            </div>
        </div>
        <?php
    }
}
