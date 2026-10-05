/**
 * Velento Shop — Product discovery filters.
 *
 * Progressive enhancement over the server-rendered shop archive:
 * - debounced AJAX requests
 * - aborts superseded requests
 * - URL state for Back/Forward/shareable filters
 * - mobile off-canvas filter drawer
 * - no third-party dependency
 */
(function () {
    'use strict';

    var config = window.velentoFilters || null;
    var root = document.querySelector('[data-shop-filters]');
    var resultsArea = document.querySelector('.velento-shop-results-area');

    if (!config || !root || !resultsArea) return;

    var drawer = document.querySelector('[data-filter-drawer]');
    var openButtons = document.querySelectorAll('[data-filter-open]');
    var closeButtons = document.querySelectorAll('[data-filter-close]');
    var requestController = null;
    var requestSequence = 0;
    var debounceTimer = null;

    function selectedValues(name) {
        return Array.prototype.slice.call(root.querySelectorAll('input[name="' + name + '[]"]:checked'))
            .map(function (input) { return input.value; });
    }

    function getState(page) {
        var sort = resultsArea.querySelector('[data-filter-sort]');

        return {
            brand: selectedValues('brand'),
            category: selectedValues('category'),
            strap: selectedValues('strap'),
            movement: selectedValues('movement'),
            movement_count: selectedValues('movement_count'),
            gender: selectedValues('gender'),
            material: selectedValues('material'),
            color: selectedValues('color'),
            water_resistance: selectedValues('water_resistance'),
            stock: (root.querySelector('input[name="stock"]:checked') || {}).value || '',
            sale: !!root.querySelector('input[name="sale"]:checked'),
            orderby: sort ? sort.value : 'menu_order',
            price_min: (root.querySelector('input[name="price_min"]') || {}).value || '',
            price_max: (root.querySelector('input[name="price_max"]') || {}).value || '',
            paged: page || 1
        };
    }

    function setLoading(isLoading) {
        resultsArea.classList.toggle('is-loading', isLoading);
        if (isLoading) {
            resultsArea.setAttribute('aria-busy', 'true');
        } else {
            resultsArea.removeAttribute('aria-busy');
        }
    }

    function openDrawer() {
        if (!drawer) return;
        drawer.classList.add('is-open');
        document.body.classList.add('velento-filters-open');
        openButtons.forEach(function (button) {
            button.setAttribute('aria-expanded', 'true');
        });
    }

    function closeDrawer() {
        if (!drawer) return;
        drawer.classList.remove('is-open');
        document.body.classList.remove('velento-filters-open');
        openButtons.forEach(function (button) {
            button.setAttribute('aria-expanded', 'false');
        });
    }

    function buildUrl(state) {
        var url = new URL(window.location.href);

        Object.keys(state).forEach(function (key) {
            url.searchParams.delete(key);
        });

        Object.keys(state).forEach(function (key) {
            var value = state[key];

            if (Array.isArray(value)) {
                value.forEach(function (item) {
                    if (item) url.searchParams.append(key + '[]', item);
                });
                return;
            }

            if (key === 'sale') {
                if (value) url.searchParams.set('sale', '1');
                return;
            }

            if (value !== '' && value !== null && typeof value !== 'undefined') {
                url.searchParams.set(key, value);
            }
        });

        if (state.paged && Number(state.paged) > 1) {
            url.searchParams.set('paged', state.paged);
        } else {
            url.searchParams.delete('paged');
        }

        return url.toString();
    }

    function syncUrl(state, replace) {
        var url = buildUrl(state);
        if (replace) {
            window.history.replaceState({ velentoFilters: true }, '', url);
        } else {
            window.history.pushState({ velentoFilters: true }, '', url);
        }
    }

    function requestResults(page, replaceUrl) {
        if (requestController) requestController.abort();

        var controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
        requestController = controller;
        var sequence = ++requestSequence;
        var state = getState(page);

        setLoading(true);

        var data = new FormData();
        data.append('action', 'velento_shop_filters');
        data.append('nonce', config.nonce);
        data.append('filters', JSON.stringify(state));

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data,
            signal: controller ? controller.signal : undefined
        })
            .then(function (response) {
                if (!response.ok) throw new Error('request_failed');
                return response.json();
            })
            .then(function (response) {
                if (sequence !== requestSequence) return;
                if (!response.success || !response.data || typeof response.data.html !== 'string') {
                    throw new Error('invalid_response');
                }

                resultsArea.innerHTML = response.data.html;
                syncUrl(state, !!replaceUrl);
                setLoading(false);
                closeDrawer();
                resultsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(function (error) {
                if (error && error.name === 'AbortError') return;
                if (sequence !== requestSequence) return;
                setLoading(false);
                // The server-rendered result stays intact when an AJAX
                // request fails; the user can continue using the filters.
            });
    }

    function scheduleRequest() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            requestResults(1, false);
        }, 260);
    }

    root.addEventListener('change', function (event) {
        if (event.target.matches('input[type="checkbox"], input[type="radio"]')) {
            scheduleRequest();
        }
    });


    document.addEventListener('change', function (event) {
        if (!event.target.matches('[data-filter-sort]')) return;
        requestResults(1, false);
    });

    resultsArea.addEventListener('click', function (event) {
        var pageLink = event.target.closest('.v-shop-pagination a');
        if (pageLink) {
            event.preventDefault();
            var url = new URL(pageLink.href, window.location.origin);
            requestResults(Number(url.searchParams.get('paged') || 1), false);
            return;
        }

        var clear = event.target.closest('[data-filter-clear]');
        if (clear) {
            event.preventDefault();
            root.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(function (input) {
                input.checked = false;
            });


            var sort = resultsArea.querySelector('[data-filter-sort]');
            if (sort) sort.value = 'menu_order';

            requestResults(1, false);
        }
    });

    root.addEventListener('click', function (event) {
        var clear = event.target.closest('[data-filter-clear]');
        if (clear) {
            event.preventDefault();
            root.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(function (input) {
                input.checked = false;
            });


            var sort = resultsArea.querySelector('[data-filter-sort]');
            if (sort) sort.value = 'menu_order';

            requestResults(1, false);
        }

        var chip = event.target.closest('[data-filter-chip-key]');
        if (chip) {
            event.preventDefault();
            var key = chip.getAttribute('data-filter-chip-key');
            var value = chip.getAttribute('data-filter-chip-value');

            if (key === 'stock') {
                var stock = root.querySelector('input[name="stock"][value="' + CSS.escape(value) + '"]');
                if (stock) stock.checked = false;
            } else if (key === 'sale') {
                var sale = root.querySelector('input[name="sale"]');
                if (sale) sale.checked = false;
            } else {
                var input = root.querySelector('input[name="' + CSS.escape(key) + '[]"][value="' + CSS.escape(value) + '"]');
                if (input) input.checked = false;
            }

            requestResults(1, false);
        }
    });

    openButtons.forEach(function (button) {
        button.addEventListener('click', openDrawer);
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeDrawer);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeDrawer();
    });

    window.addEventListener('popstate', function () {
        window.location.reload();
    });

})();
