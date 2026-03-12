@extends('main.layouts.app')

@section('title', 'Lịch Sử Cuộc Gọi')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    .ch-page { background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04); overflow: hidden; }
    .ch-header { background: linear-gradient(135deg, #1e3a5f, #2c5282); padding: 14px 20px; }
    .ch-header h2 { margin: 0; color: white; font-size: 16px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }

    /* Filter bar */
    .ch-filter { display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; }
    .ch-daterange {
        padding: 8px 14px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 13px;
        width: 220px; background: white; cursor: pointer; color: #334155;
    }
    .ch-daterange:focus { outline: none; border-color: #3b82f6; }
    .ch-filter-wrap { position: relative; }
    .ch-select {
        padding: 8px 14px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 13px;
        background: white; color: #334155; cursor: pointer; min-width: 140px;
    }
    .ch-select:focus { outline: none; border-color: #3b82f6; }
    .ch-btn-view {
        padding: 8px 22px; border: none; border-radius: 8px; font-size: 13px; font-weight: 700;
        background: #22c55e; color: white; cursor: pointer; transition: all 0.2s; text-transform: uppercase;
    }
    .ch-btn-view:hover { background: #16a34a; transform: translateY(-1px); }

    /* Layout */
    .ch-body { display: flex; min-height: 400px; position: relative; }

    /* Sidebar quick filter — dropdown style */
    .ch-sidebar {
        position: absolute; top: 100%; left: 0; z-index: 50;
        width: 150px; background: white; border: 1px solid #e2e8f0; border-radius: 0 0 10px 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12); padding: 6px 0;
        display: none; margin-top: 2px;
    }
    .ch-sidebar.show { display: block; }
    .ch-sidebar-item {
        display: block; width: 100%; padding: 9px 16px; border: none; background: none;
        text-align: left; font-size: 13px; color: #475569; cursor: pointer; transition: all 0.15s;
        border-left: 3px solid transparent;
    }
    .ch-sidebar-item:hover { background: #f1f5f9; color: #1e293b; }
    .ch-sidebar-item.active {
        background: #dbeafe; color: #1d4ed8; border-left-color: #3b82f6; font-weight: 700;
    }

    /* Table */
    .ch-content { flex: 1; overflow-x: auto; display: flex; flex-direction: column; }
    .ch-table-wrap { flex: 1; overflow-x: auto; }
    .ch-table { width: 100%; border-collapse: collapse; }
    .ch-table thead th {
        padding: 12px 14px; text-align: center; font-size: 12px; font-weight: 700;
        text-transform: uppercase; color: white; background: #1e293b;
        border-bottom: 2px solid #334155; white-space: nowrap; letter-spacing: 0.5px;
    }
    .ch-table tbody td {
        padding: 11px 14px; border-bottom: 1px solid #f1f5f9;
        font-size: 13px; color: #334155; vertical-align: middle; text-align: center;
    }
    .ch-table tbody tr { transition: background 0.15s; }
    .ch-table tbody tr:nth-child(even) { background: #f8fafc; }
    .ch-table tbody tr:hover { background: #eef2ff; }

    /* Status badges */
    .ch-status { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .ch-status.answered { background: #22c55e; color: white; }
    .ch-status.missed { background: #ef4444; color: white; }
    .ch-status.busy { background: #f59e0b; color: white; }
    .ch-status.noanswer { background: #ef4444; color: white; }

    /* Audio player */
    .ch-audio { display: flex; align-items: center; gap: 6px; min-width: 180px; }
    .ch-audio-play {
        width: 28px; height: 28px; border-radius: 50%; border: none; cursor: pointer;
        background: none; color: #475569; font-size: 14px; display: flex; align-items: center; justify-content: center;
        transition: all 0.15s; flex-shrink: 0;
    }
    .ch-audio-play:hover { color: #1e293b; }
    .ch-audio-play.playing { color: #ef4444; }
    .ch-audio-range { width: 80px; height: 4px; accent-color: #3b82f6; cursor: pointer; }
    .ch-audio-time { font-size: 11px; color: #64748b; white-space: nowrap; }
    .ch-audio-menu { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; padding: 2px; }

    /* Pagination */
    .ch-paging { display: flex; justify-content: flex-end; align-items: center; gap: 8px; padding: 12px 16px; border-top: 1px solid #e2e8f0; }
    .ch-paging-btn {
        padding: 6px 16px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc;
        font-size: 13px; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.15s;
    }
    .ch-paging-btn:hover { background: #e2e8f0; }
    .ch-paging-btn:disabled { opacity: 0.4; cursor: default; }

    /* Empty state */
    .ch-empty { text-align: center; padding: 60px 20px; color: #94a3b8; font-size: 14px; }

    /* Loading */
    .ch-loading { text-align: center; padding: 40px; color: #64748b; }
    .ch-loading i { animation: spin 1s linear infinite; margin-right: 8px; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Direction badge */
    .ch-dir { font-size: 12px; font-weight: 600; }
    .ch-dir.in { color: #3b82f6; }
    .ch-dir.out { color: #8b5cf6; }
    /* Select2 override */
    .ch-filter .select2-container { min-width: 180px; }
    .ch-filter .select2-container--default .select2-selection--single {
        height: 38px; border: 2px solid #cbd5e1; border-radius: 8px;
        display: flex; align-items: center; padding-left: 6px;
    }
    .ch-filter .select2-container--default .select2-selection--single .select2-selection__arrow { top: 5px; }
</style>
@endpush

@section('content')
<div class="ch-page">
    {{-- Header --}}
    <div class="ch-header">
        <h2><i class="fa-solid fa-clock-rotate-left"></i> DANH SÁCH CUỘC GỌI</h2>
    </div>

    {{-- Filter bar --}}
    <div class="ch-filter">
        <div class="ch-filter-wrap">
            <input type="text" id="chDateRange" class="ch-daterange" readonly onclick="toggleDateSidebar()">
            <div class="ch-sidebar" id="chSidebar">
                <button class="ch-sidebar-item active" onclick="setQuickDate('today', this)">Hôm Nay</button>
                <button class="ch-sidebar-item" onclick="setQuickDate('yesterday', this)">Hôm Qua</button>
                <button class="ch-sidebar-item" onclick="setQuickDate('week', this)">Trong 1 Tuần</button>
                <button class="ch-sidebar-item" onclick="setQuickDate('thismonth', this)">Tháng Này</button>
                <button class="ch-sidebar-item" onclick="setQuickDate('lastmonth', this)">Tháng Trước</button>
                <button class="ch-sidebar-item" onclick="setQuickDate('30days', this)">Trong 30 Ngày</button>
                <button class="ch-sidebar-item" onclick="setQuickDate('custom', this)">Chọn Ngày</button>
            </div>
        </div>
        <select id="chCallType" class="ch-select">
            <option value="" selected>Tất cả</option>
            <option value="out">Cuộc Gọi Đi</option>
            <option value="in">Cuộc Gọi Nhận</option>
            <option value="missed">Cuộc Gọi Nhỡ</option>
        </select>
        @if($canManage)
        <select id="chExtension" class="ch-select" style="width:200px;">
            <option value="">-- Tất cả nhân viên --</option>
            @foreach($phones as $p)
            <option value="{{ $p->extension }}">{{ $p->userName ?? 'Ext ' . $p->extension }} ({{ $p->extension }})</option>
            @endforeach
        </select>
        @else
        <input type="hidden" id="chExtension" value="{{ $userExtension }}">
        @endif
        <button class="ch-btn-view" onclick="loadCallLogs()"><i class="fa-solid fa-search"></i> XEM</button>
    </div>

    {{-- Body --}}
    <div class="ch-body">
        {{-- Table content --}}
        <div class="ch-content">
            <div class="ch-table-wrap">
                <table class="ch-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Gọi Lúc</th>
                            <th>Telesales</th>
                            <th>Số Máy Gọi</th>
                            <th>Số Máy Nhận</th>
                            <th>Trạng Thái</th>
                            <th>Thời Gian</th>
                            <th>Ghi Âm</th>
                        </tr>
                    </thead>
                    <tbody id="chTableBody">
                        <tr><td colspan="8" class="ch-empty">Nhấn <b>XEM</b> để tải lịch sử cuộc gọi</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="ch-paging">
                <button class="ch-paging-btn" id="btnPrev" onclick="goPrev()" disabled>
                    <i class="fa-solid fa-chevron-left"></i> Prev
                </button>
                <button class="ch-paging-btn" id="btnNext" onclick="goNext()" disabled>
                    Next <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function() {
    let currentBefore = '';
    let currentAfter = '';
    let nextBefore = '';
    let nextAfter = '';
    let currentAudio = null;
    let currentPlayBtn = null;

    // ===== Sidebar toggle =====
    window.toggleDateSidebar = function() {
        document.getElementById('chSidebar').classList.toggle('show');
    };

    // Click outside to close
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('chSidebar');
        const input = document.getElementById('chDateRange');
        if (!sidebar.contains(e.target) && e.target !== input) {
            sidebar.classList.remove('show');
        }
    });

    // ===== Date helpers =====
    function formatDate(d) {
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yyyy = d.getFullYear();
        return dd + '/' + mm + '/' + yyyy;
    }

    function getDateRange(type) {
        const today = new Date();
        let start, end;

        switch(type) {
            case 'today':
                start = end = new Date(today);
                break;
            case 'yesterday':
                start = end = new Date(today);
                start.setDate(start.getDate() - 1);
                end = new Date(start);
                break;
            case 'week':
                end = new Date(today);
                start = new Date(today);
                start.setDate(start.getDate() - 6);
                break;
            case 'thismonth':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today);
                break;
            case 'lastmonth':
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case '30days':
                end = new Date(today);
                start = new Date(today);
                start.setDate(start.getDate() - 29);
                break;
            default:
                end = new Date(today);
                start = new Date(today);
                start.setDate(start.getDate() - 6);
        }
        return { start, end };
    }

    // Set quick date filter
    window.setQuickDate = function(type, btn) {
        // Active state
        document.querySelectorAll('.ch-sidebar-item').forEach(function(el) { el.classList.remove('active'); });
        btn.classList.add('active');

        if (type === 'custom') {
            document.getElementById('chDateRange').readOnly = false;
            document.getElementById('chDateRange').focus();
            return;
        }

        document.getElementById('chDateRange').readOnly = true;
        const range = getDateRange(type);
        document.getElementById('chDateRange').value = formatDate(range.start) + ' - ' + formatDate(range.end);
        currentBefore = '';
        currentAfter = '';
        document.getElementById('chSidebar').classList.remove('show');
        loadCallLogs();
    };

    // Init: set default "Hôm Nay"
    const defaultRange = getDateRange('today');
    document.getElementById('chDateRange').value = formatDate(defaultRange.start) + ' - ' + formatDate(defaultRange.end);

    // ===== Load data =====
    window.loadCallLogs = function() {
        currentBefore = '';
        currentAfter = '';
        doFetch();
    };

    function doFetch() {
        const thoigian = document.getElementById('chDateRange').value;
        const rawType = document.getElementById('chCallType').value;

        // Xác định call_type và call_state
        let callType = rawType;
        let callState = 'answered'; // mặc định chỉ lấy answered
        if (rawType === 'missed') {
            callType = 'in';
            callState = 'not_answered';
        } else if (rawType === 'in') {
            callState = ''; // cuộc gọi nhận: hiện tất cả
        }

        if (!thoigian || !thoigian.includes(' - ')) {
            alert('Vui lòng chọn khoảng thời gian!');
            return;
        }

        document.getElementById('chTableBody').innerHTML =
            '<tr><td colspan="8" class="ch-loading"><i class="fa-solid fa-spinner"></i> Đang tải dữ liệu...</td></tr>';
        document.getElementById('btnPrev').disabled = true;
        document.getElementById('btnNext').disabled = true;

        const params = new URLSearchParams({
            thoigian: thoigian,
            call_type: callType,
            call_state: callState,
            extension: document.getElementById('chExtension') ? document.getElementById('chExtension').value : '',
            before: currentBefore,
            after: currentAfter,
        });

        fetch('/customers/call-history-data?' + params.toString())
            .then(function(r) { return r.json(); })
            .then(function(data) {
                console.log('=== POSTDATA ===', JSON.parse(data.debug_postdata || '{}'));
                renderTable(data);
            })
            .catch(function(err) {
                console.error('Fetch error:', err);
                document.getElementById('chTableBody').innerHTML =
                    '<tr><td colspan="8" class="ch-empty">Lỗi tải dữ liệu. Vui lòng thử lại.</td></tr>';
            });
    }

    function renderTable(data) {
        const tbody = document.getElementById('chTableBody');

        // API trả về: { call_logs: [...], paging: { next, prev } }
        const logs = data.call_logs || [];
        const paging = data.paging || {};

        nextBefore = paging.next || '';
        nextAfter = paging.prev || '';

        document.getElementById('btnPrev').disabled = !nextAfter;
        document.getElementById('btnNext').disabled = !nextBefore;

        if (logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="ch-empty">Không có dữ liệu</td></tr>';
            return;
        }

        let html = '';
        const extMap = data.extension_map || {};
        logs.forEach(function(log, idx) {
            const extension = log.extension_number || log.extension || '';
            const caller = log.caller || log.call_number || '';
            const callee = log.callee || log.destination || '';
            const status = mapStatus(log.call_state || log.call_status || log.status || '');
            const duration = formatDuration(log.duration || 0);
            const startedAt = formatTime(log.started_at || log.created_at || '');
            const recording = (log.recording_urls && log.recording_urls.length > 0 ? log.recording_urls[0] : '') || log.recording_url || log.audio_url || '';

            // Telesale: tra cứu caller trong extension_map
            const telesale = extMap.hasOwnProperty(caller)
                ? '<b>' + escHtml(extMap[caller] || 'Ext ' + caller) + '</b>'
                : '<span style="color:#ef4444;font-weight:700;">Khách Gọi</span>';

            html += '<tr>';
            html += '<td style="font-weight:600;color:#64748b;">' + (idx + 1) + '</td>';
            html += '<td style="white-space:nowrap;font-size:12px;">' + escHtml(startedAt) + '</td>';
            html += '<td>' + telesale + '</td>';
            html += '<td><b>' + escHtml(caller) + '</b></td>';
            html += '<td>' + escHtml(callee) + '</td>';
            html += '<td>' + statusBadge(status) + '</td>';
            html += '<td>' + duration + '</td>';
            html += '<td>' + (status === 'answered' ? audioPlayer(recording) : '<span style="color:#94a3b8;">—</span>') + '</td>';
            html += '</tr>';
        });

        tbody.innerHTML = html;
    }

    function mapStatus(s) {
        s = (s || '').toLowerCase();
        if (s === 'answered' || s === 'answer') return 'answered';
        if (s === 'missed' || s === 'cancel') return 'missed';
        if (s === 'busy') return 'busy';
        return 'noanswer';
    }

    function statusBadge(cls) {
        const labels = { 'answered': 'Nghe Máy', 'missed': 'Không Nghe Máy', 'busy': 'Không Nghe Máy', 'noanswer': 'Không Nghe Máy' };
        return '<span class="ch-status ' + cls + '">' + labels[cls] + '</span>';
    }
  
    function formatDuration(sec) {
        sec = parseInt(sec) || 0;
        const m = String(Math.floor(sec / 60)).padStart(2, '0');
        const s = String(sec % 60).padStart(2, '0');
        return m + ':' + s;
    }

    function formatTime(t) {
        if (!t) return '';
        try {
            const d = new Date(t);
            if (isNaN(d.getTime())) return t;
            const dd = String(d.getDate()).padStart(2, '0');
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const yyyy = d.getFullYear();
            const hh = String(d.getHours()).padStart(2, '0');
            const mi = String(d.getMinutes()).padStart(2, '0');
            const ss = String(d.getSeconds()).padStart(2, '0');
            return hh + ':' + mi + ':' + ss + ' - ' + dd + '/' + mm + '/' + yyyy;
        } catch (e) { return t; }
    }

    function audioPlayer(url) {
        if (!url) return '<span style="color:#94a3b8;">—</span>';
        const id = 'aud_' + Math.random().toString(36).substr(2, 6);
        return '<div class="ch-audio">' +
            '<button class="ch-audio-play" onclick="toggleAudio(this, \'' + id + '\')" title="Phát"><i class="fa-solid fa-play"></i></button>' +
            '<input type="range" class="ch-audio-range" id="rng_' + id + '" min="0" max="100" value="0" oninput="seekAudio(\'' + id + '\', this.value)">' +
            '<span class="ch-audio-time" id="tm_' + id + '">0:00 / 0:00</span>' +
            '<audio id="' + id + '" src="' + escAttr(url) + '" preload="metadata" ' +
            'ontimeupdate="updateAudioUI(\'' + id + '\')" ' +
            'onloadedmetadata="updateAudioUI(\'' + id + '\')" ' +
            'onended="endAudioUI(\'' + id + '\')">' +
            '</audio>' +
            '<button class="ch-audio-menu" title="Tùy chọn" onclick="window.open(\'' + escAttr(url) + '\', \'_blank\')"><i class="fa-solid fa-ellipsis-vertical"></i></button>' +
            '</div>';
    }

    function escHtml(s) {
        const d = document.createElement('div');
        d.textContent = s || '';
        return d.innerHTML;
    }

    function escAttr(s) {
        return (s || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    // ===== Audio playback =====
    function fmtAudioTime(sec) {
        sec = Math.floor(sec || 0);
        return Math.floor(sec / 60) + ':' + String(sec % 60).padStart(2, '0');
    }

    window.toggleAudio = function(btn, id) {
        const audio = document.getElementById(id);
        if (!audio) return;

        // Stop other playing audios
        if (currentAudio && currentAudio !== audio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
            if (currentPlayBtn) {
                currentPlayBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
                currentPlayBtn.classList.remove('playing');
            }
        }

        if (audio.paused) {
            audio.play().catch(function(e) { console.error('Audio error:', e); });
            btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            btn.classList.add('playing');
        } else {
            audio.pause();
            btn.innerHTML = '<i class="fa-solid fa-play"></i>';
            btn.classList.remove('playing');
        }
        currentAudio = audio;
        currentPlayBtn = btn;
    };

    window.seekAudio = function(id, val) {
        const audio = document.getElementById(id);
        if (audio && audio.duration) {
            audio.currentTime = (val / 100) * audio.duration;
        }
    };

    window.updateAudioUI = function(id) {
        const audio = document.getElementById(id);
        if (!audio) return;
        const rng = document.getElementById('rng_' + id);
        const tm = document.getElementById('tm_' + id);
        if (rng && audio.duration) rng.value = (audio.currentTime / audio.duration) * 100;
        if (tm) tm.textContent = fmtAudioTime(audio.currentTime) + ' / ' + fmtAudioTime(audio.duration || 0);
    };

    window.endAudioUI = function(id) {
        const audio = document.getElementById(id);
        const rng = document.getElementById('rng_' + id);
        if (rng) rng.value = 0;
        if (currentPlayBtn) {
            currentPlayBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
            currentPlayBtn.classList.remove('playing');
        }
        currentAudio = null;
        currentPlayBtn = null;
    };

    // ===== Pagination =====
    // Next: truyền paging.next vào after
    window.goNext = function() {
        currentAfter = nextBefore;
        currentBefore = '';
        doFetch();
    };

    // Prev: truyền paging.prev vào before
    window.goPrev = function() {
        currentBefore = nextAfter;
        currentAfter = '';
        doFetch();
    };
    // Init Select2 cho dropdown nhân viên
    if (document.getElementById('chExtension') && document.getElementById('chExtension').tagName === 'SELECT' && typeof jQuery !== 'undefined') {
        jQuery('#chExtension').select2({ placeholder: '-- Tất cả nhân viên --', allowClear: true, width: '200px' });
    }

    // Auto-load khi vào trang
    loadCallLogs();
})();
</script>
@endpush
