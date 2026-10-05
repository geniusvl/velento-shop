<?php
/**
 * Customizer Settings
 *
 * Panels below are intentionally scoped to what a store owner actually
 * needs to change without a developer: brand accent color, the top
 * announcement bar, and the homepage hero copy. Everything reads with
 * a get_theme_mod() fallback that matches the current hardcoded copy,
 * so nothing changes on the front end until someone edits it in
 * Appearance > Customize.
 */

if (!defined('ABSPATH')) {
    exit;
}

function velento_customize_register($wp_customize)
{
    /* ---------------------------------------------------------------
     * Section: Brand color
     * ------------------------------------------------------------- */
    $wp_customize->add_section('velento_brand_color', [
        'title'       => __('پالت رنگی ولنتو', 'velento-shop'),
        'description' => __('این رنگ‌ها روی کل سایت (و در صورت فعال بودن المنتور، به‌صورت خودکار روی رنگ‌های عمومی المنتور هم) اعمال می‌شوند. هر مشتری می‌تواند اینجا پالت دلخواه خودش را جایگزین کند.', 'velento-shop'),
        'priority'    => 25,
    ]);

    $velento_palette_fields = [
        'velento_accent_color' => ['#C9A85B', __('رنگ طلایی تاکیدی (Accent)', 'velento-shop')],
        'velento_bg_color'     => ['#FAFAFA', __('رنگ پس‌زمینه اصلی', 'velento-shop')],
        'velento_text_color'   => ['#0A0A0A', __('رنگ متن اصلی', 'velento-shop')],
        'velento_secondary_color' => ['#666666', __('رنگ متن کم‌رنگ (ثانویه)', 'velento-shop')],
        'velento_border_color' => ['#E5E5E5', __('رنگ خط جداکننده', 'velento-shop')],
    ];

    foreach ($velento_palette_fields as $setting_id => $field) {
        [$default, $label] = $field;

        $wp_customize->add_setting($setting_id, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ]);

        $wp_customize->add_control(new WP_Customize_Color_Control(
            $wp_customize,
            $setting_id,
            [
                'label'   => $label,
                'section' => 'velento_brand_color',
            ]
        ));
    }

    /* ---------------------------------------------------------------
     * Section: Announcement bar
     * ------------------------------------------------------------- */
    $wp_customize->add_section('velento_announcement', [
        'title'    => __('نوار اعلان بالای سایت', 'velento-shop'),
        'priority' => 26,
    ]);

    $wp_customize->add_setting('velento_announcement_enabled', [
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);

    $wp_customize->add_control('velento_announcement_enabled', [
        'label'   => __('نمایش نوار اعلان', 'velento-shop'),
        'section' => 'velento_announcement',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('velento_announcement_text', [
        'default'           => __('ارسال رایگان برای سفارش‌های بالای ۵ میلیون تومان', 'velento-shop'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('velento_announcement_text', [
        'label'   => __('متن نوار اعلان', 'velento-shop'),
        'section' => 'velento_announcement',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('velento_announcement_link', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control('velento_announcement_link', [
        'label'       => __('لینک نوار اعلان (اختیاری)', 'velento-shop'),
        'section'     => 'velento_announcement',
        'type'        => 'url',
        'input_attrs' => ['placeholder' => 'https://'],
    ]);

    /* ---------------------------------------------------------------
     * Section: Homepage hero
     * ------------------------------------------------------------- */
    $wp_customize->add_section('velento_hero', [
        'title'    => __('هیرو صفحه اصلی', 'velento-shop'),
        'priority' => 27,
    ]);

    $wp_customize->add_setting('velento_hero_eyebrow', [
        'default'           => __('ولنتو شاپ', 'velento-shop'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('velento_hero_eyebrow', [
        'label'   => __('برچسب کوچک بالای عنوان', 'velento-shop'),
        'section' => 'velento_hero',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('velento_hero_title', [
        'default'           => __('زمان، وقتی با ظرافت طراحی شود', 'velento-shop'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('velento_hero_title', [
        'label'   => __('عنوان اصلی', 'velento-shop'),
        'section' => 'velento_hero',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('velento_hero_subtitle', [
        'default'           => __('هر قطعه در ولنتو شاپ، ترکیبی از دقت مکانیکی و طراحی بی‌زمانه است؛ ساعتی که فراتر از اندازه‌گیری زمان، بخشی از هویت شماست.', 'velento-shop'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('velento_hero_subtitle', [
        'label'   => __('توضیح زیر عنوان', 'velento-shop'),
        'section' => 'velento_hero',
        'type'    => 'textarea',
    ]);

    $wp_customize->add_setting('velento_hero_cta_text', [
        'default'           => __('مشاهده‌ی کالکشن', 'velento-shop'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('velento_hero_cta_text', [
        'label'   => __('متن دکمه', 'velento-shop'),
        'section' => 'velento_hero',
        'type'    => 'text',
    ]);
}
add_action('customize_register', 'velento_customize_register');

/**
 * Output the accent-color override as an inline <style> tag only when
 * the store owner has actually changed it from the CSS default — keeps
 * variables.css as the single source of truth otherwise.
 */
function velento_customizer_inline_css()
{
    // mod key => [default value, CSS variable name]. Accent is the one
    // color that also applies to dark mode (see below) since it's the
    // brand identity color; the rest only override the light palette,
    // leaving the theme's own dark-mode design untouched.
    $palette = [
        'velento_accent_color'    => ['#C9A85B', '--accent'],
        'velento_bg_color'        => ['#FAFAFA', '--bg'],
        'velento_text_color'      => ['#0A0A0A', '--text'],
        'velento_secondary_color' => ['#666666', '--secondary'],
        'velento_border_color'    => ['#E5E5E5', '--border'],
    ];

    $root_vars = '';
    $dark_vars = '';

    foreach ($palette as $mod_key => [$default, $css_var]) {
        $value = get_theme_mod($mod_key, $default);
        if (empty($value) || strtolower($value) === strtolower($default)) {
            continue;
        }
        $value = sanitize_hex_color($value);
        if (!$value) {
            continue;
        }
        $root_vars .= esc_attr($css_var) . ':' . esc_attr($value) . ';';
        if ('--accent' === $css_var) {
            $dark_vars .= esc_attr($css_var) . ':' . esc_attr($value) . ';';
        }
    }

    if (!$root_vars) {
        return;
    }

    printf('<style id="velento-customizer-css">:root{%1$s}', $root_vars);
    if ($dark_vars) {
        printf('[data-theme="dark"]{%1$s}', $dark_vars);
    }
    echo '</style>';
}
add_action('wp_head', 'velento_customizer_inline_css', 20);

/**
 * Live preview (no full page reload) for the color picker in the
 * Customizer preview pane. Hero/announcement text fields intentionally
 * use the default 'refresh' transport since they change markup, not
 * just a CSS value.
 */
function velento_customizer_preview_js()
{
    wp_enqueue_script(
        'velento-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        ['customize-preview'],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('customize_preview_init', 'velento_customizer_preview_js');
