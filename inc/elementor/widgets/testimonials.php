<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Testimonials extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-testimonials'; }
    public function get_title() { return __('ولنتو | نظرات مشتریان', 'velento-shop'); }
    public function get_icon() { return 'eicon-testimonial'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('avatar', ['label' => __('تصویر (اختیاری)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::MEDIA]);
        $repeater->add_control('quote', ['label' => __('متن نظر', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('کیفیت و بسته‌بندی فوق‌العاده بود.', 'velento-shop')]);
        $repeater->add_control('name', ['label' => __('نام', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('مشتری ولنتو', 'velento-shop')]);
        $repeater->add_control('role', ['label' => __('توضیح کوتاه (شهر/خرید)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT]);
        $repeater->add_control('rating', ['label' => __('امتیاز (۱ تا ۵)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 5, 'min' => 1, 'max' => 5]);
        $this->add_control('items', [
            'label' => __('نظرات', 'velento-shop'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
            'default' => [
                ['quote' => __('کیفیت و بسته‌بندی فوق‌العاده بود، دقیقاً مثل عکس‌ها.', 'velento-shop'), 'name' => __('امیر رضایی', 'velento-shop'), 'role' => __('تهران', 'velento-shop'), 'rating' => 5],
                ['quote' => __('پشتیبانی بسیار خوب و ارسال سریع.', 'velento-shop'), 'name' => __('سارا احمدی', 'velento-shop'), 'role' => __('اصفهان', 'velento-shop'), 'rating' => 5],
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
        <div class="velento-el-widget velento-el-testimonials" id="<?php echo esc_attr($this->get_id()); ?>">
            <div class="velento-el-testimonials__track">
                <?php foreach ($items as $item) :
                    $rating = max(1, min(5, (int) $item['rating']));
                    $avatar = !empty($item['avatar']['url']) ? $item['avatar']['url'] : '';
                    ?>
                    <article class="velento-el-testimonial">
                        <div class="velento-el-testimonial__stars"><?php echo esc_html(str_repeat('★', $rating) . str_repeat('☆', 5 - $rating)); ?></div>
                        <p class="velento-el-testimonial__quote">«<?php echo esc_html($item['quote']); ?>»</p>
                        <div class="velento-el-testimonial__author">
                            <?php if ($avatar) : ?><img class="velento-el-testimonial__avatar" src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($item['name']); ?>"><?php endif; ?>
                            <span>
                                <span class="velento-el-testimonial__name"><?php echo esc_html($item['name']); ?></span>
                                <?php if ($item['role']) : ?><span class="velento-el-testimonial__role"><?php echo esc_html($item['role']); ?></span><?php endif; ?>
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="velento-el-carousel__nav">
                <button type="button" class="velento-el-testimonials__prev" aria-label="<?php esc_attr_e('قبلی', 'velento-shop'); ?>">→</button>
                <button type="button" class="velento-el-testimonials__next" aria-label="<?php esc_attr_e('بعدی', 'velento-shop'); ?>">←</button>
            </div>
        </div>
        <?php
    }
}
