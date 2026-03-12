{{-- Default Dashboard: Basic welcome --}}
<div class="dashboard-stats">
    <div class="dashboard-welcome">
        <div class="welcome-icon"><i class="fa-solid fa-hand-wave"></i></div>
        <h3>Xin chào, {{ $user->name }}!</h3>
        <p>Chúc bạn một ngày làm việc hiệu quả</p>
    </div>

    <div class="stats-grid stats-grid-2">
        <div class="stat-card stat-blue">
            <div class="stat-icon-wrap"><i class="fa-solid fa-box-open"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đơn Hàng Của Tôi</span>
                <span class="stat-value">{{ number_format($myOrders) }}</span>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon-wrap"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="stat-info">
                <span class="stat-label">Doanh Thu Của Tôi</span>
                <span class="stat-value">{{ number_format($myRevenue, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </div>

    {{-- KPI --}}
    <div class="kpi-summary-card">
        <h3><i class="fa-solid fa-bullseye"></i> KPI Tháng Này</h3>
        <div class="kpi-progress-row">
            <div class="kpi-stat">
                <span class="kpi-stat-number">{{ $myKpiTotal }}</span>
                <span class="kpi-stat-label">Tổng KPI</span>
            </div>
            <div class="kpi-stat completed">
                <span class="kpi-stat-number">{{ $myKpiCompleted }}</span>
                <span class="kpi-stat-label">Hoàn Thành</span>
            </div>
            <div class="kpi-stat pending">
                <span class="kpi-stat-number">{{ $myKpiTotal - $myKpiCompleted }}</span>
                <span class="kpi-stat-label">Chưa Hoàn Thành</span>
            </div>
            @if($myKpiTotal > 0)
            <div class="kpi-progress-bar-wrap">
                <div class="kpi-progress-bar" style="width: {{ round(($myKpiCompleted / $myKpiTotal) * 100) }}%"></div>
                <span class="kpi-progress-text">{{ round(($myKpiCompleted / $myKpiTotal) * 100) }}%</span>
            </div>
            @endif
        </div>
    </div>
</div>
