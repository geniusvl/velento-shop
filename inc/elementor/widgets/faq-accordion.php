<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Faq_Accordion extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-faq-accordion'; }
    public function get_title() { return __('ولنتو | سوالات متداول', 'velento-shop'); }
    public function get_icon() { return 'eicon-accordion'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('allow_multi', ['label' => __('اجازه باز بودن چند سوال هم‌زمان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '']);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('question', ['label' => __('سوال', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('سوال شما؟', 'velento-shop')]);
        $repeater->add_control('answer', ['label' => __('پاسخ', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('پاسخ را اینجا بنویسید.', 'velento-shop')]);
        $this->add_control('items', [
            'label' => __('سوالات', 'velento-shop'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
            'default' => [
                ['question' => __('آیا ساعت‌ها اورجینال هستند؟', 'velento-shop'), 'answer' => __('بله، تمامی محصولات دارای ضمانت اصالت هستند.', 'velento-shop')],
                ['question' => __('چقدر طول می‌کشد سفارش برسد؟', 'velento-shop'), 'answer' => __('معمولاً بین ۲ تا ۵ روز کاری.', 'velento-shop')],
            ],
            'title_field' => '{{{ question }}}',
        ]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent', 'border', 'text']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $items = $s['items'] ?: [];
        ?>
        <div class="velento-el-widget velento-el-faq" id="<?php echo esc_attr($this->get_id()); ?>" data-accordion-mode="<?php echo $s['allow_multi'] === 'yes' ? 'multi' : 'single'; ?>">
            <?php foreach ($items as $i => $item) : ?>
                <div class="velento-el-faq__item<?php echo $i === 0 ? ' is-open' : ''; ?>">
                    <button type="button" class="velento-el-faq__q"><span><?php echo esc_html($item['question']); ?></span><span class="v-icon" aria-hidden="true">+</span></button>
                    <div class="velento-el-faq__a"><div class="velento-el-faq__a-inner"><?php echo wp_kses_post(wpautop($item['answer'])); ?></div></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
