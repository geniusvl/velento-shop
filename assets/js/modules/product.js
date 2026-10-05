/**
 * Velento Shop — Single product tabs.
 * Keeps description, specifications and reviews in one focused panel.
 */
(function () {
    'use strict';

    var root = document.querySelector('.v-single-details');
    if (!root) return;

    var tabs = Array.prototype.slice.call(root.querySelectorAll('[data-product-tab]'));
    var panels = Array.prototype.slice.call(root.querySelectorAll('[data-product-panel]'));
    if (!tabs.length || !panels.length) return;

    function activate(name, updateHash) {
        var targetTab = tabs.find(function (tab) {
            return tab.getAttribute('data-product-tab') === name;
        });

        if (!targetTab) {
            targetTab = tabs[0];
            name = targetTab.getAttribute('data-product-tab');
        }

        tabs.forEach(function (tab) {
            var active = tab === targetTab;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            tab.tabIndex = active ? 0 : -1;
        });

        panels.forEach(function (panel) {
            var active = panel.getAttribute('data-product-panel') === name;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });

        if (updateHash && window.history && window.history.replaceState) {
            var url = new URL(window.location.href);
            url.hash = name;
            window.history.replaceState(null, '', url.toString());
        }
    }

    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
            activate(tab.getAttribute('data-product-tab'), true);
        });

        tab.addEventListener('keydown', function (event) {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight' && event.key !== 'Home' && event.key !== 'End') return;

            event.preventDefault();
            var nextIndex = index;
            if (event.key === 'ArrowLeft') nextIndex = (index + 1) % tabs.length;
            if (event.key === 'ArrowRight') nextIndex = (index - 1 + tabs.length) % tabs.length;
            if (event.key === 'Home') nextIndex = 0;
            if (event.key === 'End') nextIndex = tabs.length - 1;

            tabs[nextIndex].focus();
            activate(tabs[nextIndex].getAttribute('data-product-tab'), true);
        });
    });

    var initial = window.location.hash ? window.location.hash.substring(1) : '';
    activate(initial || tabs[0].getAttribute('data-product-tab'), false);

    window.addEventListener('hashchange', function () {
        var name = window.location.hash ? window.location.hash.substring(1) : '';
        if (name) activate(name, false);
    });
})();
