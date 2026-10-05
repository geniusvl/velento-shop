<?php if (!defined('ABSPATH')) exit; ?>

<?php
// Same Elementor Pro Theme Builder support as header.php — see the
// comment there for why this check is always safe to leave in place.
if (!(function_exists('elementor_theme_do_location') && elementor_theme_do_location('footer'))) :
    get_template_part('template-parts/footer/footer-main');
endif;
?>

<?php wp_footer(); ?>
</body>
</html>
