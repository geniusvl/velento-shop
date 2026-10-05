<?php
/**
 * My Account page — theme override of WooCommerce's default
 * woocommerce/myaccount/my-account.php.
 *
 * This is the file WooCommerce renders wherever the [woocommerce_my_account]
 * shortcode/block sits on the "My Account" page (the same URL the header's
 * user icon links to — see template-parts/header/actions.php). It's
 * already wrapped in the theme's normal page chrome (header/footer) by
 * whichever page template renders that shortcode, so this file only needs
 * to output the account layout itself.
 *
 * Sidebar nav + content are kept as separate action hooks
 * (woocommerce_account_navigation / woocommerce_account_content) exactly
 * like WooCommerce core does, so plugins that hook into either one still
 * work — only the wrapping markup/classes are ours.
 * See woocommerce/myaccount/navigation.php and dashboard.php for the
 * actual pretty markup.
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_account_content');
?>

<div class="velento-account">
    <div class="container velento-account__container">
        <div class="velento-account__grid">
            <aside class="velento-account__sidebar">
                <?php do_action('woocommerce_account_navigation'); ?>
            </aside>

            <div class="velento-account__content">
                <?php do_action('woocommerce_account_content'); ?>
            </div>
        </div>
    </div>
</div>

<?php
do_action('woocommerce_after_account_content');
