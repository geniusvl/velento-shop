<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Contact_Form extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-contact-form'; }
    public function get_title() { return __('ولنتو | فرم تماس ساده', 'velento-shop'); }
    public function get_icon() { return 'eicon-form-horizontal'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('با ما در تماس باشید', 'velento-shop')]);
        $this->add_control('subtitle', ['label' => __('توضیح', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('سوال یا درخواستی دارید؟ فرم زیر را پر کنید تا همکاران ما با شما تماس بگیرند.', 'velento-shop')]);
        $this->add_control('show_phone', ['label' => __('نمایش فیلد تلفن', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('button_text', ['label' => __('متن دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('ارسال پیام', 'velento-shop')]);
        $this->end_controls_section();
        $this->register_velento_color_controls();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $uid = $this->get_id();
        // Submits to the theme's own inc/ajax.php -> velento_handle_contact_form()
        // (plain wp_mail() to the site's admin email), same nonce/rate-limit
        // pattern as the built-in newsletter and login/register AJAX handlers.
        ?>
        <div class="velento-el-widget velento-el-contact" id="<?php echo esc_attr($uid); ?>">
            <?php if ($s['title'] || $s['subtitle']) : ?>
                <div class="velento-el-heading">
                    <?php if ($s['title']) : ?><h2 class="velento-el-heading__title"><?php echo esc_html($s['title']); ?></h2><?php endif; ?>
                    <?php if ($s['subtitle']) : ?><p class="velento-el-heading__subtitle"><?php echo esc_html($s['subtitle']); ?></p><?php endif; ?>
                </div>
            <?php endif; ?>
            <form class="velento-el-contact__form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                <?php wp_nonce_field('velento_contact_form', 'velento_contact_nonce'); ?>
                <input type="hidden" name="action" value="velento_contact_form">
                <div class="velento-el-contact__row">
                    <div class="form-group">
                        <label class="screen-reader-text" for="v-el-name-<?php echo esc_attr($uid); ?>"><?php esc_html_e('نام', 'velento-shop'); ?></label>
                        <input class="form-input" type="text" id="v-el-name-<?php echo esc_attr($uid); ?>" name="name" placeholder="<?php esc_attr_e('نام و نام خانوادگی', 'velento-shop'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="screen-reader-text" for="v-el-email-<?php echo esc_attr($uid); ?>"><?php esc_html_e('ایمیل', 'velento-shop'); ?></label>
                        <input class="form-input" type="email" id="v-el-email-<?php echo esc_attr($uid); ?>" name="email" placeholder="Email" required>
                    </div>
                </div>
                <?php if ($s['show_phone'] === 'yes') : ?>
                    <div class="form-group">
                        <label class="screen-reader-text" for="v-el-phone-<?php echo esc_attr($uid); ?>"><?php esc_html_e('تلفن', 'velento-shop'); ?></label>
                        <input class="form-input" type="tel" id="v-el-phone-<?php echo esc_attr($uid); ?>" name="phone" placeholder="<?php esc_attr_e('شماره تماس (اختیاری)', 'velento-shop'); ?>">
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label class="screen-reader-text" for="v-el-msg-<?php echo esc_attr($uid); ?>"><?php esc_html_e('پیام', 'velento-shop'); ?></label>
                    <textarea class="form-textarea" id="v-el-msg-<?php echo esc_attr($uid); ?>" name="message" rows="4" placeholder="<?php esc_attr_e('پیام شما', 'velento-shop'); ?>" required></textarea>
                </div>
                <button type="submit" class="velento-el-btn velento-el-btn--primary"><?php echo esc_html($s['button_text']); ?></button>
                <div class="velento-el-contact__msg-holder"></div>
            </form>
        </div>
        <?php
    }
}
