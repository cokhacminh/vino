@extends('main.layouts.app')

@section('title', 'KPI Của Tôi')

@push('styles')
<style>
    .mykpi-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .mykpi-header h2 { margin:0; font-size:22px; font-weight:700; color:#1e293b; }

    /* Stats panel */
    .stats-panel {
        background: white;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
        padding: 20px;
        margin-bottom: 24px;
    }
    .stats-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .stats-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #475569;
    }

    /* Custom month picker */
    .smp-wrap { position: relative; }
    .smp-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
    }
    .smp-btn:hover { border-color: #6d28d9; color: #6d28d9; }
    .smp-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        background: white;
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
        padding: 12px;
        z-index: 100;
        min-width: 260px;
    }
    .smp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        padding: 0 4px;
        font-weight: 700;
        font-size: 14px;
        color: #334155;
    }
    .smp-nav {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #6d28d9;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s;
    }
    .smp-nav:hover { background: #f5f3ff; }
    .smp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px; }
    .smp-month {
        padding: 8px 4px;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: #475569;
        transition: all 0.15s;
    }
    .smp-month:hover { background: #f5f3ff; color: #6d28d9; }
    .smp-month.smp-selected { background: #6d28d9; color: white; }

    /* Stat cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .scard {
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .scard::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }
    .scard-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .scard-value {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
    }
    .scard-hint {
        font-size: 10px;
        font-weight: 600;
        margin-top: 8px;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .scard:hover .scard-hint { opacity: 1; }
    .scard { cursor: pointer; transition: all 0.2s; }
    .scard:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .scard.scard-active { outline: 2px solid; outline-offset: -2px; }
    .scard-total { background: linear-gradient(135deg, #f5f3ff, #ede9fe); }
    .scard-total::before { background: #6d28d9; }
    .scard-total .scard-label { color: #7c3aed; }
    .scard-total .scard-value { color: #6d28d9; }

    .scard-done { background: linear-gradient(135deg, #f0fdf4, #dcfce7); }
    .scard-done::before { background: #16a34a; }
    .scard-done .scard-label { color: #15803d; }
    .scard-done .scard-value { color: #16a34a; }

    .scard-late { background: linear-gradient(135deg, #fef2f2, #fee2e2); }
    .scard-late::before { background: #dc2626; }
    .scard-late .scard-label { color: #b91c1c; }
    .scard-late .scard-value { color: #dc2626; }

    .scard-dat { background: linear-gradient(135deg, #f0fdf4, #dcfce7); }
    .scard-dat::before { background: #16a34a; }
    .scard-dat .scard-label { color: #15803d; }
    .scard-dat .scard-value { color: #16a34a; }

    .scard-kdat { background: linear-gradient(135deg, #fff7ed, #ffedd5); }
    .scard-kdat::before { background: #ea580c; }
    .scard-kdat .scard-label { color: #c2410c; }
    .scard-kdat .scard-value { color: #ea580c; }

    .scard-vuot { background: linear-gradient(135deg, #eff6ff, #dbeafe); }
    .scard-vuot::before { background: #2563eb; }
    .scard-vuot .scard-label { color: #1d4ed8; }
    .scard-vuot .scard-value { color: #2563eb; }

    /* Cards layout */
    .kpi-list { display:flex; flex-direction:column; gap:12px; }
    .kpi-item {
        background:white;
        border-radius:6px;
        box-shadow:0 1px 3px rgba(0,0,0,0.06),0 4px 12px rgba(0,0,0,0.03);
        overflow:hidden;
        transition:box-shadow 0.2s;
    }
    .kpi-item:hover { box-shadow:0 2px 6px rgba(0,0,0,0.1),0 8px 20px rgba(0,0,0,0.06); }
    .kpi-item-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:16px 20px;
        cursor:pointer;
        gap:12px;
    }
    .kpi-item-header:hover { background:#f8fafc; }
    .kpi-noidung { flex:1; font-size:18px; font-weight:600; color:#1e293b; line-height:1.5; }
    .kpi-badges { display:flex; gap:6px; align-items:center; flex-shrink:0; flex-wrap:wrap; }

    .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; }
    .tt-chua { background:#f1f5f9; color:#64748b; }
    .tt-dabao { background:#fef3c7; color:#d97706; }
    .tt-hople { background:#dcfce7; color:#16a34a; }
    .tt-baolai { background:#fee2e2; color:#dc2626; }
    .dg-dat { background:#dcfce7; color:#16a34a; }
    .dg-vuot { background:#dbeafe; color:#2563eb; }
    .dg-kdat { background:#fee2e2; color:#dc2626; }

    .kpi-item-body {
        display:none;
        padding:0 20px 20px;
        border-top:1px solid #f1f5f9;
    }
    .kpi-item-body.open { display:block; }

    .kpi-info { display:grid; grid-template-columns:1fr 1fr; gap:8px 16px; margin-top:12px; }
    .kpi-info-item { padding:4px 0; }
    .kpi-info-label { font-size:11px; font-weight:600; color:#94a3b8; text-transform:uppercase; }
    .kpi-info-value { font-size:13px; color:#1e293b; font-weight:500; }

    .report-form { margin-top:16px; padding-top:16px; border-top:1px solid #f1f5f9; }
    .form-group { margin-bottom:12px; }
    .form-group label { display:block; margin-bottom:5px; font-size:13px; font-weight:600; color:#374151; }
    .form-input { width:100%; padding:9px 12px; border:2px solid #e2e8f0; border-radius:10px; font-size:13px; box-sizing:border-box; }
    .form-input:focus { outline:none; border-color:#6d28d9; box-shadow:0 0 0 3px rgba(109,40,217,0.1); }
    textarea.form-input { resize:vertical; min-height:80px; }

    .btn-report { padding:10px 24px; background:linear-gradient(135deg,#6d28d9,#4f46e5); color:white; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(109,40,217,0.3); transition:all 0.2s; }
    .btn-report:hover { transform:translateY(-1px); }

    .ghi-chu-box { margin-top:12px; padding:12px; background:#fef3c7; border-radius:10px; border:1px solid #fde68a; }
    .ghi-chu-box .label { font-size:11px; font-weight:700; color:#d97706; text-transform:uppercase; margin-bottom:4px; }
    .ghi-chu-box .value { font-size:13px; color:#92400e; }

    .report-image { max-width:200px; border-radius:8px; margin-top:8px; border:1px solid #e2e8f0; cursor:pointer; }

    .alert { padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; font-weight:500; display:flex; align-items:center; gap:8px; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

    .empty-state { text-align:center; padding:40px; color:#94a3b8; background:white; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
    .empty-state i { font-size:32px; margin-bottom:12px; }

    .img-preview-modal { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center; cursor:pointer; }
    .img-preview-modal.show { display:flex; }

    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* Stat detail panel */
    .stat-detail-panel {
        background: white;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 12px rgba(0,0,0,0.04);
        margin-bottom: 16px;
        overflow: hidden;
        animation: slideDown 0.2s ease-out;
    }
    @keyframes slideDown {
        from { opacity:0; transform:translateY(-8px); }
        to { opacity:1; transform:translateY(0); }
    }
    .sdp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .sdp-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .sdp-close {
        background: none;
        border: none;
        font-size: 18px;
        color: #94a3b8;
        cursor: pointer;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    .sdp-close:hover { background: #f1f5f9; color: #475569; }
    .sdp-body { padding: 0; }
    .sdp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .sdp-table th { padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; background: #f8fafc; }
    .sdp-table td { padding: 10px 16px; border-top: 1px solid #f1f5f9; color: #334155; }
    .sdp-table tr:hover td { background: #f8fafc; }
    .sdp-empty { padding: 24px; text-align: center; color: #94a3b8; font-size: 13px; }
</style>
@endpush

@section('content')
<div>

    <div class="mykpi-header">
        <h2><i class="fa-solid fa-bullseye" style="color:#6d28d9;"></i> KPI CỦA TÔI</h2>
    </div>

    <!-- Stats Panel -->
    <div class="stats-panel">
        <div class="stats-header">
            <h3><i class="fa-solid fa-chart-simple" style="color:#6d28d9;"></i> Thống kê</h3>
            <div class="smp-wrap" id="smpWrap">
                <button type="button" class="smp-btn" id="smpBtn" onclick="toggleSmp()">
                    <i class="fa-regular fa-calendar"></i>
                    <span id="smpLabel">Tháng {{ $statsThang }}/{{ $statsNam }}</span>
                    <i class="fa-solid fa-chevron-down" style="font-size:10px; color:#94a3b8;"></i>
                </button>
                <div class="smp-dropdown" id="smpDropdown" style="display:none;"></div>
            </div>
        </div>
        <div class="stats-grid">
            <div class="scard scard-total" onclick="loadStatDetail('total', 'Tất cả KPI')">
                <div class="scard-label"><i class="fa-solid fa-list-check"></i> Số KPI</div>
                <div class="scard-value">{{ $stats['total'] }}</div>
                <div class="scard-hint">Xem chi tiết →</div>
            </div>
            <div class="scard scard-done" onclick="loadStatDetail('hoan_thanh', 'Hoàn thành đúng hạn')">
                <div class="scard-label"><i class="fa-solid fa-circle-check"></i> Hoàn thành</div>
                <div class="scard-value">{{ $stats['hoan_thanh'] }}</div>
                <div class="scard-hint">Xem chi tiết →</div>
            </div>
            <div class="scard scard-late" onclick="loadStatDetail('tre_deadline', 'Trễ Deadline')">
                <div class="scard-label"><i class="fa-solid fa-clock"></i> Trễ Deadline</div>
                <div class="scard-value">{{ $stats['tre_deadline'] }}</div>
                <div class="scard-hint">Xem chi tiết →</div>
            </div>
            <div class="scard scard-dat" onclick="loadStatDetail('dat', 'Đạt KPI')">
                <div class="scard-label"><i class="fa-solid fa-trophy"></i> Đạt KPI</div>
                <div class="scard-value">{{ $stats['dat'] }}</div>
                <div class="scard-hint">Xem chi tiết →</div>
            </div>
            <div class="scard scard-kdat" onclick="loadStatDetail('khong_dat', 'Không Đạt')">
                <div class="scard-label"><i class="fa-solid fa-xmark"></i> Không Đạt</div>
                <div class="scard-value">{{ $stats['khong_dat'] }}</div>
                <div class="scard-hint">Xem chi tiết →</div>
            </div>
            <div class="scard scard-vuot" onclick="loadStatDetail('vuot', 'Vượt KPI')">
                <div class="scard-label"><i class="fa-solid fa-rocket"></i> Vượt KPI</div>
                <div class="scard-value">{{ $stats['vuot'] }}</div>
                <div class="scard-hint">Xem chi tiết →</div>
            </div>
        </div>
    </div>

    <!-- Stat Detail Panel -->
    <div id="statDetailPanel" style="display:none;"></div>

    <!-- KPI List -->
    @if($myKpis->count() > 0)
    <div class="kpi-list">
        @foreach($myKpis as $ku)
        @php
            $ttClass = match($ku->trang_thai) {
                'Chưa Báo Cáo' => 'tt-chua',
                'Đã Báo Cáo' => 'tt-dabao',
                'Hợp Lệ' => 'tt-hople',
                'Báo Cáo Lại' => 'tt-baolai',
                default => 'tt-chua',
            };
            $dgClass = match($ku->danh_gia) {
                'Đạt KPI' => 'dg-dat',
                'Vượt KPI' => 'dg-vuot',
                'Không Đạt' => 'dg-kdat',
                default => '',
            };
            $canReport = in_array($ku->trang_thai, ['Chưa Báo Cáo', 'Báo Cáo Lại']);
            $deadlineDate = $ku->deadline_time ? \Carbon\Carbon::parse($ku->deadline_time) : null;
            $daysLeft = $deadlineDate ? (int) now()->startOfDay()->diffInDays($deadlineDate, false) : null;
        @endphp
        <div class="kpi-item">
            <div class="kpi-item-header" onclick="toggleKpiItem(this)">
                <div class="kpi-noidung">
                    <i class="fa-solid fa-clipboard-list" style="color:#6d28d9; margin-right:6px;"></i>
                    {{ $ku->kpi->tieu_de ?? $ku->kpi->noi_dung ?? '—' }}
                    @if($ku->kpi->noi_dung && $ku->kpi->tieu_de)
                    <span style="display:block; font-size:14px; color:#94a3b8; font-weight:400; margin-top:2px; margin-left:22px;">{{ Str::limit($ku->kpi->noi_dung, 100) }}</span>
                    @endif
                </div>
                <div class="kpi-badges">
                    @if($deadlineDate)
                        @if($daysLeft < 0)
                            <span class="badge" style="background:#fee2e2; color:#dc2626;"><i class="fa-solid fa-triangle-exclamation"></i> Quá hạn {{ abs($daysLeft) }} ngày</span>
                        @elseif($daysLeft === 0)
                            <span class="badge" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-clock"></i> Hôm nay</span>
                        @elseif($daysLeft <= 7)
                            <span class="badge" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-clock"></i> Còn {{ $daysLeft }} ngày</span>
                        @else
                            <span class="badge" style="background:#f0fdf4; color:#16a34a;"><i class="fa-regular fa-calendar-check"></i> {{ $deadlineDate->format('d/m/Y') }}</span>
                        @endif
                    @endif
                    <span class="badge {{ $ttClass }}">{{ $ku->trang_thai }}</span>
                    @if($ku->danh_gia)
                        <span class="badge {{ $dgClass }}">{{ $ku->danh_gia }}</span>
                    @endif
                </div>
            </div>
            <div class="kpi-item-body">
                <!-- Previous report -->
                @if($ku->bao_cao)
                <div class="kpi-info">
                    <div class="kpi-info-item" style="grid-column:1/-1;">
                        <div class="kpi-info-label">Nội dung báo cáo</div>
                        <div class="kpi-info-value">{{ $ku->bao_cao }}</div>
                    </div>
                </div>
                @endif

                @if($ku->hinh_anh)
                <div style="margin-top:8px;">
                    <div class="kpi-info-label">Ảnh đính kèm</div>
                    <img src="{{ asset('storage/kpi/' . $ku->hinh_anh) }}" class="report-image"
                         onclick="viewImage('{{ asset('storage/kpi/' . $ku->hinh_anh) }}')">
                </div>
                @endif

                @if($ku->ghi_chu)
                <div class="ghi-chu-box">
                    <div class="label"><i class="fa-solid fa-comment-dots"></i> Ghi chú từ quản lý</div>
                    <div class="value">{{ $ku->ghi_chu }}</div>
                </div>
                @endif

                <!-- Report form -->
                @if($canReport)
                <div class="report-form">
                    <form action="{{ route('mykpi.report', $ku->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label><i class="fa-solid fa-pen"></i> Báo cáo KPI</label>
                            <textarea name="bao_cao" class="form-input" placeholder="Nhập nội dung báo cáo KPI..." required>{{ $ku->bao_cao }}</textarea>
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-image"></i> Ảnh đính kèm (tùy chọn)</label>
                            <input type="file" name="hinh_anh" class="form-input" accept="image/*" style="padding:8px;">
                        </div>
                        <button type="submit" class="btn-report">
                            <i class="fa-solid fa-paper-plane"></i> Gửi Báo Cáo
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="fa-solid fa-inbox"></i>
        <p>Chưa có KPI nào</p>
    </div>
    @endif
</div>

<!-- Image Preview Modal -->
<div class="img-preview-modal" id="imageModal" onclick="this.classList.remove('show')">
    <img id="imagePreviewSrc" src="" style="max-width:90%; max-height:90vh; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.3);">
</div>

@endsection

@push('scripts')
<script>
    var _smpThang = {{ $statsThang }};
    var _smpNam = {{ $statsNam }};
    var _smpMonths = ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];
    var _activeStatFilter = null;

    function toggleSmp() {
        var dd = document.getElementById('smpDropdown');
        dd.style.display = dd.style.display === 'none' ? '' : 'none';
        if (dd.style.display !== 'none') renderSmpGrid();
    }

    function renderSmpGrid() {
        var dd = document.getElementById('smpDropdown');
        var html = '<div class="smp-header">';
        html += '<button type="button" class="smp-nav" onclick="smpNavYear(-1)">\u2039</button>';
        html += '<span>' + _smpNam + '</span>';
        html += '<button type="button" class="smp-nav" onclick="smpNavYear(1)">\u203A</button>';
        html += '</div><div class="smp-grid">';
        for (var m = 1; m <= 12; m++) {
            var cls = m === _smpThang ? 'smp-month smp-selected' : 'smp-month';
            html += '<button type="button" class="' + cls + '" onclick="smpSelect(' + m + ')">' + _smpMonths[m-1] + '</button>';
        }
        html += '</div>';
        dd.innerHTML = html;
    }

    function smpNavYear(delta) { _smpNam += delta; renderSmpGrid(); }

    function smpSelect(m) {
        _smpThang = m;
        document.getElementById('smpDropdown').style.display = 'none';
        window.location.href = `{{ route('mykpi.index') }}?stats_thang=${_smpThang}&stats_nam=${_smpNam}`;
    }

    document.addEventListener('click', function(e) {
        var wrap = document.getElementById('smpWrap');
        var dd = document.getElementById('smpDropdown');
        if (wrap && dd && !wrap.contains(e.target)) dd.style.display = 'none';
    });

    // Stat detail drill-down
    function loadStatDetail(filter, title) {
        // Toggle: click same card again to close
        if (_activeStatFilter === filter) {
            closeStatDetail();
            return;
        }

        // Highlight active card
        document.querySelectorAll('.scard').forEach(function(c) { c.classList.remove('scard-active'); });
        event.currentTarget.classList.add('scard-active');
        _activeStatFilter = filter;

        var panel = document.getElementById('statDetailPanel');
        panel.style.display = '';
        panel.innerHTML = '<div class="stat-detail-panel"><div style="padding:24px; text-align:center; color:#94a3b8;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div></div>';

        fetch(`{{ route('mykpi.statDetail') }}?thang=${_smpThang}&nam=${_smpNam}&filter=${filter}`)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderStatDetail(data, title);
            })
            .catch(function() {
                panel.innerHTML = '<div class="stat-detail-panel"><div class="sdp-empty">Không thể tải dữ liệu.</div></div>';
            });
    }

    function renderStatDetail(data, title) {
        var panel = document.getElementById('statDetailPanel');
        var html = '<div class="stat-detail-panel">';
        html += '<div class="sdp-header">';
        html += '<div class="sdp-title"><i class="fa-solid fa-filter" style="color:#6d28d9; margin-right:6px;"></i>' + title + ' <span style="font-weight:400; color:#94a3b8;">(' + data.length + ' KPI)</span></div>';
        html += '<button class="sdp-close" onclick="closeStatDetail()" title="Đóng">✕</button>';
        html += '</div>';

        if (data.length === 0) {
            html += '<div class="sdp-empty"><i class="fa-solid fa-inbox" style="font-size:20px; display:block; margin-bottom:6px;"></i>Không có KPI nào</div>';
        } else {
            html += '<div class="sdp-body"><table class="sdp-table"><thead><tr>';
            html += '<th>KPI</th><th>Trạng Thái</th><th>Đánh Giá</th><th>Deadline</th><th>Ngày BC</th>';
            html += '</tr></thead><tbody>';
            data.forEach(function(item) {
                var ttBadge = getTtBadge(item.trang_thai);
                var dgBadge = item.danh_gia ? getDgBadge(item.danh_gia) : '<span style="color:#cbd5e1;">—</span>';
                html += '<tr>';
                html += '<td style="font-weight:600; max-width:250px;">' + item.tieu_de + '</td>';
                html += '<td>' + ttBadge + '</td>';
                html += '<td>' + dgBadge + '</td>';
                html += '<td>' + (item.deadline_time || '—') + '</td>';
                html += '<td>' + (item.reported_at || '<span style="color:#cbd5e1;">—</span>') + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
        }
        html += '</div>';
        panel.innerHTML = html;
    }

    function closeStatDetail() {
        document.getElementById('statDetailPanel').style.display = 'none';
        document.getElementById('statDetailPanel').innerHTML = '';
        document.querySelectorAll('.scard').forEach(function(c) { c.classList.remove('scard-active'); });
        _activeStatFilter = null;
    }

    function getTtBadge(tt) {
        var cls = {'Chưa Báo Cáo':'tt-chua','Đã Báo Cáo':'tt-dabao','Hợp Lệ':'tt-hople','Báo Cáo Lại':'tt-baolai'};
        return '<span class="badge ' + (cls[tt]||'tt-chua') + '">' + tt + '</span>';
    }
    function getDgBadge(dg) {
        var cls = {'Đạt KPI':'dg-dat','Vượt KPI':'dg-vuot','Không Đạt':'dg-kdat'};
        return '<span class="badge ' + (cls[dg]||'') + '">' + dg + '</span>';
    }

    function toggleKpiItem(header) {
        const body = header.nextElementSibling;
        body.classList.toggle('open');
    }

    function viewImage(src) {
        document.getElementById('imagePreviewSrc').src = src;
        document.getElementById('imageModal').classList.add('show');
    }
</script>
@endpush
