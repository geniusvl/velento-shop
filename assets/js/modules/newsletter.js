/**
 * Newsletter signup form (template-parts/home/newsletter.php).
 * Submits via fetch() to admin-ajax.php so the page never reloads;
 * falls back to a normal POST if fetch/JS is unavailable.
 */
(function () {
    'use strict';

    var form = document.getElementById('velento-newsletter-form');
    if (!form) return;

    var submitBtn = form.querySelector('.newsletter-submit');
    var emailInput = form.querySelector('#velento-newsletter-email');

    function showMessage(text, isError) {
        var existing = form.querySelector('.newsletter-message');
        if (existing) existing.remove();

        var msg = document.createElement('p');
        msg.className = 'newsletter-message' + (isError ? ' is-error' : ' is-success');
        msg.textContent = text;
        msg.setAttribute('role', 'status');
        form.appendChild(msg);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(form);
        submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                submitBtn.disabled = false;
                if (data && data.success) {
                    showMessage(data.data.message, false);
                    emailInput.value = '';
                } else {
                    showMessage((data && data.data && data.data.message) || 'خطایی رخ داد.', true);
                }
            })
            .catch(function () {
                submitBtn.disabled = false;
                showMessage('ارتباط برقرار نشد. لطفاً دوباره تلاش کنید.', true);
            });
    });
})();
