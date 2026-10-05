<?php
/**
 * My Account sidebar — theme override of
 * woocommerce/myaccount/navigation.php.
 *
 * Shows the logged-in user's avatar + username at the top (as asked for
 * explicitly), then the usual WooCommerce account menu — still built from
 * wc_get_account_menu_items() so any endpoint a plugin adds (downloads,
 * subscriptions, etc.) shows up automatically. "خروج از حساب" (Log out)
 * is one of those endpoints (customer-logout) and is guaranteed to be in
 * the list; it just gets a distinct color here so it's easy to spot.
 */

if (!defined('ABSPATH')) {
    exit;
}

$velento_current_user = wp_get_current_user();
$velento_avatar_url    = get_avatar_url($velento_current_user->ID, ['size' => 96]);

do_action('woocommerce_before_account_navigation');
?>

<div class="velento-account-nav">

    <div class="velento-account-nav__profile">
        <img
            src="<?php echo esc_url($velento_avatar_url); ?>"
            alt=""
            class="velento-account-nav__avatar"
            width="56" height="56"
        >
        <div class="velento-account-nav__who">
            <span class="velento-account-nav__hello"><?php esc_html_e('خوش آمدید', 'velento-shop'); ?></span>
            <strong class="velento-account-nav__username"><?php echo esc_html($velento_current_user->display_name); ?></strong>
        </div>
    </div>

    <nav class="woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e('منوی حساب کاربری', 'velento-shop'); ?>">
        <ul class="velento-account-nav__list">
            <?php foreach (wc_get_account_menu_items() as $velento_endpoint => $velento_label) :
                $velento_is_logout = ('customer-logout' === $velento_endpoint);
                $velento_classes   = wc_get_account_menu_item_classes($velento_endpoint) . ' velento-account-nav__item';
                if ($velento_is_logout) {
                    $velento_classes .= ' velento-account-nav__item--logout';
                }
            ?>
                <li class="<?php echo esc_attr($velento_classes); ?>">
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($velento_endpoint)); ?>">
                        <?php echo velento_account_nav_icon_for_endpoint($velento_endpoint); ?>
                        <span><?php echo esc_html($velento_label); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

</div>

<?php do_action('woocommerce_after_account_navigation'); ?>
