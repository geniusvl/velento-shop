/**
 * Velento Shop — Global site JS.
 * Loaded on every page (unlike header.js/hero-slider.js/carousel.js
 * which only load where they're needed). Keep this file to small,
 * truly site-wide behaviors only.
 */
(function () {
    'use strict';

    /* ---------- Smooth-scroll for in-page anchor links ---------- */
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[href^="#"]');
        if (!link) return;

        var id = link.getAttribute('href').slice(1);
        if (!id) return;

        var target = document.getElementById(id);
        if (!target) return;

        e.preventDefault();
        target.scrollIntoView({
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
            block: 'start',
        });
        target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
    });

    /* ---------- Generic modal open/close (components/modal.css) ----------
       Any element with [data-modal-target="modal-id"] opens the matching
       .modal-overlay#modal-id; .modal__close or [data-modal-close] and
       the overlay backdrop close it. Nothing uses this yet (no modal
       markup exists on the site today) — it's wired up so the next
       feature that needs one (quick-view, size guide) just adds markup. */
    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-modal-target]');
        if (opener) {
            var overlay = document.getElementById(opener.getAttribute('data-modal-target'));
            if (overlay) {
                overlay.classList.add('is-open');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-active');
            }
            return;
        }

        var closer = e.target.closest('[data-modal-close]');
        var backdrop = e.target.classList && e.target.classList.contains('modal-overlay') ? e.target : null;
        if (closer || backdrop) {
            var openOverlay = document.querySelector('.modal-overlay.is-open');
            if (openOverlay) {
                openOverlay.classList.remove('is-open');
                openOverlay.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-active');
            }
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        var openOverlay = document.querySelector('.modal-overlay.is-open');
        if (openOverlay) {
            openOverlay.classList.remove('is-open');
            openOverlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-active');
        }
    });
})();

(function(){
})();
