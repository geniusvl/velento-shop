/**
 * Hero Brand Slider — autoplay fade/slide between the hero's brand
 * photos (Rolex / Omega / AP for now, see template-parts/home/hero.php).
 * Pauses on hover/focus and when the tab isn't visible, and skips
 * autoplay entirely for prefers-reduced-motion.
 */
(function () {
    'use strict';

    var root = document.getElementById('hero-slider');
    if (!root) return;

    var slides = Array.prototype.slice.call(root.querySelectorAll('.hero-slide'));
    var dots = Array.prototype.slice.call(root.querySelectorAll('.hero-slider-dot'));
    var count = slides.length;
    if (count < 2) return;

    var activeIndex = 0;
    var intervalId = null;
    var AUTOPLAY_MS = 4500;
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function goTo(index) {
        activeIndex = ((index % count) + count) % count;

        slides.forEach(function (slide, i) {
            var isActive = i === activeIndex;
            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        });

        dots.forEach(function (dot, i) {
            var isActive = i === activeIndex;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }

    function next() {
        goTo(activeIndex + 1);
    }

    function start() {
        if (prefersReducedMotion || intervalId) return;
        intervalId = window.setInterval(next, AUTOPLAY_MS);
    }

    function stop() {
        if (!intervalId) return;
        window.clearInterval(intervalId);
        intervalId = null;
    }

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () {
            goTo(i);
            stop();
            start(); // restart the timer from a full interval after manual selection
        });
    });

    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            stop();
        } else {
            start();
        }
    });

    start();
})();
