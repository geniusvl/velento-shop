<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Usp_Band extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-usp-band'; }
    public function get_title() { return __('ولنتو | نوار خدمات و تضمین‌ها', 'velento-shop'); }
    public function get_icon() { return 'eicon-info-box'; }

    public static function icon_map()
    {
        return [
            'badge'   => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"></circle><path d="M8.5 14.5 7 21l5-2.4 5 2.4-1.5-6.5"></path></svg>',
            'box'     => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 8 12 3.5 20.5 8 12 12.5 3.5 8Z"></path><path d="M3.5 8v8.5L12 21l8.5-4.5V8"></path><path d="M12 12.5V21"></path></svg>',
            'shield'  => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 19.5 6.5V11.5C19.5 16 16.5 19.7 12 21C7.5 19.7 4.5 16 4.5 11.5V6.5L12 3.5Z"></path><path d="m9 12 2 2 4-4.3"></path></svg>',
            'headset' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 13.5v-2a7.5 7.5 0 0 1 15 0v2"></path><rect x="3.5" y="13" width="4" height="6" rx="1.4"></rect><rect x="16.5" y="13" width="4" height="6" rx="1.4"></rect><path d="M19.5 19.5v.5a3 3 0 0 1-3 3h-3"></path></svg>',
            'truck'   => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 6.5h11v9h-11z"></path><path d="M13.5 10h4l3 3v2.5h-7z"></path><circle cx="6.5" cy="18" r="1.6"></circle><circle cx="17" cy="18" r="1.6"></circle></svg>',
        ];
    }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('icon', ['label' => __('آیکن', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'badge', 'options' => ['badge' => __('نشان', 'velento-shop'), 'box' => __('بسته', 'velento-shop'), 'shield' => __('گارانتی', 'velento-shop'), 'headset' => __('پشتیبانی', 'velento-shop'), 'truck' => __('ارسال', 'velento-shop')]]);
        $repeater->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('ضمانت اصالت', 'velento-shop')]);
        $repeater->add_control('desc', ['label' => __('توضیح', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('هر ساعت پیش از ارسال احراز اصالت می‌شود.', 'velento-shop')]);
        $this->add_control('items', [
            'label' => __('موارد', 'velento-shop'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
            'default' => [
                ['icon' => 'badge', 'title' => __('ضمانت اصالت', 'velento-shop'), 'desc' => __('هر ساعت پیش از ارسال احراز اصالت می‌شود.', 'velento-shop')],
                ['icon' => 'box', 'title' => __('ارسال ایمن', 'velento-shop'), 'desc' => __('بسته‌بندی بیمه‌شده و ارسال مطمئن.', 'velento-shop')],
                ['icon' => 'shield', 'title' => __('ضمانت‌نامه معتبر', 'velento-shop'), 'desc' => __('گارانتی رسمی روی تمامی محصولات.', 'velento-shop')],
                ['icon' => 'headset', 'title' => __('پشتیبانی اختصاصی', 'velento-shop'), 'desc' => __('مشاوره پیش و پس از خرید.', 'velento-shop')],
            ],
            'title_field' => '{{{ title }}}',
        ]);
        $this->end_controls_section();
        $this->register_velento_color_controls();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $items = $s['items'] ?: [];
        $icons = self::icon_map();
        ?>
        <div class="velento-el-widget velento-el-usp" id="<?php echo esc_attr($this->get_id()); ?>" style="--v-cols:<?php echo (int) count($items) ?: 4; ?>;">
            <?php foreach ($items as $item) : ?>
                <div class="velento-el-usp__item">
                    <span class="velento-el-usp__icon"><?php echo $icons[$item['icon']] ?? $icons['badge']; ?></span>
                    <h3 class="velento-el-usp__title"><?php echo esc_html($item['title']); ?></h3>
                    <p class="velento-el-usp__desc"><?php echo esc_html($item['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
