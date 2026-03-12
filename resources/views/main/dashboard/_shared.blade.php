{{-- Bottom Section: KPI Deadlines (left) + Best Seller (right) --}}
<div class="dashboard-bottom-grid">
    {{-- Left: KPI Deadline List --}}
    @include('main.dashboard._kpi_deadlines')

    {{-- Right: Top 10 Best Seller --}}
    @include('main.dashboard._top10')
</div>
