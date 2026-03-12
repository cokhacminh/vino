{{-- Admin Dashboard: Full system overview --}}
<div class="dashboard-stats">
    <div class="sale-stats-grid" style="grid-template-columns: 1fr 1fr 1fr;">
        {{-- Thủy Sản --}}
        <div class="sale-stats-section">
            <div class="sale-section-header">
                <i class="fa-solid fa-fish"></i> Thủy Sản
            </div>
            <div class="stats-grid stats-grid-1">
                <div class="stat-card stat-blue">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-box-open"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Số Đơn Hàng</span>
                        <span class="stat-value">{{ number_format($ordersMonth) }}</span>
                    </div>
                </div>
                <div class="stat-card stat-green">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Tổng Doanh Thu</span>
                        <span class="stat-value">{{ number_format($revenueMonth, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tôm Giống --}}
        <div class="sale-stats-section">
            <div class="sale-section-header">
                <i class="fa-solid fa-shrimp"></i> Tôm Giống
            </div>
            <div class="stats-grid stats-grid-1">
                <div class="stat-card stat-cyan">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-file-invoice"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Số Đơn Hàng</span>
                        <span class="stat-value">{{ number_format($seedOrdersMonth) }}</span>
                    </div>
                </div>
                <div class="stat-card stat-orange">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Tổng Doanh Thu</span>
                        <span class="stat-value">{{ number_format($seedRevenueMonth, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chi Phí --}}
        <div class="sale-stats-section">
            <div class="sale-section-header">
                <i class="fa-solid fa-receipt"></i> Chi Phí
            </div>
            <div class="stats-grid stats-grid-1">
                <div class="stat-card stat-red">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Số Phiếu Chi</span>
                        <span class="stat-value">{{ number_format($expenseCount) }}</span>
                    </div>
                </div>
                <div class="stat-card stat-red">
                    <div class="stat-icon-wrap"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Tổng Tiền Chi</span>
                        <span class="stat-value">{{ number_format($expensesMonth, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Trạng thái đơn hàng + Thống kê cuộc gọi --}}
    <div class="sale-stats-grid" style="margin-top:16px;">
        {{-- Trạng thái đơn hàng gộp --}}
        <div class="order-status-summary">
            <h3><i class="fa-solid fa-clipboard-list"></i> Trạng Thái Đơn Hàng</h3>
            <div class="order-status-cols">
                <div class="order-status-col">
                    <div class="os-col-header"><i class="fa-solid fa-fish"></i> Thủy Sản</div>
                    <div class="os-row"><span>Chưa Gửi</span><b>{{ $orderStats['chua_gui'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Đang Giao</span><b>{{ $orderStats['dang_giao'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Thành Công</span><b>{{ $orderStats['thanh_cong'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Thất Bại</span><b>{{ $orderStats['that_bai'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Đã Hủy</span><b>{{ $orderStats['da_huy'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Hoàn Hàng</span><b>{{ $orderStats['hoan_hang'] ?? 0 }}</b></div>
                </div>
                <div class="order-status-col">
                    <div class="os-col-header"><i class="fa-solid fa-shrimp"></i> Tôm Giống</div>
                    <div class="os-row"><span>Chờ Giao Giống</span><b>{{ $seedOrderStats['cho_giao'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Đã Giao Giống</span><b>{{ $seedOrderStats['da_giao'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Đã Huỷ Đơn</span><b>{{ $seedOrderStats['da_huy'] ?? 0 }}</b></div>
                    <div class="os-row"><span>Xả Bỏ</span><b>{{ $seedOrderStats['xa_bo'] ?? 0 }}</b></div>
                </div>
            </div>
        </div>

        {{-- Thống kê cuộc gọi --}}
        <div class="order-status-summary admin-call-stats">
            <div class="call-stats-header">
                <h3><i class="fa-solid fa-phone"></i> Thống Kê Cuộc Gọi</h3>
                <div class="call-stats-tabs">
                    <button class="call-tab active" onclick="switchAdminCallTab('today', this)">Hôm Nay</button>
                    <button class="call-tab" onclick="switchAdminCallTab('month', this)">Tháng Này</button>
                </div>
            </div>

            {{-- Tab Hôm Nay --}}
            <div class="call-tab-content" id="adminCallToday">
                <div class="call-info-section">
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone" style="color:#3b82f6;"></i>
                        <span class="call-info-label">Tổng:</span>
                        <span class="call-info-value">
                            <b>{{ $adminCallTodayTotal }}</b> cuộc,
                            <span style="color:#22c55e;"><b>{{ $adminCallTodayAnswered }}</b> nghe</span>,
                            <span style="color:#ef4444;"><b>{{ $adminCallTodayNotAnswered }}</b> không nghe</span>
                        </span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-clock" style="color:#06b6d4;"></i>
                        <span class="call-info-label">Tổng phút:</span>
                        <span class="call-info-value"><b>{{ floor($adminCallTodayDuration / 60) }}</b>p <b>{{ $adminCallTodayDuration % 60 }}</b>s</span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone-slash" style="color:#ef4444;"></i>
                        <span class="call-info-label">Gọi nhỡ:</span>
                        <span class="call-info-value"><b style="color:#ef4444;">{{ $adminCallTodayMissedIn }}</b> cuộc</span>
                        @if($adminCallTodayMissedIn > 0)
                        <button class="btn-missed-toggle" onclick="toggleMissedList('today', this)">Xem Danh Sách Cuộc Gọi Nhỡ</button>
                        @endif
                    </div>
                </div>
                <div id="todayUserTable">
                @if($adminCallTodayByUser->count() > 0)
                <table class="admin-call-table">
                    <thead>
                        <tr>
                            <th>Nhân Viên</th>
                            <th style="text-align:center;">Tổng</th>
                            <th style="text-align:center;color:#22c55e;">Nghe</th>
                            <th style="text-align:center;color:#ef4444;">Không</th>
                            <th style="text-align:center;">Phút Gọi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adminCallTodayByUser as $u)
                        <tr>
                            <td><b>{{ $u->name }}</b></td>
                            <td style="text-align:center;">{{ $u->total }}</td>
                            <td style="text-align:center;color:#22c55e;">{{ $u->answered }}</td>
                            <td style="text-align:center;color:#ef4444;">{{ $u->not_answered }}</td>
                            <td style="text-align:center;">{{ floor($u->total_duration / 60) }}p</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                </div>
                <div id="todayMissedTable" style="display:none;">
                @if($adminCallTodayMissedList->count() > 0)
                <table class="admin-call-table">
                    <thead>
                        <tr>
                            <th>Số Gọi</th>
                            <th>Gọi Đến</th>
                            <th style="text-align:center;">Giờ Gọi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adminCallTodayMissedList as $m)
                        <tr>
                            <td><b>{{ $m->caller }}</b></td>
                            <td>{{ $m->user_name }}</td>
                            <td style="text-align:center;">{{ \Carbon\Carbon::parse($m->started_at)->format('H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div style="text-align:center;color:#94a3b8;font-size:12px;padding:8px;">Không có cuộc gọi nhỡ</div>
                @endif
                </div>
            </div>

            {{-- Tab Tháng Này --}}
            <div class="call-tab-content" id="adminCallMonth" style="display:none;">
                <div class="call-info-section">
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone" style="color:#3b82f6;"></i>
                        <span class="call-info-label">Tổng:</span>
                        <span class="call-info-value">
                            <b>{{ $adminCallMonthTotal }}</b> cuộc,
                            <span style="color:#22c55e;"><b>{{ $adminCallMonthAnswered }}</b> nghe</span>,
                            <span style="color:#ef4444;"><b>{{ $adminCallMonthNotAnswered }}</b> không nghe</span>
                        </span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-clock" style="color:#06b6d4;"></i>
                        <span class="call-info-label">Tổng phút:</span>
                        <span class="call-info-value"><b>{{ floor($adminCallMonthDuration / 60) }}</b>p <b>{{ $adminCallMonthDuration % 60 }}</b>s</span>
                    </div>
                    <div class="call-info-row">
                        <i class="fa-solid fa-phone-slash" style="color:#ef4444;"></i>
                        <span class="call-info-label">Gọi nhỡ:</span>
                        <span class="call-info-value"><b style="color:#ef4444;">{{ $adminCallMonthMissedIn }}</b> cuộc</span>
                        @if($adminCallMonthMissedIn > 0)
                        <button class="btn-missed-toggle" onclick="toggleMissedList('month', this)">Xem Danh Sách</button>
                        @endif
                    </div>
                </div>
                <div id="monthUserTable">
                @if($adminCallMonthByUser->count() > 0)
                <table class="admin-call-table">
                    <thead>
                        <tr>
                            <th>Nhân Viên</th>
                            <th style="text-align:center;">Tổng</th>
                            <th style="text-align:center;color:#22c55e;">Nghe</th>
                            <th style="text-align:center;color:#ef4444;">Không</th>
                            <th style="text-align:center;">Phút</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adminCallMonthByUser as $u)
                        <tr>
                            <td><b>{{ $u->name }}</b></td>
                            <td style="text-align:center;">{{ $u->total }}</td>
                            <td style="text-align:center;color:#22c55e;">{{ $u->answered }}</td>
                            <td style="text-align:center;color:#ef4444;">{{ $u->not_answered }}</td>
                            <td style="text-align:center;">{{ floor($u->total_duration / 60) }}p</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                </div>
                <div id="monthMissedTable" style="display:none;">
                @if($adminCallMonthMissedList->count() > 0)
                <table class="admin-call-table">
                    <thead>
                        <tr>
                            <th>Số Gọi</th>
                            <th>Gọi Đến</th>
                            <th style="text-align:center;">Giờ Gọi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adminCallMonthMissedList as $m)
                        <tr>
                            <td><b>{{ $m->caller }}</b></td>
                            <td>{{ $m->user_name }}</td>
                            <td style="text-align:center;">{{ \Carbon\Carbon::parse($m->started_at)->format('H:i:s - d/m') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div style="text-align:center;color:#94a3b8;font-size:12px;padding:8px;">Không có cuộc gọi nhỡ</div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchAdminCallTab(tab, btn) {
    document.getElementById('adminCallToday').style.display = tab === 'today' ? '' : 'none';
    document.getElementById('adminCallMonth').style.display = tab === 'month' ? '' : 'none';
    document.querySelectorAll('.admin-call-stats .call-tab').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
}
function toggleMissedList(tab, btn) {
    var userTbl = document.getElementById(tab + 'UserTable');
    var missedTbl = document.getElementById(tab + 'MissedTable');
    var showingMissed = missedTbl.style.display !== 'none';
    userTbl.style.display = showingMissed ? '' : 'none';
    missedTbl.style.display = showingMissed ? 'none' : '';
    btn.textContent = showingMissed ? 'Xem Danh Sách Cuộc Gọi Nhỡ' : 'Xem Thống Kê Cuộc Gọi Nhân Viên';
}
</script>
@endpush

@push('styles')
<style>
.admin-call-stats .call-stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.admin-call-stats .call-stats-header h3 { margin: 0; }
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
    gap: 6px;
    margin-bottom: 12px;
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
.admin-call-table {
    width: 100%;
    font-size: 12px;
    border-collapse: collapse;
}
.admin-call-table thead th {
    text-align: left;
    padding: 6px 8px;
    color: #64748b;
    font-weight: 600;
    border-bottom: 2px solid #e2e8f0;
    font-size: 11px;
}
.admin-call-table tbody td {
    padding: 6px 8px;
    border-bottom: 1px solid #f1f5f9;
}
.admin-call-table tbody tr:hover {
    background: #f8fafc;
}
.btn-missed-toggle {
    border: none;
    background: #fef2f2;
    color: #ef4444;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 6px;
    cursor: pointer;
    margin-left: 6px;
    transition: all .2s;
}
.btn-missed-toggle:hover {
    background: #ef4444;
    color: #fff;
}
.order-status-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.os-col-header {
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    padding-bottom: 6px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 4px;
}
.os-col-header i { margin-right: 4px; }
.os-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 13px;
    border-bottom: 1px solid #f1f5f9;
}
.os-row span { color: #475569; }
.os-row b { color: #1e293b; }
</style>
@endpush
