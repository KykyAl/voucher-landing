(function () {
    'use strict';

    var cartElement = document.getElementById('cart');
    if (!cartElement) {
        return;
    }

    var STORAGE_KEY = 'voucher-cart';
    var maxQty = parseInt(cartElement.dataset.maxQuantity, 10) || 10;
    var cards = document.querySelectorAll('.voucher-ticket[data-code]');
    var itemList = document.getElementById('cart-items');
    var itemTemplate = document.getElementById('cart-item-template');

    var rupiah = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    });

    // Katalog diambil dari kartu di halaman, hanya voucher yang tersedia.
    var catalog = {};
    cards.forEach(function (card) {
        if (card.dataset.available !== '1') {
            return;
        }
        catalog[card.dataset.code] = {
            name: card.dataset.name,
            price: parseInt(card.dataset.price, 10),
            faceValue: parseInt(card.dataset.faceValue, 10),
            duration: card.dataset.duration,
            featured: card.classList.contains('is-featured'),
        };
    });

    var cart = loadCart();

    function text(id, value) {
        document.getElementById(id).textContent = value;
    }

    /**
     * Isi keranjang tersimpan di browser; voucher yang sudah tidak tersedia
     * dibuang dan total jumlah dipangkas ke batas maksimal.
     */
    function loadCart() {
        var stored = {};
        try {
            stored = JSON.parse(window.localStorage.getItem(STORAGE_KEY)) || {};
        } catch (error) {
            stored = {};
        }

        var result = {};
        var total = 0;
        Object.keys(stored).forEach(function (code) {
            var qty = Math.min(parseInt(stored[code], 10) || 0, maxQty - total);
            if (catalog[code] && qty > 0) {
                result[code] = qty;
                total += qty;
            }
        });

        return result;
    }

    function saveCart() {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
        } catch (error) {
            // Penyimpanan diblokir (mis. mode privat); keranjang tetap jalan selama halaman terbuka.
        }
    }

    function totalQuantity() {
        return Object.keys(cart).reduce(function (sum, code) {
            return sum + cart[code];
        }, 0);
    }

    function setQuantity(code, qty) {
        if (!catalog[code]) {
            return;
        }

        var others = totalQuantity() - (cart[code] || 0);
        qty = Math.max(0, Math.min(qty, maxQty - others));

        if (qty === 0) {
            delete cart[code];
        } else {
            cart[code] = qty;
        }

        saveCart();
        render();
    }

    function bumpCounters() {
        document.querySelectorAll('[data-cart-count]').forEach(function (badge) {
            badge.classList.remove('is-bump');
            void badge.offsetWidth;
            badge.classList.add('is-bump');
        });
    }

    function renderCards(isFull) {
        cards.forEach(function (card) {
            var code = card.dataset.code;
            if (!catalog[code]) {
                return;
            }

            var qty = cart[code] || 0;
            var addButton = card.querySelector('[data-cart-add]');
            var stepper = card.querySelector('.qty-stepper');

            card.classList.toggle('is-in-cart', qty > 0);
            addButton.hidden = qty > 0;
            addButton.disabled = isFull && qty === 0;
            stepper.hidden = qty === 0;
            stepper.querySelector('[data-cart-qty]').textContent = qty;
            stepper.querySelector('[data-cart-step="1"]').disabled = isFull;
        });
    }

    function renderItems(isFull) {
        itemList.replaceChildren();

        Object.keys(cart).forEach(function (code) {
            var voucher = catalog[code];
            var item = itemTemplate.content.firstElementChild.cloneNode(true);

            item.dataset.code = code;
            item.classList.toggle('is-featured', voucher.featured);
            item.querySelector('[data-field="duration"]').textContent = voucher.duration;
            item.querySelector('[data-field="name"]').textContent = voucher.name;
            item.querySelector('[data-field="price"]').textContent = rupiah.format(voucher.price);
            item.querySelector('[data-field="subtotal"]').textContent = rupiah.format(voucher.price * cart[code]);
            item.querySelector('[data-cart-qty]').textContent = cart[code];
            item.querySelector('[data-cart-step="1"]').disabled = isFull;
            item.querySelector('[data-cart-remove]').setAttribute('aria-label', 'Hapus ' + voucher.name + ' dari keranjang');

            itemList.appendChild(item);
        });
    }

    function render() {
        var qty = totalQuantity();
        var isEmpty = qty === 0;
        var isFull = qty >= maxQty;
        var faceTotal = 0;
        var payTotal = 0;

        Object.keys(cart).forEach(function (code) {
            faceTotal += catalog[code].faceValue * cart[code];
            payTotal += catalog[code].price * cart[code];
        });

        document.querySelectorAll('[data-cart-count]').forEach(function (badge) {
            badge.textContent = qty;
            badge.hidden = isEmpty;
        });

        document.getElementById('summary-empty').classList.toggle('d-none', !isEmpty);
        document.getElementById('summary-filled').classList.toggle('d-none', isEmpty);
        document.getElementById('btn-cart').disabled = isEmpty;
        var hasSaving = faceTotal > payTotal;
        text('summary-qty', qty);
        text('summary-saving', rupiah.format(faceTotal - payTotal));
        document.getElementById('summary-saving-wrap').classList.toggle('d-none', !hasSaving);
        document.querySelectorAll('[data-saving-row]').forEach(function (row) {
            row.classList.toggle('d-none', !hasSaving);
        });
        text('summary-total', rupiah.format(payTotal));

        document.getElementById('cart-empty').classList.toggle('d-none', !isEmpty);
        document.getElementById('cart-footer').classList.toggle('d-none', isEmpty);
        document.getElementById('cart-limit').classList.toggle('d-none', !isFull);
        text('cart-count-text', qty);
        text('cart-face-total', rupiah.format(faceTotal));
        text('cart-saving', '−' + rupiah.format(faceTotal - payTotal));
        text('cart-total', rupiah.format(payTotal));

        renderCards(isFull);
        renderItems(isFull);
    }

    document.addEventListener('click', function (event) {
        var addButton = event.target.closest('[data-cart-add]');
        var stepButton = event.target.closest('[data-cart-step]');
        var removeButton = event.target.closest('[data-cart-remove]');
        var owner = event.target.closest('[data-code]');

        if (addButton && !addButton.disabled) {
            setQuantity(addButton.value, 1);
            bumpCounters();
            // Tombol tambah disembunyikan; pindahkan fokus ke stepper kartu yang sama.
            owner.querySelector('[data-cart-step="1"]').focus();
        } else if (stepButton && owner) {
            var code = owner.dataset.code;
            var isInDrawer = cartElement.contains(stepButton);
            var step = parseInt(stepButton.dataset.cartStep, 10);

            setQuantity(code, (cart[code] || 0) + step);
            if (step > 0) {
                bumpCounters();
            }
            restoreFocus(code, isInDrawer, stepButton.dataset.cartStep);
        } else if (removeButton && owner) {
            setQuantity(owner.dataset.code, 0);
            cartElement.focus();
        } else if (owner && owner.classList.contains('voucher-ticket') && !event.target.closest('.voucher-action')) {
            // Klik di area kartu (bukan tombol/stepper) = tambah 1 ke keranjang.
            var cardCode = owner.dataset.code;
            if (!catalog[cardCode] || totalQuantity() >= maxQty) {
                return;
            }
            setQuantity(cardCode, (cart[cardCode] || 0) + 1);
            bumpCounters();
        }
    });

    /**
     * Daftar & kartu dirender ulang setiap perubahan, jadi fokus keyboard
     * dikembalikan ke tombol yang setara setelah render.
     */
    function restoreFocus(code, isInDrawer, step) {
        var scope = isInDrawer
            ? itemList.querySelector('[data-code="' + code + '"]')
            : document.querySelector('.voucher-ticket[data-code="' + code + '"]');

        if (!scope) {
            cartElement.focus();
            return;
        }

        var target = scope.querySelector('[data-cart-step="' + step + '"]');
        if (!target || target.disabled || target.offsetParent === null) {
            target = scope.querySelector('[data-cart-add]') || scope.querySelector('[data-cart-step="-1"]');
        }
        target.focus();
    }

    document.getElementById('cart-clear').addEventListener('click', function () {
        cart = {};
        saveCart();
        render();
        cartElement.focus();
    });

    // Sinkron dengan tab lain yang membuka halaman yang sama.
    window.addEventListener('storage', function (event) {
        if (event.key === STORAGE_KEY) {
            cart = loadCart();
            render();
        }
    });

    render();
})();
