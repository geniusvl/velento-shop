<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Store_Map extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-store-map'; }
    public function get_title() { return __('ولنتو | نقشه و آدرس فروشگاه', 'velento-shop'); }
    public function get_icon() { return 'eicon-google-maps'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('map_embed_url', [
            'label'       => __('آدرس embed نقشه گوگل (اختیاری)', 'velento-shop'),
            'type'        => \Elementor\Controls_Manager::URL,
            'description' => __('از گوگل‌مپ: Share > Embed a map > کپی مقدار src را اینجا بگذارید. خالی بگذارید تا فقط کارت آدرس نمایش داده شود.', 'velento-shop'),
        ]);
        $this->add_control('title', ['label' => __('عنوان', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('نمایشگاه ولنتو', 'velento-shop')]);
        $this->add_control('address', ['label' => __('آدرس', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => __('تهران، خیابان ولیعصر، پلاک ۰', 'velento-shop')]);
        $this->add_control('phone', ['label' => __('تلفن', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => ''] );
        $this->add_control('hours', ['label' => __('ساعات کاری', 'velento-shop'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __('شنبه تا پنجشنبه، ۱۰ تا ۲۰', 'velento-shop')]);
        $this->end_controls_section();
        $this->register_velento_color_controls(['accent', 'bg', 'border']);
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $embed_url = !empty($s['map_embed_url']['url']) ? $s['map_embed_url']['url'] : '';
        ?>
        <div class="velento-el-widget velento-el-map" id="<?php echo esc_attr($this->get_id()); ?>">
            <?php if ($embed_url) : ?>
                <div class="velento-el-map__frame">
                    <iframe src="<?php echo esc_url($embed_url); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title="<?php echo esc_attr($s['title']); ?>"></iframe>
                </div>
            <?php endif; ?>
            <div class="velento-el-map__card">
                <?php if ($s['title']) : ?><h3 class="velento-el-map__title"><?php echo esc_html($s['title']); ?></h3><?php endif; ?>
                <?php if ($s['address']) : ?><p class="velento-el-map__row"><span class="velento-el-icon">📍</span><?php echo esc_html($s['address']); ?></p><?php endif; ?>
                <?php if ($s['phone']) : ?><p class="velento-el-map__row"><span class="velento-el-icon">📞</span><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $s['phone'])); ?>"><?php echo esc_html($s['phone']); ?></a></p><?php endif; ?>
                <?php if ($s['hours']) : ?><p class="velento-el-map__row"><span class="velento-el-icon">🕒</span><?php echo esc_html($s['hours']); ?></p><?php endif; ?>
            </div>
        </div>
        <?php
    }
}
