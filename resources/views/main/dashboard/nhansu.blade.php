{{-- Nhân Sự Dashboard: HR overview --}}
<div class="dashboard-stats">
    <div class="stats-grid stats-grid-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon-wrap"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <span class="stat-label">Tổng Nhân Viên</span>
                <span class="stat-value">{{ number_format($totalUsers) }}</span>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon-wrap"><i class="fa-solid fa-user-check"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đang Làm Việc</span>
                <span class="stat-value">{{ number_format($activeUsers) }}</span>
            </div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-icon-wrap"><i class="fa-solid fa-user-xmark"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đã Nghỉ Việc</span>
                <span class="stat-value">{{ number_format($deactiveUsers) }}</span>
            </div>
        </div>
    </div>



    {{-- KPI Overview --}}
    <div class="kpi-summary-card">
        <h3><i class="fa-solid fa-bullseye"></i> KPI Tổng Quát Tháng Này</h3>
        <div class="kpi-progress-row">
            <div class="kpi-stat">
                <span class="kpi-stat-number">{{ $kpiTotal }}</span>
                <span class="kpi-stat-label">Tổng KPI</span>
            </div>
            <div class="kpi-stat completed">
                <span class="kpi-stat-number">{{ $kpiCompleted }}</span>
                <span class="kpi-stat-label">Hoàn Thành</span>
            </div>
            <div class="kpi-stat pending">
                <span class="kpi-stat-number">{{ $kpiTotal - $kpiCompleted }}</span>
                <span class="kpi-stat-label">Chưa Hoàn Thành</span>
            </div>
            @if($kpiTotal > 0)
            <div class="kpi-progress-bar-wrap">
                <div class="kpi-progress-bar" style="width: {{ round(($kpiCompleted / $kpiTotal) * 100) }}%"></div>
                <span class="kpi-progress-text">{{ round(($kpiCompleted / $kpiTotal) * 100) }}%</span>
            </div>
            @endif
        </div>
    </div>
</div>
