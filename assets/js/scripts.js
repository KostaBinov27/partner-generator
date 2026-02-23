document.addEventListener('DOMContentLoaded', function () {

    const toggle = document.getElementById('billingToggle');
    const monthlyLabel = document.getElementById('lbl-monthly');
    const yearlyLabel = document.getElementById('lbl-yearly');

    toggle.addEventListener('change', function () {

        const isYearly = toggle.checked;
        const cards = document.querySelectorAll('.card');

        cards.forEach(function (card) {

            const orig = card.querySelector('.price-orig');
            const curr = card.querySelector('.price-curr');
            const period = card.querySelector('.price-period');
            const value = card.querySelector('.price-value');

            if (isYearly) {

                const origYearly = orig.getAttribute('price-original-yearly');
                const discYearly = curr.getAttribute('price-discounted-yearly');

                orig.innerHTML = '£' + origYearly + '<small>/yr</small>';
                value.textContent = discYearly;
                period.textContent = '/yr';

            } else {

                const origMonthly = orig.getAttribute('price-original-monthly');
                const discMonthly = curr.getAttribute('price-discounted-monthly');

                orig.innerHTML = '£' + origMonthly + '<small>/mo</small>';
                value.textContent = discMonthly;
                period.textContent = '/mo';
            }

        });

        // Toggle active state
        monthlyLabel.classList.toggle('active', !isYearly);
        yearlyLabel.classList.toggle('active', isYearly);

    });

});