/**
 * Velento Shop — Customizer live-preview.
 * The five "پالت رنگی ولنتو" color controls (inc/customizer.php) use
 * the 'postMessage' transport, so each one is bound here to update the
 * matching CSS custom property instantly, without a full refresh.
 * Every other control in inc/customizer.php uses the default 'refresh'
 * transport instead.
 */
(function ($) {
    'use strict';

    var palette = {
        velento_accent_color: '--accent',
        velento_bg_color: '--bg',
        velento_text_color: '--text',
        velento_secondary_color: '--secondary',
        velento_border_color: '--border',
    };

    var current = {};

    function render() {
        var rootDecl = '';
        var darkDecl = '';

        Object.keys(current).forEach(function (cssVar) {
            var val = current[cssVar];
            if (!val) return;
            rootDecl += cssVar + ':' + val + ';';
            if (cssVar === '--accent') {
                darkDecl += cssVar + ':' + val + ';';
            }
        });

        var styleTag = document.getElementById('velento-customizer-preview-css');
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = 'velento-customizer-preview-css';
            document.head.appendChild(styleTag);
        }
        styleTag.textContent = ':root{' + rootDecl + '} [data-theme="dark"]{' + darkDecl + '}';
    }

    Object.keys(palette).forEach(function (settingId) {
        var cssVar = palette[settingId];
        wp.customize(settingId, function (value) {
            value.bind(function (newval) {
                current[cssVar] = newval || '';
                render();
            });
        });
    });
})(jQuery);
