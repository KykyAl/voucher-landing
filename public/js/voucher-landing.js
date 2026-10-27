(function () {
    'use strict';

    var form = document.getElementById('voucher-form');
    if (!form) {
        return;
    }

    var options = form.querySelectorAll('.voucher-option');
    var quantityInput = document.getElementById('quantity');
    var continueButton = document.getElementById('btn-continue');
    var minQty = parseInt(quantityInput.min, 10) || 1;
    var maxQty = parseInt(quantityInput.max, 10) || 10;

    var rupiah = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    });

    function text(id, value) {
        document.getElementById(id).textContent = value;
    }

    function clampQuantity() {
        var qty = parseInt(quantityInput.value, 10);
        if (isNaN(qty)) {
            qty = minQty;
        }
        qty = Math.min(Math.max(qty, minQty), maxQty);
        quantityInput.value = qty;

        return qty;
    }

    function selectedOption() {
        return form.querySelector('.voucher-option:checked');
    }

    function render() {
        var option = selectedOption();
        var qty = clampQuantity();
        var hasSelection = option !== null;

        document.getElementById('summary-empty').classList.toggle('d-none', hasSelection);
        document.getElementById('summary-filled').classList.toggle('d-none', !hasSelection);
        continueButton.disabled = !hasSelection;

        if (!hasSelection) {
            return;
        }

        var price = parseInt(option.dataset.price, 10);
        var total = rupiah.format(price * qty);

        text('summary-name', option.dataset.name);
        text('summary-qty', qty);
        text('summary-total', total);

        text('modal-name', option.dataset.name);
        text('modal-validity', option.dataset.validity + ' hari');
        text('modal-price', rupiah.format(price));
        text('modal-qty', qty + ' voucher');
        text('modal-total', total);
    }

    options.forEach(function (option) {
        option.addEventListener('change', render);
    });

    quantityInput.addEventListener('change', render);
    quantityInput.addEventListener('input', function () {
        if (quantityInput.value !== '') {
            render();
        }
    });

    form.querySelectorAll('[data-qty-step]').forEach(function (button) {
        button.addEventListener('click', function () {
            quantityInput.value = clampQuantity() + parseInt(button.dataset.qtyStep, 10);
            render();
        });
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
    });

    render();
})();
