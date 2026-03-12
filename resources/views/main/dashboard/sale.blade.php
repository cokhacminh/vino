{{-- Sale Dashboard: Personal sales stats --}}
<div class="dashboard-stats">
    <div class="sale-stats-grid">
        {{-- Left: Thủy Sản --}}
        <div class="sale-stats-section">
            <div class="sale-section-header">
                <i class="fa-solid fa-fish"></i> Thủy Sản
            </div>
            <div class="stats-grid stats-grid-2">
                <div class="stat-card stat-blue">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-box-open"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Tổng Đơn Thủy Sản</span>
                        <span class="stat-value">{{ number_format($myOrders) }}</span>
                    </div>
                </div>
                <div class="stat-card stat-green">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Doanh Thu Thủy Sản</span>
                        <span class="stat-value">{{ number_format($myRevenue, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Tôm Giống --}}
        <div class="sale-stats-section">
            <div class="sale-section-header">
                <i class="fa-solid fa-shrimp"></i> Tôm Giống
            </div>
            <div class="stats-grid stats-grid-2">
                <div class="stat-card stat-cyan">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-shrimp"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Sản Lượng Giống</span>
                        <span class="stat-value">{{ number_format($mySeedSLTT) }}</span>
                    </div>
                </div>
                <div class="stat-card stat-orange">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Doanh Thu Tôm Giống</span>
                        <span class="stat-value">{{ number_format($mySeedRevenue, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI & Cuộc Gọi --}}
    <div class="kpi-call-row">
        {{-- Personal KPI --}}
        <div class="kpi-summary-card kpi-half">
            <h3><i class="fa-solid fa-bullseye"></i> KPI Tháng Này</h3>
            <div class="kpi-stats-row">
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
            </div>
            @if($myKpiTotal > 0)
            <div class="kpi-progress-bar-wrap">
                <div class="kpi-progress-bar" style="width: {{ round(($myKpiCompleted / $myKpiTotal) * 100) }}%"></div>
                <span class="kpi-progress-text">{{ round(($myKpiCompleted / $myKpiTotal) * 100) }}%</span>
            </div>
            @endif
        </div>

        {{-- Thống kê cuộc gọi --}}
        <div class="kpi-summary-card kpi-half call-stats-card">
            <div class="call-stats-header">
                <h3><i class="fa-solid fa-phone"></i> Thống Kê Cuộc Gọi</h3>
                <div class="call-stats-tabs">
                    <button class="call-tab active" onclick="switchCallTab('today', this)">Hôm Nay</button>
                    <button class="call-tab" onclick="switchCallTab('month', this)">Tháng Này</button>
                </div>
            </div>

            {{-- Tab Hôm Nay --}}
            <div class="call-tab-content" id="callTabToday">
                <div class="call-info-section">
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone" style="color:#3b82f6;"></i>
                        <span class="call-info-label">Tổng cuộc gọi:</span>
                        <span class="call-info-value">
                            <b>{{ $callTodayTotal }}</b> cuộc gọi,
                            <span style="color:#22c55e;"><b>{{ $callTodayAnswered }}</b> nghe máy</span>,
                            <span style="color:#ef4444;"><b>{{ $callTodayNotAnswered }}</b> không nghe máy</span>
                        </span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-clock" style="color:#06b6d4;"></i>
                        <span class="call-info-label">Tổng phút gọi:</span>
                        <span class="call-info-value"><b>{{ floor($callTodayDuration / 60) }}</b> phút <b>{{ $callTodayDuration % 60 }}</b> giây</span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone-slash" style="color:#ef4444;"></i>
                        <span class="call-info-label">Cuộc gọi nhỡ:</span>
                        <span class="call-info-value"><b style="color:#ef4444;">{{ $callTodayMissedIn }}</b> cuộc</span>
                    </div>
                </div>
                @if($callTodayMissedList->count() > 0)
                <div class="call-missed-list">
                    <div class="call-missed-title"><i class="fa-solid fa-list"></i> Danh sách cuộc gọi nhỡ</div>
                    <table class="call-missed-table">
                        <thead>
                            <tr><th>Số điện thoại</th><th>Thời gian gọi</th></tr>
                        </thead>
                        <tbody>
                            @foreach($callTodayMissedList as $missed)
                            <tr>
                                <td><b>{{ $missed->caller }}</b></td>
                                <td>{{ \Carbon\Carbon::parse($missed->started_at)->format('H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Tab Tháng Này --}}
            <div class="call-tab-content" id="callTabMonth" style="display:none;">
                <div class="call-info-section">
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone" style="color:#3b82f6;"></i>
                        <span class="call-info-label">Tổng cuộc gọi:</span>
                        <span class="call-info-value">
                            <b>{{ $callMonthTotal }}</b> cuộc gọi,
                            <span style="color:#22c55e;"><b>{{ $callMonthAnswered }}</b> nghe máy</span>,
                            <span style="color:#ef4444;"><b>{{ $callMonthNotAnswered }}</b> không nghe máy</span>
                        </span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-clock" style="color:#06b6d4;"></i>
                        <span class="call-info-label">Tổng phút gọi:</span>
                        <span class="call-info-value"><b>{{ floor($callMonthDuration / 60) }}</b> phút <b>{{ $callMonthDuration % 60 }}</b> giây</span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone-slash" style="color:#ef4444;"></i>
                        <span class="call-info-label">Cuộc gọi nhỡ:</span>
                        <span class="call-info-value"><b style="color:#ef4444;">{{ $callMonthMissedIn }}</b> cuộc</span>
                    </div>
                </div>
                @if($callMonthMissedList->count() > 0)
                <div class="call-missed-list">
                    <div class="call-missed-title"><i class="fa-solid fa-list"></i> Danh sách cuộc gọi nhỡ</div>
                    <table class="call-missed-table">
                        <thead>
                            <tr><th>Số điện thoại</th><th>Thời gian gọi</th></tr>
                        </thead>
                        <tbody>
                            @foreach($callMonthMissedList as $missed)
                            <tr>
                                <td><b>{{ $missed->caller }}</b></td>
                                <td>{{ \Carbon\Carbon::parse($missed->started_at)->format('H:i:s - d/m') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchCallTab(tab, btn) {
    document.getElementById('callTabToday').style.display = tab === 'today' ? '' : 'none';
    document.getElementById('callTabMonth').style.display = tab === 'month' ? '' : 'none';
    document.querySelectorAll('.call-tab').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
}
</script>
@endpush

@push('styles')
<style>
.kpi-call-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 16px;
}
.kpi-half { margin-top: 0 !important; }
.kpi-stats-row {
    display: flex;
    gap: 16px;
    margin-bottom: 12px;
}
.call-stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.call-stats-header h3 { margin: 0; }
.call-stats-tabs {
    display: flex;
    gap: 4px;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 3px;
}
.call-tab {
    border: none;
    background: transparent;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: #64748b;
    transition: all .2s;
}
.call-tab.active {
    background: #fff;
    color: #0ea5e9;
    box-shadow: 0 1px 3px rgba(0,0,0,.1);
}
.call-info-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.call-info-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}
.call-info-row i { width: 16px; text-align: center; }
.call-info-label {
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
}
.call-info-value { color: #475569; }
.call-missed-list {
    margin-top: 12px;
    border-top: 1px solid #e2e8f0;
    padding-top: 10px;
}
.call-missed-title {
    font-size: 12px;
    font-weight: 700;
    color: #ef4444;
    margin-bottom: 8px;
}
.call-missed-table {
    width: 100%;
    font-size: 12px;
    border-collapse: collapse;
    max-height: 120px;
}
.call-missed-table thead th {
    text-align: left;
    padding: 4px 8px;
    color: #64748b;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
    font-size: 11px;
}
.call-missed-table tbody td {
    padding: 5px 8px;
    border-bottom: 1px solid #f1f5f9;
}
.call-missed-table tbody tr:hover {
    background: #fef2f2;
}
@media (max-width: 768px) {
    .kpi-call-row { grid-template-columns: 1fr; }
}
</style>
@endpush
