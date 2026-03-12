@extends('main.layouts.app')
@section('title', 'Đơn Đặt Giống')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .sg-page {padding: 10px;background: white;border-radius: 8px;box-shadow: 0 4px 12px rgba(0,0,0,0.15);}
    .sg-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 16px; flex-wrap: wrap; gap: 12px;
    }
    .sg-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
    .sg-header-btn {
        padding: 8px 18px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #22c55e, #16a34a); color: white;
        font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s;height: 36px;
    }
    .sg-header-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(34,197,94,0.4); }

    /* Date range picker */
    .sg-date-wrap {
        display: flex; align-items: center; gap: 6px; margin-bottom: 14px;
    }
    .sg-date-input {
        padding: 7px 12px; border: 2px solid #3b82f6; border-radius: 6px;
        font-size: 13px; width: 220px; cursor: pointer; background: white; color: #1e293b;
        font-weight: 600;
    }
    .sg-date-presets {
        position: absolute; top: 100%; left: 0; background: white; border: 2px solid #3b82f6;
        border-radius: 8px; overflow: hidden; z-index: 999; min-width: 160px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15); display:none;
    }
    .sg-date-presets.open { display: block; }
    .sg-date-preset {
        padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer;
        border-bottom: 1px solid #f1f5f9; color: #1e293b; transition: all 0.15s;
    }
    .sg-date-preset:hover { background: #3b82f6; color: white; }
    .sg-date-preset:last-child { border-bottom: none; }

    /* Status Tabs */
    .sg-tabs { display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap; }
    .sg-tab {
        padding: 8px 14px; border-radius: 8px; cursor: pointer;
        font-size: 12px; font-weight: 600; border: 2px solid #e2e8f0;
        background: white; color: #64748b; transition: all 0.2s; text-decoration: none;height: 16px;
    }
    .sg-tab:hover {
        border-color: #94a3b8; transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 12px rgba(59,130,246,0.15);
    }
    .sg-tab:active { transform: translateY(0) scale(0.97); box-shadow: none; }
    .sg-tab.active {
        border-color: #3b82f6; background: #eff6ff; color: #2563eb;
        box-shadow: 0 2px 8px rgba(59,130,246,0.2);
        animation: sgTabPop 0.3s ease;
    }
    @keyframes sgTabPop {
        0% { transform: scale(0.95); }
        50% { transform: scale(1.06); }
        100% { transform: scale(1); }
    }
    .sg-tab .sg-count {
        display: inline-block; min-width: 18px; padding: 1px 6px;
        border-radius: 10px; font-size: 10px; margin-left: 4px;
        background: #e2e8f0; color: #475569; text-align: center;
    }
    .sg-tab.active .sg-count { background: #3b82f6; color: white; }

    /* Controls */
    .sg-controls {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 14px; flex-wrap: wrap; gap: 10px;
    }
    .sg-controls-left { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; }
    .sg-controls-left select {
        padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;
    }
    .sg-search {
        padding: 7px 14px; border: 1px solid #d1d5db; border-radius: 8px;
        font-size: 13px; width: 260px; outline: none;
    }
    .sg-search:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }

    /* Table */
    .sg-table-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; }
    .sg-table {
        width: 100%; border-collapse: collapse; font-size: 12.5px;
    }
    .sg-table thead th {
        background: #334155; color: white; padding: 10px 10px;
        font-weight: 600; text-align: left; white-space: nowrap;
        border-right: 1px solid #475569; position: sticky; top: 0;font-size:18px
    }
    .sg-table thead th:nth-child(1),
    .sg-table thead th:nth-child(3),
    .sg-table thead th:nth-child(4){
        text-align: center;
    }
    .sg-table thead th:last-child { border-right: none;text-align: center; }
    .sg-table thead th i { margin-left: 3px; opacity: 0.5; font-size: 10px; }
    .sg-table tbody tr { border-bottom: 1px solid #e2e8f0; }
    .sg-table tbody tr:hover { background: #f8fafc; }
    .sg-table tbody tr:nth-child(even) { background: #fafbfc; }
    .sg-table tbody tr:nth-child(even):hover { background: #f0f4f8; }
    .sg-table td { font-size:17px;padding: 12px 10px; vertical-align: middle; }

    /* Columns */
    .sg-col-madh { width: 90px; text-align: center; }
    .sg-col-madh .madh { font-weight: 700; color: #1e293b; }
    .sg-col-madh .nv { color: #dc2626; font-weight: 600; }
    .sg-col-madh .ngay { color: #64748b; }

    .sg-col-kh { min-width: 180px; }
    .sg-col-kh .kh-name { font-weight: 700; color: #1e293b; }
    .sg-col-kh .kh-phone { color: #475569; }
    .sg-col-kh .kh-addr { color: #64748b; line-height: 1.4; margin-top: 2px; }

    .sg-col-kv { width: 100px; text-align: center; }
    .sg-col-kv .kv-tinh { font-weight: 700; color: #1e293b; }
    .sg-col-kv .kv-doman { color: #dc2626; font-weight: 600;margin-top: 6px; }

    .sg-col-sl { width: 130px; text-align: center; }
    .sg-col-sl .sl-main {
        font-size: 20px; font-weight: 800; color: #dc2626;
    }
    .sg-col-sl .sl-detail {
        font-size: 16px; color: #64748b; margin-top: 2px;
        border-top: 1px dashed #cbd5e1; padding-top: 3px;
    }

    .sg-col-tt { min-width: 100px; }
    .sg-col-tt .tt-text { font-size: 12px; color: #334155; white-space: pre-line; }

    .sg-col-ng { width: 80px; }
    .sg-col-ng .ng-text { font-size: 12px; color: #334155; white-space: pre-line; }

    .sg-col-actions { width: 140px; text-align: center; }

    /* Status badges */
    .sg-badge {
        display: inline-block; padding: 8px 10px; border-radius: 4px;
        font-size: 14px; font-weight: 700; white-space: nowrap;
    }
    .sg-badge-cho { background: #3b82f6; color: white; }
    .sg-badge-dagiao { background: #22c55e; color: white; }
    .sg-badge-dahuy { background: #ef4444; color: white; }
    .sg-badge-xabo { background: #f97316; color: white; }
    .sg-badge-thanhtoan { background: #dc2626; color: white; font-size: 10px; margin-top: 3px; }
    .sg-badge-chamkhach { background: #22c55e; color: white; font-size: 10px; margin-top: 3px; }

    /* Action buttons grid */
    .sg-actions-grid {
        display: flex; gap: 4px; flex-wrap: wrap; justify-content: center;
        margin-top: 5px;
    }
    .sg-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 35px; height: 30px; border-radius: 4px; border: none;
        cursor: pointer; font-size: 17px; transition: all 0.15s;
    }
    .sg-btn:hover { transform: scale(1.15); }
    .sg-btn-edit { background: #22c55e; color: white; }
    .sg-btn-status { background: #f59e0b; color: white; }
    .sg-btn-delete { background: #ef4444; color: white; }
    .sg-btn-note { background: #3b82f6; color: white; }
    .sg-btn-deliver { background: #3b82f6; color: white; }
    .sg-btn-cancel { background: #f97316; color: white; }
    .sg-btn-payment { background: #8b5cf6; color: white; }
    .sg-btn-history { background: #06b6d4; color: white; }

    /* Pagination */
    .sg-pagination {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 14px; flex-wrap: wrap; gap: 10px;
    }
    .sg-pg-info { font-size: 13px; color: #64748b; }
    .sg-pg-btns { display: flex; gap: 4px; }
    .sg-pg-btn {
        padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;
        background: white; font-size: 12px; cursor: pointer;
    }
    .sg-pg-btn:hover { background: #f1f5f9; }
    .sg-pg-btn.active { background: #3b82f6; color: white; border-color: #3b82f6; }

    /* Empty */
    .sg-empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .sg-empty .empty-icon { font-size: 48px; margin-bottom: 12px; }

    /* Create/Edit Modal */
    .sg-modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 9999;
        display: none; justify-content: center; align-items: flex-start; padding: 30px 10px;
        overflow-y: auto;
    }
    .sg-modal-overlay.show { display: flex; }
    .sg-modal {
        background: white; border-radius: 12px; width: 100%; max-width: 780px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;
    }
    .sg-modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 24px; border-bottom: 1px solid #e2e8f0;
    }
    .sg-modal-header h3 { margin: 0; font-size: 17px; font-weight: 700; color: #1e293b; }
    .sg-modal-close {
        background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8;
        line-height: 1;
    }
    .sg-modal-close:hover { color: #ef4444; }
    .sg-modal-body { padding: 24px; }
    .sg-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px; }
    .sg-form-group { }
    .sg-form-group label {
        display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 5px;
    }
    .sg-form-group input, .sg-form-group select, .sg-form-group textarea {
        width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 8px;
        font-size: 13px; outline: none; box-sizing: border-box;
    }
    .sg-form-group input:focus, .sg-form-group select:focus, .sg-form-group textarea:focus {
        border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.12);
    }
    .sg-form-group textarea { resize: vertical; }
    .sg-form-calc {
        display: flex; align-items: center; gap: 6px; flex-wrap: nowrap;
        margin-bottom: 14px; padding: 8px; background: #f8fafc; border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .sg-form-calc .calc-field { flex: none; }
    .sg-form-calc .calc-field label {
        font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 4px; display: block; white-space: nowrap;
    }
    .sg-form-calc .calc-field input {
        width: 120px; padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 6px;
        font-size: 13px; text-align: center; box-sizing: border-box;
    }
    .sg-form-calc .calc-field input:focus {
        border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.12); outline: none;
    }
    .sg-km-group { display: flex; align-items: center; gap: 1px; }
    .sg-km-group label.km-label { font-size: 12px; font-weight: 700; color: #475569; margin-right: 2px; }
    .sg-km-group .km-option {
        display: flex !important;height: 25px; align-items: center; gap: 3px; font-size: 13px !important; font-weight: 600; color: #334155;
    }
    .sg-km-group .km-option input[type="radio"] { accent-color: #3b82f6;width: 20px;height: 20px;}
    .sg-thanh-tien {
        padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 6px;
        font-size: 14px; font-weight: 700; text-align: center; color: #dc2626;
        background: #fef2f2; width: 120px;
    }
    .sg-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 14px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc;
    }
    .sg-modal-btn {
        padding: 9px 22px; border: none; border-radius: 8px;
        font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s;
    }
    .sg-modal-btn-cancel { background: #ef4444; color: white; }
    .sg-modal-btn-cancel:hover { background: #dc2626; }
    .sg-modal-btn-submit { background: #3b82f6; color: white; }
    .sg-modal-btn-submit:hover { background: #2563eb; }
    tbody#sgHistoryBody {
        font-size: 16px;
    }
</style>
@endpush

@section('content')
<div class="sg-page">
    <div class="sg-header">
        <h2><i class="fa-solid fa-seedling" style="color:#22c55e;"></i> Đơn Đặt Giống</h2>
        
    </div>

    {{-- Status Tabs + Date Range Filter --}}
    <div class="sg-tabs">
        <div class="sg-date-wrap" style="position:relative;">
            <input type="text" class="sg-date-input" id="sgDateRange" readonly
                   value="{{ $dateFrom && $dateTo ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : 'Chọn khoảng thời gian' }}"
                   onclick="toggleDatePresets()">
            <div class="sg-date-presets" id="sgDatePresets">
                <div class="sg-date-preset" onclick="setDatePreset('week')">Trong 1 Tuần</div>
                <div class="sg-date-preset" onclick="setDatePreset('thisMonth')">Tháng Này</div>
                <div class="sg-date-preset" onclick="setDatePreset('lastMonth')">Tháng Trước</div>
                <div class="sg-date-preset" onclick="setDatePreset('30days')">Trong 30 Ngày</div>
                <div class="sg-date-preset" onclick="setDatePreset('thisYear')">Trong Năm Nay</div>
                <div class="sg-date-preset" onclick="setDatePreset('custom')">Chọn Ngày</div>
            </div>
            {{-- Hidden flatpickr for custom range --}}
            <input type="text" id="sgFlatpickr" style="position:absolute; opacity:0; pointer-events:none; width:1px; height:1px;">
        </div>
        <a href="{{ route('seedOrders.index', array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo])) }}" class="sg-tab {{ $statusFilter == 'all' ? 'active' : '' }}">
            Tất cả <span class="sg-count">{{ $allCount }}</span>
        </a>
        @foreach($statusCounts as $status => $count)
        <a href="{{ route('seedOrders.index', array_filter(['status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}" class="sg-tab {{ $statusFilter == $status ? 'active' : '' }}">
            {{ $status }} <span class="sg-count">{{ $count }}</span>
        </a>
        @endforeach
        <button class="sg-header-btn" onclick="openSgCreate()"><i class="fa-solid fa-plus"></i> Thêm Đơn Hàng</button>
    </div>

    {{-- Controls --}}
    <div class="sg-controls">
        <div class="sg-controls-left">
            Hiển thị
            <select id="pageSize" onchange="changeSgPageSize()">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50" selected>50</option>
                <option value="100">100</option>
            </select>
            dòng
        </div>
        <div>
            <label style="font-size:13px; color:#64748b;">Lọc nhanh:</label>
            <input type="text" class="sg-search" id="sgSearch" placeholder="Tìm mã đơn, khách hàng, SĐT, tỉnh..." oninput="filterSgTable()">
        </div>
    </div>

    {{-- Table --}}
    <div class="sg-table-wrap">
        <table class="sg-table" id="sgTable">
            <thead>
                <tr>
                    <th>Mã đơn <i class="fa-solid fa-sort"></i></th>
                    <th>Khách hàng <i class="fa-solid fa-sort"></i></th>
                    <th>Khu Vực <i class="fa-solid fa-sort"></i></th>
                    <th>Số Lượng</th>
                    <th>Tình Trạng <i class="fa-solid fa-sort"></i></th>
                    <th>Nhận Giống <i class="fa-solid fa-sort"></i></th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php
                    $ngay = $order->NgayGiao ? \Carbon\Carbon::parse($order->NgayGiao)->format('d/m/y') : '';
                    $fullAddr = trim(implode(' , ', array_filter([$order->DiaChi, $order->Xa, $order->Huyen])));
                    $doman = $order->DoMan;
                    $domanStr = '';
                    if ($doman !== null) {
                        if ($doman < 10) $domanStr = $doman . '‰';
                        else $domanStr = $doman . '%';
                    }
                    $badgeClass = 'sg-badge-cho';
                    if ($order->TrangThai === 'Đã Giao Giống') $badgeClass = 'sg-badge-dagiao';
                    elseif ($order->TrangThai === 'Đã Huỷ Đơn') $badgeClass = 'sg-badge-dahuy';
                    elseif ($order->TrangThai === 'Xả Bỏ') $badgeClass = 'sg-badge-xabo';
                @endphp
                <tr class="sg-row"
                    data-search="{{ strtolower($order->MaDH . ' ' . ($order->TenKH ?? '') . ' ' . ($order->SoDienThoai ?? '') . ' ' . ($order->Tinh ?? '') . ' ' . ($order->TenNV ?? '')) }}"
                    data-date="{{ $order->NgayGiao ?? '' }}">
                    <td class="sg-col-madh">
                        <div class="madh">{{ $order->MaDH }}</div>
                        <div class="nv">{{ $order->TenNV ?? '' }}</div>
                        <div class="ngay">{{ $ngay }}</div>
                    </td>
                    <td class="sg-col-kh">
                        <div class="kh-name">{{ $order->TenKH ?? '—' }}</div>
                        <div class="kh-phone">{{ $order->SoDienThoai ?? '' }}</div>
                        @if($fullAddr)
                            <div class="kh-addr">{{ $fullAddr }}</div>
                        @endif
                    </td>
                    <td class="sg-col-kv">
                        <div class="kv-tinh">{{ $order->Tinh ?? '—' }}</div>
                        @if($domanStr)
                            <div class="kv-doman">{{ $domanStr }}</div>
                        @endif
                    </td>
                    <td class="sg-col-sl">
                        <div class="sl-main">{{ number_format($order->SoLuong ?? 0, 0, ',', ',') }}</div>
                        <div class="sl-detail">
                            {{ number_format($order->SLTT ?? 0, 0, ',', ',') }} x {{ number_format($order->GiaBan ?? 0, 0, ',', ',') }}<br>
                            {{ number_format($order->TongTien ?? 0, 0, ',', ',') }}
                        </div>
                    </td>
                    <td class="sg-col-tt">
                        <div class="tt-text">{{ $order->TinhTrangHienTai ?? '' }}</div>
                    </td>
                    <td class="sg-col-ng">
                        <div class="ng-text">{{ $order->NhanGiong ?? '' }}</div>
                    </td>
                    <td class="sg-col-actions">
                        <span class="sg-badge {{ $badgeClass }}">{{ $order->TrangThai }}</span>
                        <div class="sg-actions-grid">
                            @if($order->TrangThai === 'Đã Huỷ Đơn')
                                {{-- Cancelled: only delete button for Admin --}}
                                @can('Admin')
                                <form method="POST" action="{{ route('seedOrders.destroy', $order->id) }}" style="display:contents;"
                                      onsubmit="return confirm('Xóa vĩnh viễn {{ $order->MaDH }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="sg-btn sg-btn-delete" title="Xóa"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                                @endcan
                            @elseif($order->TrangThai === 'Chờ Giao Giống')
                                {{-- Waiting: Edit + Deliver + Cancel --}}
                                <button type="button" class="sg-btn sg-btn-edit" title="Sửa Đơn" onclick="openSgEdit({{ $order->id }})"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" class="sg-btn sg-btn-deliver" title="Giao Đơn" onclick="openSgDeliver({{ $order->id }}, '{{ $order->MaDH }}')"><i class="fa-solid fa-truck-fast"></i></button>
                                <button type="button" class="sg-btn sg-btn-cancel" title="Hủy Đơn" onclick="cancelSgOrder({{ $order->id }}, '{{ $order->MaDH }}')"><i class="fa-solid fa-ban"></i></button>
                            @elseif($order->TrangThai === 'Đã Giao Giống')
                                {{-- Delivered: Admin-only edit + Admin/Kế Toán payment --}}
                                @can('Admin')
                                <button type="button" class="sg-btn sg-btn-edit" title="Sửa Đơn" onclick="openSgEdit({{ $order->id }})"><i class="fa-solid fa-pen-to-square"></i></button>
                                @endcan
                                @canany(['Admin', 'Kế Toán'])
                                @if(!in_array($order->MaDH, $paidMaDHs))
                                <button type="button" class="sg-btn sg-btn-payment" title="Thanh Toán" onclick="openSgPayment({{ $order->id }}, '{{ $order->MaDH }}', {{ $order->TongTien ?? 0 }})"><i class="fa-solid fa-money-check-dollar"></i></button>
                                @endif
                                @endcanany
                                <button type="button" class="sg-btn sg-btn-note" title="Cập Nhật" onclick="openSgNote({{ $order->id }}, '{{ $order->MaDH }}')"><i class="fa-solid fa-arrows-rotate"></i></button>
                                <button type="button" class="sg-btn sg-btn-history" title="Lịch Sử" onclick="openSgHistory('{{ $order->MaDH }}')"><i class="fa-solid fa-clock-rotate-left"></i></button>
                            @endif
                        </div>
                    
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($orders->count() > 0)
    <div class="sg-pagination" id="sgPagination">
        <span class="sg-pg-info" id="sgPageInfo"></span>
        <div class="sg-pg-btns" id="sgPageButtons"></div>
    </div>
    @endif

    @if($orders->count() === 0)
    <div class="sg-empty">
        <div class="empty-icon">🌱</div>
        <h3>Không có đơn đặt giống</h3>
        <p>Không tìm thấy kết quả phù hợp</p>
    </div>
    @endif
</div>

{{-- ===== CREATE / EDIT MODAL ===== --}}
<div class="sg-modal-overlay" id="sgFormModal">
    <div class="sg-modal">
        <div class="sg-modal-header">
            <h3 id="sgFormTitle">THÊM ĐƠN HÀNG</h3>
            <button class="sg-modal-close" onclick="closeSgForm()">&times;</button>
        </div>
        <form method="POST" id="sgForm" action="{{ route('seedOrders.store') }}">
            @csrf
            <input type="hidden" name="_method" id="sgFormMethod" value="POST">
            <input type="hidden" name="edit_id" id="sgEditId" value="">
            <div class="sg-modal-body">
                {{-- Row 1: Name + Phone --}}
                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label>Nhập tên khách hàng</label>
                        <input type="text" name="tenkh" id="sgTenKH" required>
                    </div>
                    <div class="sg-form-group">
                        <label>Nhập số điện thoại</label>
                        <input type="text" name="sodienthoai" id="sgSDT" required>
                    </div>
                </div>
                {{-- Row 2: NgayGiao + DoMan --}}
                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label>Ngày Giao</label>
                        <input type="text" name="ngaygiao" id="sgNgayGiao" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="sg-form-group">
                        <label>Độ Mặn</label>
                        <input type="number" name="doman" id="sgDoMan" min="0" max="100">
                    </div>
                </div>
                {{-- Row 3: NhanVien + Tinh --}}
                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label>Nhân Viên</label>
                        <select name="manv" id="sgMaNV">
                            <option value="">Chọn Nhân Viên</option>
                            @foreach($staffList as $nv)
                                <option value="{{ $nv->id }}">{{ $nv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sg-form-group">
                        <label>Tỉnh/Thành phố</label>
                        <select name="tinh" id="sgTinh" onchange="loadSgDistricts(this.value)">
                            <option value="">— Chọn tỉnh —</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p->ProvinceID }}">{{ $p->Tinh }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tinh_text" id="sgTinhText">
                    </div>
                </div>
                {{-- Row 4: Huyen + Xa --}}
                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label>Quận/Huyện</label>
                        <select name="huyen" id="sgHuyen" onchange="loadSgWards(this.value)">
                            <option value="">— Chọn quận/huyện —</option>
                        </select>
                        <input type="hidden" name="huyen_text" id="sgHuyenText">
                    </div>
                    <div class="sg-form-group">
                        <label>Xã/Phường</label>
                        <select name="xa" id="sgXa" onchange="setSgXaText()">
                            <option value="">— Chọn xã/phường —</option>
                        </select>
                        <input type="hidden" name="xa_text" id="sgXaText">
                    </div>
                </div>
                {{-- Calculation row --}}
                <div class="sg-form-calc">
                    <div class="calc-field">
                        <label>Lượng Tính Tiền</label>
                        <input type="text" id="sgSLTT" oninput="onSlttInput()">
                        <input type="hidden" name="sltt" id="sgSLTTRaw">
                    </div>
                    <div class="calc-field">
                        <label>Khuyến Mãi</label>
                        <div class="sg-km-group">
                            <label class="km-option"><input type="radio" name="khuyenmai" value="100" checked onchange="onKmChange()"> 100%</label>
                            <label class="km-option"><input type="radio" name="khuyenmai" value="80" onchange="onKmChange()"> 80%</label>
                            <label class="km-option"><input type="radio" name="khuyenmai" value="50" onchange="onKmChange()"> 50%</label>
                            <label class="km-option"><input type="radio" name="khuyenmai" value="30" onchange="onKmChange()"> 30%</label>
                        </div>
                    </div>
                    <div class="calc-field">
                        <label>Số Đầu Con</label>
                        <input type="text" id="sgSoLuong" oninput="onSoLuongInput()">
                        <input type="hidden" name="soluong" id="sgSoLuongRaw">
                    </div>
                    <div class="calc-field" style="text-align: center;">
                        <label>Giá Bán</label>
                        <input type="number" name="giaban" id="sgGiaBan" value="120" oninput="calcSgTotal()" style="text-align: center;width: 90px;"    >
                    </div>
                    <div class="calc-field" style="text-align: center;">
                        <label>Thành Tiền</label>
                        <input type="text" id="sgThanhTien" class="sg-thanh-tien" readonly>
                        <input type="hidden" name="tongtien" id="sgTongTienHidden">
                    </div>
                </div>
                {{-- Dia chi --}}
                <div class="sg-form-group" style="margin-bottom:14px;">
                    <label>Địa chỉ</label>
                    <input type="text" name="diachi" id="sgDiaChi">
                </div>
                {{-- Ghi chu --}}
                <div class="sg-form-group">
                    <label>Ghi Chú</label>
                    <textarea name="ghichu" id="sgGhiChu" rows="3"></textarea>
                </div>
            </div>
            <div class="sg-modal-footer">
                <button type="button" class="sg-modal-btn sg-modal-btn-cancel" onclick="closeSgForm()">Hủy</button>
                <button type="submit" class="sg-modal-btn sg-modal-btn-submit" id="sgFormSubmitBtn">Tạo Đơn Hàng</button>
            </div>
        </form>
    </div>
</div>

{{-- Status Change Modal --}}
<div id="sgStatusModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:12px; width:340px; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; background:linear-gradient(135deg,#f8fafc,#eef2ff);">
            <h3 style="margin:0; font-size:15px; font-weight:700;">Đổi Trạng Thái - <span id="sgStatusMaDH"></span></h3>
        </div>
        <form method="POST" id="sgStatusForm">
            @csrf @method('PUT')
            <div style="padding:20px;">
                <select name="TrangThai" id="sgStatusSelect" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;">
                    <option value="Chờ Giao Giống">Chờ Giao Giống</option>
                    <option value="Đã Giao Giống">Đã Giao Giống</option>
                    <option value="Đã Huỷ Đơn">Đã Huỷ Đơn</option>
                    <option value="Xả Bỏ">Xả Bỏ</option>
                </select>
                <div style="margin-top:12px;">
                    <label style="font-size:13px; color:#64748b; font-weight:600;">Thanh Toán</label>
                    <select name="ThanhToan" id="sgThanhToanSelect" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; margin-top:4px;">
                        <option value="Chưa Thanh Toán">Chưa Thanh Toán</option>
                        <option value="Đã Thanh Toán">Đã Thanh Toán</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:10px; padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; justify-content:flex-end;">
                <button type="button" style="padding:8px 18px; border:none; border-radius:8px; background:#e2e8f0; color:#475569; font-size:13px; font-weight:600; cursor:pointer;" onclick="closeSgStatus()">Hủy</button>
                <button type="submit" style="padding:8px 18px; border:none; border-radius:8px; background:#3b82f6; color:white; font-size:13px; font-weight:600; cursor:pointer;">Lưu</button>
            </div>
        </form>
    </div>
</div>

{{-- Cập Nhật Modal --}}
<div id="sgNoteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:12px; width:450px; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; background:linear-gradient(135deg,#f8fafc,#eef2ff);">
            <h3 style="margin:0; font-size:15px; font-weight:700;">Cập Nhật - <span id="sgNoteMaDH"></span></h3>
        </div>
        <form method="POST" id="sgNoteForm">
            @csrf @method('PUT')
            <input type="hidden" name="update_type" value="cap_nhat_tinh_trang">
            <input type="hidden" name="MaDH" id="sgNoteFormMaDH">
            <div style="padding:20px;">
                <div style="margin-bottom:14px;">
                    <label style="font-size:13px; color:#64748b; font-weight:700; margin-bottom:5px; display:block;">Thông Tin Gần Đây Nhất</label>
                    <textarea id="sgNoteOldInfo" rows="4" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; resize:vertical; background:#f1f5f9; color:#475569; cursor:default; box-sizing:border-box;" readonly></textarea>
                </div>
                <div>
                    <label style="font-size:13px; color:#1e293b; font-weight:700; margin-bottom:5px; display:block;">Tình Trạng Hiện Tại</label>
                    <textarea name="TinhTrangHienTai" id="sgNoteNewInfo" rows="4" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; resize:vertical; box-sizing:border-box;" placeholder="Nhập tình trạng hiện tại..."></textarea>
                </div>
            </div>
            <div style="display:flex; gap:10px; padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; justify-content:flex-end;">
                <button type="button" style="padding:8px 18px; border:none; border-radius:8px; background:#e2e8f0; color:#475569; font-size:13px; font-weight:600; cursor:pointer;" onclick="closeSgNote()">Hủy</button>
                <button type="submit" style="padding:8px 18px; border:none; border-radius:8px; background:#3b82f6; color:white; font-size:13px; font-weight:600; cursor:pointer;">Cập Nhật</button>
            </div>
        </form>
    </div>
</div>

{{-- Lịch Sử Modal --}}
<div id="sgHistoryModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:12px; width:720px; max-height:80vh; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; display:flex; flex-direction:column;">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; background:linear-gradient(135deg,#f0f9ff,#e0f2fe);">
            <h3 style="margin:0; font-size:15px; font-weight:700; color:#0c4a6e;"><i class="fa-solid fa-clock-rotate-left" style="margin-right:6px;"></i>Lịch Sử Cập Nhật - <span id="sgHistoryMaDH"></span></h3>
        </div>
        <div style="padding:16px 20px; overflow-y:auto; flex:1;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th style="padding:8px 10px; text-align:left; font-weight:700; color:#475569; border-bottom:2px solid #e2e8f0;">Ngày</th>
                        <th style="padding:8px 10px; text-align:left; font-weight:700; color:#475569; border-bottom:2px solid #e2e8f0;">Nội Dung</th>
                        <th style="padding:8px 10px; text-align:left; font-weight:700; color:#475569; border-bottom:2px solid #e2e8f0;width:90px;">Nhân Viên</th>
                        @can('Admin')
                        <th style="padding:8px 10px; text-align:center; font-weight:700; color:#475569; border-bottom:2px solid #e2e8f0; width:50px;"></th>
                        @endcan
                    </tr>
                </thead>
                <tbody id="sgHistoryBody">
                    <tr><td colspan="4" style="text-align:center; padding:20px; color:#94a3b8;">Không có dữ liệu</td></tr>
                </tbody>
            </table>
        </div>
        <div style="padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; text-align:right;">
            <button type="button" style="padding:8px 18px; border:none; border-radius:8px; background:#e2e8f0; color:#475569; font-size:13px; font-weight:600; cursor:pointer;" onclick="closeSgHistory()">Đóng</button>
        </div>
    </div>
</div>

{{-- Deliver Modal - Giao Đơn --}}
<div id="sgDeliverModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:12px; width:500px; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:17px; font-weight:700; color:#1e293b;">GIAO ĐƠN ĐẶT GIỐNG <span id="sgDeliverMaDH"></span></h3>
            <button style="background:none; border:none; font-size:22px; cursor:pointer; color:#94a3b8; line-height:1;" onclick="closeSgDeliver()">&times;</button>
        </div>
        <form method="POST" id="sgDeliverForm">
            @csrf @method('PUT')
            <input type="hidden" name="TrangThai" value="Đã Giao Giống">
            <div style="padding:24px;">
                <label style="display:block; font-size:14px; font-weight:700; color:#1e293b; margin-bottom:8px;">Khách Đánh Giá</label>
                <textarea name="NhanGiong" id="sgDeliverText" rows="5" style="width:100%; padding:12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; resize:vertical; box-sizing:border-box;"></textarea>
            </div>
            <div style="display:flex; gap:10px; padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; justify-content:center;">
                <button type="button" style="padding:8px 24px; border:none; border-radius:8px; background:#ef4444; color:white; font-size:13px; font-weight:700; cursor:pointer;" onclick="closeSgDeliver()">HỦY</button>
                <button type="submit" style="padding:8px 24px; border:none; border-radius:8px; background:#3b82f6; color:white; font-size:13px; font-weight:700; cursor:pointer;">CẬP NHẬT</button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden cancel form --}}
<form method="POST" id="sgCancelForm" style="display:none;">
    @csrf @method('PUT')
    <input type="hidden" name="TrangThai" value="Đã Huỷ Đơn">
</form>

{{-- Payment Modal - Thanh Toán --}}
<div id="sgPaymentModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; justify-content:center; align-items:flex-start; padding:40px 10px; overflow-y:auto;">
    <div style="background:white; border-radius:12px; width:500px; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:17px; font-weight:700; color:#1e293b;">THANH TOÁN - <span id="sgPaymentMaDH"></span></h3>
            <button style="background:none; border:none; font-size:22px; cursor:pointer; color:#94a3b8; line-height:1;" onclick="closeSgPayment()">&times;</button>
        </div>
        <form method="POST" id="sgPaymentForm" action="{{ route('seedOrders.payment') }}">
            @csrf
            <input type="hidden" name="seed_order_id" id="sgPayOrderId">
            <input type="hidden" name="MaDH" id="sgPayMaDH">
            <div style="padding:24px; display:flex; flex-direction:column; gap:14px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:5px;">Ngày Thanh Toán</label>
                    <input type="text" name="NgayThanhToan" id="sgPayDate" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; box-sizing:border-box;" placeholder="DD/MM/YYYY">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:5px;">Số Post Nhận</label>
                    <input type="text" id="sgPaySoPost" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; box-sizing:border-box;" oninput="fmtPayInput(this)">
                    <input type="hidden" name="SoLuongNhan" id="sgPaySoPostRaw">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:5px;">Số Tiền Thu</label>
                    <input type="text" id="sgPayThucNhan" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; box-sizing:border-box;" oninput="fmtPayInput(this); calcSgDoanhSo()">
                    <input type="hidden" name="ThucNhan" id="sgPayThucNhanRaw">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:5px;">Chuyển Trả Trại</label>
                    <input type="text" id="sgPayChuyenTra" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; box-sizing:border-box;" value="0" oninput="fmtPayInput(this); calcSgDoanhSo()">
                    <input type="hidden" name="ChuyenTraTrai" id="sgPayChuyenTraRaw">
                </div>
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:13px; font-weight:700; color:#166534;">Doanh Số</span>
                    <span id="sgPayDoanhSo" style="font-size:16px; font-weight:800; color:#16a34a;">0</span>
                    <input type="hidden" name="DoanhSo" id="sgPayDoanhSoHidden">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:5px;">Ghi Chú</label>
                    <textarea name="GhiChu" id="sgPayGhiChu" rows="3" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; resize:vertical; box-sizing:border-box;"></textarea>
                </div>
            </div>
            <div style="display:flex; gap:10px; padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; justify-content:center;">
                <button type="button" style="padding:8px 24px; border:none; border-radius:8px; background:#ef4444; color:white; font-size:13px; font-weight:700; cursor:pointer;" onclick="closeSgPayment()">HỦY</button>
                <button type="submit" style="padding:8px 24px; border:none; border-radius:8px; background:#3b82f6; color:white; font-size:13px; font-weight:700; cursor:pointer;">CẬP NHẬT</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // ========== PAGINATION ==========
    let sgAllRows = [], sgFilteredRows = [], sgCurrentPage = 1, sgPageSize = 50;

    document.addEventListener('DOMContentLoaded', function() {
        sgAllRows = Array.from(document.querySelectorAll('#sgTable tbody tr.sg-row'));
        sgFilteredRows = [...sgAllRows];
        renderSgPage();
        // Init flatpickr for custom date range
        flatpickr('#sgFlatpickr', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            onChange: function(dates) {
                if (dates.length === 2) {
                    const from = dates[0].toISOString().split('T')[0];
                    const to = dates[1].toISOString().split('T')[0];
                    applyDateFilter(from, to);
                }
            }
        });
        // Init flatpickr for create form NgayGiao field
        flatpickr('#sgNgayGiao', { dateFormat: 'd/m/Y', allowInput: true });
        // Init select2 on Tỉnh in create form
        $('#sgTinh').select2({ width: '100%', dropdownParent: $('#sgFormModal') });
        $('#sgMaNV').select2({ width: '100%', dropdownParent: $('#sgFormModal') });
    });

    function renderSgPage() {
        const total = sgFilteredRows.length;
        const totalPages = Math.ceil(total / sgPageSize) || 1;
        if (sgCurrentPage > totalPages) sgCurrentPage = totalPages;
        const start = (sgCurrentPage - 1) * sgPageSize, end = Math.min(start + sgPageSize, total);
        sgAllRows.forEach(r => r.style.display = 'none');
        for (let i = start; i < end; i++) sgFilteredRows[i].style.display = '';

        const info = document.getElementById('sgPageInfo');
        if (info) info.textContent = total > 0 ? `Hiển thị ${start+1}–${end} / ${total} đơn` : '';

        const bc = document.getElementById('sgPageButtons');
        if (!bc) return;
        bc.innerHTML = '';
        const pb = (t, d, a, c) => {
            const b = document.createElement('button');
            b.className = 'sg-pg-btn' + (a ? ' active' : '');
            b.textContent = t; b.disabled = d; b.onclick = c;
            bc.appendChild(b);
        };
        pb('‹', sgCurrentPage === 1, false, () => { sgCurrentPage--; renderSgPage(); });
        let sp = Math.max(1, sgCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
        if (ep - sp < 4) sp = Math.max(1, ep - 4);
        for (let i = sp; i <= ep; i++) pb(i, false, i === sgCurrentPage, () => { sgCurrentPage = i; renderSgPage(); });
        pb('›', sgCurrentPage === totalPages, false, () => { sgCurrentPage++; renderSgPage(); });
    }

    function changeSgPageSize() {
        sgPageSize = parseInt(document.getElementById('pageSize').value);
        sgCurrentPage = 1;
        renderSgPage();
    }

    function filterSgTable() {
        const q = document.getElementById('sgSearch').value.toLowerCase().trim();
        sgFilteredRows = sgAllRows.filter(r => {
            if (!q) return true;
            return (r.dataset.search || '').includes(q);
        });
        sgCurrentPage = 1;
        renderSgPage();
    }

    // ========== DATE RANGE FILTER ==========
    function toggleDatePresets() {
        const el = document.getElementById('sgDatePresets');
        el.classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const wrap = document.querySelector('.sg-date-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('sgDatePresets').classList.remove('open');
        }
    });

    function setDatePreset(preset) {
        const today = new Date();
        let from, to;
        switch (preset) {
            case 'week':
                const w = new Date(today); w.setDate(w.getDate() - 7);
                from = fmt(w); to = fmt(today);
                break;
            case 'thisMonth':
                from = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
                to = fmt(new Date(today.getFullYear(), today.getMonth() + 1, 0));
                break;
            case 'lastMonth':
                from = fmt(new Date(today.getFullYear(), today.getMonth() - 1, 1));
                to = fmt(new Date(today.getFullYear(), today.getMonth(), 0));
                break;
            case '30days':
                const d30 = new Date(today); d30.setDate(d30.getDate() - 30);
                from = fmt(d30); to = fmt(today);
                break;
            case 'thisYear':
                from = fmt(new Date(today.getFullYear(), 0, 1));
                to = fmt(today);
                break;
            case 'custom':
                document.getElementById('sgDatePresets').classList.remove('open');
                document.getElementById('sgFlatpickr')._flatpickr.open();
                return;
        }
        applyDateFilter(from, to);
    }

    function fmt(d) {
        return d.getFullYear() + '-' +
               String(d.getMonth() + 1).padStart(2, '0') + '-' +
               String(d.getDate()).padStart(2, '0');
    }

    function applyDateFilter(from, to) {
        const url = new URL(window.location.href);
        url.searchParams.set('date_from', from);
        url.searchParams.set('date_to', to);
        // Keep status filter
        window.location.href = url.toString();
    }

    // ========== STATUS MODAL ==========
    function openSgStatus(id, current, maDH) {
        document.getElementById('sgStatusMaDH').textContent = maDH;
        document.getElementById('sgStatusForm').action = `/seed-orders/${id}`;
        document.getElementById('sgStatusSelect').value = current;
        document.getElementById('sgStatusModal').style.display = 'flex';
    }
    function closeSgStatus() {
        document.getElementById('sgStatusModal').style.display = 'none';
    }
    document.getElementById('sgStatusModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSgStatus();
    });

    // ========== CẬP NHẬT MODAL ==========
    function openSgNote(id, maDH) {
        document.getElementById('sgNoteMaDH').textContent = maDH;
        document.getElementById('sgNoteFormMaDH').value = maDH;
        document.getElementById('sgNoteForm').action = `/seed-orders/${id}`;
        document.getElementById('sgNoteNewInfo').value = '';
        fetch(`/seed-orders/${id}/data`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('sgNoteOldInfo').value = data.TinhTrangHienTai || '';
            })
            .catch(() => {});
        document.getElementById('sgNoteModal').style.display = 'flex';
    }
    function closeSgNote() {
        document.getElementById('sgNoteModal').style.display = 'none';
    }
    document.getElementById('sgNoteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSgNote();
    });

    // ========== LỊCH SỬ MODAL ==========
    const isAdmin = {{ auth()->user() && auth()->user()->can('Admin') ? 'true' : 'false' }};

    function openSgHistory(maDH) {
        document.getElementById('sgHistoryMaDH').textContent = maDH;
        document.getElementById('sgHistoryBody').innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px; color:#94a3b8;">\u0110ang t\u1EA3i...</td></tr>';
        document.getElementById('sgHistoryModal').style.display = 'flex';

        fetch(`/seed-orders/history/${encodeURIComponent(maDH)}`)
            .then(r => r.json())
            .then(data => {
                const body = document.getElementById('sgHistoryBody');
                if (!data.length) {
                    body.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px; color:#94a3b8;">Ch\u01B0a c\u00F3 l\u1ECBch s\u1EED</td></tr>';
                    return;
                }
                body.innerHTML = data.map(log => {
                    const dt = new Date(log.ThoiGian);
                    const dateStr = String(dt.getDate()).padStart(2,'0') + '/' + String(dt.getMonth()+1).padStart(2,'0') + '/' + dt.getFullYear() + ' ' + String(dt.getHours()).padStart(2,'0') + ':' + String(dt.getMinutes()).padStart(2,'0');
                    let deleteBtn = '';
                    if (isAdmin) {
                        deleteBtn = `<td style="padding:8px 10px; text-align:center; border-bottom:1px solid #f1f5f9;"><button onclick="deleteSgLog(${log.id}, '${maDH}')" style="background:#ef4444; color:white; border:none; border-radius:4px; width:28px; height:28px; cursor:pointer; font-size:12px;" title="X\u00F3a"><i class="fa-solid fa-trash-can"></i></button></td>`;
                    }
                    return `<tr>
                        <td style="padding:8px 10px; border-bottom:1px solid #f1f5f9; white-space:nowrap; color:#64748b;">${dateStr}</td>
                        <td style="padding:8px 10px; border-bottom:1px solid #f1f5f9;">${log.ChiTiet || ''}</td>
                        <td style="padding:8px 10px; border-bottom:1px solid #f1f5f9; color:#64748b;">${log.TenNV || ''}</td>
                        ${deleteBtn}
                    </tr>`;
                }).join('');
            })
            .catch(() => {
                document.getElementById('sgHistoryBody').innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px; color:#ef4444;">L\u1ED7i t\u1EA3i d\u1EEF li\u1EC7u</td></tr>';
            });
    }

    function deleteSgLog(logId, maDH) {
        if (!confirm('X\u00F3a m\u1EE5c l\u1ECBch s\u1EED n\u00E0y?')) return;
        fetch(`/seed-orders/history/${logId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) openSgHistory(maDH);
            else alert(data.message || 'L\u1ED7i');
        })
        .catch(() => alert('L\u1ED7i x\u00F3a'));
    }

    function closeSgHistory() {
        document.getElementById('sgHistoryModal').style.display = 'none';
    }
    document.getElementById('sgHistoryModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSgHistory();
    });

    // ========== CREATE / EDIT FORM ==========
    function numFmt(n) { return Number(n).toLocaleString('vi-VN'); }

    function openSgCreate() {
        resetSgForm();
        document.getElementById('sgFormTitle').textContent = 'THÊM ĐƠN HÀNG';
        document.getElementById('sgFormMethod').value = 'POST';
        document.getElementById('sgEditId').value = '';
        document.getElementById('sgForm').action = '{{ route("seedOrders.store") }}';
        document.getElementById('sgFormSubmitBtn').textContent = 'Tạo Đơn Hàng';
        // Default staff to current user
        @if(auth()->user())
        $('#sgMaNV').val('{{ auth()->user()->id }}').trigger('change');
        @endif
        document.getElementById('sgFormModal').classList.add('show');
    }

    function openSgEdit(id) {
        resetSgForm();
        document.getElementById('sgFormTitle').textContent = 'SỬA ĐƠN HÀNG';
        document.getElementById('sgFormMethod').value = 'PUT';
        document.getElementById('sgEditId').value = id;
        document.getElementById('sgForm').action = `/seed-orders/${id}`;
        document.getElementById('sgFormSubmitBtn').textContent = 'Cập Nhật';

        fetch(`/seed-orders/${id}/data`)
            .then(r => r.json())
            .then(async (data) => {
                // Also fetch customer info
                const khRes = await fetch(`/seed-orders/${id}/full-data`);
                const full = await khRes.json();

                document.getElementById('sgTenKH').value = full.TenKH || '';
                document.getElementById('sgSDT').value = full.SoDienThoai || '';
                if (data.NgayGiao) {
                    const d = new Date(data.NgayGiao);
                    document.getElementById('sgNgayGiao').value =
                        String(d.getDate()).padStart(2,'0') + '/' +
                        String(d.getMonth()+1).padStart(2,'0') + '/' +
                        d.getFullYear();
                }
                document.getElementById('sgDoMan').value = data.DoMan || '';
                $('#sgMaNV').val(data.MaNV || '').trigger('change');

                // Load province/district/ward
                if (full.Tinh) {
                    const provOpt = Array.from(document.getElementById('sgTinh').options).find(o => o.text === full.Tinh);
                    if (provOpt) {
                        $('#sgTinh').val(provOpt.value).trigger('change');
                        document.getElementById('sgTinhText').value = full.Tinh;
                        // Wait for districts to load
                        await loadSgDistricts(provOpt.value, full.Huyen, full.Xa);
                    }
                }

                const slttVal = data.SLTT || '';
                document.getElementById('sgSLTT').value = slttVal ? numFmt(slttVal) : '';
                document.getElementById('sgSLTTRaw').value = slttVal;
                const slVal = data.SoLuong || '';
                document.getElementById('sgSoLuong').value = slVal ? numFmt(slVal) : '';
                document.getElementById('sgSoLuongRaw').value = slVal;
                document.getElementById('sgGiaBan').value = data.GiaBan || 120;
                // Set KhuyenMai radio
                const kmVal = data.KhuyenMai || 100;
                const kmRadio = document.querySelector(`input[name="khuyenmai"][value="${kmVal}"]`);
                if (kmRadio) kmRadio.checked = true;

                document.getElementById('sgDiaChi').value = full.DiaChi || '';
                document.getElementById('sgGhiChu').value = data.GhiChu || '';

                calcSgTotal();
                document.getElementById('sgFormModal').classList.add('show');
            })
            .catch(err => alert('Lỗi: ' + err.message));
    }

    function closeSgForm() {
        document.getElementById('sgFormModal').classList.remove('show');
    }
    document.getElementById('sgFormModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSgForm();
    });

    function resetSgForm() {
        document.getElementById('sgForm').reset();
        document.getElementById('sgHuyen').innerHTML = '<option value="">— Chọn quận/huyện —</option>';
        document.getElementById('sgXa').innerHTML = '<option value="">— Chọn xã/phường —</option>';
        document.getElementById('sgThanhTien').value = '';
        document.getElementById('sgTongTienHidden').value = '';
        $('#sgTinh').val('').trigger('change');
        $('#sgMaNV').val('').trigger('change');
    }

    function parseFmt(s) { return parseInt(String(s).replace(/[^0-9]/g, '')) || 0; }

    function getKm() {
        return parseInt(document.querySelector('input[name="khuyenmai"]:checked')?.value || 100);
    }

    // Nhập SLTT → tính Số Đầu Con = SLTT + SLTT × KM% = SLTT × (1 + KM/100)
    function onSlttInput() {
        const el = document.getElementById('sgSLTT');
        const raw = parseFmt(el.value);
        el.value = raw ? numFmt(raw) : '';
        document.getElementById('sgSLTTRaw').value = raw;
        const km = getKm();
        const soDauCon = Math.round(raw * (1 + km / 100));
        document.getElementById('sgSoLuong').value = soDauCon ? numFmt(soDauCon) : '';
        document.getElementById('sgSoLuongRaw').value = soDauCon;
        calcSgTotal();
    }

    // Nhập Số Đầu Con → tính lại SLTT = Số Đầu Con / (1 + KM/100)
    function onSoLuongInput() {
        const el = document.getElementById('sgSoLuong');
        const raw = parseFmt(el.value);
        el.value = raw ? numFmt(raw) : '';
        document.getElementById('sgSoLuongRaw').value = raw;
        const km = getKm();
        const factor = 1 + km / 100;
        const sltt = factor > 0 ? Math.round(raw / factor) : 0;
        document.getElementById('sgSLTT').value = sltt ? numFmt(sltt) : '';
        document.getElementById('sgSLTTRaw').value = sltt;
        calcSgTotal();
    }

    // Thay đổi KM → tính lại Số Đầu Con từ SLTT
    function onKmChange() {
        const sltt = parseFmt(document.getElementById('sgSLTT').value);
        const km = getKm();
        const soDauCon = Math.round(sltt * (1 + km / 100));
        document.getElementById('sgSoLuong').value = soDauCon ? numFmt(soDauCon) : '';
        document.getElementById('sgSoLuongRaw').value = soDauCon;
        calcSgTotal();
    }

    // Thành Tiền = SLTT × Giá Bán
    function calcSgTotal() {
        const sltt = parseFmt(document.getElementById('sgSLTT').value);
        const gia = parseInt(document.getElementById('sgGiaBan').value) || 0;
        const total = sltt * gia;
        document.getElementById('sgThanhTien').value = numFmt(total);
        document.getElementById('sgTongTienHidden').value = total;
    }

    // ========== PROVINCE / DISTRICT / WARD LOADING ==========
    async function loadSgDistricts(provinceId, presetHuyen, presetXa) {
        const sel = document.getElementById('sgHuyen');
        sel.innerHTML = '<option value="">— Đang tải... —</option>';
        document.getElementById('sgXa').innerHTML = '<option value="">— Chọn xã/phường —</option>';
        // Set province text
        const tinhSel = document.getElementById('sgTinh');
        document.getElementById('sgTinhText').value = tinhSel.options[tinhSel.selectedIndex]?.text || '';

        if (!provinceId) { sel.innerHTML = '<option value="">— Chọn quận/huyện —</option>'; return; }

        try {
            const res = await fetch(`/orders/districts/${provinceId}`);
            const data = await res.json();
            sel.innerHTML = '<option value="">— Chọn quận/huyện —</option>';
            data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.DistrictID;
                opt.textContent = d.Huyen;
                sel.appendChild(opt);
            });
            if (presetHuyen) {
                const match = Array.from(sel.options).find(o => o.text === presetHuyen);
                if (match) {
                    sel.value = match.value;
                    document.getElementById('sgHuyenText').value = presetHuyen;
                    await loadSgWards(match.value, presetXa);
                }
            }
        } catch(e) {
            sel.innerHTML = '<option value="">— Lỗi tải —</option>';
        }
    }

    async function loadSgWards(districtId, presetXa) {
        const sel = document.getElementById('sgXa');
        sel.innerHTML = '<option value="">— Đang tải... —</option>';
        const huyenSel = document.getElementById('sgHuyen');
        document.getElementById('sgHuyenText').value = huyenSel.options[huyenSel.selectedIndex]?.text || '';

        if (!districtId) { sel.innerHTML = '<option value="">— Chọn xã/phường —</option>'; return; }

        try {
            const res = await fetch(`/orders/wards/${districtId}`);
            const data = await res.json();
            sel.innerHTML = '<option value="">— Chọn xã/phường —</option>';
            data.forEach(w => {
                const opt = document.createElement('option');
                opt.value = w.WardID;
                opt.textContent = w.Xa;
                sel.appendChild(opt);
            });
            if (presetXa) {
                const match = Array.from(sel.options).find(o => o.text === presetXa);
                if (match) {
                    sel.value = match.value;
                    document.getElementById('sgXaText').value = presetXa;
                }
            }
        } catch(e) {
            sel.innerHTML = '<option value="">— Lỗi tải —</option>';
        }
    }

    function setSgXaText() {
        const sel = document.getElementById('sgXa');
        document.getElementById('sgXaText').value = sel.options[sel.selectedIndex]?.text || '';
    }

    // ========== DELIVER MODAL ==========
    function openSgDeliver(id, maDH) {
        document.getElementById('sgDeliverMaDH').textContent = maDH;
        document.getElementById('sgDeliverForm').action = `/seed-orders/${id}`;
        document.getElementById('sgDeliverText').value = '';
        document.getElementById('sgDeliverModal').style.display = 'flex';
    }
    function closeSgDeliver() {
        document.getElementById('sgDeliverModal').style.display = 'none';
    }
    document.getElementById('sgDeliverModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSgDeliver();
    });

    // ========== CANCEL ORDER ==========
    function cancelSgOrder(id, maDH) {
        if (!confirm(`Bạn có chắc muốn hủy đơn ${maDH}?`)) return;
        const form = document.getElementById('sgCancelForm');
        form.action = `/seed-orders/${id}`;
        form.submit();
    }

    // ========== PAYMENT MODAL ==========
    // Format number with thousand separators
    function fmtPayInput(el) {
        let raw = el.value.replace(/[^\d]/g, '');
        el.value = raw ? parseInt(raw).toLocaleString('vi-VN') : '';
    }
    function parsePayRaw(id) {
        return parseInt((document.getElementById(id).value || '0').replace(/[^\d]/g, '')) || 0;
    }

    function openSgPayment(id, maDH, tongTien) {
        document.getElementById('sgPaymentMaDH').textContent = maDH;
        document.getElementById('sgPayOrderId').value = id;
        document.getElementById('sgPayMaDH').value = maDH;
        document.getElementById('sgPayThucNhan').value = tongTien ? parseInt(tongTien).toLocaleString('vi-VN') : '';
        document.getElementById('sgPayChuyenTra').value = '0';
        document.getElementById('sgPaySoPost').value = '';
        document.getElementById('sgPayGhiChu').value = '';
        document.getElementById('sgPayDate').value = '';
        calcSgDoanhSo();
        document.getElementById('sgPaymentModal').style.display = 'flex';
        if (!document.getElementById('sgPayDate')._flatpickr) {
            flatpickr('#sgPayDate', { dateFormat: 'd/m/Y', allowInput: true });
        }
    }
    function closeSgPayment() {
        document.getElementById('sgPaymentModal').style.display = 'none';
    }
    document.getElementById('sgPaymentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSgPayment();
    });
    function calcSgDoanhSo() {
        const thucNhan = parsePayRaw('sgPayThucNhan');
        const chuyenTra = parsePayRaw('sgPayChuyenTra');
        const doanhSo = thucNhan - chuyenTra;
        document.getElementById('sgPayDoanhSo').textContent = doanhSo.toLocaleString('vi-VN');
        document.getElementById('sgPayDoanhSoHidden').value = doanhSo;
    }
    // On submit: copy raw values to hidden fields
    document.getElementById('sgPaymentForm')?.addEventListener('submit', function() {
        document.getElementById('sgPaySoPostRaw').value = parsePayRaw('sgPaySoPost');
        document.getElementById('sgPayThucNhanRaw').value = parsePayRaw('sgPayThucNhan');
        document.getElementById('sgPayChuyenTraRaw').value = parsePayRaw('sgPayChuyenTra');
    });
</script>
@endpush
