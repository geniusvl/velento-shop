<?php
if (!defined('ABSPATH')) exit;

class Velento_El_Blog_Grid extends Velento_El_Widget_Base
{
    public function get_name() { return 'velento-blog-grid'; }
    public function get_title() { return __('ولنتو | گرید مطالب وبلاگ', 'velento-shop'); }
    public function get_icon() { return 'eicon-post-list'; }

    protected function register_controls()
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'velento-shop')]);
        $this->add_control('limit', ['label' => __('تعداد مطلب', 'velento-shop'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 12]);
        $this->add_responsive_control('columns', [
            'label' => __('تعداد ستون', 'velento-shop'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '3',
            'tablet_default' => '2', 'mobile_default' => '1',
            'options' => ['1' => '1', '2' => '2', '3' => '3'],
            'selectors' => ['{{WRAPPER}} .velento-el-blog' => '--v-cols: {{VALUE}};'],
        ]);
        $this->add_control('excerpt_length', ['label' => __('طول خلاصه (کلمه)', 'velento-shop'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 18]);
        $this->end_controls_section();
        $this->register_velento_color_controls();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $query = new WP_Query([
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => (int) $s['limit'],
            'ignore_sticky_posts' => true,
        ]);

        if (!$query->have_posts()) {
            echo '<p>' . esc_html__('مطلبی برای نمایش پیدا نشد.', 'velento-shop') . '</p>';
            return;
        }
        ?>
        <div class="velento-el-widget velento-el-blog" id="<?php echo esc_attr($this->get_id()); ?>" style="--v-cols:<?php echo esc_attr($s['columns'] ?: 3); ?>;">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article class="velento-el-post">
                    <a class="velento-el-post__media" href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large', ['loading' => 'lazy']); else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/branding/logo-light.png'); ?>" alt="" loading="lazy">
                        <?php endif; ?>
                    </a>
                    <div class="velento-el-post__body">
                        <time class="velento-el-post__date"><?php echo esc_html(get_the_date()); ?></time>
                        <h3 class="velento-el-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="velento-el-post__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), (int) $s['excerpt_length'], '…')); ?></p>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php
    }
}
