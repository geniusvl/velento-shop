<?php
/**
 * Security Hardening
 *
 * Baseline, non-destructive hardening only — nothing here disables
 * functionality a store owner might rely on (REST API stays on since
 * WooCommerce/plugins/blocks need it; only XML-RPC, which almost no
 * modern site uses and is a common brute-force target, is disabled).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Lightweight IP-based rate limiter for public endpoints.
 *
 * Uses the server-provided REMOTE_ADDR only; forwarded headers are intentionally
 * ignored because they can be spoofed when a trusted proxy is not configured.
 * Returns true when the request is allowed and false when the limit is reached.
 */
function velento_rate_limit($bucket, $limit, $window = 600)
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'unknown';
    $fingerprint = hash_hmac('sha256', $bucket . '|' . $ip, wp_salt('auth'));
    $key = 'velento_rl_' . substr($fingerprint, 0, 32);
    $count = get_transient($key);

    if (false === $count) {
        set_transient($key, 1, absint($window));
        return true;
    }

    $count = absint($count);
    if ($count >= absint($limit)) {
        return false;
    }

    set_transient($key, $count + 1, absint($window));
    return true;
}

// Hide the exact WordPress version from page <head>, RSS feeds, and
// script/style query strings — makes it slightly harder to fingerprint
// the install for known-version exploits.
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

function velento_remove_version_from_assets($src)
{
    if (empty($src)) {
        return $src;
    }
    // Only obscure the version fingerprint on WordPress core/admin assets —
    // theme (and plugin) assets must keep their `?ver=` query string, since
    // that's what busts the browser cache whenever a theme file changes.
    // Stripping it from every asset (the previous behaviour here) meant
    // bumping the theme's Version: header in style.css never had any real
    // effect: the CSS/JS URLs never changed, so browsers kept serving
    // stale cached copies indefinitely regardless of how many times the
    // files were edited.
    if (strpos($src, '/wp-includes/') === false && strpos($src, '/wp-admin/') === false) {
        return $src;
    }
    return remove_query_arg('ver', $src);
}
add_filter('style_loader_src', 'velento_remove_version_from_assets');
add_filter('script_loader_src', 'velento_remove_version_from_assets');

// Disable XML-RPC — legacy protocol, frequent target for brute-force
// and DDoS pingback abuse, not needed by WooCommerce or block editor.
add_filter('xmlrpc_enabled', '__return_false');

// Remove the XML-RPC and Windows Live Writer links WordPress adds to
// <head> even when XML-RPC itself is filtered off above.
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// Don't reveal whether a login failure was a bad username or a bad
// password — generic message only, so credential-stuffing attempts
// can't use the response to enumerate valid usernames.
add_filter('login_errors', function () {
    return __('نام کاربری یا رمز عبور نادرست است.', 'velento-shop');
});

// Block direct access to readme.html / license.txt in the theme root
// (informational only — actual blocking still needs server-level
// rules, this just removes the easy in-app disclosure).
add_filter('robots_txt', function ($output) {
    $output .= "\nDisallow: /wp-content/themes/*/README.md\n";
    $output .= "Disallow: /wp-content/themes/*/CHANGELOG.md\n";
    return $output;
}, 10, 1);

// Disable the theme/plugin file editor in wp-admin — the single most
// common way a compromised admin account turns into a full site
// compromise (editing a theme file directly injects arbitrary PHP).
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}


/**
 * Validate front-end comment/review input before WordPress persists it.
 * WordPress still performs its own escaping/storage handling; these checks
 * add strict size/control-character limits so oversized or malformed payloads
 * cannot be used as a cheap application-layer DoS vector.
 */
function velento_validate_comment_input($commentdata)
{
    if (!is_array($commentdata)) {
        return $commentdata;
    }

    $content = isset($commentdata['comment_content']) ? (string) $commentdata['comment_content'] : '';
    if (strlen($content) > 10000) {
        wp_die(
            esc_html__('متن دیدگاه بیش از حد طولانی است.', 'velento-shop'),
            esc_html__('خطای ورودی', 'velento-shop'),
            ['response' => 413]
        );
    }

    if (strpos($content, "\0") !== false) {
        wp_die(
            esc_html__('محتوای واردشده معتبر نیست.', 'velento-shop'),
            esc_html__('خطای ورودی', 'velento-shop'),
            ['response' => 400]
        );
    }

    $content_without_tags = wp_strip_all_tags($content);
    if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $content_without_tags)) {
        wp_die(
            esc_html__('محتوای واردشده شامل کاراکتر نامعتبر است.', 'velento-shop'),
            esc_html__('خطای ورودی', 'velento-shop'),
            ['response' => 400]
        );
    }

    if (isset($commentdata['comment_author']) && strlen((string) $commentdata['comment_author']) > 120) {
        wp_die(
            esc_html__('نام واردشده بیش از حد طولانی است.', 'velento-shop'),
            esc_html__('خطای ورودی', 'velento-shop'),
            ['response' => 400]
        );
    }

    if (isset($commentdata['comment_author_email']) && strlen((string) $commentdata['comment_author_email']) > 254) {
        wp_die(
            esc_html__('ایمیل واردشده معتبر نیست.', 'velento-shop'),
            esc_html__('خطای ورودی', 'velento-shop'),
            ['response' => 400]
        );
    }

    return $commentdata;
}
add_filter('preprocess_comment', 'velento_validate_comment_input', 20);

/**
 * Add browser-side limits too. These are only UX protections; the
 * server-side validation above remains authoritative.
 */
function velento_comment_field_limits($field)
{
    if (strpos($field, '<textarea') !== false && strpos($field, 'maxlength=') === false) {
        $field = preg_replace('/<textarea\b/i', '<textarea maxlength="10000"', $field, 1);
    }
    return $field;
}
add_filter('comment_form_field_comment', 'velento_comment_field_limits', 20);

/**
 * WooCommerce order notes are user-controlled text. WooCommerce performs
 * its own nonce and sanitization, but the theme also enforces a hard upper
 * bound before checkout processing.
 */
function velento_validate_order_note()
{
    if (!isset($_POST['order_comments'])) {
        return;
    }

    $note = (string) wp_unslash($_POST['order_comments']);

    if (strlen($note) > 5000 || strpos($note, "\0") !== false) {
        wc_add_notice(
            __('یادداشت سفارش بیش از حد طولانی یا نامعتبر است.', 'velento-shop'),
            'error'
        );
    }
}
add_action('woocommerce_checkout_process', 'velento_validate_order_note', 20);

/**
 * Security response headers that are safe for a normal WordPress/WooCommerce
 * storefront. CSP is intentionally not forced here because arbitrary plugin
 * scripts and payment providers may legitimately need their own origins.
 */
function velento_security_headers()
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
add_action('send_headers', 'velento_security_headers');
