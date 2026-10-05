<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Promo_Banner extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-promo-banner'; }
    public function get_title() { return __('ولنتو | بنر تمام‌عرض (تصویر/ویدیو)', 'velento-shop'); }
    public function get_icon() { return 'eicon-video-playlist'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('bg_type', [
            'label' => __('نوع پس‌زمینه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'image',
            'options' => ['image' => __('تصویر', 'velento-shop'), 'video' => __('ویدیو (mp4)', 'velento-shop')],
        ]);
        $this->add_control('bg_image', ['label' => __('تصویر پس‌زمینه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['bg_type' => 'image']]);
        $this->add_control('bg_video', ['label' => __('فایل ویدیو', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA, 'media_types' => ['video'], 'condition' => ['bg_type' => 'video']]);
        $this->add_control('eyebrow', ['label' => __('برچسب کوچک', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('کالکشن جدید', 'velento-shop')]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('لحظه‌ای بی‌زمان', 'velento-shop')]);
        $this->add_control('desc', ['label' => __('توضیح', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA]);
        $this->add_control('cta_text', ['label' => __('متن دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مشاهده', 'velento-shop')]);
        $this->add_control('cta_link', ['label' => __('لینک دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::URL]);
        $this->end_controls_section();

        $this->register_velento_color_controls(['accent']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $url = !empty($s['cta_link']['url']) ? $s['cta_link']['url'] : '';
        ?>
        <section class="velento-el-widget velento-el-promo" id="<?php echo esc_attr($this->get_id()); ?>">
            <div class="velento-el-promo__bg">
                <?php if ($s['bg_type'] === 'video' && !empty($s['bg_video']['url'])) : ?>
                    <video autoplay muted loop playsinline><source src="<?php echo esc_url($s['bg_video']['url']); ?>" type="video/mp4"></video>
                <?php else :
                    $img = !empty($s['bg_image']['url']) ? $s['bg_image']['url'] : \Elementor\Utils::get_placeholder_image_src();
                    ?>
                    <img src="<?php echo esc_url($img); ?>" alt="" loading="lazy">
                <?php endif; ?>
            </div>
            <div class="velento-el-promo__overlay"></div>
            <div class="velento-el-promo__body">
                <?php if ($s['eyebrow']) : ?><p class="velento-el-promo__eyebrow"><?php echo esc_html($s['eyebrow']); ?></p><?php endif; ?>
                <?php if ($s['title']) : ?><h2 class="velento-el-promo__title"><?php echo esc_html($s['title']); ?></h2><?php endif; ?>
                <?php if ($s['desc']) : ?><p class="velento-el-promo__desc"><?php echo esc_html($s['desc']); ?></p><?php endif; ?>
                <?php if ($s['cta_text']) : ?><a class="velento-el-btn velento-el-btn--primary" href="<?php echo esc_url($url); ?>"><?php echo esc_html($s['cta_text']); ?></a><?php endif; ?>
            </div>
        </section>
        <?php
    }
}
