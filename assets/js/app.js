// === AppPane — JavaScript ===

document.addEventListener('DOMContentLoaded', function () {
    initNavbar();
    initDropdowns();
    initCountdown();
    initQuantityControls();
    initConfirmActions();
});

// --- Navbar mobile toggle ---
function initNavbar() {
    var toggler = document.getElementById('navToggler');
    var collapse = document.getElementById('navMain');
    if (!toggler || !collapse) return;
    toggler.addEventListener('click', function () {
        collapse.classList.toggle('show');
    });
}

// --- Dropdown toggle ---
function initDropdowns() {
    document.querySelectorAll('.dropdown-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var dropdown = btn.closest('.dropdown');
            var isOpen = dropdown.classList.contains('show');
            document.querySelectorAll('.dropdown.show').forEach(function (d) {
                d.classList.remove('show');
            });
            if (!isOpen) dropdown.classList.add('show');
        });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown.show').forEach(function (d) {
            d.classList.remove('show');
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown.show').forEach(function (d) {
                d.classList.remove('show');
            });
        }
    });
}

// --- Countdown Timer ---
function initCountdown() {
    var el = document.getElementById('countdown');
    if (!el) return;
    var deadline = el.dataset.deadline;
    if (!deadline) return;
    var target = new Date(deadline).getTime();

    function update() {
        var diff = target - Date.now();
        if (diff <= 0) {
            el.innerHTML = '<span style="color:#dc3545;font-weight:700">Ordini chiusi!</span>';
            document.querySelectorAll('.btn-aggiungi').forEach(function (btn) {
                btn.disabled = true;
                btn.textContent = 'Chiuso';
            });
            return;
        }
        var h = Math.floor(diff / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        el.innerHTML =
            '<span class="time-value">' + String(h).padStart(2, '0') + '</span><small>h</small> ' +
            '<span class="time-value">' + String(m).padStart(2, '0') + '</span><small>m</small> ' +
            '<span class="time-value">' + String(s).padStart(2, '0') + '</span><small>s</small>';
        setTimeout(update, 1000);
    }
    update();
}

// --- Quantity +/- Controls ---
function initQuantityControls() {
    document.querySelectorAll('.qty-control').forEach(function (control) {
        var input    = control.querySelector('input');
        var btnMinus = control.querySelector('.qty-minus');
        var btnPlus  = control.querySelector('.qty-plus');
        var max = parseInt(input.dataset.max) || 99;

        if (btnMinus) {
            btnMinus.addEventListener('click', function () {
                var val = parseInt(input.value) || 1;
                if (val > 1) { input.value = val - 1; input.dispatchEvent(new Event('change')); }
            });
        }
        if (btnPlus) {
            btnPlus.addEventListener('click', function () {
                var val = parseInt(input.value) || 1;
                if (val < max) { input.value = val + 1; input.dispatchEvent(new Event('change')); }
            });
        }
        if (input) {
            input.addEventListener('change', function () {
                var val = parseInt(this.value) || 1;
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
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });
}
