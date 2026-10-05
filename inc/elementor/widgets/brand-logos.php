<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Brand_Logos extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-brand-logos'; }
    public function get_title() { return __('ولنتو | لوگوی برندها', 'velento-shop'); }
    public function get_icon() { return 'eicon-thumbnails-half'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('marquee', ['label' => __('حرکت خودکار (مارکی)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes']);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('logo', ['label' => __('لوگو', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $repeater->add_control('name', ['label' => __('نام برند', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('برند', 'velento-shop')]);
        $this->add_control('logos', [
            'label' => __('لوگوها', 'velento-shop'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
            'default' => [
                ['name' => 'Rolex'], ['name' => 'Omega'], ['name' => 'Patek Philippe'], ['name' => 'Audemars Piguet'],
            ],
            'title_field' => '{{{ name }}}',
        ]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['bg', 'border']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $logos = $s['logos'] ?: [];
        if (!$logos) return;
        $class = 'velento-el-widget velento-el-brands' . ($s['marquee'] === 'yes' ? ' is-marquee' : '');
        $loops = $s['marquee'] === 'yes' ? 2 : 1;
        ?>
        <div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($this->get_id()); ?>">
            <div class="velento-el-brands__track">
                <?php for ($r = 0; $r < $loops; $r++) : foreach ($logos as $logo) :
                    $img = !empty($logo['logo']['url']) ? $logo['logo']['url'] : \Elementor\Utils::get_placeholder_image_src();
                    ?>
                    <img class="velento-el-brands__logo" src="<?php echo esc_url($img); ?>" alt="<?php echo $r === 0 ? esc_attr($logo['name']) : ''; ?>" loading="lazy">
                <?php endforeach; endfor; ?>
            </div>
        </div>
        <?php
    }
}
