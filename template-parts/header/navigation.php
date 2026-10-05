<?php if (!defined('ABSPATH')) exit; ?>

<nav class="main-navigation" aria-label="<?php echo esc_attr__('پیمایش اصلی', 'velento-shop'); ?>">
    <?php
    wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'primary-menu',
        'fallback_cb'    => 'velento_default_menu',
    ]);
    ?>
</nav>
