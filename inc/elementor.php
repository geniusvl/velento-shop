<?php
/**
 * Elementor Integration — Bootstrap
 *
 * Everything Elementor-related lives under /inc/elementor/ so it stays
 * out of the way of the core theme. This file is always required from
 * functions.php (cheap — just a few file_exists-guarded requires and
 * action hooks), but does nothing at all unless the Elementor plugin
 * is actually active on the site. That means the theme works exactly
 * as before with Elementor absent, and gains full page-builder support
 * (custom widget category with 22 Velento widgets, theme-location
 * support for Elementor Pro's Theme Builder, and a live color-palette
 * bridge to Elementor's own Global Colors) the moment a store owner
 * installs it.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/elementor/compat.php';
