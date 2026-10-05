<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Hero_Slider extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-hero-slider'; }
    public function get_title() { return __('ولنتو | اسلایدر هیرو', 'velento-shop'); }
    public function get_icon() { return 'eicon-slider-full-screen'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('eyebrow', ['label' => __('برچسب بالای عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('ولنتو شاپ', 'velento-shop')]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('زمان، وقتی با ظرافت طراحی شود', 'velento-shop')]);
        $this->add_control('subtitle', ['label' => __('توضیح', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('هر قطعه در ولنتو شاپ، ترکیبی از دقت مکانیکی و طراحی بی‌زمانه است.', 'velento-shop')]);
        $this->add_control('cta_text', ['label' => __('متن دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مشاهده‌ی کالکشن', 'velento-shop')]);
        $this->add_control('cta_link', ['label' => __('لینک دکمه', 'velento-shop'), 'type' => \Elementor\Controls_Manager::URL, 'default' => ['url' => '']]);
        $this->add_control('trust_items', ['label' => __('موارد اعتماد (هر خط یک مورد)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => "ضمانت اصالت\nارسال ایمن به سراسر کشور\nخدمات پس از فروش اختصاصی"]);
        $this->end_controls_section();

        $this->start_controls_section('slides_section', ['label' => __('اسلایدها', 'velento-shop')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('image', ['label' => __('تصویر', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $repeater->add_control('brand', ['label' => __('برند', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('رولکس', 'velento-shop')]);
        $repeater->add_control('name', ['label' => __('نام مدل', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مدل کلاسیک', 'velento-shop')]);
        $this->add_control('slides', [
            'label'       => __('اسلایدها', 'velento-shop'),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                ['brand' => __('رولکس', 'velento-shop'), 'name' => __('دی-دیت اویستر', 'velento-shop')],
                ['brand' => __('امگا', 'velento-shop'), 'name' => __('اسپیدمستر کرونوگراف', 'velento-shop')],
            ],
            'title_field' => '{{{ brand }}} — {{{ name }}}',
        ]);
        $this->end_controls_section();

        $this->register_velento_color_controls();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $slides = $s['slides'] ?: [];
        $trust = array_filter(array_map('trim', explode("\n", (string) $s['trust_items'])));
        $cta_url = !empty($s['cta_link']['url']) ? $s['cta_link']['url'] : '';
        ?>
        <section class="velento-el-widget velento-el-hero" id="<?php echo esc_attr($this->get_id()); ?>">
            <div class="velento-el-hero__wrap">
                <div class="velento-el-hero__copy">
                    <?php if ($s['eyebrow']) : ?><p class="velento-el-hero__eyebrow"><span aria-hidden="true">✦</span> <?php echo esc_html($s['eyebrow']); ?> <span aria-hidden="true">✦</span></p><?php endif; ?>
                    <?php if ($s['title']) : ?><h2 class="velento-el-hero__title"><?php echo esc_html($s['title']); ?></h2><?php endif; ?>
                    <?php if ($s['subtitle']) : ?><p class="velento-el-hero__subtitle"><?php echo esc_html($s['subtitle']); ?></p><?php endif; ?>
                    <?php if ($s['cta_text']) : ?>
                        <div class="velento-el-hero__actions">
                            <a class="velento-el-btn velento-el-btn--primary" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($s['cta_text']); ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if ($trust) : ?>
                        <ul class="velento-el-hero__trust">
                            <?php foreach ($trust as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <?php if ($slides) : ?>
                    <div class="velento-el-hero__showcase" aria-roledescription="carousel">
                        <?php foreach ($slides as $i => $slide) :
                            $img = !empty($slide['image']['url']) ? $slide['image']['url'] : \Elementor\Utils::get_placeholder_image_src();
                            ?>
                            <figure class="velento-el-hero__slide<?php echo $i === 0 ? ' is-active' : ''; ?>">
                                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($slide['brand'] . ' ' . $slide['name']); ?>" loading="lazy">
                                <figcaption class="velento-el-hero__caption"><b><?php echo esc_html($slide['brand']); ?></b><span><?php echo esc_html($slide['name']); ?></span></figcaption>
                            </figure>
                        <?php endforeach; ?>
                        <?php if (count($slides) > 1) : ?>
                            <div class="velento-el-hero__dots">
                                <?php foreach ($slides as $i => $slide) : ?>
                                    <button type="button" class="velento-el-hero__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" aria-label="<?php echo esc_attr($slide['brand']); ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
