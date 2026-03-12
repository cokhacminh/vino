{{--
    Reusable Month Picker Component
    
    Usage:
        @include('main.components.month-picker', ['month' => $thang, 'year' => $nam])

    Required assets (add once per page):
        @push('styles')
        <link rel="stylesheet" href="{{ asset('css/main/month-picker.css') }}">
        @endpush

        @push('scripts')
        <script src="{{ asset('js/main/month-picker.js') }}"></script>
        <script>
            window.onMonthPickerSelect = function(month, year) {
                // Handle month selection here
            };
        </script>
        @endpush
--}}

<div class="kmp-wrap" id="kmpWrap" data-month="{{ $month }}" data-year="{{ $year }}">
    <button type="button" class="kmp-btn">
        <i class="fa-regular fa-calendar"></i>
        <span id="kmpLabel">Tháng {{ $month }}/{{ $year }}</span>
        <i class="fa-solid fa-chevron-down" style="font-size:10px; color:#94a3b8;"></i>
    </button>
    <div class="kmp-dropdown" id="kmpDropdown" style="display:none;"></div>
</div>
