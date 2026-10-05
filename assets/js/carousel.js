/**
 * Featured Watches Carousel.
 *
 * Generic "center + sides" positioning: every item gets a --pos value
 * (its distance from the active index, shortest-path around the loop).
 * CSS only reads --x/--scale/--opacity/--z, which we compute here from
 * --pos — so this works whether there are 3 items or 30, no markup or
 * CSS changes needed either way.
 */
(function () {
    'use strict';

    var stage = document.getElementById('watch-carousel');
    var track = document.getElementById('carousel-track');
    var dotsWrap = document.getElementById('carousel-dots');
    if (!stage || !track) return;

    var items = Array.prototype.slice.call(track.querySelectorAll('.carousel-item'));
    var count = items.length;
    if (count === 0) return;

    var prevBtn = stage.querySelector('.carousel-prev');
    var nextBtn = stage.querySelector('.carousel-next');

    var activeIndex = 0;
    var spacing = 260; // px between center and each side item; recalculated on resize

    function measureSpacing() {
        var sample = items[0];
        if (!sample) return;
        // side items sit ~1.05x their own width away from center, so
        // they read as clearly separate cards without overflowing the
        // row now that two tiers are visible on each side
        spacing = sample.offsetWidth * 1.05;
    }

    function render() {
        items.forEach(function (item, i) {
            var diff = i - activeIndex;
            // shortest path around the loop (e.g. with 3 items, going
            // "next" from the last one should wrap to +1, not -2)
            if (diff > count / 2) diff -= count;
            if (diff < -count / 2) diff += count;

            var absDiff = Math.abs(diff);
            var isActive = diff === 0;

            item.classList.toggle('is-active', isActive);
            item.setAttribute('aria-hidden', isActive ? 'false' : 'true');

            if (absDiff > 2) {
                // more than two steps away — push out of view entirely
                item.style.setProperty('--x', (diff * spacing * 2.6) + 'px');
                item.style.setProperty('--scale', '0.45');
                item.style.setProperty('--opacity', '0');
                item.style.setProperty('--z', '0');
                item.style.pointerEvents = 'none';
            } else {
                // Tier 0 = active (center), tier 1 = immediate neighbors,
                // tier 2 = outer neighbors — each a bit smaller/fainter,
                // filling the row rather than just center + one aside.
                var sign = diff === 0 ? 0 : (diff > 0 ? 1 : -1);
                var offsetSteps = absDiff === 0 ? 0 : (absDiff === 1 ? 1 : 1.7);
                var scale = isActive ? 1 : (absDiff === 1 ? 0.8 : 0.62);
                var opacity = isActive ? 1 : (absDiff === 1 ? 0.6 : 0.32);

                item.style.setProperty('--x', (sign * offsetSteps * spacing) + 'px');
                item.style.setProperty('--scale', String(scale));
                item.style.setProperty('--opacity', String(opacity));
                item.style.setProperty('--z', String(10 - absDiff));
                item.style.pointerEvents = 'auto';
            }
        });

        if (dotsWrap) {
            Array.prototype.forEach.call(dotsWrap.children, function (dot, i) {
                dot.classList.toggle('is-active', i === activeIndex);
                dot.setAttribute('aria-selected', i === activeIndex ? 'true' : 'false');
            });
        }
    }

    function goTo(index) {
        activeIndex = ((index % count) + count) % count; // wrap both directions
        render();
    }

    function buildDots() {
        if (!dotsWrap) return;
        for (var i = 0; i < count; i++) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'carousel-dot';
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-label', 'نمایش ساعت ' + (i + 1));
            (function (idx) {
                dot.addEventListener('click', function () { goTo(idx); });
            })(i);
            dotsWrap.appendChild(dot);
        }
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(activeIndex - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(activeIndex + 1); });

    // Clicking a side item jumps straight to it — nice shortcut on top
    // of the arrows/dots.
    items.forEach(function (item, i) {
        item.addEventListener('click', function () {
            if (i !== activeIndex) goTo(i);
        });
    });

    // Basic keyboard support when the carousel has focus.
    stage.setAttribute('tabindex', '0');
    stage.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') goTo(activeIndex + 1);   // RTL: left = next
        if (e.key === 'ArrowRight') goTo(activeIndex - 1);  // RTL: right = prev
    });

    var resizeTimer = null;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            measureSpacing();
            render();
        }, 120);
    });

    buildDots();
    measureSpacing();
    render();
})();
