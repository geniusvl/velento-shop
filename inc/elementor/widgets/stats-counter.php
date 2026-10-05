<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Stats_Counter extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-stats-counter'; }
    public function get_title() { return __('ولنتو | آمار و اعداد', 'velento-shop'); }
    public function get_icon() { return 'eicon-counter'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('number', ['label' => __('عدد', 'velento-shop'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 100]);
        $repeater->add_control('suffix', ['label' => __('پسوند (مثلاً + یا ٪)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '+']);
        $repeater->add_control('label', ['label' => __('برچسب', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مشتری راضی', 'velento-shop')]);
        $this->add_control('items', [
            'label' => __('آمارها', 'velento-shop'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
            'default' => [
                ['number' => 5000, 'suffix' => '+', 'label' => __('مشتری راضی', 'velento-shop')],
                ['number' => 12, 'suffix' => '', 'label' => __('سال سابقه', 'velento-shop')],
                ['number' => 98, 'suffix' => '٪', 'label' => __('رضایت مشتریان', 'velento-shop')],
                ['number' => 40, 'suffix' => '+', 'label' => __('برند معتبر', 'velento-shop')],
            ],
            'title_field' => '{{{ label }}}',
        ]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $items = $s['items'] ?: [];
        ?>
        <div class="velento-el-widget velento-el-stats" id="<?php echo esc_attr($this->get_id()); ?>" style="--v-cols:<?php echo (int) count($items) ?: 4; ?>;">
            <?php foreach ($items as $item) : ?>
                <div class="velento-el-stat">
                    <span class="velento-el-stat__num" data-target="<?php echo esc_attr((int) $item['number']); ?>" data-suffix="<?php echo esc_attr($item['suffix']); ?>">0<?php echo esc_html($item['suffix']); ?></span>
                    <p class="velento-el-stat__label"><?php echo esc_html($item['label']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
