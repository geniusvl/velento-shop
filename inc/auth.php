<?php
/**
 * SMS OTP Login / Registration — REST API
 *
 * Architecture:
 *   Browser -> WP REST API (/wp-json/velento/v1/auth/*) -> PHP -> Kavenegar
 *
 * The Kavenegar API key never reaches the browser; it's read server-side
 * only (inc/admin/otp-settings.php). Authentication itself is handled
 * entirely by WordPress' own user tables (wp_users/wp_usermeta) — no
 * custom DB table needed. OTP codes live in transients, which
 * self-expire, so nothing has to be cleaned up manually.
 *
 * Endpoints:
 *   POST /wp-json/velento/v1/auth/send-code   { mobile }
 *   POST /wp-json/velento/v1/auth/verify-code { mobile, code }
 */

if (!defined('ABSPATH')) {
    exit;
}

const VELENTO_OTP_TTL      = 120; // seconds a code stays valid
const VELENTO_OTP_LOCK_TTL = 60;  // seconds before the same number can request again
const VELENTO_OTP_MAX_TRY  = 5;   // wrong-code attempts allowed before the code is invalidated

/**
 * Register the two REST routes.
 */
function velento_register_auth_routes()
{
    register_rest_route('velento/v1', '/auth/send-code', [
        'methods'             => 'POST',
        'callback'            => 'velento_rest_send_code',
        'permission_callback' => '__return_true',
        'args'                => [
            'mobile' => [
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);

    register_rest_route('velento/v1', '/auth/verify-code', [
        'methods'             => 'POST',
        'callback'            => 'velento_rest_verify_code',
        'permission_callback' => '__return_true',
        'args'                => [
            'mobile' => [
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'code' => [
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);
}
add_action('rest_api_init', 'velento_register_auth_routes');

/**
 * Small helper: is this a syntactically valid Iranian mobile number?
 */
function velento_is_valid_mobile($mobile)
{
    return (bool) preg_match('/^09\d{9}$/', $mobile);
}

/**
 * Sends the OTP through Kavenegar's Verify Lookup endpoint.
 * Returns true/false; never throws — caller decides what to tell the user.
 */
function velento_send_kavenegar_otp($mobile, $code)
{
    $api_key  = velento_get_kavenegar_key();
    $template = velento_get_kavenegar_template();

    if (empty($api_key)) {
        return false;
    }

    $url = sprintf(
        'https://api.kavenegar.com/v1/%s/verify/lookup.json',
        rawurlencode($api_key)
    );

    $response = wp_remote_get(add_query_arg([
        'receptor' => $mobile,
        'token'    => $code,
        'template' => $template,
    ], $url), [
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $status = wp_remote_retrieve_response_code($response);
    return $status >= 200 && $status < 300;
}

/**
 * POST /wp-json/velento/v1/auth/send-code
 */
function velento_rest_send_code(WP_REST_Request $request)
{
    $mobile = $request->get_param('mobile');

    if (!velento_rate_limit('otp_send', 5, 600)) {
        return new WP_Error(
            'velento_rate_limited',
            __('تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop'),
            ['status' => 429]
        );
    }

    if (!velento_is_valid_mobile($mobile)) {
        return new WP_Error(
            'velento_invalid_mobile',
            __('شماره موبایل معتبر نیست.', 'velento-shop'),
            ['status' => 400]
        );
    }

    $mobile_bucket = 'otp-mobile-send-' . substr(hash_hmac('sha256', $mobile, wp_salt('auth')), 0, 24);
    if (!velento_rate_limit($mobile_bucket, 3, 600)) {
        return new WP_Error(
            'velento_mobile_rate_limited',
            __('برای این شماره درخواست‌های زیادی ثبت شده است. کمی بعد دوباره تلاش کنید.', 'velento-shop'),
            ['status' => 429]
        );
    }

    if (get_transient('velento_otp_lock_' . $mobile)) {
        return new WP_Error(
            'velento_otp_locked',
            __('لطفاً کمی صبر کنید و دوباره تلاش کنید.', 'velento-shop'),
            ['status' => 429]
        );
    }

    $code = (string) random_int(10000, 99999);

    $sent = velento_send_kavenegar_otp($mobile, $code);
    if (!$sent) {
        return new WP_Error(
            'velento_sms_failed',
            __('ارسال پیامک با خطا مواجه شد. لطفاً بعداً تلاش کنید.', 'velento-shop'),
            ['status' => 502]
        );
    }

    set_transient('velento_otp_' . $mobile, $code, VELENTO_OTP_TTL);
    set_transient('velento_otp_tries_' . $mobile, 0, VELENTO_OTP_TTL);
    set_transient('velento_otp_lock_' . $mobile, 1, VELENTO_OTP_LOCK_TTL);

    return rest_ensure_response([
        'success' => true,
        'message' => __('کد تایید برای شما پیامک شد.', 'velento-shop'),
        'ttl'     => VELENTO_OTP_TTL,
    ]);
}

/**
 * POST /wp-json/velento/v1/auth/verify-code
 * Verifies the code, then logs the user in — creating the WordPress
 * account on first sign-in if it doesn't exist yet.
 */
function velento_rest_verify_code(WP_REST_Request $request)
{
    $mobile = $request->get_param('mobile');
    $code   = $request->get_param('code');

    if (!velento_rate_limit('otp_verify', 20, 600)) {
        return new WP_Error(
            'velento_rate_limited',
            __('تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop'),
            ['status' => 429]
        );
    }

    if (!velento_is_valid_mobile($mobile) || !preg_match('/^\d{5}$/', (string) $code)) {
        return new WP_Error(
            'velento_invalid_input',
            __('اطلاعات وارد شده معتبر نیست.', 'velento-shop'),
            ['status' => 400]
        );
    }

    $verify_bucket = 'otp-mobile-verify-' . substr(hash_hmac('sha256', $mobile, wp_salt('auth')), 0, 24);
    if (!velento_rate_limit($verify_bucket, 10, 600)) {
        return new WP_Error(
            'velento_mobile_verify_rate_limited',
            __('تعداد تلاش برای این شماره بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.', 'velento-shop'),
            ['status' => 429]
        );
    }

    $stored = get_transient('velento_otp_' . $mobile);
    if (false === $stored) {
        return new WP_Error(
            'velento_otp_expired',
            __('کد منقضی شده است. دوباره درخواست دهید.', 'velento-shop'),
            ['status' => 410]
        );
    }

    // Limit brute-force guesses against a single issued code.
    $tries = (int) get_transient('velento_otp_tries_' . $mobile);
    if ($tries >= VELENTO_OTP_MAX_TRY) {
        delete_transient('velento_otp_' . $mobile);
        return new WP_Error(
            'velento_otp_too_many_tries',
            __('تعداد تلاش‌ها بیش از حد مجاز است. دوباره درخواست دهید.', 'velento-shop'),
            ['status' => 429]
        );
    }

    if (!hash_equals((string) $stored, $code)) {
        set_transient('velento_otp_tries_' . $mobile, $tries + 1, VELENTO_OTP_TTL);
        return new WP_Error(
            'velento_otp_mismatch',
            __('کد وارد شده اشتباه است.', 'velento-shop'),
            ['status' => 401]
        );
    }

    delete_transient('velento_otp_' . $mobile);
    delete_transient('velento_otp_tries_' . $mobile);

    $existing = get_users([
        'meta_key'   => 'velento_phone',
        'meta_value' => $mobile,
        'number'     => 1,
        'fields'     => 'all',
    ]);

    if (!empty($existing)) {
        $user = $existing[0];
    } else {
        $user_id = wp_insert_user([
            'user_login' => $mobile,
            'user_pass'  => wp_generate_password(24, true, true),
            'role'       => 'customer',
        ]);

        if (is_wp_error($user_id)) {
            return new WP_Error(
                'velento_user_create_failed',
                __('ساخت حساب کاربری با خطا مواجه شد.', 'velento-shop'),
                ['status' => 500]
            );
        }

        update_user_meta($user_id, 'velento_phone', $mobile);
        $user = get_user_by('id', $user_id);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);
    do_action('wp_login', $user->user_login, $user);

    return rest_ensure_response([
        'success'  => true,
        'message'  => __('ورود با موفقیت انجام شد.', 'velento-shop'),
        'redirect' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/'),
    ]);
}
