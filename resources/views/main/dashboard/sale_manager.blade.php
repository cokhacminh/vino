{{-- Sale Manager Dashboard: Team sales management --}}
<div class="dashboard-stats">
    <div class="stats-grid stats-grid-4">
        <div class="stat-card stat-purple">
            <div class="stat-icon-wrap"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <span class="stat-label">Tổng Nhân Viên</span>
                <span class="stat-value">{{ number_format($teamMembers) }}</span>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon-wrap"><i class="fa-solid fa-box-open"></i></div>
            <div class="stat-info">
                <span class="stat-label">Đơn Hàng Tháng</span>
                <span class="stat-value">{{ number_format($ordersMonth) }}</span>
            </div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-icon-wrap"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="stat-info">
                <span class="stat-label">Doanh Thu Tháng</span>
                <span class="stat-value">{{ number_format($revenueMonth, 0, ',', '.') }}đ</span>
            </div>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-icon-wrap"><i class="fa-solid fa-user-plus"></i></div>
            <div class="stat-info">
                <span class="stat-label">Khách Hàng Mới</span>
                <span class="stat-value">{{ number_format($newCustomers) }}</span>
            </div>
        </div>
    </div>

    {{-- Order status summary --}}
    <div class="order-status-summary">
        <h3><i class="fa-solid fa-clipboard-list"></i> Trạng Thái Đơn Hàng Thủy Sản</h3>
        <div class="status-pills">
            <div class="status-pill s-pending">
                <span class="pill-label">Chưa Gửi</span>
                <span class="pill-value">{{ $orderStats['chua_gui'] ?? 0 }}</span>
            </div>
            <div class="status-pill s-shipping">
                <span class="pill-label">Đang Giao</span>
                <span class="pill-value">{{ $orderStats['dang_giao'] ?? 0 }}</span>
            </div>
            <div class="status-pill s-success">
                <span class="pill-label">Thành Công</span>
                <span class="pill-value">{{ $orderStats['thanh_cong'] ?? 0 }}</span>
            </div>
            <div class="status-pill s-failed">
                <span class="pill-label">Thất Bại</span>
                <span class="pill-value">{{ $orderStats['that_bai'] ?? 0 }}</span>
            </div>
            <div class="status-pill s-cancelled">
                <span class="pill-label">Đã Hủy</span>
                <span class="pill-value">{{ $orderStats['da_huy'] ?? 0 }}</span>
            </div>
            <div class="status-pill s-returned">
                <span class="pill-label">Hoàn Hàng</span>
                <span class="pill-value">{{ $orderStats['hoan_hang'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Seed orders --}}
    <div class="seed-order-summary">
        <div class="stats-grid stats-grid-1">
            <div class="stat-card stat-cyan">
                <div class="stat-icon-wrap"><i class="fa-solid fa-shrimp"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Đơn Tôm Giống Tháng</span>
                    <span class="stat-value">{{ number_format($seedOrdersMonth) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
