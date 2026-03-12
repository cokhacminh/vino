{{-- Kế Toán Dashboard: Accounting overview --}}
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
                        <span class="stat-value">{{ number_format($seedInvoiceCount) }}</span>
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


</div>
