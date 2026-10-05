<?php
/**
 * Template Part: Announcement Bar
 *
 * Optional thin bar above the header — free-shipping threshold, a
 * promo, a guarantee message. Off by default; the store owner turns
 * it on and edits the text/link from Appearance > Customize >
 * "نوار اعلان بالای سایت" (inc/customizer.php), no code changes needed.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!get_theme_mod('velento_announcement_enabled', false)) {
    return;
}

$velento_announcement_text = get_theme_mod('velento_announcement_text', '');
if (empty($velento_announcement_text)) {
    return;
}

$velento_announcement_link = get_theme_mod('velento_announcement_link', '');
?>

<div class="announcement-bar" role="note">
    <div class="container announcement-bar-inner">
        <?php if (!empty($velento_announcement_link)) : ?>
            <a href="<?php echo esc_url($velento_announcement_link); ?>" class="announcement-bar-text">
                <?php echo esc_html($velento_announcement_text); ?>
            </a>
        <?php else : ?>
            <span class="announcement-bar-text"><?php echo esc_html($velento_announcement_text); ?></span>
        <?php endif; ?>
    </div>
</div>
