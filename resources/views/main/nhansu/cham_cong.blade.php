@extends('main.layouts.app')

@section('title', 'Chấm Công')

@push('styles')
<style>
    .cc-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .cc-header h2 { margin:0; font-size:22px; font-weight:700; color:#1e293b; }
    .cc-filters { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }

    /* IP Badge */
    .ip-badge {
        display:inline-flex; align-items:center; gap:6px;
        padding:6px 12px; background:#f0fdf4; border:1.5px solid #86efac;
        border-radius:10px; font-size:12px; font-weight:600; color:#16a34a;
    }
    .ip-badge.no-ip { background:#fef2f2; border-color:#fca5a5; color:#dc2626; }
    .btn-update-ip {
        display:inline-flex; align-items:center; gap:5px;
        padding:8px 14px; background:linear-gradient(135deg,#7c3aed,#6d28d9);
        border:none; border-radius:10px; font-size:12px; font-weight:600;
        color:white; cursor:pointer; transition:all 0.2s;
    }
    .btn-update-ip:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(109,40,217,0.3); }

    /* Month picker */
    .cmp-wrap { position:relative; }
    .cmp-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:8px 14px; background:linear-gradient(135deg,#f8fafc,#f1f5f9);
        border:2px solid #e2e8f0; border-radius:10px;
        font-size:13px; font-weight:600; color:#475569;
        cursor:pointer; transition:all 0.2s;
    }
    .cmp-btn:hover { border-color:#6d28d9; color:#6d28d9; }
    .cmp-dropdown {
        position:absolute; top:calc(100% + 6px); right:0;
        background:white; border-radius:14px;
        box-shadow:0 8px 30px rgba(0,0,0,0.12),0 2px 8px rgba(0,0,0,0.06);
        padding:12px; z-index:100; min-width:260px;
    }
    .cmp-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding:0 4px; }
    .cmp-header span { font-size:15px; font-weight:700; color:#1e293b; }
    .cmp-nav { background:none; border:none; font-size:20px; cursor:pointer; color:#6d28d9; padding:4px 8px; border-radius:6px; }
    .cmp-nav:hover { background:#f5f3ff; }
    .cmp-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:4px; }
    .cmp-month {
        padding:8px 4px; border:none; border-radius:8px; font-size:12px;
        font-weight:600; cursor:pointer; background:transparent; color:#475569;
        transition:all 0.15s;
    }
    .cmp-month:hover { background:#f5f3ff; color:#6d28d9; }
    .cmp-selected { background:#6d28d9 !important; color:white !important; }

    /* Grid table */
    .cc-card { background:white; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04); overflow:hidden; }
    .cc-scroll { overflow-x:auto; }
    .cc-table { border-collapse:collapse; width:auto; min-width:100%; }
    .cc-table th, .cc-table td { padding:0; border:1px solid #e2e8f0; text-align:center; }
    .cc-table thead th {
        padding:8px 4px; background:#f8fafc; font-size:11px; font-weight:700;
        color:#64748b; white-space:nowrap; position:sticky; top:0; z-index:2;
    }
    .cc-table thead th.sticky-col { position:sticky; z-index:4; }
    .cc-table .sticky-col {
        position:sticky; left:0; background:white; z-index:3;
        border-right:2px solid #cbd5e1;
    }
    .cc-table thead .sticky-col { background:#f8fafc; }
    .cc-table .sticky-col-2 {
        position:sticky; left:180px; background:white; z-index:3;
        border-right:2px solid #cbd5e1;
    }
    .cc-table thead .sticky-col-2 { background:#f8fafc; }

    .cc-table tbody tr:hover td { background:#f8fafc; }
    .cc-table tbody tr:hover .sticky-col,
    .cc-table tbody tr:hover .sticky-col-2 { background:#f8fafc; }

    .user-cell { padding:8px 10px !important; text-align:left; white-space:nowrap; min-width:180px; font-size:12px; font-weight:600; color:#1e293b; }
    .dept-cell { padding:6px 8px !important; text-align:left; white-space:nowrap; min-width:100px; font-size:11px; color:#64748b; }

    /* Day cells */
    .day-cell {
        width:36px; height:36px; min-width:36px;
        cursor:pointer; position:relative; transition:background 0.15s;
        font-size:14px; line-height:36px;
    }
    .day-cell:hover { background:#f5f3ff; }
    .day-sun { background:#fff1f2 !important; }
    .day-sun:hover { background:#ffe4e6 !important; }
    /* Cells with notes indicator */
    .day-cell.has-note::after {
        content:''; position:absolute; top:2px; right:2px;
        width:5px; height:5px; border-radius:50%; background:#f59e0b;
    }
    .day-cell.has-time::before {
        content:attr(data-time); position:absolute; bottom:0; left:0; right:0;
        font-size:7px; line-height:1; color:#94a3b8; white-space:nowrap;
        overflow:hidden; text-overflow:ellipsis;
    }

    /* Status popup - enhanced */
    .cc-popup {
        display:none; position:fixed; z-index:1000;
        background:white; border-radius:14px;
        box-shadow:0 8px 30px rgba(0,0,0,0.15),0 2px 8px rgba(0,0,0,0.08);
        padding:0; min-width:220px; overflow:hidden;
    }
    .cc-popup-statuses { padding:6px; }
    .cc-popup-item {
        display:flex; align-items:center; gap:8px;
        padding:8px 12px; border:none; background:transparent; width:100%;
        font-size:12px; font-weight:600; cursor:pointer; border-radius:8px;
        text-align:left; color:#334155; transition:background 0.15s;
    }
    .cc-popup-item:hover { background:#f5f3ff; }
    .cc-popup-item .pi { font-size:16px; width:22px; text-align:center; }
    .cc-popup-divider { height:1px; background:#e2e8f0; margin:0; }
    .cc-popup-clear { color:#dc2626; }
    .cc-popup-clear:hover { background:#fef2f2; }
    .cc-popup-extra {
        padding:8px 12px; background:#f8fafc; border-top:1px solid #e2e8f0;
    }
    .cc-popup-extra label { display:block; font-size:10px; font-weight:700; color:#64748b; margin-bottom:3px; text-transform:uppercase; letter-spacing:0.5px; }
    .cc-popup-extra .time-row { display:flex; gap:6px; margin-bottom:6px; }
    .cc-popup-extra input[type="time"],
    .cc-popup-extra input[type="text"] {
        flex:1; padding:5px 8px; border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:12px; font-weight:500; outline:none; transition:border 0.2s;
    }
    .cc-popup-extra input:focus { border-color:#7c3aed; }
    .cc-popup-extra .time-wrap { flex:1; }

    /* Tooltip */
    .cc-tooltip {
        display:none; position:fixed; z-index:999;
        background:#1e293b; color:white; border-radius:10px;
        padding:8px 12px; font-size:11px; max-width:200px;
        box-shadow:0 4px 12px rgba(0,0,0,0.2); pointer-events:none;
    }
    .cc-tooltip .tt-status { font-weight:700; margin-bottom:3px; }
    .cc-tooltip .tt-time { color:#94a3b8; }
    .cc-tooltip .tt-note { color:#fbbf24; margin-top:3px; font-style:italic; }

    /* Summary cols */
    .sum-col { padding:6px 4px !important; font-size:12px; font-weight:700; min-width:40px; }
    .sum-header { background:#f5f3ff !important; color:#6d28d9 !important; }

    /* Legend */
    .cc-legend { display:flex; gap:16px; padding:12px 20px; border-top:1px solid #f1f5f9; flex-wrap:wrap; }
    .cc-legend-item { display:flex; align-items:center; gap:5px; font-size:12px; color:#475569; font-weight:500; }
    .cc-legend-icon { font-size:16px; width:22px; text-align:center; }

    @media (max-width:768px) {
        .user-cell { min-width:140px; }
    }

    /* Settings panel */
    .cc-settings {
        background:white; border-radius:14px;
        box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04);
        margin-bottom:20px; overflow:hidden;
    }
    .cc-settings-header {
        display:flex; justify-content:space-between; align-items:center;
        padding:14px 20px; cursor:pointer; transition:background 0.2s;
    }
    .cc-settings-header:hover { background:#f8fafc; }
    .cc-settings-header h3 {
        margin:0; font-size:14px; font-weight:700; color:#1e293b;
        display:flex; align-items:center; gap:8px;
    }
    .cc-settings-header h3 i { color:#6d28d9; }
    .cc-settings-toggle { font-size:12px; color:#94a3b8; transition:transform 0.3s; }
    .cc-settings-body {
        max-height:0; overflow:hidden; transition:max-height 0.3s ease;
    }
    .cc-settings-body.open { max-height:300px; }
    .cc-settings-grid {
        display:grid; grid-template-columns:repeat(3,1fr); gap:12px;
        padding:0 20px 16px;
    }
    @media (max-width:768px) { .cc-settings-grid { grid-template-columns:1fr 1fr; } }
    .setting-item label {
        display:block; font-size:10px; font-weight:700; color:#94a3b8;
        text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;
    }
    .setting-item input[type="time"] {
        width:100%; padding:7px 10px; border:2px solid #e2e8f0; border-radius:8px;
        font-size:14px; font-weight:600; color:#1e293b; outline:none; transition:border 0.2s;
    }
    .setting-item input[type="time"]:focus { border-color:#7c3aed; }
    .setting-item input[type="time"]:disabled { background:#f8fafc; color:#64748b; cursor:not-allowed; }
    .setting-item .s-value {
        padding:7px 10px; background:#f8fafc; border-radius:8px;
        font-size:14px; font-weight:600; color:#1e293b;
    }
    .cc-settings-footer {
        display:none; padding:0 20px 16px; text-align:right;
    }
    .cc-settings-footer.show { display:block; }
    .btn-save-settings {
        padding:8px 20px; border:none; border-radius:8px;
        background:linear-gradient(135deg,#6d28d9,#4f46e5); color:white;
        font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s;
    }
    .btn-save-settings:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(109,40,217,0.3); }
</style>
@endpush

@section('content')
<div>
    <div class="cc-header">
        <h2><i class="fa-solid fa-clipboard-check" style="color:#6d28d9;"></i> Chấm Công</h2>
        <div class="cc-filters">
            @if($todayIp)
                <span class="ip-badge">
                    <i class="fa-solid fa-shield-check"></i>
                    IP: {{ $todayIp->wan_ip }}
                </span>
            @else
                <span class="ip-badge no-ip">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Chưa có IP chấm công hôm nay
                </span>
            @endif
            <button type="button" class="btn-update-ip" onclick="updateCheckinIp()">
                <i class="fa-solid fa-rotate"></i> Cập Nhật IP Chấm Công
            </button>
            <div class="cmp-wrap" id="cmpWrap">
                <button type="button" class="cmp-btn" onclick="toggleCmp()">
                    <i class="fa-regular fa-calendar"></i>
                    <span id="cmpLabel">Tháng {{ $thang }}/{{ $nam }}</span>
                    <i class="fa-solid fa-chevron-down" style="font-size:10px; color:#94a3b8;"></i>
                </button>
                <div class="cmp-dropdown" id="cmpDropdown" style="display:none;"></div>
            </div>
        </div>
    </div>

    <!-- Thiết lập thời gian chấm công -->
    <div class="cc-settings">
        <div class="cc-settings-header" onclick="toggleSettings()">
            <h3><i class="fa-solid fa-gear"></i> Thiết Lập Thời Gian Chấm Công</h3>
            <i class="fa-solid fa-chevron-down cc-settings-toggle" id="settingsToggle"></i>
        </div>
        <div class="cc-settings-body" id="settingsBody">
            <div class="cc-settings-grid">
                <div class="setting-item">
                    <label>Giờ bắt đầu làm việc</label>
                    @if($isAdmin)
                    <input type="time" id="s_gio_bat_dau" value="{{ $settings['gio_bat_dau'] ?? '08:00' }}">
                    @else
                    <div class="s-value">{{ $settings['gio_bat_dau'] ?? '08:00' }}</div>
                    @endif
                </div>
                <div class="setting-item">
                    <label>Giờ kết thúc làm việc</label>
                    @if($isAdmin)
                    <input type="time" id="s_gio_ket_thuc" value="{{ $settings['gio_ket_thuc'] ?? '17:30' }}">
                    @else
                    <div class="s-value">{{ $settings['gio_ket_thuc'] ?? '17:30' }}</div>
                    @endif
                </div>
                <div class="setting-item">
                    <label>Giờ tính đi trễ</label>
                    @if($isAdmin)
                    <input type="time" id="s_gio_tre_han" value="{{ $settings['gio_tre_han'] ?? '08:30' }}">
                    @else
                    <div class="s-value">{{ $settings['gio_tre_han'] ?? '08:30' }}</div>
                    @endif
                </div>
                <div class="setting-item">
                    <label>Giờ tính về sớm</label>
                    @if($isAdmin)
                    <input type="time" id="s_gio_som_han" value="{{ $settings['gio_som_han'] ?? '17:00' }}">
                    @else
                    <div class="s-value">{{ $settings['gio_som_han'] ?? '17:00' }}</div>
                    @endif
                </div>
                <div class="setting-item">
                    <label>Giờ mở check-in</label>
                    @if($isAdmin)
                    <input type="time" id="s_checkin_mo" value="{{ $settings['checkin_mo'] ?? '06:00' }}">
                    @else
                    <div class="s-value">{{ $settings['checkin_mo'] ?? '06:00' }}</div>
                    @endif
                </div>
                <div class="setting-item">
                    <label>Giờ đóng check-in</label>
                    @if($isAdmin)
                    <input type="time" id="s_checkin_dong" value="{{ $settings['checkin_dong'] ?? '22:00' }}">
                    @else
                    <div class="s-value">{{ $settings['checkin_dong'] ?? '22:00' }}</div>
                    @endif
                </div>
            </div>
            @if($isAdmin)
            <div class="cc-settings-footer show">
                <button type="button" class="btn-save-settings" onclick="saveSettings()">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Thiết Lập
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="cc-card">
        <div class="cc-scroll">
            <table class="cc-table">
                <thead>
                    <tr>
                        <th class="sticky-col" style="text-align:left; padding-left:10px;">Nhân Viên</th>
                        <th class="sticky-col-2" style="text-align:left; padding-left:8px;">P.Ban</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dt = \Carbon\Carbon::createFromDate($nam, $thang, $d);
                                $isSun = $dt->dayOfWeek === 0;
                                $dayLabel = $dt->format('D');
                                $dayMap = ['Mon'=>'T2','Tue'=>'T3','Wed'=>'T4','Thu'=>'T5','Fri'=>'T6','Sat'=>'T7','Sun'=>'CN'];
                            @endphp
                            <th class="{{ $isSun ? 'day-sun' : '' }}" style="padding:4px 2px;">
                                <div style="font-size:10px; color:#94a3b8;">{{ $dayMap[$dayLabel] ?? $dayLabel }}</div>
                                <div style="font-size:13px;">{{ $d }}</div>
                            </th>
                        @endfor
                        <th class="sum-header">✅</th>
                        <th class="sum-header">❌</th>
                        <th class="sum-header">📋</th>
                        <th class="sum-header">½</th>
                        <th class="sum-header">🏠</th>
                        <th class="sum-header" style="background:#dcfce7 !important; color:#16a34a !important;">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    @php
                        $cntDL = 0; $cntKP = 0; $cntP = 0; $cntNN = 0; $cntWFH = 0;
                    @endphp
                    <tr>
                        <td class="user-cell sticky-col">
                            <i class="fa-solid fa-user-circle" style="color:#6d28d9; margin-right:4px;"></i>
                            {{ $user->name }}
                        </td>
                        <td class="dept-cell sticky-col-2">{{ $user->TenPB ?? '—' }}</td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateStr = sprintf('%04d-%02d-%02d', $nam, $thang, $d);
                                $key = $user->id . '-' . $dateStr;
                                $rec = $records[$key] ?? null;
                                $status = $rec ? $rec->trang_thai : null;
                                $gioVao = $rec && $rec->gio_vao ? substr($rec->gio_vao, 0, 5) : '';
                                $gioRa = $rec && $rec->gio_ra ? substr($rec->gio_ra, 0, 5) : '';
                                $ghiChu = $rec ? ($rec->ghi_chu ?? '') : '';
                                $dt = \Carbon\Carbon::createFromDate($nam, $thang, $d);
                                $isSun = $dt->dayOfWeek === 0;
                                $icon = '';
                                if ($status === 'Đi Làm') { $icon = '✅'; $cntDL++; }
                                elseif ($status === 'Không Phép') { $icon = '❌'; $cntKP++; }
                                elseif ($status === 'Phép') { $icon = '📋'; $cntP++; }
                                elseif ($status === 'Nửa Ngày') { $icon = '½'; $cntNN++; }
                                elseif ($status === 'WFH') { $icon = '🏠'; $cntWFH++; }
                                $extraCls = '';
                                if ($ghiChu) $extraCls .= ' has-note';
                            @endphp
                            <td class="day-cell {{ $isSun ? 'day-sun' : '' }}{{ $extraCls }}"
                                data-uid="{{ $user->id }}" data-date="{{ $dateStr }}"
                                data-gio-vao="{{ $gioVao }}" data-gio-ra="{{ $gioRa }}"
                                data-ghi-chu="{{ e($ghiChu) }}"
                                onclick="showPopup(event, this)"
                                onmouseenter="showTooltip(event, this)"
                                onmouseleave="hideTooltip()">
                                {{ $icon }}
                            </td>
                        @endfor
                        @php $tongCong = $cntDL + $cntWFH + ($cntNN * 0.5); @endphp
                        <td class="sum-col" style="color:#16a34a;">{{ $cntDL ?: '' }}</td>
                        <td class="sum-col" style="color:#dc2626;">{{ $cntKP ?: '' }}</td>
                        <td class="sum-col" style="color:#2563eb;">{{ $cntP ?: '' }}</td>
                        <td class="sum-col" style="color:#d97706;">{{ $cntNN ?: '' }}</td>
                        <td class="sum-col" style="color:#7c3aed;">{{ $cntWFH ?: '' }}</td>
                        <td class="sum-col" style="color:#16a34a; background:#f0fdf4; font-size:13px;">{{ $tongCong ? (fmod($tongCong, 1) == 0 ? (int)$tongCong : $tongCong) : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="cc-legend">
            <div class="cc-legend-item"><span class="cc-legend-icon">✅</span> Đi Làm</div>
            <div class="cc-legend-item"><span class="cc-legend-icon">❌</span> Không Phép</div>
            <div class="cc-legend-item"><span class="cc-legend-icon">📋</span> Phép</div>
            <div class="cc-legend-item"><span class="cc-legend-icon">½</span> Nửa Ngày</div>
            <div class="cc-legend-item"><span class="cc-legend-icon">🏠</span> WFH</div>
            <div class="cc-legend-item"><span class="cc-legend-icon" style="font-size:10px;">🟡</span> Có ghi chú</div>
        </div>
    </div>
</div>

<!-- Status Popup -->
<div class="cc-popup" id="ccPopup">
    <div class="cc-popup-statuses">
        <button type="button" class="cc-popup-item" onclick="selectStatus('Đi Làm')"><span class="pi">✅</span> Đi Làm</button>
        <button type="button" class="cc-popup-item" onclick="selectStatus('Không Phép')"><span class="pi">❌</span> Không Phép</button>
        <button type="button" class="cc-popup-item" onclick="selectStatus('Phép')"><span class="pi">📋</span> Phép</button>
        <button type="button" class="cc-popup-item" onclick="selectStatus('Nửa Ngày')"><span class="pi">½</span> Nửa Ngày</button>
        <button type="button" class="cc-popup-item" onclick="selectStatus('WFH')"><span class="pi">🏠</span> WFH</button>
        <div class="cc-popup-divider"></div>
        <button type="button" class="cc-popup-item cc-popup-clear" onclick="selectStatus('')"><span class="pi">🗑</span> Xóa</button>
    </div>
    <div class="cc-popup-extra">
        <div class="time-row">
            <div class="time-wrap">
                <label>Giờ vào</label>
                <input type="time" id="popGioVao">
            </div>
            <div class="time-wrap">
                <label>Giờ ra</label>
                <input type="time" id="popGioRa">
            </div>
        </div>
        <label>Ghi chú</label>
        <input type="text" id="popGhiChu" placeholder="Lý do nghỉ, đến trễ..." style="width:100%; box-sizing:border-box;">
    </div>
</div>

<!-- Tooltip -->
<div class="cc-tooltip" id="ccTooltip">
    <div class="tt-status"></div>
    <div class="tt-time"></div>
    <div class="tt-note"></div>
</div>
@stop

@push('scripts')
<script>
    var _cmpThang = {{ $thang }};
    var _cmpNam = {{ $nam }};
    var _cmpMonths = ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];
    var _activeCell = null;
    var _statusIcons = {
        'Đi Làm': '✅',
        'Không Phép': '❌',
        'Phép': '📋',
        'Nửa Ngày': '½',
        'WFH': '🏠'
    };

    // ========== Month picker ==========
    function toggleCmp() {
        var dd = document.getElementById('cmpDropdown');
        dd.style.display = dd.style.display === 'none' ? '' : 'none';
        if (dd.style.display !== 'none') renderCmpGrid();
    }
    function renderCmpGrid() {
        var dd = document.getElementById('cmpDropdown');
        var html = '<div class="cmp-header">';
        html += '<button type="button" class="cmp-nav" onclick="cmpNavYear(-1)">‹</button>';
        html += '<span>' + _cmpNam + '</span>';
        html += '<button type="button" class="cmp-nav" onclick="cmpNavYear(1)">›</button>';
        html += '</div><div class="cmp-grid">';
        for (var m = 1; m <= 12; m++) {
            var cls = m === _cmpThang ? 'cmp-month cmp-selected' : 'cmp-month';
            html += '<button type="button" class="' + cls + '" onclick="cmpSelect(' + m + ')">' + _cmpMonths[m-1] + '</button>';
        }
        html += '</div>';
        dd.innerHTML = html;
    }
    function cmpNavYear(d) { _cmpNam += d; renderCmpGrid(); }
    function cmpSelect(m) {
        _cmpThang = m;
        document.getElementById('cmpDropdown').style.display = 'none';
        window.location.href = '{{ route("chamcong.index") }}?thang=' + _cmpThang + '&nam=' + _cmpNam;
    }

    // ========== Tooltip ==========
    function showTooltip(e, td) {
        var status = td.textContent.trim();
        if (!status) return;
        var gioVao = td.getAttribute('data-gio-vao');
        var gioRa = td.getAttribute('data-gio-ra');
        var ghiChu = td.getAttribute('data-ghi-chu');

        var tip = document.getElementById('ccTooltip');
        var statusText = '';
        for (var k in _statusIcons) {
            if (_statusIcons[k] === status) { statusText = k; break; }
        }
        tip.querySelector('.tt-status').textContent = statusText || status;
        var timeText = '';
        if (gioVao) timeText += '🕐 Vào: ' + gioVao;
        if (gioRa) timeText += (timeText ? '  ' : '') + '🕐 Ra: ' + gioRa;
        tip.querySelector('.tt-time').textContent = timeText;
        tip.querySelector('.tt-time').style.display = timeText ? '' : 'none';
        tip.querySelector('.tt-note').textContent = ghiChu ? '📝 ' + ghiChu : '';
        tip.querySelector('.tt-note').style.display = ghiChu ? '' : 'none';

        tip.style.display = 'block';
        var rect = td.getBoundingClientRect();
        tip.style.left = (rect.left + rect.width / 2 - 60) + 'px';
        tip.style.top = (rect.top - tip.offsetHeight - 6) + 'px';
    }
    function hideTooltip() {
        document.getElementById('ccTooltip').style.display = 'none';
    }

    // ========== Status popup ==========
    function showPopup(e, td) {
        e.stopPropagation();
        hideTooltip();
        _activeCell = td;
        var popup = document.getElementById('ccPopup');

        // Pre-fill time & note
        document.getElementById('popGioVao').value = td.getAttribute('data-gio-vao') || '';
        document.getElementById('popGioRa').value = td.getAttribute('data-gio-ra') || '';
        document.getElementById('popGhiChu').value = td.getAttribute('data-ghi-chu') || '';

        popup.style.display = 'block';
        var rect = td.getBoundingClientRect();
        var popupW = 220;
        var popupH = popup.offsetHeight || 350;
        var x = rect.left + rect.width / 2 - popupW / 2;
        var y = rect.bottom + 4;
        if (y + popupH > window.innerHeight) y = rect.top - popupH - 4;
        if (x < 4) x = 4;
        if (x + popupW > window.innerWidth) x = window.innerWidth - popupW - 4;
        popup.style.left = x + 'px';
        popup.style.top = y + 'px';
    }

    function selectStatus(status) {
        if (!_activeCell) return;
        var uid = _activeCell.getAttribute('data-uid');
        var date = _activeCell.getAttribute('data-date');
        var cell = _activeCell;
        var gioVao = document.getElementById('popGioVao').value || null;
        var gioRa = document.getElementById('popGioRa').value || null;
        var ghiChu = document.getElementById('popGhiChu').value || null;

        // Optimistic update
        cell.textContent = status ? (_statusIcons[status] || '') : '';

        document.getElementById('ccPopup').style.display = 'none';

        var body = { user_id: uid, ngay: date, trang_thai: status || null };
        if (gioVao) body.gio_vao = gioVao;
        if (gioRa) body.gio_ra = gioRa;
        if (ghiChu !== null) body.ghi_chu = ghiChu;

        fetch('{{ route("chamcong.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(body)
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.ok) {
                cell.textContent = data.trang_thai ? (_statusIcons[data.trang_thai] || '') : '';
                cell.setAttribute('data-gio-vao', data.gio_vao || '');
                cell.setAttribute('data-gio-ra', data.gio_ra || '');
                cell.setAttribute('data-ghi-chu', data.ghi_chu || '');
                // Update CSS classes
                cell.classList.toggle('has-note', !!data.ghi_chu);
                updateRowSummary(cell.parentElement);
            }
        })
        .catch(function() {
            cell.textContent = '⚠';
        });

        _activeCell = null;
    }

    function updateRowSummary(row) {
        var cells = row.querySelectorAll('.day-cell');
        var cnt = { '✅':0, '❌':0, '📋':0, '½':0, '🏠':0 };
        cells.forEach(function(c) {
            var t = c.textContent.trim();
            if (cnt.hasOwnProperty(t)) cnt[t]++;
        });
        var sumCells = row.querySelectorAll('.sum-col');
        var keys = ['✅','❌','📋','½','🏠'];
        keys.forEach(function(k, i) {
            if (sumCells[i]) sumCells[i].textContent = cnt[k] || '';
        });
        var tong = cnt['✅'] + cnt['🏠'] + (cnt['½'] * 0.5);
        if (sumCells[5]) sumCells[5].textContent = tong ? (tong % 1 === 0 ? tong : tong) : '';
    }

    // ========== Update Checkin IP ==========
    function updateCheckinIp() {
        // Lấy LAN IP qua WebRTC
        getLanIp(function(lanIp) {
            fetch('{{ route("chamcong.updateIp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ lan_ip: lanIp || '' })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.ok) {
                    // Update badge
                    var badges = document.querySelectorAll('.ip-badge');
                    badges.forEach(function(b) {
                        b.className = 'ip-badge';
                        b.innerHTML = '<i class="fa-solid fa-shield-check"></i> IP: ' + data.wan_ip;
                    });
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon:'success', title:'Thành công!', text: data.message, timer:2000, showConfirmButton:false });
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(function() {
                alert('Lỗi cập nhật IP!');
            });
        });
    }

    function getLanIp(callback) {
        try {
            var pc = new RTCPeerConnection({ iceServers: [] });
            pc.createDataChannel('');
            pc.createOffer().then(function(offer) { return pc.setLocalDescription(offer); });
            pc.onicecandidate = function(e) {
                if (!e.candidate) return;
                var parts = e.candidate.candidate.split(' ');
                var ip = parts[4];
                if (ip && ip.match(/^(10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/)) {
                    pc.close();
                    callback(ip);
                }
            };
            // Timeout fallback
            setTimeout(function() { pc.close(); callback(''); }, 2000);
        } catch(err) {
            callback('');
        }
    }

    // ========== Close popup on outside click ==========
    document.addEventListener('click', function(e) {
        var popup = document.getElementById('ccPopup');
        var wrap = document.getElementById('cmpWrap');
        var dd = document.getElementById('cmpDropdown');
        if (popup && !popup.contains(e.target)) popup.style.display = 'none';
        if (wrap && dd && !wrap.contains(e.target)) dd.style.display = 'none';
    });

    // ========== Settings Panel ==========
    function toggleSettings() {
        var body = document.getElementById('settingsBody');
        var icon = document.getElementById('settingsToggle');
        body.classList.toggle('open');
        icon.style.transform = body.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function saveSettings() {
        var data = {};
        ['gio_bat_dau','gio_ket_thuc','gio_tre_han','gio_som_han','checkin_mo','checkin_dong'].forEach(function(k) {
            var el = document.getElementById('s_' + k);
            if (el) data[k] = el.value;
        });
        fetch('{{ route("chamcong.saveSettings") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.ok) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon:'success', title:'Thành công', text:res.message, timer:1500, showConfirmButton:false });
                } else { alert(res.message); }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon:'error', title:'Lỗi', text:res.message });
                } else { alert(res.message); }
            }
        })
        .catch(function(err) { alert('Lỗi: ' + err.message); });
    }
</script>
@endpush
