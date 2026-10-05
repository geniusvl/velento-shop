<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Newsletter extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-newsletter'; }
    public function get_title() { return __('ولنتو | عضویت در خبرنامه', 'velento-shop'); }
    public function get_icon() { return 'eicon-form-horizontal'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('از تازه‌ترین کالکشن‌ها باخبر شوید', 'velento-shop')]);
        $this->add_control('subtitle', ['label' => __('توضیح', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('ایمیل خود را ثبت کنید تا پیش از دیگران مطلع شوید.', 'velento-shop')]);
        $this->add_control('button_text', ['label' => __('متن دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('ثبت', 'velento-shop')]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        // Reuses the very same AJAX handler as the theme's built-in
        // newsletter block (inc/ajax.php -> velento_handle_newsletter_subscribe),
        // so submissions from this widget land in the same subscriber list.
        ?>
        <div class="velento-el-widget velento-el-newsletter" id="<?php echo esc_attr($this->get_id()); ?>">
            <div>
                <h2 class="velento-el-newsletter__title"><?php echo esc_html($s['title']); ?></h2>
                <?php if ($s['subtitle']) : ?><p class="velento-el-newsletter__subtitle"><?php echo esc_html($s['subtitle']); ?></p><?php endif; ?>
            </div>
            <form class="velento-el-newsletter__form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                <?php wp_nonce_field('velento_subscribe', 'velento_newsletter_nonce'); ?>
                <input type="hidden" name="action" value="velento_subscribe">
                <label class="screen-reader-text" for="velento-el-email-<?php echo esc_attr($this->get_id()); ?>"><?php esc_html_e('آدرس ایمیل', 'velento-shop'); ?></label>
                <input type="email" id="velento-el-email-<?php echo esc_attr($this->get_id()); ?>" name="email" placeholder="Email" required>
                <button type="submit" class="velento-el-btn velento-el-btn--primary"><?php echo esc_html($s['button_text']); ?></button>
                <div class="velento-el-newsletter__msg-holder"></div>
            </form>
        </div>
        <?php
    }
}
