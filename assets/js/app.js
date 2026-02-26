// === AppPane — JavaScript ===

document.addEventListener('DOMContentLoaded', function () {
    initCountdown();
    initQuantityControls();
    initConfirmActions();
});

// --- Countdown Timer ---
function initCountdown() {
    const el = document.getElementById('countdown');
    if (!el) return;

    const deadline = el.dataset.deadline;
    if (!deadline) return;

    const target = new Date(deadline).getTime();

    function update() {
        const now = Date.now();
        const diff = target - now;

        if (diff <= 0) {
            el.innerHTML = '<span class="text-danger fw-bold">Ordini chiusi!</span>';
            // Disable all add-to-cart buttons
            document.querySelectorAll('.btn-aggiungi').forEach(btn => {
                btn.disabled = true;
                btn.textContent = 'Chiuso';
            });
            return;
        }

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        el.innerHTML =
            '<span class="time-value">' + String(hours).padStart(2, '0') + '</span><small>h</small> ' +
            '<span class="time-value">' + String(minutes).padStart(2, '0') + '</span><small>m</small> ' +
            '<span class="time-value">' + String(seconds).padStart(2, '0') + '</span><small>s</small>';

        setTimeout(update, 1000);
    }

    update();
}

// --- Quantity +/- Controls ---
function initQuantityControls() {
    document.querySelectorAll('.qty-control').forEach(function (control) {
        const input = control.querySelector('input');
        const btnMinus = control.querySelector('.qty-minus');
        const btnPlus = control.querySelector('.qty-plus');
        const max = parseInt(input.dataset.max) || 99;

        if (btnMinus) {
            btnMinus.addEventListener('click', function () {
                let val = parseInt(input.value) || 1;
                if (val > 1) {
                    input.value = val - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }

        if (btnPlus) {
            btnPlus.addEventListener('click', function () {
                let val = parseInt(input.value) || 1;
                if (val < max) {
                    input.value = val + 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }

        if (input) {
            input.addEventListener('change', function () {
                let val = parseInt(this.value) || 1;
                if (val < 1) val = 1;
                if (val > max) val = max;
                this.value = val;
            });
        }
    });
}

// --- Confirm Actions ---
function initConfirmActions() {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
}
