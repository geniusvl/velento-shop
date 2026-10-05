<?php if (!defined('ABSPATH')) exit; ?>

<div class="site-branding">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
        <img class="logo-img logo-light" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/branding/logo-light.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="64" height="64">
        <img class="logo-img logo-dark" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/branding/logo-dark.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="64" height="64">
    </a>
</div>
