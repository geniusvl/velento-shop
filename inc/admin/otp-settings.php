<?php
/**
 * Admin Settings: Kavenegar OTP
 *
 * Adds a simple settings page under Settings > ورود پیامکی so the
 * store owner can paste their Kavenegar API Key + template name
 * without touching code. Read anywhere via velento_get_kavenegar_key().
 */

if (!defined('ABSPATH')) {
    exit;
}

function velento_otp_settings_menu()
{
    add_options_page(
        __('ورود پیامکی', 'velento-shop'),
        __('ورود پیامکی', 'velento-shop'),
        'manage_options',
        'velento-otp-settings',
        'velento_otp_settings_page'
    );
}
add_action('admin_menu', 'velento_otp_settings_menu');

function velento_otp_settings_register()
{
    register_setting('velento_otp_settings_group', 'velento_kavenegar_api_key', [
        'sanitize_callback' => 'velento_sanitize_kavenegar_key',
    ]);
    register_setting('velento_otp_settings_group', 'velento_kavenegar_template', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'verify',
    ]);
}
add_action('admin_init', 'velento_otp_settings_register');

/**
 * The saved API key is never printed back into the admin form, so an empty
 * submission means "keep the existing key" rather than "erase it".
 */
function velento_sanitize_kavenegar_key($value)
{
    $value = sanitize_text_field((string) $value);
    if ($value === '') {
        return get_option('velento_kavenegar_api_key', '');
    }
    return $value;
}

function velento_otp_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('تنظیمات ورود پیامکی (کاوه‌نگار)', 'velento-shop'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('velento_otp_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="velento_kavenegar_api_key"><?php esc_html_e('API Key', 'velento-shop'); ?></label>
                    </th>
                    <td>
                        <?php if (defined('VELENTO_KAVENEGAR_API_KEY') && VELENTO_KAVENEGAR_API_KEY !== '') : ?>
                            <p><strong><?php esc_html_e('کلید API از فایل wp-config.php خوانده می‌شود.', 'velento-shop'); ?></strong></p>
                        <?php else : ?>
                        <input
                            type="password"
                            id="velento_kavenegar_api_key"
                            name="velento_kavenegar_api_key"
                            value=""
                            placeholder="<?php echo get_option('velento_kavenegar_api_key', '') !== '' ? esc_attr__('ذخیره شده — برای تغییر، کلید جدید را وارد کنید', 'velento-shop') : ''; ?>"
                            class="regular-text"
                            dir="ltr"
                            autocomplete="new-password"
                        >
                        <?php endif; ?>
                        <p class="description"><?php esc_html_e('کلید API را از پنل کاوه‌نگار (kavenegar.com) کپی کنید.', 'velento-shop'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="velento_kavenegar_template"><?php esc_html_e('نام قالب پیامک (Template)', 'velento-shop'); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="velento_kavenegar_template"
                            name="velento_kavenegar_template"
                            value="<?php echo esc_attr(get_option('velento_kavenegar_template', 'verify')); ?>"
                            class="regular-text"
                            dir="ltr"
                            autocomplete="off"
                        >
                        <p class="description"><?php esc_html_e('نام قالب تاییدشده برای سرویس Verify Lookup در پنل کاوه‌نگار.', 'velento-shop'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Small accessor so the rest of the theme never touches get_option()
 * directly for these two values.
 */
function velento_get_kavenegar_key()
{
    // Preferred: define('VELENTO_KAVENEGAR_API_KEY', '...'); in wp-config.php
    // so the secret never lives in the database or in version control.
    if (defined('VELENTO_KAVENEGAR_API_KEY') && VELENTO_KAVENEGAR_API_KEY !== '') {
        return VELENTO_KAVENEGAR_API_KEY;
    }
    return get_option('velento_kavenegar_api_key', '');
}

function velento_get_kavenegar_template()
{
    return get_option('velento_kavenegar_template', 'verify');
}
