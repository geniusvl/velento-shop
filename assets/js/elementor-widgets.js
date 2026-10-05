/**
 * Velento Shop — Elementor Widgets JS
 *
 * Everything here is instance-scoped (queries inside a single widget
 * wrapper, never document-wide by a fixed #id) so the same widget can
 * be dropped onto a page any number of times without one copy
 * fighting another. Re-runs on Elementor's own frontend-init event so
 * widgets initialize correctly both on the live page and inside the
 * editor preview after a widget is added/edited without a reload.
 */
(function () {
    'use strict';

    function each(list, fn) { Array.prototype.forEach.call(list, fn); }

    /* ---------------- Hero Slider ---------------- */
    function initHero(root) {
        if (root.dataset.velentoInit) return;
        root.dataset.velentoInit = '1';

        var slides = root.querySelectorAll('.velento-el-hero__slide');
        var dots = root.querySelectorAll('.velento-el-hero__dot');
        var count = slides.length;
        if (count < 2) return;

        var active = 0;
        var timer = null;
        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function go(i) {
            active = ((i % count) + count) % count;
            each(slides, function (s, idx) { s.classList.toggle('is-active', idx === active); });
            each(dots, function (d, idx) { d.classList.toggle('is-active', idx === active); });
        }
        function start() { if (reduced || timer) return; timer = setInterval(function () { go(active + 1); }, 4500); }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }

        each(dots, function (d, idx) {
            d.addEventListener('click', function () { go(idx); stop(); start(); });
        });
        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);
        start();
    }

    /* ---------------- Carousel / Testimonials (scroll-snap + arrows) ---------------- */
    function initScroller(root, trackSelector, prevSelector, nextSelector) {
        if (root.dataset.velentoInit) return;
        root.dataset.velentoInit = '1';

        var track = root.querySelector(trackSelector);
        var prev = root.querySelector(prevSelector);
        var next = root.querySelector(nextSelector);
        if (!track) return;

        function step() {
            var item = track.firstElementChild;
            return item ? item.getBoundingClientRect().width + 16 : track.clientWidth * .8;
        }
        if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
        if (next) next.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    }

    /* ---------------- FAQ Accordion ---------------- */
    function initFaq(root) {
        if (root.dataset.velentoInit) return;
        root.dataset.velentoInit = '1';

        var items = root.querySelectorAll('.velento-el-faq__item');
        each(items, function (item) {
            var btn = item.querySelector('.velento-el-faq__q');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var wasOpen = item.classList.contains('is-open');
                if (root.dataset.accordionMode !== 'multi') {
                    each(items, function (i) { i.classList.remove('is-open'); });
                }
                item.classList.toggle('is-open', !wasOpen);
            });
        });
    }

    /* ---------------- Stats Counter ---------------- */
    function initStats(root) {
        if (root.dataset.velentoInit) return;
        root.dataset.velentoInit = '1';

        var nums = root.querySelectorAll('.velento-el-stat__num[data-target]');
        if (!nums.length || !('IntersectionObserver' in window)) return;

        function animate(el) {
            var target = parseFloat(el.getAttribute('data-target')) || 0;
            var suffix = el.getAttribute('data-suffix') || '';
            var duration = 1400;
            var startTime = null;

            function tick(ts) {
                if (!startTime) startTime = ts;
                var progress = Math.min((ts - startTime) / duration, 1);
                var value = Math.floor(progress * target);
                el.textContent = value.toLocaleString('fa-IR') + suffix;
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target.toLocaleString('fa-IR') + suffix;
            }
            requestAnimationFrame(tick);
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animate(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: .4 });

        each(nums, function (n) { observer.observe(n); });
    }

    /* ---------------- Countdown Timer ---------------- */
    function initCountdown(root) {
        if (root.dataset.velentoInit) return;
        root.dataset.velentoInit = '1';

        var target = new Date(root.getAttribute('data-target')).getTime();
        if (!target || isNaN(target)) return;

        var dEl = root.querySelector('[data-unit="d"]');
        var hEl = root.querySelector('[data-unit="h"]');
        var mEl = root.querySelector('[data-unit="m"]');
        var sEl = root.querySelector('[data-unit="s"]');

        function pad(n) { return String(n).padStart(2, '0'); }

        function tick() {
            var diff = target - Date.now();
            if (diff <= 0) {
                root.classList.add('is-ended');
                clearInterval(intervalId);
                return;
            }
            var d = Math.floor(diff / 86400000);
            var h = Math.floor((diff % 86400000) / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);
            if (dEl) dEl.textContent = d;
            if (hEl) hEl.textContent = pad(h);
            if (mEl) mEl.textContent = pad(m);
            if (sEl) sEl.textContent = pad(s);
        }

        tick();
        var intervalId = setInterval(tick, 1000);
    }

    /* ---------------- Newsletter (AJAX, same handler as template-parts) ---------------- */
    function initNewsletter(root) {
        if (root.dataset.velentoInit) return;
        root.dataset.velentoInit = '1';

        var form = root.querySelector('form');
        if (!form) return;
        var input = form.querySelector('input[type="email"]');
        var btn = form.querySelector('button, input[type="submit"]');
        var msgHolder = root.querySelector('.velento-el-newsletter__msg-holder');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var data = new FormData(form);
            if (btn) btn.disabled = true;

            fetch(form.action, { method: 'POST', body: data, credentials: 'same-origin' })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    var text = (json && json.data && json.data.message) || '';
                    var ok = !!(json && json.success);
                    if (msgHolder) {
                        msgHolder.innerHTML = '';
                        var p = document.createElement('p');
                        p.className = 'velento-el-newsletter__msg ' + (ok ? 'is-success' : 'is-error');
                        p.textContent = text || (ok ? 'ثبت شد.' : 'خطایی رخ داد.');
                        msgHolder.appendChild(p);
                    }
                    if (ok && input) input.value = '';
                })
                .catch(function () {
                    if (msgHolder) {
                        msgHolder.innerHTML = '<p class="velento-el-newsletter__msg is-error">ارتباط برقرار نشد.</p>';
                    }
                })
                .finally(function () {
                    if (btn) btn.disabled = false;
                });
        });
    }

    /* ---------------- Contact Form (AJAX) ---------------- */
    function initContactForm(root) {
        if (root.dataset.velentoInit) return;
        root.dataset.velentoInit = '1';

        var form = root.querySelector('form');
        if (!form) return;
        var btn = form.querySelector('button[type="submit"]');
        var msgHolder = root.querySelector('.velento-el-contact__msg-holder');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var data = new FormData(form);
            if (btn) btn.disabled = true;

            fetch(form.action, { method: 'POST', body: data, credentials: 'same-origin' })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    var text = (json && json.data && json.data.message) || '';
                    var ok = !!(json && json.success);
                    if (msgHolder) {
                        msgHolder.innerHTML = '';
                        var p = document.createElement('p');
                        p.className = 'velento-el-newsletter__msg ' + (ok ? 'is-success' : 'is-error');
                        p.textContent = text || (ok ? 'ارسال شد.' : 'خطایی رخ داد.');
                        msgHolder.appendChild(p);
                    }
                    if (ok) form.reset();
                })
                .catch(function () {
                    if (msgHolder) {
                        msgHolder.innerHTML = '<p class="velento-el-newsletter__msg is-error">ارتباط برقرار نشد.</p>';
                    }
                })
                .finally(function () {
                    if (btn) btn.disabled = false;
                });
        });
    }

    function initAll(scope) {
        scope = scope || document;
        each(scope.querySelectorAll('.velento-el-hero'), initHero);
        each(scope.querySelectorAll('.velento-el-carousel'), function (el) {
            initScroller(el, '.velento-el-carousel__track', '.velento-el-carousel__prev', '.velento-el-carousel__next');
        });
        each(scope.querySelectorAll('.velento-el-testimonials'), function (el) {
            initScroller(el, '.velento-el-testimonials__track', '.velento-el-testimonials__prev', '.velento-el-testimonials__next');
        });
        each(scope.querySelectorAll('.velento-el-faq'), initFaq);
        each(scope.querySelectorAll('.velento-el-stats'), initStats);
        each(scope.querySelectorAll('.velento-el-countdown[data-target]'), initCountdown);
        each(scope.querySelectorAll('.velento-el-newsletter'), initNewsletter);
        each(scope.querySelectorAll('.velento-el-contact'), initContactForm);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initAll(document); });
    } else {
        initAll(document);
    }

    // Elementor editor: re-init a single widget's markup right after it
    // renders/updates in the preview, without a full page reload.
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            initAll($scope[0] || document);
        });
    }
})();
