/**
 * MONTH PICKER - Reusable Component
 *
 * Usage:
 *   1. Include month-picker.css & month-picker.js
 *   2. Add HTML: @include('main.components.month-picker', ['month' => $thang, 'year' => $nam])
 *   3. Define a callback: window.onMonthPickerSelect = function(month, year) { ... }
 *
 * The component auto-initialises on DOMContentLoaded for elements with
 * id="kmpWrap" / id="kmpDropdown" / id="kmpLabel".
 */

(function () {
    var _months = [
        'Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6',
        'Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'
    ];

    var _thang, _nam;

    function init() {
        var wrap = document.getElementById('kmpWrap');
        if (!wrap) return;

        _thang = parseInt(wrap.dataset.month) || new Date().getMonth() + 1;
        _nam   = parseInt(wrap.dataset.year)  || new Date().getFullYear();

        // Toggle button
        var btn = wrap.querySelector('.kmp-btn');
        if (btn) btn.addEventListener('click', toggle);

        // Close on outside click
        document.addEventListener('click', function (e) {
            var dd = document.getElementById('kmpDropdown');
            if (wrap && dd && !wrap.contains(e.target)) dd.style.display = 'none';
        });
    }

    function toggle() {
        var dd = document.getElementById('kmpDropdown');
        if (!dd) return;
        dd.style.display = dd.style.display === 'none' ? '' : 'none';
        if (dd.style.display !== 'none') render();
    }

    function render() {
        var dd = document.getElementById('kmpDropdown');
        if (!dd) return;

        var html = '<div class="kmp-header">';
        html += '<button type="button" class="kmp-nav" data-dir="-1">‹</button>';
        html += '<span>' + _nam + '</span>';
        html += '<button type="button" class="kmp-nav" data-dir="1">›</button>';
        html += '</div><div class="kmp-grid">';

        for (var m = 1; m <= 12; m++) {
            var cls = m === _thang ? 'kmp-month kmp-selected' : 'kmp-month';
            html += '<button type="button" class="' + cls + '" data-month="' + m + '">' + _months[m - 1] + '</button>';
        }
        html += '</div>';
        dd.innerHTML = html;

        // Bind nav buttons
        dd.querySelectorAll('.kmp-nav').forEach(function (b) {
            b.addEventListener('click', function (e) {
                e.stopPropagation();
                _nam += parseInt(this.dataset.dir);
                render();
            });
        });

        // Bind month buttons
        dd.querySelectorAll('.kmp-month').forEach(function (b) {
            b.addEventListener('click', function (e) {
                e.stopPropagation();
                _thang = parseInt(this.dataset.month);
                dd.style.display = 'none';

                // Update label
                var label = document.getElementById('kmpLabel');
                if (label) label.textContent = 'Tháng ' + _thang + '/' + _nam;

                // Fire callback
                if (typeof window.onMonthPickerSelect === 'function') {
                    window.onMonthPickerSelect(_thang, _nam);
                }
            });
        });
    }

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
