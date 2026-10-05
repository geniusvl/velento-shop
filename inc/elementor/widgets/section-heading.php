<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Section_Heading extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-section-heading'; }
    public function get_title() { return __('ولنتو | عنوان بخش', 'velento-shop'); }
    public function get_icon() { return 'eicon-heading'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('eyebrow', ['label' => __('برچسب کوچک', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('کالکشن منتخب', 'velento-shop')]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('ساعتی برای هر شخصیت', 'velento-shop')]);
        $this->add_control('highlight', ['label' => __('بخشی از عنوان که با رنگ طلایی نمایش داده شود (اختیاری)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'description' => __('اگر این کلمه دقیقاً داخل عنوان وجود داشته باشد، رنگ طلایی می‌گیرد.', 'velento-shop')]);
        $this->add_control('subtitle', ['label' => __('توضیح', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA]);
        $this->add_control('align', [
            'label'   => __('چینش', 'velento-shop'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'right',
            'options' => ['right' => __('راست', 'velento-shop'), 'center' => __('وسط', 'velento-shop')],
        ]);
        $this->end_controls_section();

        $this->register_velento_color_controls(['accent', 'text']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $title = esc_html($s['title']);
        if (!empty($s['highlight']) && mb_strpos($s['title'], $s['highlight']) !== false) {
            $title = str_replace(esc_html($s['highlight']), '<em>' . esc_html($s['highlight']) . '</em>', $title);
        }
        ?>
        <div class="velento-el-widget velento-el-heading<?php echo $s['align'] === 'center' ? ' is-center' : ''; ?>" id="<?php echo esc_attr($this->get_id()); ?>">
            <?php if ($s['eyebrow']) : ?><p class="velento-el-heading__eyebrow"><?php echo esc_html($s['eyebrow']); ?></p><?php endif; ?>
            <?php if ($s['title']) : ?><h2 class="velento-el-heading__title"><?php echo wp_kses_post($title); ?></h2><?php endif; ?>
            <?php if ($s['subtitle']) : ?><p class="velento-el-heading__subtitle"><?php echo esc_html($s['subtitle']); ?></p><?php endif; ?>
        </div>
        <?php
    }
}
