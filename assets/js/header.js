/**
 * Velento Shop — Header interactions.
 * Theme toggle, search overlay, cart drawer and mobile nav.
 * No dependencies — plain DOM APIs only.
 */
(function () {
    'use strict';

    var root = document.documentElement;
    var header = document.getElementById('site-header');
    if (!header) return;

    var themeToggle = header.querySelector('.theme-toggle');
    var navToggle = header.querySelector('.nav-toggle');
    var searchToggle = header.querySelector('.search-toggle');
    var cartToggle = header.querySelector('.cart-link');
    var searchOverlay = document.getElementById('velento-search-overlay');
    var searchClose = searchOverlay ? searchOverlay.querySelector('.search-overlay-close') : null;
    var searchInput = searchOverlay ? searchOverlay.querySelector('.search-overlay-input') : null;
    var cartDrawer = document.getElementById('velento-cart-drawer');
    var cartClose = cartDrawer ? cartDrawer.querySelector('.velento-cart-drawer__close') : null;
    var cartContent = cartDrawer ? cartDrawer.querySelector('.velento-cart-drawer__content') : null;
    var loginToggle = header.querySelector('.login-toggle');
    var loginDrawer = document.getElementById('velento-login-drawer');
    var loginClose = loginDrawer ? loginDrawer.querySelector('.velento-login-drawer__close') : null;
    var siteOverlay = document.getElementById('site-overlay');
    var body = document.body;

    /* Restore the user's theme before the first interaction. Default is dark
       to match Velento's luxury editorial direction. */
    try {
        var savedTheme = localStorage.getItem('velento-theme');
        root.setAttribute('data-theme', savedTheme === 'light' ? 'light' : 'dark');
    } catch (e) {
        root.setAttribute('data-theme', 'dark');
    }

    /* ---------- Theme toggle ---------- */
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('velento-theme', next); } catch (e) {}
        });
    }

    /* ---------- Shared state ---------- */
    function unlockScrollIfClosed() {
        if (!body.classList.contains('search-active') && !body.classList.contains('cart-active') && !body.classList.contains('login-active') && !body.classList.contains('nav-active')) {
            body.classList.remove('search-active', 'cart-active', 'login-active', 'nav-active');
        }
    }

    /* ---------- Mobile nav ---------- */
    function closeNav() {
        header.classList.remove('nav-open');
        if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
        body.classList.remove('nav-active');
        unlockScrollIfClosed();
    }

    if (navToggle) {
        navToggle.addEventListener('click', function () {
            var isOpen = header.classList.toggle('nav-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (isOpen) {
                closeSearch();
                closeCart();
                closeLogin();
                body.classList.add('nav-active');
            } else {
                body.classList.remove('nav-active');
                unlockScrollIfClosed();
            }
        });
    }

    /* ---------- Search overlay ---------- */
    function openSearch() {
        if (!searchOverlay) return;
        closeCart();
        closeLogin();
        closeNav();
        header.classList.add('search-open');
        searchOverlay.classList.add('is-open');
        searchOverlay.setAttribute('aria-hidden', 'false');
        if (searchToggle) searchToggle.setAttribute('aria-expanded', 'true');
        body.classList.add('search-active');
        window.setTimeout(function () {
            if (searchInput) searchInput.focus();
        }, 150);
    }

    function closeSearch() {
        if (!searchOverlay) return;
        header.classList.remove('search-open');
        searchOverlay.classList.remove('is-open');
        searchOverlay.setAttribute('aria-hidden', 'true');
        if (searchToggle) searchToggle.setAttribute('aria-expanded', 'false');
        body.classList.remove('search-active');
        unlockScrollIfClosed();
    }

    if (searchToggle) {
        searchToggle.addEventListener('click', function () {
            var isOpen = searchOverlay && searchOverlay.classList.contains('is-open');
            if (isOpen) closeSearch(); else openSearch();
        });
    }

    if (searchClose) searchClose.addEventListener('click', closeSearch);

    /* ---------- Cart drawer ---------- */
    function openCart() {
        if (!cartDrawer) return;
        closeSearch();
        closeLogin();
        closeNav();
        cartDrawer.classList.add('is-open');
        cartDrawer.setAttribute('aria-hidden', 'false');
        body.classList.add('cart-active');
        if (cartClose) window.setTimeout(function () { cartClose.focus(); }, 180);
    }

    function closeCart() {
        if (!cartDrawer) return;
        cartDrawer.classList.remove('is-open');
        cartDrawer.setAttribute('aria-hidden', 'true');
        body.classList.remove('cart-active');
        unlockScrollIfClosed();
    }

    if (cartToggle) {
        cartToggle.addEventListener('click', function (e) {
            e.preventDefault();
            var isOpen = body.classList.contains('cart-active');
            if (isOpen) closeCart(); else openCart();
        });
    }

    if (cartClose) cartClose.addEventListener('click', closeCart);

    /* ---------- Login / register drawer ---------- */
    var loginSwitchButtons = loginDrawer ? loginDrawer.querySelectorAll('[data-login-target]') : [];
    var loginNotice = loginDrawer ? loginDrawer.querySelector('[data-role="notice"]') : null;
    var loginForm = document.getElementById('velento-login-form');
    var registerForm = document.getElementById('velento-register-form');
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function setLoginMode(mode) {
        if (!loginDrawer) return;
        loginDrawer.setAttribute('data-mode', mode);

        loginDrawer.querySelectorAll('[data-login-panel]').forEach(function (panel) {
            panel.hidden = panel.getAttribute('data-login-panel') !== mode;
        });

        loginSwitchButtons.forEach(function (tab) {
            var active = tab.getAttribute('data-login-target') === mode;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        hideNotice();

        var firstField = loginDrawer.querySelector('[data-login-panel="' + mode + '"] input');
        if (firstField) window.setTimeout(function () { firstField.focus(); }, 200);
    }

    function showNotice(message, isSuccess) {
        if (!loginNotice) return;
        loginNotice.textContent = message;
        loginNotice.hidden = false;
        loginNotice.classList.toggle('is-success', !!isSuccess);
    }

    function hideNotice() {
        if (!loginNotice) return;
        loginNotice.hidden = true;
        loginNotice.classList.remove('is-success');
    }

    function openLogin(mode) {
        if (!loginDrawer) return;
        closeSearch();
        closeCart();
        closeNav();
        setLoginMode(mode || 'signin');
        loginDrawer.classList.add('is-open');
        loginDrawer.setAttribute('aria-hidden', 'false');
        if (loginToggle) loginToggle.setAttribute('aria-expanded', 'true');
        body.classList.add('login-active');
        if (loginClose) window.setTimeout(function () { loginClose.focus(); }, 180);
    }

    function closeLogin() {
        if (!loginDrawer) return;
        loginDrawer.classList.remove('is-open');
        loginDrawer.setAttribute('aria-hidden', 'true');
        if (loginToggle) loginToggle.setAttribute('aria-expanded', 'false');
        body.classList.remove('login-active');
        unlockScrollIfClosed();
    }

    if (loginToggle && loginDrawer) {
        loginToggle.addEventListener('click', function (e) {
            e.preventDefault();
            var isOpen = body.classList.contains('login-active');
            if (isOpen) closeLogin(); else openLogin();
        });
    }

    if (loginClose) loginClose.addEventListener('click', closeLogin);

    loginSwitchButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            setLoginMode(btn.getAttribute('data-login-target'));
        });
    });

    // Shared "call admin-ajax.php, show a loading state, report success
    // or failure in the notice bar" helper for both forms below.
    function submitLoginForm(form, action, extraFields) {
        if (!window.velentoLogin || !window.velentoLogin.ajaxUrl) return;

        var submitBtn = form.querySelector('.velento-login-drawer__submit');
        var data = new FormData();
        data.append('action', action);
        data.append('nonce', window.velentoLogin.nonce);
        Object.keys(extraFields).forEach(function (key) {
            data.append(key, extraFields[key]);
        });

        if (submitBtn) submitBtn.classList.add('is-loading');
        hideNotice();

        fetch(window.velentoLogin.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (result.success) {
                    showNotice((result.data && result.data.message) || 'انجام شد.', true);
                    // Send them to their profile page (not just a reload)
                    // now that sign-in/registration succeeded.
                    var destination = (window.velentoLogin && window.velentoLogin.accountUrl) || window.location.href;
                    window.setTimeout(function () { window.location.href = destination; }, 700);
                    return;
                }
                showNotice((result.data && result.data.message) || 'مشکلی پیش آمد.', false);
            })
            .catch(function () {
                showNotice('ارتباط با سرور برقرار نشد.', false);
            })
            .finally(function () {
                if (submitBtn) submitBtn.classList.remove('is-loading');
            });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var username = loginForm.querySelector('#velento-login-username').value.trim();
            var password = loginForm.querySelector('#velento-login-password').value;
            var remember = loginForm.querySelector('#velento-login-remember').checked;

            if (!username || !password) {
                showNotice('لطفاً ایمیل یا نام کاربری و رمز عبور را وارد کنید.', false);
                return;
            }

            submitLoginForm(loginForm, 'velento_login', {
                username: username,
                password: password,
                remember: remember ? '1' : ''
            });
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var email = registerForm.querySelector('#velento-register-email').value.trim();
            var password = registerForm.querySelector('#velento-register-password').value;
            var password2 = registerForm.querySelector('#velento-register-password2').value;

            if (!emailPattern.test(email)) {
                showNotice('ایمیل را به‌صورت صحیح وارد کنید؛ مثل name@gmail.com', false);
                return;
            }

            if (password.length < 8 || password.length > 20) {
                showNotice('رمز عبور باید بین ۸ تا ۲۰ کاراکتر باشد.', false);
                return;
            }

            if (password !== password2) {
                showNotice('رمز عبور و تکرار آن یکسان نیستند.', false);
                return;
            }

            submitLoginForm(registerForm, 'velento_register', {
                email: email,
                password: password,
                password2: password2
            });
        });
    }

    if (siteOverlay) {
        siteOverlay.addEventListener('click', function () {
            closeSearch();
            closeCart();
            closeLogin();
            closeNav();
        });
    }

    /* ---------- Cart AJAX ---------- */
    function cartNonce() {
        return body.getAttribute('data-velento-cart-nonce') || '';
    }

    function refreshCartDrawer() {
        if (!cartContent || !window.velentoCart || !window.velentoCart.ajaxUrl) return Promise.resolve();
        var data = new FormData();
        data.append('action', 'velento_cart_refresh');
        data.append('nonce', window.velentoCart.nonce);

        return fetch(window.velentoCart.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (result.success && result.data && result.data.content) {
                    cartContent.innerHTML = result.data.content;
                    updateCartCount(result.data.count);
                }
            })
            .catch(function () {});
    }

    function updateCartCount(count) {
        document.querySelectorAll('.cart-count-fragment').forEach(function (badge) {
            badge.textContent = count > 0 ? count : '';
            badge.setAttribute('data-count', String(count || 0));
        });
    }

    function updateCartItem(key, quantity) {
        if (!window.velentoCart || !window.velentoCart.ajaxUrl) return;
        var data = new FormData();
        data.append('action', 'velento_cart_update');
        data.append('nonce', window.velentoCart.nonce);
        data.append('cart_key', key);
        data.append('quantity', quantity);

        if (cartContent) cartContent.classList.add('is-loading');
        fetch(window.velentoCart.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (result.success && result.data) {
                    if (cartContent && result.data.content) cartContent.innerHTML = result.data.content;
                    updateCartCount(result.data.count);
                    document.body.dispatchEvent(new CustomEvent('velento_cart_updated', { detail: result.data }));
                }
            })
            .catch(function () {})
            .finally(function () {
                if (cartContent) cartContent.classList.remove('is-loading');
            });
    }

    if (cartContent) {
        cartContent.addEventListener('click', function (e) {
            var remove = e.target.closest('[data-cart-remove]');
            if (remove) {
                e.preventDefault();
                updateCartItem(remove.getAttribute('data-cart-remove'), 0);
                return;
            }

            var qty = e.target.closest('[data-cart-qty]');
            if (!qty) return;

            e.preventDefault();
            var key = qty.getAttribute('data-cart-key');
            var row = qty.closest('.velento-cart-item');
            var currentEl = row ? row.querySelector('.velento-cart-qty span') : null;
            var current = currentEl ? parseInt(currentEl.textContent, 10) || 1 : 1;
            var next = qty.getAttribute('data-cart-qty') === 'increase' ? current + 1 : current - 1;
            updateCartItem(key, Math.max(0, next));
        });
    }

    /* WooCommerce's native AJAX add-to-cart event. Refresh the drawer and
       open it so the customer immediately sees the result of their action. */
    if (window.jQuery) {
        window.jQuery(document.body).on('added_to_cart', function () {
            // Never wait for a network request before showing the drawer.
            // Open immediately, then refresh its contents in the background.
            openCart();
            refreshCartDrawer();
        });
    }

    /* ---------- Keyboard / accessibility ---------- */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSearch();
            closeCart();
            closeLogin();
            closeNav();
        }
    });
})();
