<?php
/**
 * Elementor Compatibility Layer
 *
 * Scope of what lives in this file:
 *   1. A "ولنتو شاپ" widget category + registration of the 22 widgets
 *      in /inc/elementor/widgets/.
 *   2. Forcing the theme's own CSS/JS bundle to load inside the
 *      Elementor editor iframe and on any page built with Elementor,
 *      so widgets always render with the real Velento design tokens
 *      instead of looking unstyled.
 *   3. Elementor Pro Theme Builder locations (header/footer), so a
 *      store owner with Pro can replace the built-in header/footer
 *      with an Elementor-built one without editing PHP.
 *   4. A one-way bridge that mirrors the theme's Customizer color
 *      palette into Elementor's own Global Colors, so "پالت رنگی
 *      ولنتو" shows up as pickable swatches in *every* Elementor color
 *      control — not just the 22 Velento widgets.
 *
 * Every hook below is registered on 'elementor/loaded', so none of
 * this runs (and none of it can fatal) on a site without the
 * Elementor plugin installed.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Everything below only makes sense once Elementor itself has loaded.
 */
function velento_elementor_init()
{
    require_once __DIR__ . '/widgets/base.php';

    add_action('elementor/elements/categories_registered', 'velento_elementor_register_category');
    add_action('elementor/widgets/register', 'velento_elementor_register_widgets');

    add_action('elementor/frontend/after_enqueue_styles', 'velento_elementor_enqueue_theme_assets');
    add_action('elementor/editor/after_enqueue_styles', 'velento_elementor_enqueue_theme_assets');
    add_action('elementor/preview/enqueue_styles', 'velento_elementor_enqueue_theme_assets');

    add_action('elementor/theme/register_locations', 'velento_elementor_register_locations');

    add_action('customize_save_after', 'velento_elementor_sync_colors_to_kit');
    add_action('admin_init', 'velento_elementor_maybe_sync_colors_once');
}
add_action('elementor/loaded', 'velento_elementor_init');

/**
 * Widget category shown at the top of the Elementor panel so the
 * store owner can find all 22 Velento widgets in one place instead of
 * hunting through Elementor's generic "Basic"/"General" groups.
 */
function velento_elementor_register_category($elements_manager)
{
    $elements_manager->add_category('velento', [
        'title' => __('ولنتو شاپ', 'velento-shop'),
        'icon'  => 'eicon-watches',
    ]);
}

/**
 * Registers every widget file in /inc/elementor/widgets/ (excluding
 * base.php, which is the shared abstract class, already required in
 * velento_elementor_init() above).
 */
function velento_elementor_register_widgets($widgets_manager)
{
    $widgets_dir = __DIR__ . '/widgets/';

    $widget_files = [
        'hero-slider'       => 'Velento_El_Hero_Slider',
        'featured-carousel' => 'Velento_El_Featured_Carousel',
        'product-grid'      => 'Velento_El_Product_Grid',
        'product-spotlight' => 'Velento_El_Product_Spotlight',
        'section-heading'   => 'Velento_El_Section_Heading',
        'cta-banner'        => 'Velento_El_Cta_Banner',
        'promo-banner'      => 'Velento_El_Promo_Banner',
        'usp-band'          => 'Velento_El_Usp_Band',
        'trust-badges'      => 'Velento_El_Trust_Badges',
        'newsletter'        => 'Velento_El_Newsletter',
        'announcement-bar'  => 'Velento_El_Announcement_Bar',
        'brand-logos'       => 'Velento_El_Brand_Logos',
        'image-text'        => 'Velento_El_Image_Text',
        'testimonials'      => 'Velento_El_Testimonials',
        'faq-accordion'     => 'Velento_El_Faq_Accordion',
        'blog-grid'         => 'Velento_El_Blog_Grid',
        'category-band'     => 'Velento_El_Category_Band',
        'stats-counter'     => 'Velento_El_Stats_Counter',
        'countdown'         => 'Velento_El_Countdown',
        'button'            => 'Velento_El_Button',
        'contact-form'      => 'Velento_El_Contact_Form',
        'store-map'         => 'Velento_El_Store_Map',
    ];

    foreach ($widget_files as $file => $class_name) {
        $path = $widgets_dir . $file . '.php';
        if (!is_readable($path)) {
            continue;
        }
        require_once $path;
        if (class_exists($class_name)) {
            $widgets_manager->register(new $class_name());
        }
    }
}

/**
 * Elementor's editor/preview iframe and the AJAX-rendered frontend of
 * an Elementor page don't go through the theme's normal page-type
 * checks in inc/enqueue.php (is_shop(), is_product(), etc. are all
 * false there), so a widget like Product Grid or Blog Grid would
 * render with zero styling. Instead of teaching enqueue.php about
 * Elementor, just force-load the full theme CSS bundle (it's a small,
 * cached set of files) plus the widgets' own stylesheet/script
 * whenever Elementor is involved in rendering the current request.
 */
function velento_elementor_enqueue_theme_assets()
{
    if (function_exists('velento_enqueue_assets')) {
        velento_enqueue_assets();
    }

    $theme_version = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'velento-elementor-widgets',
        get_template_directory_uri() . '/assets/css/elementor-widgets.css',
        ['velento-responsive'],
        $theme_version
    );

    wp_enqueue_script(
        'velento-elementor-widgets',
        get_template_directory_uri() . '/assets/js/elementor-widgets.js',
        [],
        $theme_version,
        true
    );
}

/**
 * Registers the header/footer locations Elementor Pro's Theme Builder
 * hooks into. Harmless without Elementor Pro — Pro-only APIs are never
 * called unless Pro itself is what's driving the request, since only
 * Pro ever calls elementor_theme_do_location() (see header.php /
 * footer.php, which check function_exists() before calling it).
 */
function velento_elementor_register_locations($elementor_theme_manager)
{
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
    $elementor_theme_manager->register_location('single');
    $elementor_theme_manager->register_location('archive');
}

/**
 * Pushes the theme's Customizer color palette (see the "پالت رنگی
 * ولنتو" section added in inc/customizer.php) into Elementor's own
 * Global Colors on the active Kit, under an easily-recognizable
 * "ولنتو —" prefix. This is a one-way sync (Customizer -> Elementor);
 * it runs after every Customizer save and is safe to run repeatedly —
 * it updates the same four entries in place rather than appending
 * duplicates.
 */
function velento_elementor_sync_colors_to_kit()
{
    if (!did_action('elementor/loaded')) {
        return;
    }
    if (!class_exists('\Elementor\Plugin') || empty(\Elementor\Plugin::$instance->kits_manager)) {
        return;
    }

    $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
    if (!$kit) {
        return;
    }

    $palette = [
        'velento_accent' => __('ولنتو — تاکیدی (طلایی)', 'velento-shop'),
        'velento_bg'     => __('ولنتو — پس‌زمینه', 'velento-shop'),
        'velento_text'   => __('ولنتو — متن', 'velento-shop'),
        'velento_border' => __('ولنتو — خط جداکننده', 'velento-shop'),
    ];

    $defaults = [
        'velento_accent' => '#C9A85B',
        'velento_bg'     => '#FAFAFA',
        'velento_text'   => '#0A0A0A',
        'velento_border' => '#E5E5E5',
    ];

    $existing = $kit->get_settings('custom_colors');
    if (!is_array($existing)) {
        $existing = [];
    }

    // Drop any earlier Velento-synced entries, then re-add fresh ones —
    // simpler and safer than trying to patch entries by _id in place.
    $existing = array_values(array_filter($existing, function ($item) use ($palette) {
        return empty($item['_id']) || !array_key_exists($item['_id'], $palette);
    }));

    foreach ($palette as $mod_key => $label) {
        $value = get_theme_mod($mod_key, $defaults[$mod_key]);
        if (empty($value)) {
            $value = $defaults[$mod_key];
        }
        $existing[] = [
            '_id'   => $mod_key,
            'title' => $label,
            'color' => $value,
        ];
    }

    $kit->update_settings(['custom_colors' => $existing]);
}

/**
 * The Kit-sync above only fires on customize_save_after — meaning a
 * fresh install where the store owner never opened the Customizer
 * would never see the Velento swatches in Elementor at all. This runs
 * the same sync exactly once (flagged via an option) the first time
 * wp-admin loads with Elementor active, so the palette is available
 * immediately with its defaults.
 */
function velento_elementor_maybe_sync_colors_once()
{
    if (get_option('velento_elementor_colors_synced')) {
        return;
    }
    velento_elementor_sync_colors_to_kit();
    update_option('velento_elementor_colors_synced', 1);
}
