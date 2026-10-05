/**
 * Velento Shop — Live product search.
 *
 * Owns everything that happens *inside* #velento-search-overlay: the
 * AJAX search-as-you-type, recent searches and recently viewed
 * products. Opening/closing the overlay itself stays in header.js —
 * this module only reacts to it (via the aria-hidden attribute) and
 * never touches it directly, so the two can change independently.
 *
 * No dependencies — plain DOM APIs + fetch, same as header.js.
 */
(function () {
    'use strict';

    var config = window.velentoSearch || null;
    var overlay = document.getElementById('velento-search-overlay');
    if (!overlay) return;

    var form = overlay.querySelector('.search-overlay-form');
    var input = overlay.querySelector('.search-overlay-input');
    var resultsEl = overlay.querySelector('[data-search-results]');
    var viewAllTemplate = overlay.querySelector('[data-search-view-all-template]');
    var viewAllEl = null; // created on demand, only when there are results to view
    var recentPanel = overlay.querySelector('[data-search-recent-panel]');
    var recentList = overlay.querySelector('[data-search-recent-list]');
    var clearRecentBtn = overlay.querySelector('[data-search-clear-recent]');
    var viewedPanel = overlay.querySelector('[data-search-viewed-panel]');
    var viewedList = overlay.querySelector('[data-search-viewed-list]');
    var clearViewedBtn = overlay.querySelector('[data-search-clear-viewed]');

    if (!form || !input) return;

    var STORAGE_RECENT = 'velento_recent_searches';
    var STORAGE_VIEWED = 'velento_recently_viewed';
    var MIN_CHARS = (config && config.minChars) || 2;
    var DEBOUNCE_MS = 300;

    var debounceTimer = null;
    var activeController = null;
    var requestSeq = 0;

    /* ---------- localStorage helpers ---------- */
    function readStore(key) {
        try {
            var data = JSON.parse(localStorage.getItem(key) || '[]');
            return Array.isArray(data) ? data : [];
        } catch (e) {
            return [];
        }
    }

    function writeStore(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {}
    }

    /* Merge the server-provided list (logged-in users) with the local
       cache, de-duplicated, most-recent-first, capped to `limit`. */
    function mergeUnique(serverList, localList, keyFn, limit) {
        var seen = {};
        var merged = [];
        (serverList || []).concat(localList || []).forEach(function (item) {
            var key = keyFn(item);
            if (!key || seen[key]) return;
            seen[key] = true;
            merged.push(item);
        });
        return merged.slice(0, limit);
    }

    function postAjax(action, extraFields) {
        if (!config || !config.ajaxUrl) return;
        var data = new FormData();
        data.append('action', action);
        data.append('nonce', config.nonce);
        Object.keys(extraFields || {}).forEach(function (key) {
            data.append(key, extraFields[key]);
        });
        // Fire-and-forget: the UI already updated optimistically.
        fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data }).catch(function () {});
    }

    /* ---------- recent searches ---------- */
    function getRecentSearches() {
        return mergeUnique(
            config && config.recentSearches,
            readStore(STORAGE_RECENT),
            function (term) { return String(term || '').toLowerCase(); },
            8
        );
    }

    function addRecentSearch(term) {
        term = (term || '').trim();
        if (!term) return;

        var list = getRecentSearches().filter(function (existing) {
            return existing.toLowerCase() !== term.toLowerCase();
        });
        list.unshift(term);
        list = list.slice(0, 3);

        writeStore(STORAGE_RECENT, list);
        if (config) config.recentSearches = [];
        renderRecentSearches(list);

        if (config && config.isLoggedIn) {
            postAjax('velento_search_recent_save', { term: term });
        }
    }

    function clearRecentSearches() {
        writeStore(STORAGE_RECENT, []);
        if (config) config.recentSearches = [];
        renderRecentSearches([]);

        if (config && config.isLoggedIn) {
            postAjax('velento_search_recent_clear', {});
        }
    }

    function renderRecentSearches(list) {
        if (!recentList || !recentPanel) return;
        recentList.innerHTML = '';

        if (!list.length) {
            recentPanel.hidden = true;
            return;
        }

        recentPanel.hidden = false;
        list.forEach(function (term) {
            var li = document.createElement('li');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'search-chip';
            btn.textContent = term;
            btn.setAttribute('data-term', term);
            li.appendChild(btn);
            recentList.appendChild(li);
        });
    }

    /* ---------- recently viewed ---------- */
    function getRecentlyViewed() {
        return mergeUnique(
            config && config.recentlyViewed,
            readStore(STORAGE_VIEWED),
            function (product) { return product && product.id; },
            3
        );
    }

    /* Guests only: logged-in users are tracked server-side on the
       product page request itself (inc/search.php), so their list is
       already current by the time it's localized to this page. */
    function trackCurrentProductAsViewed() {
        if (!config || !config.currentProduct || config.isLoggedIn) return;

        var product = config.currentProduct;
        var list = getRecentlyViewed().filter(function (existing) {
            return existing.id !== product.id;
        });
        list.unshift(product);
        list = list.slice(0, 3);
        writeStore(STORAGE_VIEWED, list);
    }

    function clearRecentlyViewed() {
        writeStore(STORAGE_VIEWED, []);
        if (config) config.recentlyViewed = [];
        renderRecentlyViewed([]);

        if (config && config.isLoggedIn) {
            postAjax('velento_search_viewed_clear', {});
        }
    }

    function buildProductRow(product) {
        var li = document.createElement('li');
        var link = document.createElement('a');
        link.className = 'search-product';
        link.href = product.url || '#';

        var media = document.createElement('span');
        media.className = 'search-product-media';
        var img = document.createElement('img');
        img.src = product.image || '';
        img.alt = product.name || '';
        img.loading = 'lazy';
        media.appendChild(img);

        var info = document.createElement('span');
        info.className = 'search-product-info';

        if (product.brand) {
            var brand = document.createElement('span');
            brand.className = 'search-product-brand';
            brand.textContent = product.brand;
            info.appendChild(brand);
        }

        var name = document.createElement('span');
        name.className = 'search-product-name';
        name.textContent = product.name || '';
        info.appendChild(name);

        var bottom = document.createElement('span');
        bottom.className = 'search-product-bottom';

        var price = document.createElement('span');
        price.className = 'search-product-price';
        // Trusted server-rendered WooCommerce markup (wc_price/get_price_html),
        // never raw user input — safe to set as HTML.
        price.innerHTML = product.price_html || '';
        bottom.appendChild(price);

        if (product.stock_text) {
            var stock = document.createElement('span');
            stock.className = 'search-product-stock' + (product.in_stock ? '' : ' is-out');
            stock.textContent = product.stock_text;
            bottom.appendChild(stock);
        }

        info.appendChild(bottom);
        link.appendChild(media);
        link.appendChild(info);
        li.appendChild(link);
        return li;
    }

    function renderRecentlyViewed(list) {
        if (!viewedList || !viewedPanel) return;
        viewedList.innerHTML = '';

        if (!list.length) {
            viewedPanel.hidden = true;
            return;
        }

        viewedPanel.hidden = false;
        list.forEach(function (product) {
            viewedList.appendChild(buildProductRow(product));
        });
    }

    /* ---------- search state machine ---------- */
    function removeViewAll() {
        if (viewAllEl && viewAllEl.parentNode) {
            viewAllEl.parentNode.removeChild(viewAllEl);
        }
        viewAllEl = null;
    }

    function resetResultState() {
        if (resultsEl) { resultsEl.innerHTML = ''; resultsEl.hidden = true; }
        removeViewAll();
    }

    function showIdleState() {
        resetResultState();
        renderRecentSearches(getRecentSearches());
        renderRecentlyViewed(getRecentlyViewed());
    }

    // While a request is in flight: nothing shown yet (no spinner) —
    // just clear out whatever was there (previous results or the idle
    // panels) so a stale answer can never linger on screen.
    function showPendingState() {
        resetResultState();
        if (recentPanel) recentPanel.hidden = true;
        if (viewedPanel) viewedPanel.hidden = true;
    }

    function buildViewAllUrl(term) {
        var base = form.getAttribute('action') || '/';
        var separator = base.indexOf('?') > -1 ? '&' : '?';
        return base + separator + 's=' + encodeURIComponent(term) + '&post_type=product';
    }

    function renderResults(products, term) {
        resetResultState();
        if (recentPanel) recentPanel.hidden = true;
        if (viewedPanel) viewedPanel.hidden = true;

        if (!products.length) {
            return; // nothing to show in the live dropdown; the shop
                    // results page (search.php) is where "not found" lives
        }

        if (resultsEl) {
            products.forEach(function (product) {
                resultsEl.appendChild(buildProductRow(product));
            });
            resultsEl.hidden = false;
        }

        if (viewAllTemplate && resultsEl) {
            viewAllEl = viewAllTemplate.content.firstElementChild.cloneNode(true);
            viewAllEl.href = buildViewAllUrl(term);
            resultsEl.insertAdjacentElement('afterend', viewAllEl);
        }
    }

    function runSearch(term) {
        if (!config || !config.ajaxUrl) return;

        if (activeController) activeController.abort();
        var controller = (typeof AbortController !== 'undefined') ? new AbortController() : null;
        activeController = controller;
        var seq = ++requestSeq;

        showPendingState();

        var data = new FormData();
        data.append('action', 'velento_search');
        data.append('nonce', config.nonce);
        data.append('term', term);

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data,
            signal: controller ? controller.signal : undefined
        })
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (seq !== requestSeq) return; // a newer keystroke already superseded this
                var products = (result.success && result.data && result.data.products) || [];
                renderResults(products, term);
            })
            .catch(function (err) {
                if ((err && err.name === 'AbortError') || seq !== requestSeq) return;
                // A network/server failure isn't "no results" — don't
                // claim the search doesn't exist, just clear the panel.
                resetResultState();
            });
    }

    /* ---------- events ---------- */
    input.addEventListener('input', function () {
        var value = input.value.trim();
        if (debounceTimer) clearTimeout(debounceTimer);

        if (value.length < MIN_CHARS) {
            if (activeController) activeController.abort();
            showIdleState();
            return;
        }

        debounceTimer = setTimeout(function () {
            runSearch(value);
        }, DEBOUNCE_MS);
    });

    // Full form submit still navigates to the normal WooCommerce search
    // results page (plain GET, untouched) — just remember the term first.
    form.addEventListener('submit', function () {
        addRecentSearch(input.value.trim());
    });

    if (resultsEl) {
        resultsEl.addEventListener('click', function () {
            addRecentSearch(input.value.trim());
        });
    }

    if (recentList) {
        recentList.addEventListener('click', function (e) {
            var chip = e.target.closest('[data-term]');
            if (!chip) return;
            var term = chip.getAttribute('data-term');
            input.value = term;
            input.focus();
            runSearch(term);
        });
    }

    if (clearRecentBtn) clearRecentBtn.addEventListener('click', clearRecentSearches);
    if (clearViewedBtn) clearViewedBtn.addEventListener('click', clearRecentlyViewed);

    /* Arrow-key navigation across whichever product list is visible. */
    overlay.addEventListener('keydown', function (e) {
        if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;

        var list = (resultsEl && !resultsEl.hidden) ? resultsEl
            : (viewedPanel && !viewedPanel.hidden) ? viewedList
            : null;
        if (!list) return;

        var items = Array.prototype.slice.call(list.querySelectorAll('.search-product'));
        if (!items.length) return;

        e.preventDefault();
        var currentIndex = items.indexOf(document.activeElement);
        var nextIndex = currentIndex === -1
            ? (e.key === 'ArrowDown' ? 0 : items.length - 1)
            : (e.key === 'ArrowDown' ? currentIndex + 1 : currentIndex - 1);

        if (nextIndex < 0) nextIndex = items.length - 1;
        if (nextIndex >= items.length) nextIndex = 0;
        items[nextIndex].focus();
    });

    // Reset to idle state whenever header.js closes the overlay, so the
    // next open always starts clean instead of showing stale results.
    if (window.MutationObserver) {
        new MutationObserver(function () {
            if (overlay.getAttribute('aria-hidden') === 'true') {
                input.value = '';
                if (activeController) activeController.abort();
                showIdleState();
            }
        }).observe(overlay, { attributes: true, attributeFilter: ['aria-hidden'] });
    }

    /* ---------- init ---------- */
    trackCurrentProductAsViewed();
    showIdleState();
})();
