<?php
/**
 * Shared base class for every Velento Elementor widget.
 *
 * Two things live here that every single widget reuses:
 *
 *   1. register_velento_color_controls() — a "پالت رنگی" style section
 *      with four optional color pickers (accent / background / text /
 *      border). Each one is wired straight to the matching Velento CSS
 *      custom property (--accent/--bg/--text/--border), scoped to just
 *      this widget's wrapper. Left empty, a control changes nothing —
 *      the widget inherits the site-wide palette (which itself can be
 *      set once in Appearance > Customize > "پالت رنگی ولنتو", or per
 *      Elementor Global Color). Set it, and only *this* widget
 *      instance switches to the custom color, because every component
 *      stylesheet in the theme already reads colors through these same
 *      CSS variables — this is the exact mechanism that lets a
 *      customer "apply the color palette they want, anywhere."
 *
 *   2. get_categories() / get_keywords() so every widget files itself
 *      under the "ولنتو شاپ" panel category registered in compat.php.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\Elementor\Widget_Base')) {
    return;
}

abstract class Velento_El_Widget_Base extends \Elementor\Widget_Base
{
    public function get_categories()
    {
        return ['velento'];
    }

    public function get_keywords()
    {
        return ['velento', 'ولنتو', 'ساعت', 'luxury', 'watch'];
    }

    public function get_icon()
    {
        return 'eicon-single-page';
    }

    /**
     * Call from inside register_controls() on every widget, once, to
     * add the standard "پالت رنگی" style section. $vars lets a widget
     * drop controls that don't apply to it (e.g. a plain button has no
     * meaningful "background" separate from the button color itself).
     *
     * @param string[] $vars Subset of ['accent','bg','text','border'].
     */
    protected function register_velento_color_controls($vars = ['accent', 'bg', 'text', 'border'])
    {
        $this->start_controls_section('velento_palette_section', [
            'label' => __('پالت رنگی ولنتو', 'velento-shop'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('velento_palette_note', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => __('خالی بگذارید تا این بخش از پالت رنگی سراسری سایت (سفارشی‌ساز یا رنگ‌های عمومی المنتور) پیروی کند. با انتخاب رنگ، فقط همین ویجت رنگ خودش را می‌گیرد.', 'velento-shop'),
            'content_classes' => 'elementor-descriptor',
        ]);

        $labels = [
            'accent' => __('رنگ تاکیدی (طلایی)', 'velento-shop'),
            'bg'     => __('رنگ پس‌زمینه', 'velento-shop'),
            'text'   => __('رنگ متن', 'velento-shop'),
            'border' => __('رنگ خط جداکننده', 'velento-shop'),
        ];

        foreach ($vars as $var) {
            if (!isset($labels[$var])) {
                continue;
            }
            $this->add_control('velento_color_' . $var, [
                'label'     => $labels[$var],
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}}' => '--' . $var . ': {{VALUE}};',
                ],
            ]);
        }

        $this->end_controls_section();
    }

    /**
     * Small helper so every widget's render() can open its root node
     * with a consistent, unique wrapper class (used by
     * assets/js/elementor-widgets.js to scope querySelector calls to
     * just this instance when a page has the same widget more than
     * once).
     */
    protected function velento_instance_class($base)
    {
        return $base . ' ' . $base . '--' . $this->get_id();
    }
}
