<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Trust_Badges extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-trust-badges'; }
    public function get_title() { return __('ولنتو | ردیف نشان‌های اعتماد', 'velento-shop'); }
    public function get_icon() { return 'eicon-shield-alt'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('icon', ['label' => __('آیکن', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'badge', 'options' => ['badge' => __('نشان', 'velento-shop'), 'box' => __('بسته', 'velento-shop'), 'shield' => __('گارانتی', 'velento-shop'), 'headset' => __('پشتیبانی', 'velento-shop'), 'truck' => __('ارسال', 'velento-shop')]]);
        $repeater->add_control('label', ['label' => __('متن', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('پرداخت امن', 'velento-shop')]);
        $this->add_control('items', [
            'label' => __('موارد', 'velento-shop'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
            'default' => [
                ['icon' => 'shield', 'label' => __('پرداخت امن', 'velento-shop')],
                ['icon' => 'truck', 'label' => __('ارسال سریع', 'velento-shop')],
                ['icon' => 'badge', 'label' => __('ضمانت اصالت', 'velento-shop')],
                ['icon' => 'headset', 'label' => __('پشتیبانی ۲۴ ساعته', 'velento-shop')],
            ],
            'title_field' => '{{{ label }}}',
        ]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent', 'bg', 'border']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $items = $s['items'] ?: [];
        $icons = class_exists('Velento_El_Usp_Band') ? Velento_El_Usp_Band::icon_map() : [];
        ?>
        <div class="velento-el-widget velento-el-badges" id="<?php echo esc_attr($this->get_id()); ?>">
            <?php foreach ($items as $item) : ?>
                <span class="velento-el-badges__item"><span class="velento-el-icon"><?php echo $icons[$item['icon']] ?? ''; ?></span><?php echo esc_html($item['label']); ?></span>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
