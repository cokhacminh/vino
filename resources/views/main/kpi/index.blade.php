@extends('main.layouts.app')

@section('title', 'Quản Lý KPI')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
    .kpi-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .kpi-header h2 { margin:0; font-size:22px; font-weight:700; color:#1e293b; }
    .kpi-filter { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:20px; }
    .kpi-filter select, .kpi-filter input { padding:8px 12px; border:2px solid #e2e8f0; border-radius:10px; font-size:13px; background:white; }
    .kpi-filter select:focus, .kpi-filter input:focus { outline:none; border-color:#6d28d9; }

    .btn-add { padding:10px 20px; background:linear-gradient(135deg,#6d28d9,#4f46e5); color:white; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(109,40,217,0.3); transition:all 0.2s; }
    .btn-add:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(109,40,217,0.4); }

    .kpi-card { background:white; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04); overflow-x:auto; }
    .kpi-table { width:100%; border-collapse:collapse; }
    .kpi-table thead th { padding:14px 16px; background:#f8fafc; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; text-align:center; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
    .kpi-table tbody td { padding:12px 16px; border-bottom:1px solid #f1f5f9; font-size:14px; color:#334155; vertical-align:middle; text-align:center; }
    .kpi-table tbody tr:hover { background:#f8fafc; }
    .kpi-table .td-noidung { text-align:left; max-width:400px; }

    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-info { background:#dbeafe; color:#2563eb; }
    .badge-purple { background:#ede9fe; color:#7c3aed; }
    .badge-green { background:#dcfce7; color:#16a34a; }
    .badge-amber { background:#fef3c7; color:#d97706; }

    .btn-actions { display:flex; gap:6px; justify-content:center; }
    .btn-action { width:32px; height:32px; border:none; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px; transition:all 0.2s; }
    .btn-action:hover { transform:scale(1.1); }
    .btn-detail { background:#dbeafe; color:#2563eb; }
    .btn-edit { background:#fef3c7; color:#d97706; }
    .btn-delete { background:#fee2e2; color:#dc2626; }

    /* Modal */
    .modal-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center; }
    .modal-overlay.show { display:flex; }
    .modal-box { background:white; border-radius:16px; width:95%; max-width:700px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.15); }
    .modal-header { display:flex; justify-content:space-between; align-items:center; padding:16px 24px; border-bottom:1px solid #f1f5f9; position:sticky; top:0; background:white; z-index:2; }
    .modal-header h3 { margin:0; font-size:18px; font-weight:700; color:#1e293b; }
    .modal-close { background:none; border:none; font-size:20px; cursor:pointer; color:#94a3b8; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#e2e8f0; }
    .modal-body { padding:20px 24px; }
    .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:16px 24px; border-top:1px solid #f1f5f9; }

    .form-group { margin-bottom:14px; }
    .form-group label { display:block; margin-bottom:5px; font-size:13px; font-weight:600; color:#374151; }
    .form-input { width:100%; padding:9px 12px; border:2px solid #e2e8f0; border-radius:10px; font-size:13px; transition:border-color 0.2s; box-sizing:border-box; }
    .form-input:focus { outline:none; border-color:#6d28d9; box-shadow:0 0 0 3px rgba(109,40,217,0.1); }
    textarea.form-input { resize:vertical; min-height:80px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }

    /* Radio group */
    .radio-group { display:flex; gap:6px; }
    .radio-option {width: 130px;}
    .radio-option input { display:none; }
    .radio-option label { display:block; padding:12px; text-align:center; border:2px solid #e2e8f0; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; color:#64748b; transition:all 0.2s; white-space:nowrap; }
    .radio-option input:checked + label { border-color:#6d28d9; background:#f5f3ff; color:#6d28d9; }

    /* Inline row: radio + selects on same line */
    .inline-row { display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:14px; }
    .inline-row .radio-col { flex-shrink:0; }
    .inline-row .radio-col > label { display:block; margin-bottom:5px; font-size:13px; font-weight:600; color:#374151; }
    .inline-row .input-col { flex:1; min-width:160px; }
    .inline-row .input-col > label { display:block; margin-bottom:5px; font-size:13px; font-weight:600; color:#374151; }
    .inline-row .input-col .form-input { margin-bottom:0; }
    .inline-row .form-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; flex:1; min-width:280px; }
    .inline-row .form-row .form-group { margin-bottom:0; }

    .btn-save { padding:10px 24px; background:linear-gradient(135deg,#6d28d9,#4f46e5); color:white; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; box-shadow:0 2px 8px rgba(109,40,217,0.3); }
    .btn-cancel { padding:10px 24px; background:#f1f5f9; color:#64748b; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; }

    /* Detail Modal */
    .modal-box.wide { max-width:900px; }
    .detail-table { width:100%; border-collapse:collapse; margin-top:10px; }
    .detail-table th { padding:10px 2px; background:#f8fafc; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; text-align:center; border-bottom:2px solid #e2e8f0; }
    .detail-table td { padding:10px 2px; border-bottom:1px solid #f1f5f9; font-size:13px; text-align:center; vertical-align:middle; }

    .badge-trang-thai { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
    .tt-chua { background:#f1f5f9; color:#64748b; }
    .tt-dabao { background:#fef3c7; color:#d97706; }
    .tt-hople { background:#dcfce7; color:#16a34a; }
    .tt-baolai { background:#fee2e2; color:#dc2626; }
    .dg-dat { background:#dcfce7; color:#16a34a; }
    .dg-vuot { background:#dbeafe; color:#2563eb; }
    .dg-kdat { background:#fee2e2; color:#dc2626; }

    .eval-form { display:flex; gap:6px; align-items:center;justify-content: center; flex-wrap:wrap; }
    .eval-btn { padding:4px 10px; border:none; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer; transition:all 0.2s; }
    .eval-btn.btn-hople { background:#dcfce7; color:#16a34a; }
    .eval-btn.btn-baolai { background:#fee2e2; color:#dc2626; }
    .eval-btn:hover { transform:scale(1.05); }

    .eval-score { margin-top:8px; display:flex; gap:4px; flex-wrap:wrap;justify-content: center; }
    .score-btn { padding:4px 8px; border:1px solid #e2e8f0; border-radius:6px; font-size:11px; cursor:pointer; background:white; font-weight:600; transition:all 0.2s; }
    .score-btn:hover { border-color:#6d28d9; }

    .img-preview { max-width:100px; max-height:60px; border-radius:6px; cursor:pointer; object-fit:cover; border:1px solid #e2e8f0; }

    .alert { padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; font-weight:500; display:flex; align-items:center; gap:8px; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }

    .empty-state { text-align:center; padding:40px; color:#94a3b8; }
    .empty-state i { font-size:32px; margin-bottom:12px; }

    /* Select2 custom */
    .select2-container--default .select2-selection--single {
        height:38px; border:2px solid #e2e8f0; border-radius:10px; padding:4px 8px; font-size:13px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { top:5px; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color:#6d28d9; }
    .select2-dropdown { border:2px solid #e2e8f0; border-radius:10px; z-index:9999; }
    .select2-results__option--highlighted { background:#6d28d9 !important; }
    .select2-container { width:100% !important; }

    .kpi-title { font-weight:700; color:#1e293b; display:block; margin-bottom:2px; }
    .kpi-desc { font-size:12px; color:#94a3b8; display:block; }

    /* Flatpickr overrides */
    .flatpickr-calendar { border-radius:12px !important; box-shadow:0 8px 24px rgba(0,0,0,0.12) !important; border:2px solid #e2e8f0 !important; }
    .flatpickr-months .flatpickr-month { border-radius:10px 10px 0 0; }
    .fp-input { cursor:pointer; background:white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236d28d9' viewBox='0 0 16 16'%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5z'/%3E%3C/svg%3E") no-repeat right 12px center; background-size:14px; }
    .flatpickr-monthSelect-month { border-radius:8px !important; }
    .flatpickr-monthSelect-month.selected { background:#6d28d9 !important; }

    /* Year picker dropdown */
    .year-picker-dropdown { position:absolute; z-index:9999; background:white; border:2px solid #e2e8f0; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.12); padding:12px; width:240px; }

    /* Detail month picker */
    .dmp-wrap { position:relative; display:inline-block; }
    .dmp-btn { padding:6px 14px; border:2px solid #e2e8f0; border-radius:8px; background:white; font-size:13px; font-weight:600; color:#334155; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s; }
    .dmp-btn:hover { border-color:#6d28d9; }
    .dmp-dropdown { position:absolute; top:calc(100% + 4px); right:0; z-index:9999; background:white; border:2px solid #e2e8f0; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.12); padding:12px; width:260px; }
    .dmp-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
    .dmp-header span { font-size:15px; font-weight:700; color:#1e293b; }
    .dmp-nav { background:none; border:none; font-size:18px; cursor:pointer; color:#6d28d9; font-weight:700; width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; }
    .dmp-nav:hover { background:#f5f3ff; }
    .dmp-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:6px; }
    .dmp-month { padding:8px 4px; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; background:white; color:#334155; text-align:center; transition:all 0.15s; }
    .dmp-month:hover { border-color:#6d28d9; background:#f5f3ff; color:#6d28d9; }
    .dmp-selected { background:#6d28d9 !important; color:white !important; border-color:#6d28d9 !important; }
    .yp-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding:0 4px; }
    .yp-header span { font-size:14px; font-weight:700; color:#1e293b; }
    .yp-nav { background:none; border:none; font-size:18px; cursor:pointer; color:#6d28d9; font-weight:700; width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; }
    .yp-nav:hover { background:#f5f3ff; }
    .yp-grid { display:grid; grid-template-columns:repeat(5, 1fr); gap:6px; }
    .yp-year { padding:8px 4px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; background:white; color:#334155; text-align:center; transition:all 0.15s; }
    .yp-year:hover { border-color:#6d28d9; background:#f5f3ff; color:#6d28d9; }
    .yp-selected { background:#6d28d9 !important; color:white !important; border-color:#6d28d9 !important; }
</style>
@endpush

@section('content')
<div>


    <div class="kpi-header">
        <h2><i class="fa-solid fa-chart-line" style="color:#6d28d9;"></i> QUẢN LÝ KPI</h2>
        @if(Auth::user()->hasRole('Admin') || Auth::user()->can('Admin'))
        <button class="btn-add" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Thêm KPI
        </button>
        @endif
    </div>

    <!-- Filter -->
    <div class="kpi-filter">
        <select id="filterCV" onchange="applyFilter()">
            <option value="">Tất cả Chức Vụ</option>
            @foreach($chucvuList as $cv)
                <option value="{{ $cv->MaCV }}" {{ $filterCV == $cv->MaCV ? 'selected' : '' }}>{{ $cv->TenCV }}</option>
            @endforeach
        </select>
    </div>

    <div class="kpi-card">
        <table class="kpi-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>KPI</th>
                    <th>Áp Dụng</th>
                    <th>Deadline</th>
                    <th>Số User</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kpis as $i => $kpi)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="td-noidung">
                        <span class="kpi-title">{{ $kpi->tieu_de }}</span>
                        @if($kpi->noi_dung)
                            <span class="kpi-desc">{{ Str::limit($kpi->noi_dung, 80) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($kpi->loai_ap_dung === 'Cá Nhân')
                            <span class="badge badge-purple">
                                <i class="fa-solid fa-user"></i> {{ $kpi->targetUser ? $kpi->targetUser->name : '—' }}
                            </span>
                        @else
                            <span class="badge badge-info">{{ $cvMap[$kpi->MaCV] ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($kpi->tan_suat === 'Hàng Tháng')
                            <span class="badge badge-green"><i class="fa-solid fa-repeat"></i> Hàng Tháng</span>
                        @else
                            <span class="badge badge-amber"><i class="fa-solid fa-calendar-day"></i> {{ $kpi->deadline ? \Carbon\Carbon::parse($kpi->deadline)->format('d/m/Y') : '—' }}</span>
                        @endif
                    </td>
                    <td>{{ $kpi->kpi_users_count }}</td>
                    <td>
                        <div class="btn-actions">
                            <button class="btn-action btn-detail" title="Chi tiết" onclick="openDetail({{ $kpi->id }})">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Admin') || Auth::user()->can('Admin'))
                            <button class="btn-action btn-edit" title="Sửa" onclick="openEditModal({{ json_encode($kpi) }})">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn-action btn-delete" title="Xóa" onclick="deleteKpi({{ $kpi->id }})">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-state"><i class="fa-solid fa-inbox"></i><br>Chưa có KPI nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ===================== INLINE: CHI TIẾT KPI ===================== -->
    <div id="inlineDetail" style="display:none; margin-top:16px;">
        <div class="kpi-card" style="border:2px solid #6d28d9;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:14px 20px; background:linear-gradient(135deg,#f5f3ff,#ede9fe); border-bottom:2px solid #e2e8f0; flex-wrap:wrap; gap:10px;">
                <div style="flex:1; min-width:200px;">
                    <strong id="detailTieuDe" style="font-size:16px; color:#1e293b;"></strong>
                    <p id="detailNoiDung" style="margin:4px 0 0; font-size:13px; color:#64748b;"></p>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    {{-- Month picker for Hàng Tháng --}}
                    <div class="dmp-wrap" id="dmpWrap" style="display:none;">
                        <button type="button" class="dmp-btn" id="dmpBtn" onclick="toggleDetailMonthPicker()">
                            <i class="fa-regular fa-calendar"></i>
                            <span id="dmpLabel"></span>
                            <i class="fa-solid fa-chevron-down" style="font-size:10px; color:#94a3b8;"></i>
                        </button>
                        <div class="dmp-dropdown" id="dmpDropdown" style="display:none;"></div>
                    </div>
                    {{-- Deadline warning for Cố Định --}}
                    <div id="deadlineBadge" style="display:none; padding:6px 14px; border-radius:8px; font-size:13px; font-weight:600;"></div>
                    <button onclick="closeInlineDetail()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#94a3b8; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;" title="Đóng">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            <div style="overflow-x:auto;padding:0 12px">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Nhân Viên</th>
                            <th>Trạng Thái</th>
                            <th style="text-align: left;">Báo Cáo</th>
                            <th>Ảnh</th>
                            <th>Đánh Giá</th>
                            <th>Ghi Chú</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="detailBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================== BẢNG KPI SẮP/QUÁ HẠN ===================== -->
    @if($deadlineKpiUsers->count() > 0)
    <div class="kpi-card" style="margin-top:20px;">
        <div style="padding:14px 20px; background:linear-gradient(135deg,#fef3c7,#fef9c3); border-bottom:2px solid #fde68a; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:15px; font-weight:700; color:#92400e;">
                <i class="fa-solid fa-bell" style="margin-right:6px;"></i> KPI Sắp/Quá Hạn Deadline
                <span style="font-weight:400; color:#d97706; font-size:13px;">({{ $deadlineKpiUsers->count() }})</span>
            </h3>
        </div>
        <table class="kpi-table">
            <thead>
                <tr>
                    <th style="text-align:left;">KPI</th>
                    <th>Nhân Viên</th>
                    <th>Trạng Thái</th>
                    <th style="text-align:left;">Báo Cáo</th>
                    <th>Ảnh</th>
                    <th>Đánh Giá</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deadlineKpiUsers as $dku)
                @php
                    $dlDate = $dku->deadline_time ? \Carbon\Carbon::parse($dku->deadline_time) : null;
                    $dlDays = $dlDate ? (int) now()->startOfDay()->diffInDays($dlDate, false) : null;
                    $ttCls = match($dku->trang_thai) {
                        'Chưa Báo Cáo' => 'background:#f1f5f9; color:#64748b;',
                        'Đã Báo Cáo' => 'background:#fef3c7; color:#d97706;',
                        'Hợp Lệ' => 'background:#dcfce7; color:#16a34a;',
                        'Báo Cáo Lại' => 'background:#fee2e2; color:#dc2626;',
                        default => 'background:#f1f5f9; color:#64748b;',
                    };
                @endphp
                <tr>
                    {{-- KPI --}}
                    <td class="td-noidung" style="font-weight:600;">
                        {{ $dku->kpi->tieu_de ?? $dku->kpi->noi_dung ?? '—' }}
                        @if($dlDate)
                        <div style="margin-top:3px;">
                            @if($dlDays < 0)
                                <span class="badge" style="background:#fee2e2; color:#dc2626; font-size:10px;"><i class="fa-solid fa-triangle-exclamation"></i> Quá hạn {{ abs($dlDays) }} ngày</span>
                            @elseif($dlDays === 0)
                                <span class="badge" style="background:#fef3c7; color:#d97706; font-size:10px;"><i class="fa-solid fa-clock"></i> Hôm nay</span>
                            @elseif($dlDays <= 7)
                                <span class="badge" style="background:#fef3c7; color:#d97706; font-size:10px;"><i class="fa-solid fa-clock"></i> Còn {{ $dlDays }} ngày</span>
                            @else
                                <span class="badge" style="background:#f0fdf4; color:#16a34a; font-size:10px;"><i class="fa-regular fa-calendar-check"></i> {{ $dlDate->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        @endif
                    </td>
                    {{-- Nhân Viên --}}
                    <td>
                        <span class="badge badge-purple"><i class="fa-solid fa-user"></i> {{ $dku->user->name ?? '—' }}</span>
                    </td>
                    {{-- Trạng Thái --}}
                    <td>
                        <span class="badge" style="{{ $ttCls }}">{{ $dku->trang_thai }}</span>
                    </td>
                    {{-- Báo Cáo --}}
                    <td style="text-align:left; max-width:250px; font-size:13px; color:#475569;">
                        @if($dku->bao_cao)
                            {{ Str::limit($dku->bao_cao, 120) }}
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    {{-- Ảnh --}}
                    <td>
                        @if($dku->hinh_anh)
                            <img src="{{ asset('storage/kpi/' . $dku->hinh_anh) }}" 
                                 style="width:40px; height:40px; object-fit:cover; border-radius:6px; cursor:pointer; border:1px solid #e2e8f0;"
                                 onclick="viewImage('{{ asset('storage/kpi/' . $dku->hinh_anh) }}')"
                                 title="Click để phóng to">
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    {{-- Đánh Giá --}}
                    <td>
                        @if($dku->danh_gia)
                            @php
                                $dgCls = match($dku->danh_gia) {
                                    'Đạt KPI' => 'background:#dcfce7; color:#16a34a;',
                                    'Vượt KPI' => 'background:#dbeafe; color:#2563eb;',
                                    'Không Đạt' => 'background:#fee2e2; color:#dc2626;',
                                    default => '',
                                };
                            @endphp
                            <span class="badge" style="{{ $dgCls }}">{{ $dku->danh_gia }}</span>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    {{-- Thao Tác --}}
                    <td>
                        @if($dku->trang_thai === 'Đã Báo Cáo')
                            <div style="display:flex; flex-direction:column; gap:4px; align-items:center;">
                                <div style="display:flex; gap:4px;">
                                    <button class="eval-btn btn-hople" onclick="evaluateKpi({{ $dku->id }}, 'Hợp Lệ')" title="Hợp Lệ" style="padding:4px 8px; border-radius:6px; border:none; font-size:11px; font-weight:600; cursor:pointer; background:#dcfce7; color:#16a34a;">✓ Hợp Lệ</button>
                                    <button class="eval-btn btn-baolai" onclick="evaluateReject({{ $dku->id }})" title="Báo Cáo Lại" style="padding:4px 8px; border-radius:6px; border:none; font-size:11px; font-weight:600; cursor:pointer; background:#fee2e2; color:#dc2626;">✗ BC Lại</button>
                                </div>
                            </div>
                        @elseif($dku->trang_thai === 'Hợp Lệ' && !$dku->danh_gia)
                            <div style="display:flex; gap:3px; justify-content:center; flex-wrap:wrap;">
                                <button onclick="scoreKpi({{ $dku->id }}, 'Đạt KPI')" style="padding:3px 6px; border:1px solid #e2e8f0; border-radius:6px; font-size:10px; cursor:pointer; background:white; font-weight:600; color:#16a34a;">Đạt</button>
                                <button onclick="scoreKpi({{ $dku->id }}, 'Vượt KPI')" style="padding:3px 6px; border:1px solid #e2e8f0; border-radius:6px; font-size:10px; cursor:pointer; background:white; font-weight:600; color:#2563eb;">Vượt</button>
                                <button onclick="scoreKpi({{ $dku->id }}, 'Không Đạt')" style="padding:3px 6px; border:1px solid #e2e8f0; border-radius:6px; font-size:10px; cursor:pointer; background:white; font-weight:600; color:#dc2626;">K.Đạt</button>
                            </div>
                        @else
                            <span style="color:#cbd5e1; font-size:11px;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

<!-- ===================== MODAL: THÊM KPI ===================== -->
<div class="modal-overlay" id="modalAdd">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-plus-circle"></i> Thêm KPI</h3>
            <button class="modal-close" onclick="closeModal('modalAdd')">✕</button>
        </div>
        <form action="{{ route('kpi.store') }}" method="POST" onsubmit="return preventDoubleSubmit(this)">
            @csrf
            <div class="modal-body">
                <!-- Loại áp dụng + Phòng Ban/Chức Vụ or Nhân viên -->
                <div class="inline-row">
                    <div class="radio-col">
                        <label>Áp dụng</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="loai_ap_dung" id="add_loai_cv" value="Chức Vụ" checked onchange="toggleAddTarget()">
                                <label for="add_loai_cv"><i class="fa-solid fa-users"></i> Chức Vụ</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="loai_ap_dung" id="add_loai_cn" value="Cá Nhân" onchange="toggleAddTarget()">
                                <label for="add_loai_cn"><i class="fa-solid fa-user"></i> Cá Nhân</label>
                            </div>
                        </div>
                    </div>
                    <!-- Chức Vụ target: Phòng Ban -> Chức Vụ -->
                    <div id="add_group_cv" class="form-row">
                        <div class="form-group">
                            <label>Phòng Ban</label>
                            <select id="add_phongban" class="form-input" onchange="loadChucVu('add')">
                                <option value="">-- Chọn Phòng Ban --</option>
                                @foreach($phongbanList as $pb)
                                    <option value="{{ $pb->MaPB }}">{{ $pb->TenPB }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Chức Vụ</label>
                            <select name="MaCV" id="add_MaCV" class="form-input">
                                <option value="">-- Chọn Chức Vụ --</option>
                            </select>
                        </div>
                    </div>
                    <!-- Cá Nhân target: Select2 user -->
                    <div class="input-col" id="add_group_user" style="display:none;">
                        <label>Nhân viên</label>
                        <select name="target_user_id" id="add_user_id" class="form-input select2-user">
                            <option value="">-- Chọn Nhân Viên --</option>
                            @foreach($activeUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tần suất + Deadline/Năm áp dụng -->
                <div class="inline-row">
                    <div class="radio-col">
                        <label>Tần suất</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="tan_suat" id="add_ts_cd" value="Cố Định" checked onchange="toggleAddFreq()">
                                <label for="add_ts_cd"><i class="fa-solid fa-calendar-day"></i> Cố định</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="tan_suat" id="add_ts_ht" value="Hàng Tháng" onchange="toggleAddFreq()">
                                <label for="add_ts_ht"><i class="fa-solid fa-repeat"></i> Hàng tháng</label>
                            </div>
                        </div>
                    </div>
                    <div class="input-col" id="add_group_deadline">
                        <label><i class="fa-regular fa-calendar-check"></i> Deadline</label>
                        <input type="text" name="deadline" id="add_deadline" class="form-input fp-input" placeholder="Chọn ngày deadline..." readonly required>
                    </div>
                    <div class="input-col" id="add_group_year" style="display:none;">
                        <label><i class="fa-regular fa-calendar"></i> Năm áp dụng</label>
                        <input type="text" name="nam" id="add_nam" class="form-input fp-input" placeholder="Chọn năm..." readonly required>
                    </div>
                </div>

                <!-- Tiêu đề + Nội dung -->
                <div class="form-group">
                    <label>Tiêu đề KPI <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="tieu_de" class="form-input" placeholder="VD: Doanh thu bán hàng tháng 2" required>
                </div>
                <div class="form-group">
                    <label>Mô tả chi tiết</label>
                    <textarea name="noi_dung" class="form-input" placeholder="Mô tả chi tiết nội dung, tiêu chí, yêu cầu của KPI..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalAdd')">Hủy</button>
                <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Lưu KPI</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: SỬA KPI ===================== -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-pen-to-square"></i> Sửa KPI</h3>
            <button class="modal-close" onclick="closeModal('modalEdit')">✕</button>
        </div>
        <form id="editForm" method="POST" onsubmit="return preventDoubleSubmit(this)">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <!-- Loại áp dụng + Phòng Ban/Chức Vụ or Nhân viên -->
                <div class="inline-row">
                    <div class="radio-col">
                        <label>Áp dụng</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="loai_ap_dung" id="edit_loai_cv" value="Chức Vụ" checked onchange="toggleEditTarget()">
                                <label for="edit_loai_cv"><i class="fa-solid fa-users"></i> Chức Vụ</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="loai_ap_dung" id="edit_loai_cn" value="Cá Nhân" onchange="toggleEditTarget()">
                                <label for="edit_loai_cn"><i class="fa-solid fa-user"></i> Cá Nhân</label>
                            </div>
                        </div>
                    </div>
                    <!-- Chức Vụ target -->
                    <div id="edit_group_cv" class="form-row">
                        <div class="form-group">
                            <label>Phòng Ban</label>
                            <select id="edit_phongban" class="form-input" onchange="loadChucVu('edit')">
                                <option value="">-- Chọn Phòng Ban --</option>
                                @foreach($phongbanList as $pb)
                                    <option value="{{ $pb->MaPB }}">{{ $pb->TenPB }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Chức Vụ</label>
                            <select name="MaCV" id="edit_MaCV" class="form-input">
                                <option value="">-- Chọn Chức Vụ --</option>
                            </select>
                        </div>
                    </div>
                    <!-- Cá Nhân target -->
                    <div class="input-col" id="edit_group_user" style="display:none;">
                        <label>Nhân viên</label>
                        <select name="target_user_id" id="edit_user_id" class="form-input select2-user">
                            <option value="">-- Chọn Nhân Viên --</option>
                            @foreach($activeUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tần suất + Deadline/Năm áp dụng -->
                <div class="inline-row">
                    <div class="radio-col">
                        <label>Tần suất</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="tan_suat" id="edit_ts_cd" value="Cố Định" checked onchange="toggleEditFreq()">
                                <label for="edit_ts_cd"><i class="fa-solid fa-calendar-day"></i> Cố định</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="tan_suat" id="edit_ts_ht" value="Hàng Tháng" onchange="toggleEditFreq()">
                                <label for="edit_ts_ht"><i class="fa-solid fa-repeat"></i> Hàng tháng</label>
                            </div>
                        </div>
                    </div>
                    <div class="input-col" id="edit_group_deadline">
                        <label><i class="fa-regular fa-calendar-check"></i> Deadline</label>
                        <input type="text" name="deadline" id="edit_deadline" class="form-input fp-input" placeholder="Chọn ngày deadline..." readonly required>
                    </div>
                    <div class="input-col" id="edit_group_year" style="display:none;">
                        <label><i class="fa-regular fa-calendar"></i> Năm áp dụng</label>
                        <input type="text" name="nam" id="edit_nam" class="form-input fp-input" placeholder="Chọn năm..." readonly required>
                    </div>
                </div>

                <!-- Tiêu đề + Nội dung -->
                <div class="form-group">
                    <label>Tiêu đề KPI <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="tieu_de" id="edit_tieu_de" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Mô tả chi tiết</label>
                    <textarea name="noi_dung" id="edit_noi_dung" class="form-input"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalEdit')">Hủy</button>
                <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Cập Nhật</button>
            </div>
        </form>
    </div>
</div>



<!-- ===================== MODAL: XÓA KPI ===================== -->
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h3 style="color:#dc2626;"><i class="fa-solid fa-triangle-exclamation"></i> Xóa KPI</h3>
            <button class="modal-close" onclick="closeModal('modalDelete')">✕</button>
        </div>
        <form id="deleteForm" method="POST" onsubmit="return preventDoubleSubmit(this)">
            @csrf
            @method('DELETE')
            <div class="modal-body" style="text-align:center; padding:24px;">
                <p>Bạn có chắc chắn muốn <strong style="color:#dc2626;">xóa KPI</strong> này?<br>Toàn bộ dữ liệu báo cáo sẽ bị xóa.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalDelete')">Hủy</button>
                <button type="submit" class="btn-save" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    <i class="fa-solid fa-trash-can"></i> Xóa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: XEM ẢNH ===================== -->
<div class="modal-overlay" id="modalImage" onclick="closeModal('modalImage')">
    <div style="display:flex; align-items:center; justify-content:center; height:100%;">
        <img id="imagePreview" src="" style="max-width:90%; max-height:90vh; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.3);">
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
    // Chức vụ data for phòng ban mapping
    const allChucVu = @json($chucvuList);

    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    function applyFilter() {
        const cv = document.getElementById('filterCV').value;
        window.location.href = `{{ route('kpi.index') }}?chucvu=${cv}`;
    }

    // ===== Load Chức Vụ by Phòng Ban =====
    function loadChucVu(prefix) {
        const maPB = document.getElementById(prefix + '_phongban').value;
        const cvSelect = document.getElementById(prefix + '_MaCV');
        cvSelect.innerHTML = '<option value="">-- Chọn Chức Vụ --</option>';

        if (!maPB) return;

        // Filter from preloaded data
        const filtered = allChucVu.filter(cv => cv.MaPB == maPB);
        filtered.forEach(cv => {
            cvSelect.innerHTML += `<option value="${cv.MaCV}">${cv.TenCV}</option>`;
        });
    }

    // ===== ADD MODAL =====
    function toggleAddTarget() {
        const isCV = document.getElementById('add_loai_cv').checked;
        document.getElementById('add_group_cv').style.display = isCV ? '' : 'none';
        document.getElementById('add_group_user').style.display = isCV ? 'none' : '';
        if (!isCV) {
            setTimeout(() => {
                if (!$('#add_user_id').data('select2')) {
                    $('#add_user_id').select2({ dropdownParent: $('#modalAdd'), placeholder: '-- Tìm nhân viên --', allowClear: true, width: '100%' });
                }
            }, 50);
        }
    }

    // ===== Flatpickr instances =====
    var fpAddDeadline, fpEditDeadline;

    function initDatePicker(inputId, appendEl, defaultVal) {
        return flatpickr('#' + inputId, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            locale: 'vn',
            defaultDate: defaultVal || null,
            appendTo: appendEl || undefined,
            static: true
        });
    }

    // ===== Custom Year Picker =====
    function showYearPicker(input) {
        var existing = document.getElementById('yearPickerDropdown');
        if (existing) { existing.remove(); return; }

        var selected = parseInt(input.value) || new Date().getFullYear();
        var div = document.createElement('div');
        div.id = 'yearPickerDropdown';
        div.className = 'year-picker-dropdown';
        div.innerHTML = '<div class="yp-header"><button type="button" class="yp-nav" onclick="ypNav(-10)">\u2039</button><span id="ypRange"></span><button type="button" class="yp-nav" onclick="ypNav(10)">\u203A</button></div><div id="ypGrid" class="yp-grid"></div>';

        var rect = input.getBoundingClientRect();
        div.style.position = 'fixed';
        div.style.top = (rect.bottom + 4) + 'px';
        div.style.left = rect.left + 'px';
        document.body.appendChild(div);

        window._ypInput = input;
        window._ypStart = selected - (selected % 10);
        ypRender(selected);

        setTimeout(function() { document.addEventListener('click', ypClose); }, 10);
    }

    function ypClose(e) {
        var dd = document.getElementById('yearPickerDropdown');
        if (dd && !dd.contains(e.target) && !e.target.classList.contains('fp-input')) {
            dd.remove();
            document.removeEventListener('click', ypClose);
        }
    }

    function ypRender(selected) {
        var grid = document.getElementById('ypGrid');
        var range = document.getElementById('ypRange');
        if (!grid) return;
        range.textContent = window._ypStart + ' — ' + (window._ypStart + 9);
        var html = '';
        for (var y = window._ypStart; y < window._ypStart + 10; y++) {
            var cls = y === selected ? 'yp-year yp-selected' : 'yp-year';
            html += '<button type="button" class="' + cls + '" onclick="ypSelect(' + y + ')">' + y + '</button>';
        }
        grid.innerHTML = html;
    }

    function ypNav(delta) {
        window._ypStart += delta;
        ypRender(parseInt(window._ypInput.value) || new Date().getFullYear());
    }

    function ypSelect(y) {
        window._ypInput.value = y;
        var dd = document.getElementById('yearPickerDropdown');
        if (dd) dd.remove();
        document.removeEventListener('click', ypClose);
    }

    function toggleAddFreq() {
        var isCoDinh = document.getElementById('add_ts_cd').checked;
        document.getElementById('add_group_deadline').style.display = isCoDinh ? '' : 'none';
        document.getElementById('add_group_year').style.display = isCoDinh ? 'none' : '';
        document.getElementById('add_deadline').required = isCoDinh;
        document.getElementById('add_nam').required = !isCoDinh;
    }

    function openAddModal() {
        var addForm = document.querySelector('#modalAdd form');
        if (addForm) addForm.dataset.submitted = '';
        document.getElementById('add_loai_cv').checked = true;
        document.getElementById('add_ts_cd').checked = true;
        document.getElementById('add_phongban').value = '';
        document.getElementById('add_MaCV').innerHTML = '<option value="">-- Chọn Chức Vụ --</option>';
        if ($('#add_user_id').data('select2')) $('#add_user_id').val('').trigger('change');
        toggleAddTarget();
        toggleAddFreq();

        // Init Flatpickr date picker for deadline
        if (fpAddDeadline) fpAddDeadline.destroy();
        fpAddDeadline = initDatePicker('add_deadline', document.querySelector('#modalAdd .modal-body'));

        // Init year value
        document.getElementById('add_nam').value = new Date().getFullYear();

        openModal('modalAdd');
    }

    // Year picker click handlers
    document.getElementById('add_nam').addEventListener('click', function() { showYearPicker(this); });
    document.getElementById('edit_nam').addEventListener('click', function() { showYearPicker(this); });

    // ===== EDIT MODAL =====
    function toggleEditTarget() {
        var isCV = document.getElementById('edit_loai_cv').checked;
        document.getElementById('edit_group_cv').style.display = isCV ? '' : 'none';
        document.getElementById('edit_group_user').style.display = isCV ? 'none' : '';
        if (!isCV) {
            setTimeout(function() {
                if (!$('#edit_user_id').data('select2')) {
                    $('#edit_user_id').select2({ dropdownParent: $('#modalEdit'), placeholder: '-- Tìm nhân viên --', allowClear: true, width: '100%' });
                }
            }, 50);
        }
    }
    function toggleEditFreq() {
        var isCoDinh = document.getElementById('edit_ts_cd').checked;
        document.getElementById('edit_group_deadline').style.display = isCoDinh ? '' : 'none';
        document.getElementById('edit_group_year').style.display = isCoDinh ? 'none' : '';
        document.getElementById('edit_deadline').required = isCoDinh;
        document.getElementById('edit_nam').required = !isCoDinh;
    }

    function openEditModal(kpi) {
        document.getElementById('editForm').action = '/kpi/' + kpi.id;

        if (kpi.loai_ap_dung === 'Cá Nhân') {
            document.getElementById('edit_loai_cn').checked = true;
        } else {
            document.getElementById('edit_loai_cv').checked = true;
        }
        toggleEditTarget();

        if (kpi.loai_ap_dung !== 'Cá Nhân' && kpi.MaCV) {
            var cvItem = allChucVu.find(function(cv) { return cv.MaCV == kpi.MaCV; });
            if (cvItem) {
                document.getElementById('edit_phongban').value = cvItem.MaPB;
                loadChucVu('edit');
                setTimeout(function() { document.getElementById('edit_MaCV').value = kpi.MaCV; }, 50);
            }
        }

        if (kpi.loai_ap_dung === 'Cá Nhân' && kpi.target_user_id) {
            setTimeout(function() {
                if ($('#edit_user_id').data('select2')) {
                    $('#edit_user_id').val(kpi.target_user_id).trigger('change');
                } else {
                    document.getElementById('edit_user_id').value = kpi.target_user_id;
                }
            }, 100);
        }

        if (kpi.tan_suat === 'Hàng Tháng') {
            document.getElementById('edit_ts_ht').checked = true;
        } else {
            document.getElementById('edit_ts_cd').checked = true;
        }
        toggleEditFreq();

        // Init Flatpickr date picker for deadline
        if (fpEditDeadline) fpEditDeadline.destroy();
        var deadlineVal = kpi.deadline || null;
        fpEditDeadline = initDatePicker('edit_deadline', document.querySelector('#modalEdit .modal-body'), deadlineVal);

        document.getElementById('edit_nam').value = kpi.nam || new Date().getFullYear();
        document.getElementById('edit_tieu_de').value = kpi.tieu_de || '';
        document.getElementById('edit_noi_dung').value = kpi.noi_dung || '';
        openModal('modalEdit');
    }

    function deleteKpi(id) {
        var df = document.getElementById('deleteForm');
        df.action = '/kpi/' + id;
        df.dataset.submitted = '';
        var btn = df.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = false; btn.style.opacity = ''; btn.innerHTML = '<i class="fa-solid fa-trash-can"></i> Xóa'; }
        openModal('modalDelete');
    }

    var _currentDetailKpiId = null;
    var _currentDetailTanSuat = null;
    var _dmpThang = new Date().getMonth() + 1;
    var _dmpNam = new Date().getFullYear();
    var _dmpMonthNames = ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];

    function updateDmpLabel() {
        document.getElementById('dmpLabel').textContent = 'Tháng ' + _dmpThang + '/' + _dmpNam;
    }

    function toggleDetailMonthPicker() {
        var dd = document.getElementById('dmpDropdown');
        dd.style.display = dd.style.display === 'none' ? '' : 'none';
        if (dd.style.display !== 'none') renderDmpGrid();
    }

    function renderDmpGrid() {
        var dd = document.getElementById('dmpDropdown');
        var html = '<div class="dmp-header">';
        html += '<button type="button" class="dmp-nav" onclick="dmpNavYear(-1)">\u2039</button>';
        html += '<span>' + _dmpNam + '</span>';
        html += '<button type="button" class="dmp-nav" onclick="dmpNavYear(1)">\u203A</button>';
        html += '</div><div class="dmp-grid">';
        for (var m = 1; m <= 12; m++) {
            var cls = m === _dmpThang ? 'dmp-month dmp-selected' : 'dmp-month';
            html += '<button type="button" class="' + cls + '" onclick="dmpSelectMonth(' + m + ')">' + _dmpMonthNames[m-1] + '</button>';
        }
        html += '</div>';
        dd.innerHTML = html;
    }

    function dmpNavYear(delta) {
        _dmpNam += delta;
        renderDmpGrid();
    }

    function dmpSelectMonth(m) {
        _dmpThang = m;
        updateDmpLabel();
        document.getElementById('dmpDropdown').style.display = 'none';
        if (_currentDetailKpiId) {
            fetchDetailData(_currentDetailKpiId, _dmpThang, _dmpNam);
        }
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        var wrap = document.getElementById('dmpWrap');
        var dd = document.getElementById('dmpDropdown');
        if (wrap && dd && !wrap.contains(e.target)) dd.style.display = 'none';
    });

    function openDetail(kpiId) {
        // Toggle: if same KPI clicked again, close it
        if (_currentDetailKpiId === kpiId) {
            closeInlineDetail();
            return;
        }
        _currentDetailKpiId = kpiId;

        // First fetch to determine KPI type
        fetch('/kpi/' + kpiId + '/detail')
            .then(r => r.json())
            .then(data => {
                _currentDetailTanSuat = data.kpi.tan_suat;

                if (data.kpi.tan_suat === 'Hàng Tháng') {
                    // Show month picker, hide deadline badge
                    document.getElementById('dmpWrap').style.display = '';
                    document.getElementById('deadlineBadge').style.display = 'none';

                    // Default to current month in the KPI's year
                    _dmpNam = data.kpi.nam || new Date().getFullYear();
                    _dmpThang = new Date().getMonth() + 1;
                    updateDmpLabel();

                    fetchDetailData(kpiId, _dmpThang, _dmpNam);
                } else {
                    // Cố Định: hide month picker, show deadline badge
                    document.getElementById('dmpWrap').style.display = 'none';

                    // Show deadline warning
                    var badge = document.getElementById('deadlineBadge');
                    if (data.kpi.deadline) {
                        var deadlineDate = new Date(data.kpi.deadline + 'T00:00:00');
                        var today = new Date();
                        today.setHours(0,0,0,0);
                        var diffDays = Math.ceil((deadlineDate - today) / (1000 * 60 * 60 * 24));
                        var ddStr = deadlineDate.toLocaleDateString('vi-VN');

                        if (diffDays < 0) {
                            badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Đã quá hạn ' + Math.abs(diffDays) + ' ngày (' + ddStr + ')';
                            badge.style.background = '#fef2f2';
                            badge.style.color = '#dc2626';
                            badge.style.border = '1px solid #fecaca';
                        } else if (diffDays === 0) {
                            badge.innerHTML = '<i class="fa-solid fa-clock"></i> Hôm nay là deadline (' + ddStr + ')';
                            badge.style.background = '#fffbeb';
                            badge.style.color = '#d97706';
                            badge.style.border = '1px solid #fde68a';
                        } else if (diffDays <= 7) {
                            badge.innerHTML = '<i class="fa-solid fa-clock"></i> Còn ' + diffDays + ' ngày (' + ddStr + ')';
                            badge.style.background = '#fffbeb';
                            badge.style.color = '#d97706';
                            badge.style.border = '1px solid #fde68a';
                        } else {
                            badge.innerHTML = '<i class="fa-regular fa-calendar-check"></i> Deadline: ' + ddStr + ' (còn ' + diffDays + ' ngày)';
                            badge.style.background = '#f0fdf4';
                            badge.style.color = '#16a34a';
                            badge.style.border = '1px solid #bbf7d0';
                        }
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }

                    // Render the data directly (already fetched)
                    renderDetailData(data);
                }
            });
    }

    function fetchDetailData(kpiId, thang, nam) {
        fetch('/kpi/' + kpiId + '/detail?thang=' + thang + '&nam=' + nam)
            .then(r => r.json())
            .then(data => renderDetailData(data));
    }

    function renderDetailData(data) {
        document.getElementById('detailTieuDe').textContent = data.kpi.tieu_de || '';
        document.getElementById('detailNoiDung').textContent = data.kpi.noi_dung || '';
        const tbody = document.getElementById('detailBody');
        tbody.innerHTML = '';

        if (data.users.length === 0) {
            var emptyMsg = _currentDetailTanSuat === 'Hàng Tháng'
                ? 'Không có dữ liệu cho tháng này'
                : 'Chưa có nhân viên nào được gán';
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:24px; color:#94a3b8;"><i class="fa-solid fa-inbox"></i> ' + emptyMsg + '</td></tr>';
        } else {
            data.users.forEach(u => {
                const ttClass = u.trang_thai === 'Chưa Báo Cáo' ? 'tt-chua'
                    : u.trang_thai === 'Đã Báo Cáo' ? 'tt-dabao'
                    : u.trang_thai === 'Hợp Lệ' ? 'tt-hople' : 'tt-baolai';

                const dgHtml = u.danh_gia
                    ? `<span class="badge-trang-thai ${u.danh_gia === 'Đạt KPI' ? 'dg-dat' : u.danh_gia === 'Vượt KPI' ? 'dg-vuot' : 'dg-kdat'}">${u.danh_gia}</span>`
                    : '—';

                const imgHtml = u.hinh_anh
                    ? `<img src="/storage/kpi/${u.hinh_anh}" class="img-preview" onclick="viewImage('/storage/kpi/${u.hinh_anh}')">`
                    : '—';

                let actionHtml = '';
                if (u.trang_thai === 'Đã Báo Cáo') {
                    actionHtml = `
                        <div class="eval-form">
                            <button class="eval-btn btn-hople" onclick="evaluateKpi(${u.id}, 'Hợp Lệ')">✓ Hợp Lệ</button>
                            <button class="eval-btn btn-baolai" onclick="evaluateReject(${u.id})">✗ Báo Cáo Lại</button>
                        </div>`;
                } else if (u.trang_thai === 'Hợp Lệ' && !u.danh_gia) {
                    actionHtml = `
                        <div class="eval-score">
                            <button class="score-btn" onclick="scoreKpi(${u.id}, 'Không Đạt')">Không Đạt</button>
                            <button class="score-btn" onclick="scoreKpi(${u.id}, 'Đạt KPI')">Đạt KPI</button>
                            <button class="score-btn" onclick="scoreKpi(${u.id}, 'Vượt KPI')">Vượt KPI</button>
                        </div>`;
                } else {
                    actionHtml = '<span style="color:#94a3b8; font-size:12px;">—</span>';
                }

                tbody.innerHTML += `<tr>
                    <td style="text-align:left; font-weight:600;">${u.user_name}</td>
                    <td><span class="badge-trang-thai ${ttClass}">${u.trang_thai}</span></td>
                    <td style="text-align:left; max-width:200px; font-size:12px;">${u.bao_cao || '—'}</td>
                    <td>${imgHtml}</td>
                    <td>${dgHtml}</td>
                    <td style="font-size:12px; max-width:150px;">${u.ghi_chu || '—'}</td>
                    <td>${actionHtml}</td>
                </tr>`;
            });
        }

        // Show inline detail and scroll to it
        var panel = document.getElementById('inlineDetail');
        panel.style.display = '';
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function closeInlineDetail() {
        document.getElementById('inlineDetail').style.display = 'none';
        document.getElementById('dmpDropdown').style.display = 'none';
        document.getElementById('dmpWrap').style.display = 'none';
        document.getElementById('deadlineBadge').style.display = 'none';
        _currentDetailKpiId = null;
        _currentDetailTanSuat = null;
    }

    function evaluateKpi(id, trangThai) {
        fetch('/kpi/evaluate/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ trang_thai: trangThai, ghi_chu: '' })
        }).then(r => r.json()).then(() => location.reload());
    }

    function evaluateReject(id) {
        const ghiChu = prompt('Ghi chú lý do báo cáo lại:');
        if (ghiChu === null) return;
        fetch('/kpi/evaluate/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ trang_thai: 'Báo Cáo Lại', ghi_chu: ghiChu })
        }).then(r => r.json()).then(() => location.reload());
    }

    function scoreKpi(id, danhGia) {
        fetch('/kpi/evaluate/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ trang_thai: 'Hợp Lệ', danh_gia: danhGia })
        }).then(r => r.json()).then(() => location.reload());
    }

    function viewImage(src) {
        document.getElementById('imgPreviewFull').src = src;
        document.getElementById('imgPreviewOverlay').style.display = 'flex';
    }

    // ===== Anti double-submit (inline onsubmit) =====
    function preventDoubleSubmit(form) {
        if (form.dataset.submitted === 'true') {
            return false;
        }
        form.dataset.submitted = 'true';
        var btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        }
        return true;
    }
</script>

<!-- Image Preview Overlay -->
<div id="imgPreviewOverlay" onclick="this.style.display='none'" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); z-index:9999; justify-content:center; align-items:center; cursor:pointer;">
    <img id="imgPreviewFull" src="" style="max-width:90%; max-height:90%; border-radius:8px; box-shadow:0 8px 30px rgba(0,0,0,0.5);">
</div>
@endpush
