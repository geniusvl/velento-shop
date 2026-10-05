<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Featured_Carousel extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-featured-carousel'; }
    public function get_title() { return __('ولنتو | کاروسل ساعت‌های منتخب', 'velento-shop'); }
    public function get_icon() { return 'eicon-post-slider'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('heading', ['label' => __('عنوان بخش', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('ساعت‌های شاخص', 'velento-shop')]);
        $this->add_control('eyebrow', ['label' => __('زیرعنوان کوچک', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('کالکشن منتخب', 'velento-shop')]);

        $repeater = new \Elementor\Repeater();
        $repeater->add_control('image', ['label' => __('تصویر', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $repeater->add_control('name', ['label' => __('نام', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مدل کلاسیک', 'velento-shop')]);
        $repeater->add_control('tag', ['label' => __('برچسب', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('دست‌ساز رسمی', 'velento-shop')]);
        $repeater->add_control('link', ['label' => __('لینک', 'velento-shop'), 'type' => \Elementor\Controls_Manager::URL]);
        $this->add_control('items', [
            'label'       => __('آیتم‌ها', 'velento-shop'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                ['name' => __('مدل کلاسیک', 'velento-shop'), 'tag' => __('دست‌ساز رسمی', 'velento-shop')],
                ['name' => __('مدل کرونوگراف', 'velento-shop'), 'tag' => __('ورزشی', 'velento-shop')],
                ['name' => __('مدل هریتیج', 'velento-shop'), 'tag' => __('کلاسیک', 'velento-shop')],
            ],
            'title_field' => '{{{ name }}}',
        ]);
        $this->end_controls_section();

        $this->register_velento_color_controls();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $items = $s['items'] ?: [];
        ?>
        <section class="velento-el-widget velento-el-carousel" id="<?php echo esc_attr($this->get_id()); ?>">
            <div class="velento-el-carousel__stage">
                <?php if ($s['heading']) : ?>
                    <div class="velento-el-heading is-center">
                        <?php if ($s['eyebrow']) : ?><p class="velento-el-heading__eyebrow"><?php echo esc_html($s['eyebrow']); ?></p><?php endif; ?>
                        <h2 class="velento-el-heading__title"><?php echo esc_html($s['heading']); ?></h2>
                    </div>
                <?php endif; ?>
                <div class="velento-el-carousel__track">
                    <?php foreach ($items as $item) :
                        $img = !empty($item['image']['url']) ? $item['image']['url'] : \Elementor\Utils::get_placeholder_image_src();
                        $url = !empty($item['link']['url']) ? $item['link']['url'] : '';
                        $tag = $url ? 'a' : 'div';
                        ?>
                        <<?php echo $tag; ?> class="velento-el-carousel__item" <?php echo $url ? 'href="' . esc_url($url) . '"' : ''; ?>>
                            <span class="velento-el-carousel__photo"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($item['name']); ?>" loading="lazy"></span>
                            <p class="velento-el-carousel__tag"><?php echo esc_html($item['tag']); ?></p>
                            <h3 class="velento-el-carousel__name"><?php echo esc_html($item['name']); ?></h3>
                        </<?php echo $tag; ?>>
                    <?php endforeach; ?>
                </div>
                <div class="velento-el-carousel__nav">
                    <button type="button" class="velento-el-carousel__prev" aria-label="<?php esc_attr_e('قبلی', 'velento-shop'); ?>">→</button>
                    <button type="button" class="velento-el-carousel__next" aria-label="<?php esc_attr_e('بعدی', 'velento-shop'); ?>">←</button>
                </div>
            </div>
        </section>
        <?php
    }
}
