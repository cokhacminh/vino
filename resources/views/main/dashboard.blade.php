@extends('main.layouts.app')
@section('title', 'Thống Kê')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px}
.stat-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;transition:all .3s;box-shadow:var(--shadow-sm)}
.stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg);border-color:var(--primary-light)}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px}
.stat-icon.purple{background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#7c3aed}
.stat-icon.blue{background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb}
.stat-icon.green{background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669}
.stat-icon.amber{background:linear-gradient(135deg,#fef3c7,#fde68a);color:#d97706}
.stat-icon.red{background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626}
.stat-value{font-size:26px;font-weight:800;color:var(--text);line-height:1.2}
.stat-label{font-size:13px;color:var(--text-secondary);margin-top:4px;font-weight:500}
.section-title{font-size:18px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px}
.section-title i{color:var(--primary)}
.data-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);box-shadow:var(--shadow-sm)}
.data-table th{background:#f8fafc;color:var(--text-secondary);font-weight:600;font-size:13px;padding:12px 16px;text-align:left;border-bottom:1px solid var(--border)}
.data-table td{padding:10px 16px;color:var(--text);font-size:14px;border-bottom:1px solid var(--border-light)}
.data-table tr:hover td{background:#f8fafc}
.data-table tr:last-child td{border-bottom:none}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:24px}
@media(max-width:768px){.two-col{grid-template-columns:1fr}}
.dashboard-month-picker .form-input{background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:8px 14px;font-size:14px;cursor:pointer}
</style>
@endpush
@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h2><i class="fa-solid fa-chart-line"></i> THỐNG KÊ TÌNH HÌNH</h2>
        <div class="dashboard-month-picker">
            <form method="GET" action="{{ route('dashboard') }}" id="monthForm">
                <input type="text" name="month" id="dashboardMonthPicker" value="{{ $month }}" class="form-input fp-month-input" readonly placeholder="Chọn tháng...">
            </form>
        </div>
    </div>
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fa-solid fa-users"></i></div>
            <div class="stat-value">{{ number_format($totalUsers) }}</div>
            <div class="stat-label">Nhân Viên</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-user-group"></i></div>
            <div class="stat-value">{{ number_format($totalCustomers) }}</div>
            <div class="stat-label">Khách Hàng</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-box-open"></i></div>
            <div class="stat-value">{{ number_format($ordersMonth) }}</div>
            <div class="stat-label">Đơn Hàng Tháng</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="stat-value">{{ number_format($revenueMonth) }}đ</div>
            <div class="stat-label">Doanh Thu Tháng</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fa-solid fa-wallet"></i></div>
            <div class="stat-value">{{ number_format($expensesMonth) }}đ</div>
            <div class="stat-label">Chi Phí Tháng</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fa-solid fa-cubes"></i></div>
            <div class="stat-value">{{ number_format($totalProducts) }}</div>
            <div class="stat-label">Sản Phẩm</div>
        </div>
    </div>
    <div class="two-col">
        <div>
            <div class="section-title"><i class="fa-solid fa-trophy"></i> Top 10 Best Seller</div>
            <table class="data-table">
                <thead><tr><th>#</th><th>Nhân Viên</th><th>Số Đơn</th><th>Doanh Thu</th></tr></thead>
                <tbody>
                    @forelse($topSellers as $i => $seller)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $seller->TenNV ?? 'N/A' }}</td>
                        <td>{{ number_format($seller->SoDonHang) }}</td>
                        <td style="color:#059669;font-weight:600">{{ number_format($seller->TongDoanhThu) }}đ</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-muted)">Chưa có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <div class="section-title"><i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b"></i> Cảnh Báo Tồn Kho Thấp</div>
            <table class="data-table">
                <thead><tr><th>Mã SP</th><th>Tên SP</th><th>Tồn Kho</th><th>ĐVT</th></tr></thead>
                <tbody>
                    @forelse($lowStock as $item)
                    <tr>
                        <td><code style="color:var(--primary);background:var(--primary-bg);padding:2px 8px;border-radius:6px">{{ $item->MaSP }}</code></td>
                        <td>{{ $item->TenSP }}</td>
                        <td style="color:#dc2626;font-weight:700">{{ number_format($item->SoLuong, 1) }}</td>
                        <td>{{ $item->DonViTinh ?? '' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-muted)">Không có cảnh báo</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr('#dashboardMonthPicker', {
        plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: 'Y-m', altFormat: 'F Y' })],
        locale: 'vn', altInput: true, altFormat: 'F Y', dateFormat: 'Y-m',
        defaultDate: '{{ $month }}',
        onChange: function() { document.getElementById('monthForm').submit(); }
    });
});
</script>
@endpush
