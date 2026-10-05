<?php
/**
 * Velento Shop Theme — Bootstrap
 *
 * Loads every theme module from /inc in one place and in a clear order,
 * so adding a new module later never requires touching this loop again.
 */

if (!defined('ABSPATH')) {
    exit;
}

$velento_modules = [
    '/inc/setup.php',       // Theme support flags + nav menu registration
    '/inc/enqueue.php',     // Styles & scripts
    '/inc/widgets.php',     // Sidebar / footer widget areas
    '/inc/customizer.php',  // Customizer settings (colors, logo, etc.)
    '/inc/woocommerce.php', // WooCommerce hooks & template overrides
    '/inc/ajax.php',        // Front-end AJAX endpoints
    '/inc/search.php',      // Live product search + recent searches + recently viewed
    '/inc/filters.php',     // AJAX product discovery filters + query builder
    '/inc/api.php',         // REST API extensions
    '/inc/security.php',    // Hardening (disable XML-RPC, hide version, etc.)
    '/inc/helpers.php',     // Shared utility/helper functions
    '/inc/admin/otp-settings.php', // Admin settings page for Kavenegar API key
    '/inc/auth.php',        // SMS OTP login/registration (REST API endpoints)
    '/inc/elementor.php',   // Elementor page-builder compatibility + 20 Velento widgets
];

foreach ($velento_modules as $module) {
    $path = get_template_directory() . $module;

    if (is_readable($path)) {
        require_once $path;
    }
}
