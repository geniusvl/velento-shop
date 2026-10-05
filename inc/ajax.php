<?php
/**
 * Front-end AJAX Endpoints
 * TODO: register wp_ajax_* / wp_ajax_nopriv_* handlers here.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Newsletter signup (template-parts/home/newsletter.php).
 *
 * Stores addresses in a single option as a first pass so the form is
 * fully working tonight. TODO: once volume grows, move this to a
 * dedicated CPT/table or forward straight to an ESP (Mailchimp, etc.)
 * instead of a single serialized option.
 */
function velento_handle_newsletter_subscribe()
{
    check_ajax_referer('velento_subscribe', 'velento_newsletter_nonce');

    if (!velento_rate_limit('newsletter', 10, 600)) {
        wp_send_json_error([
            'message' => __('تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop'),
        ], 429);
    }

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

    if (strlen($email) > 254) {
        wp_send_json_error(['message' => __('ایمیل بیش از حد طولانی است.', 'velento-shop')], 400);
    }

    if (empty($email) || !is_email($email)) {
        wp_send_json_error([
            'message' => __('لطفاً یک ایمیل معتبر وارد کنید.', 'velento-shop'),
        ], 400);
    }

    $subscribers = get_option('velento_newsletter_subscribers', []);
    if (!is_array($subscribers)) {
        $subscribers = [];
    }

    if (in_array($email, $subscribers, true)) {
        wp_send_json_success([
            'message' => __('این ایمیل قبلاً ثبت شده است.', 'velento-shop'),
        ]);
    }

    $subscribers[] = $email;
    update_option('velento_newsletter_subscribers', $subscribers, false);

    wp_send_json_success([
        'message' => __('با تشکر! ایمیل شما با موفقیت ثبت شد.', 'velento-shop'),
    ]);
}
add_action('wp_ajax_velento_subscribe', 'velento_handle_newsletter_subscribe');
add_action('wp_ajax_nopriv_velento_subscribe', 'velento_handle_newsletter_subscribe');

/**
 * Cart drawer endpoints. They intentionally use WooCommerce's cart object,
 * so product prices, taxes and totals remain controlled by WooCommerce.
 */
function velento_cart_ajax_guard()
{
    check_ajax_referer('velento_cart', 'nonce');

    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(['message' => __('سبد خرید در دسترس نیست.', 'velento-shop')], 400);
    }
}

function velento_cart_update_item()
{
    velento_cart_ajax_guard();

    $cart_key = isset($_POST['cart_key']) ? wc_clean(wp_unslash($_POST['cart_key'])) : '';
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 0;

    $cart_items = WC()->cart->get_cart();
    if (!$cart_key || !isset($cart_items[$cart_key])) {
        wp_send_json_error(['message' => __('آیتم سبد پیدا نشد.', 'velento-shop')], 404);
    }

    if ($quantity < 1) {
        WC()->cart->remove_cart_item($cart_key);
    } else {
        WC()->cart->set_quantity($cart_key, min($quantity, 99), true);
    }

    WC()->cart->calculate_totals();

    wp_send_json_success([
        'content' => velento_get_cart_drawer_content(),
        'count'   => WC()->cart->get_cart_contents_count(),
    ]);
}
add_action('wp_ajax_velento_cart_update', 'velento_cart_update_item');
add_action('wp_ajax_nopriv_velento_cart_update', 'velento_cart_update_item');

function velento_cart_refresh()
{
    velento_cart_ajax_guard();

    wp_send_json_success([
        'content' => velento_get_cart_drawer_content(),
        'count'   => WC()->cart->get_cart_contents_count(),
    ]);
}
add_action('wp_ajax_velento_cart_refresh', 'velento_cart_refresh');
add_action('wp_ajax_nopriv_velento_cart_refresh', 'velento_cart_refresh');

/**
 * Login / register drawer (template-parts/header/login-drawer.php).
 *
 * Deliberately plain WordPress core — wp_signon() and wp_insert_user()
 * — with no WooCommerce dependency, so this keeps working even on a
 * request where WooCommerce isn't active/loaded yet.
 */
function velento_handle_login()
{
    check_ajax_referer('velento_login', 'nonce');

    if (!velento_rate_limit('login', 10, 600)) {
        wp_send_json_error([
            'message' => __('تعداد تلاش‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop'),
        ], 429);
    }

    if (is_user_logged_in()) {
        wp_send_json_error(['message' => __('شما در حال حاضر وارد شده‌اید.', 'velento-shop')], 400);
    }

    $username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
    $password = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';

    if (strlen($username) > 254 || strlen($password) > 256) {
        wp_send_json_error(['message' => __('اطلاعات ورود معتبر نیست.', 'velento-shop')], 400);
    }

    $login_fingerprint = hash_hmac('sha256', 'login-user|' . strtolower($username), wp_salt('auth'));
    if (!velento_rate_limit('login-user-' . substr($login_fingerprint, 0, 24), 5, 600)) {
        wp_send_json_error(['message' => __('تعداد تلاش‌ها برای این حساب بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.', 'velento-shop')], 429);
    }
    $remember = !empty($_POST['remember']);

    if ($username === '' || $password === '') {
        wp_send_json_error([
            'message' => __('لطفاً ایمیل یا نام کاربری و رمز عبور را وارد کنید.', 'velento-shop'),
        ], 400);
    }

    $result = wp_signon([
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember,
    ], is_ssl());

    if (is_wp_error($result)) {
        wp_send_json_error([
            'message' => __('ایمیل/نام کاربری یا رمز عبور اشتباه است.', 'velento-shop'),
        ], 401);
    }

    wp_send_json_success([
        'message' => __('ورود با موفقیت انجام شد.', 'velento-shop'),
    ]);
}
add_action('wp_ajax_nopriv_velento_login', 'velento_handle_login');
add_action('wp_ajax_velento_login', 'velento_handle_login');

/**
 * Registration: plain wp_insert_user(), no WooCommerce customer
 * helpers. Username is auto-generated from the email's local part
 * (kept unique) since the drawer only asks for an email + password.
 */
function velento_handle_register()
{
    check_ajax_referer('velento_login', 'nonce');

    if (!velento_rate_limit('register', 5, 600)) {
        wp_send_json_error([
            'message' => __('تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop'),
        ], 429);
    }

    if (is_user_logged_in()) {
        wp_send_json_error(['message' => __('شما در حال حاضر وارد شده‌اید.', 'velento-shop')], 400);
    }

    $email     = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $password  = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';

    if (strlen($email) > 254 || strlen($password) > 256) {
        wp_send_json_error(['message' => __('اطلاعات ثبت‌نام معتبر نیست.', 'velento-shop')], 400);
    }
    $password2 = isset($_POST['password2']) ? (string) wp_unslash($_POST['password2']) : '';

    if (!is_email($email)) {
        wp_send_json_error([
            'message' => __('ایمیل را به‌صورت صحیح وارد کنید؛ مثل name@gmail.com', 'velento-shop'),
        ], 400);
    }

    if (strlen($password) < 8 || strlen($password) > 20) {
        wp_send_json_error([
            'message' => __('رمز عبور باید بین ۸ تا ۲۰ کاراکتر باشد.', 'velento-shop'),
        ], 400);
    }

    if ($password !== $password2) {
        wp_send_json_error([
            'message' => __('رمز عبور و تکرار آن یکسان نیستند.', 'velento-shop'),
        ], 400);
    }

    if (email_exists($email)) {
        wp_send_json_error([
            'message' => __('حسابی با این ایمیل قبلاً ثبت شده است.', 'velento-shop'),
        ], 400);
    }

    // Turn "name@example.com" into a unique username: strip anything
    // that's not a safe username character, then append a numeric
    // suffix if that base is already taken.
    $base_username = sanitize_user(preg_replace('/[^a-zA-Z0-9_.\-]/', '', strstr($email, '@', true)), true);
    if ($base_username === '') {
        $base_username = 'user';
    }
    $username = $base_username;
    $suffix   = 1;
    while (username_exists($username)) {
        $username = $base_username . $suffix;
        $suffix++;
    }

    $user_id = wp_insert_user([
        'user_login' => $username,
        'user_email' => $email,
        'user_pass'  => $password,
    ]);

    if (is_wp_error($user_id)) {
        wp_send_json_error([
            'message' => __('ثبت‌نام انجام نشد. لطفاً دوباره تلاش کنید.', 'velento-shop'),
        ], 400);
    }

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, false, is_ssl());

    wp_send_json_success([
        'message' => __('ثبت‌نام با موفقیت انجام شد.', 'velento-shop'),
    ]);
}
add_action('wp_ajax_nopriv_velento_register', 'velento_handle_register');
add_action('wp_ajax_velento_register', 'velento_handle_register');

/**
 * Contact form (inc/elementor/widgets/contact-form.php — the Elementor
 * "فرم تماس ساده" widget). Plain wp_mail() to the site's admin email,
 * following the exact same nonce + rate-limit + sanitize pattern as
 * the newsletter/login handlers above rather than introducing a new
 * validation style.
 */
function velento_handle_contact_form()
{
    check_ajax_referer('velento_contact_form', 'nonce');

    if (!velento_rate_limit('contact-form', 8, 600)) {
        wp_send_json_error([
            'message' => __('تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.', 'velento-shop'),
        ], 429);
    }

    $name    = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone   = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    if (strlen($name) > 100 || strlen($email) > 254 || strlen($phone) > 30 || strlen($message) > 3000) {
        wp_send_json_error(['message' => __('یکی از فیلدها بیش از حد طولانی است.', 'velento-shop')], 400);
    }

    if ($name === '' || $message === '' || !is_email($email)) {
        wp_send_json_error([
            'message' => __('لطفاً نام، ایمیل معتبر و پیام را وارد کنید.', 'velento-shop'),
        ], 400);
    }

    $to      = get_option('admin_email');
    $subject = sprintf(/* translators: %s: site name */ __('پیام جدید تماس با ما از %s', 'velento-shop'), wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
    $body    = sprintf(
        "نام: %s\nایمیل: %s\nتلفن: %s\n\nپیام:\n%s",
        $name,
        $email,
        $phone ?: '-',
        $message
    );
    $headers = ['Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>'];

    $sent = wp_mail($to, $subject, $body, $headers);

    if (!$sent) {
        wp_send_json_error([
            'message' => __('ارسال پیام ناموفق بود. لطفاً بعداً دوباره تلاش کنید.', 'velento-shop'),
        ], 500);
    }

    wp_send_json_success([
        'message' => __('پیام شما با موفقیت ارسال شد. به‌زودی با شما تماس می‌گیریم.', 'velento-shop'),
    ]);
}
add_action('wp_ajax_velento_contact_form', 'velento_handle_contact_form');
add_action('wp_ajax_nopriv_velento_contact_form', 'velento_handle_contact_form');
