<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Countdown extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-countdown'; }
    public function get_title() { return __('ولنتو | تایمر شمارش معکوس', 'velento-shop'); }
    public function get_icon() { return 'eicon-countdown'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('پایان جشنواره فروش ویژه', 'velento-shop')]);
        $this->add_control('target_date', [
            'label'       => __('تاریخ و ساعت پایان (میلادی)', 'velento-shop'),
            'type'        => \Elementor\Controls_Manager::DATE_TIME,
            'description' => __('چون فیلد تاریخ المنتور میلادی است، تاریخ شمسی مدنظر را ابتدا به میلادی تبدیل کنید.', 'velento-shop'),
        ]);
        $this->add_control('ended_text', ['label' => __('متن پس از پایان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('این جشنواره به پایان رسیده است.', 'velento-shop')]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent', 'bg', 'border']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $target = !empty($s['target_date']) ? $s['target_date'] : gmdate('Y-m-d H:i', strtotime('+7 days'));
        ?>
        <div class="velento-el-widget" id="<?php echo esc_attr($this->get_id()); ?>">
            <?php if ($s['title']) : ?><h3 class="velento-el-heading__title" style="text-align:center;margin-bottom:16px;"><?php echo esc_html($s['title']); ?></h3><?php endif; ?>
            <div class="velento-el-countdown" data-target="<?php echo esc_attr($target); ?>">
                <div class="velento-el-countdown__box"><span class="velento-el-countdown__num" data-unit="d">0</span><span class="velento-el-countdown__label"><?php esc_html_e('روز', 'velento-shop'); ?></span></div>
                <div class="velento-el-countdown__box"><span class="velento-el-countdown__num" data-unit="h">00</span><span class="velento-el-countdown__label"><?php esc_html_e('ساعت', 'velento-shop'); ?></span></div>
                <div class="velento-el-countdown__box"><span class="velento-el-countdown__num" data-unit="m">00</span><span class="velento-el-countdown__label"><?php esc_html_e('دقیقه', 'velento-shop'); ?></span></div>
                <div class="velento-el-countdown__box"><span class="velento-el-countdown__num" data-unit="s">00</span><span class="velento-el-countdown__label"><?php esc_html_e('ثانیه', 'velento-shop'); ?></span></div>
                <p class="velento-el-countdown__msg" style="display:none;"><?php echo esc_html($s['ended_text']); ?></p>
            </div>
        </div>
        <style>#<?php echo esc_attr($this->get_id()); ?> .velento-el-countdown.is-ended > *:not(.velento-el-countdown__msg){display:none;} #<?php echo esc_attr($this->get_id()); ?> .velento-el-countdown.is-ended .velento-el-countdown__msg{display:block;}</style>
        <?php
    }
}
