---
description: How to implement a month/year picker input in a Blade view
---

# Month Picker Rule

Whenever a form input needs to select a **month** (or month/year), **NEVER** use `<input type="month">`. Instead, always use **flatpickr** with the **monthSelect plugin**.

## Required Assets

Include these in the Blade view (inline, since the layout does not support `@stack`):

```html
<!-- CSS (before the content) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
    .flatpickr-calendar { border-radius:12px !important; box-shadow:0 8px 24px rgba(0,0,0,0.12) !important; border:2px solid #e2e8f0 !important; }
    .flatpickr-monthSelect-month { border-radius:8px !important; }
    .flatpickr-monthSelect-month.selected { background:#6d28d9 !important; }
</style>

<!-- JS (after the content) -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
```

## HTML Input

Use a text input with `readonly` attribute and the CSS class `fp-month-input`:

```html
<input type="text" name="month" id="myMonthPicker" value="{{ $month }}" class="form-input fp-month-input" readonly placeholder="Chọn tháng...">
```

## JavaScript Initialization

```javascript
flatpickr('#myMonthPicker', {
    plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: 'Y-m', altFormat: 'F Y' })],
    locale: 'vn',
    altInput: true,
    altFormat: 'F Y',
    dateFormat: 'Y-m',
    defaultDate: '{{ $month }}',  // pass the current value from Blade
    onChange: function(selectedDates, dateStr) {
        // Submit form or handle change
        document.getElementById('monthForm').submit();
    }
});
```

## Key Points

- `dateFormat: 'Y-m'` sends `2026-02` format to the server
- `altFormat: 'F Y'` displays "Tháng Hai 2026" (localized) to the user
- Always set `readonly` on the input so users can't type manually
- Always set `locale: 'vn'` for Vietnamese month names
- The `.fp-month-input` CSS class is defined in `public/css/main/main.css`

## Reference Implementation

See `resources/views/main/dashboard.blade.php` for a working example.
