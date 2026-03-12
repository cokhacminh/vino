@extends('main.layouts.app')

@section('title', 'Chia Data')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .dd-page { padding: 10px; }

    /* Section cards */
    .dd-section {
        background: white; border-radius: 14px; margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .dd-section-header {
        padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;
        cursor: pointer; user-select: none; transition: background 0.2s;
    }
    .dd-section-header:hover { background: #f8fafc; }
    .dd-section-header h3 { margin: 0; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .dd-section-header .dd-toggle { font-size: 14px; color: #94a3b8; transition: transform 0.3s; }
    .dd-section-header.collapsed .dd-toggle { transform: rotate(-90deg); }
    .dd-section-body { padding: 20px; }
    .dd-section-header.collapsed + .dd-section-body { display: none; }

    /* Header colors */
    .dd-header-recall h3 { color: #dc2626; }
    .dd-header-recall { border-bottom: 3px solid #fca5a5; }
    .dd-header-distribute h3 { color: #059669; }
    .dd-header-distribute { border-bottom: 3px solid #6ee7b7; }

    /* Stats cards row */
    .dd-stats-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
    .dd-stat-card {
        flex: 1; min-width: 120px; padding: 14px 18px; border-radius: 10px;
        text-align: center; position: relative; overflow: hidden;
    }
    .dd-stat-card .dd-stat-value { font-size: 28px; font-weight: 800; }
    .dd-stat-card .dd-stat-label { font-size: 12px; font-weight: 600; margin-top: 4px; opacity: 0.85; }
    .dd-stat-total { background: linear-gradient(135deg, #1e293b, #334155); color: white; }
    .dd-stat-group { background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; }

    /* Select / form */
    .dd-form-row { display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 16px; }
    .dd-form-group { display: flex; flex-direction: column; gap: 4px; }
    .dd-form-group label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .dd-select, .dd-input {
        padding: 9px 14px; border: 2px solid #e2e8f0; border-radius: 10px;
        font-size: 14px; transition: border-color 0.2s; background: white;
        min-width: 180px; box-sizing: border-box;
    }
    .dd-select:focus, .dd-input:focus { outline: none; border-color: #3b82f6; }
    .dd-input { width: 100px; min-width: 80px; text-align: center; }

    /* Radio buttons */
    .dd-radio-group { display: flex; gap: 8px; align-items: center; }
    .dd-radio-label {
        display: flex; align-items: center; gap: 6px; padding: 7px 14px;
        border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer;
        font-size: 13px; font-weight: 600; transition: all 0.2s;
    }
    .dd-radio-label:hover { border-color: #94a3b8; }
    .dd-radio-label input[type="radio"] { display: none; }
    .dd-radio-label.active-reset { border-color: #ef4444; background: #fef2f2; color: #dc2626; }
    .dd-radio-label.active-keep { border-color: #3b82f6; background: #eff6ff; color: #2563eb; }

    /* Buttons */
    .dd-btn {
        padding: 10px 22px; border: none; border-radius: 10px; font-size: 14px;
        font-weight: 700; cursor: pointer; transition: all 0.2s; display: inline-flex;
        align-items: center; gap: 6px;
    }
    .dd-btn:hover { transform: translateY(-1px); }
    .dd-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    .dd-btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .dd-btn-danger:hover { box-shadow: 0 4px 12px rgba(239,68,68,0.4); }
    .dd-btn-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .dd-btn-success:hover { box-shadow: 0 4px 12px rgba(16,185,129,0.4); }

    /* Tables */
    .dd-table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
    .dd-table { width: 100%; border-collapse: collapse; }
    .dd-table thead th {
        padding: 11px 14px; text-align: center; font-size: 12px; font-weight: 700;
        text-transform: uppercase; color: white; background: #1e293b;
        border-bottom: 2px solid #e2e8f0; white-space: nowrap; letter-spacing: 0.3px;
    }
    .dd-table tbody td {
        padding: 10px 14px; border-bottom: 1px solid #f1f5f9;
        font-size: 14px; color: #334155; vertical-align: middle; text-align: center;
    }
    .dd-table tbody tr { transition: background 0.15s; }
    .dd-table tbody tr:nth-child(even) { background: #f8fafc; }
    .dd-table tbody tr:hover { background: #e8f0fe; }

    /* Badge */
    .dd-badge {
        display: inline-block; padding: 3px 10px; border-radius: 5px;
        font-size: 12px; font-weight: 700; white-space: nowrap;
    }
    .dd-badge-status {
        display: inline-block; padding: 2px 8px; border-radius: 4px;
        font-size: 11px; font-weight: 700;
    }
    .dd-badge-active { background: #dcfce7; color: #15803d; }
    .dd-badge-deactive { background: #fee2e2; color: #dc2626; }

    /* Recall stats table */
    .dd-recall-stats { margin: 16px 0; }
    .dd-recall-actions { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-top: 16px; }

    /* Distribute staff list */
    .dd-staff-total { font-weight: 800; font-size: 16px; color: #1e293b; }

    /* Responsive */
    @media (max-width: 768px) {
        .dd-form-row { flex-direction: column; align-items: stretch; }
        .dd-select, .dd-input { min-width: 100%; }
        .dd-stats-row { flex-direction: column; }
        .dd-stat-card { min-width: 100%; }
    }

    /* Loading */
    .dd-loading {
        display: flex; align-items: center; justify-content: center; padding: 30px;
        color: #94a3b8; font-size: 14px; gap: 8px;
    }
    .dd-loading i { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .dd-empty {
        text-align: center; padding: 30px; color: #94a3b8; font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="dd-page">

    {{-- ==================== PHẦN 1: THU HỒI DATA ==================== --}}
    <div class="dd-section">
        <div class="dd-section-header dd-header-recall" onclick="toggleSection(this)">
            <h3><i class="fa-solid fa-arrow-rotate-left"></i> THU HỒI DATA</h3>
            <i class="fa-solid fa-chevron-down dd-toggle"></i>
        </div>
        <div class="dd-section-body">
            {{-- Chọn nhân viên --}}
            <div class="dd-form-row">
                <div class="dd-form-group">
                    <label>Chọn Nhân Viên</label>
                    <select class="dd-select" id="recallStaffSelect" onchange="loadRecallStats()">
                        <option value="">-- Chọn nhân viên --</option>
                        @foreach($salesStaff as $s)
                            <option value="{{ $s->id }}" data-status="{{ $s->TinhTrang }}">
                                {{ $s->name }} ({{ $s->TinhTrang }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Bảng thống kê data của NV đó --}}
            <div id="recallStatsArea">
                <div class="dd-empty"><i class="fa-solid fa-arrow-up"></i> Chọn nhân viên để xem thống kê data</div>
            </div>
        </div>
    </div>

    {{-- ==================== PHẦN 2: CHIA DATA ==================== --}}
    <div class="dd-section">
        <div class="dd-section-header dd-header-distribute" onclick="toggleSection(this)">
            <h3><i class="fa-solid fa-share-nodes"></i> CHIA DATA</h3>
            <i class="fa-solid fa-chevron-down dd-toggle"></i>
        </div>
        <div class="dd-section-body">
            {{-- Thống kê data trắng --}}
            <div style="margin-bottom: 14px;">
                <h4 style="margin:0 0 10px; color:#475569; font-size:15px;">
                    <i class="fa-solid fa-database" style="color:#94a3b8;"></i> Data Trắng (Chưa gán nhân viên)
                </h4>
                <div class="dd-stats-row">
                    <div class="dd-stat-card dd-stat-total">
                        <div class="dd-stat-value">{{ number_format($totalBlank) }}</div>
                        <div class="dd-stat-label">TỔNG DATA TRẮNG</div>
                    </div>
                    @foreach($groups as $g)
                        <div class="dd-stat-card dd-stat-group">
                            <div class="dd-stat-value" style="color:{{ $g->background }};">{{ number_format($blankStats[$g->MaNhomKH] ?? 0) }}</div>
                            <div class="dd-stat-label">
                                <span class="dd-badge" style="background:{{ $g->background }};color:{{ $g->color }};font-size:11px;padding:2px 8px;">{{ $g->TenNhomKH }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Form chia data --}}
            <div class="dd-form-row" style="margin-bottom: 14px;">
                <div class="dd-form-group">
                    <label>Chọn Nhóm Khách Hàng Để Chia</label>
                    <select class="dd-select" id="distGroupSelect">
                        @foreach($groups as $g)
                            <option value="{{ $g->MaNhomKH }}">{{ $g->TenNhomKH }} ({{ number_format($blankStats[$g->MaNhomKH] ?? 0) }} số)</option>
                        @endforeach
                    </select>
                </div>
                <div class="dd-form-group">
                    <label>Chế Độ</label>
                    <div class="dd-radio-group" id="distModeGroup">
                        <label class="dd-radio-label active-keep" onclick="setDistMode(this, 'keep')">
                            <input type="radio" name="dist_mode" value="keep" checked>
                            <i class="fa-solid fa-shield-halved"></i> Giữ Nguyên
                        </label>
                        <label class="dd-radio-label" onclick="setDistMode(this, 'reset')">
                            <input type="radio" name="dist_mode" value="reset">
                            <i class="fa-solid fa-rotate"></i> Reset Data
                        </label>
                    </div>
                </div>
            </div>

            {{-- Bảng danh sách NV với data sở hữu --}}
            <div class="dd-table-wrap">
                <table class="dd-table" id="distTable">
                    <thead>
                        <tr>
                            <th style="width:40px;">STT</th>
                            <th>Nhân Viên</th>
                            <th>Trạng Thái</th>
                            <th>Tổng Data</th>
                            @foreach($groups as $g)
                                <th>
                                    <span class="dd-badge" style="background:{{ $g->background }};color:{{ $g->color }};font-size:10px;padding:2px 6px;">{{ $g->TenNhomKH }}</span>
                                </th>
                            @endforeach
                            <th style="width:100px;">Chia Thêm</th>
                            <th style="width:100px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesStaff as $i => $s)
                        @php
                            $sStats = $staffStats[$s->id] ?? collect();
                            $sTotal = $sStats->sum('cnt');
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="font-weight:600; text-align:left; padding-left:16px;">{{ $s->name }}</td>
                            <td>
                                <span class="dd-badge-status {{ $s->TinhTrang === 'Active' ? 'dd-badge-active' : 'dd-badge-deactive' }}">
                                    {{ $s->TinhTrang }}
                                </span>
                            </td>
                            <td><span class="dd-staff-total">{{ number_format($sTotal) }}</span></td>
                            @foreach($groups as $g)
                                @php
                                    $cnt = $sStats->firstWhere('MaNhomKH', $g->MaNhomKH);
                                @endphp
                                <td>{{ $cnt ? number_format($cnt->cnt) : 0 }}</td>
                            @endforeach
                            <td>
                                <input type="number" class="dd-input dist-qty" data-staff="{{ $s->id }}" min="0" value="0" style="width:80px;">
                            </td>
                            <td>
                                <button class="dd-btn dd-btn-success" style="padding:6px 12px; font-size:12px;"
                                    onclick="distributeForStaff({{ $s->id }}, '{{ addslashes($s->name) }}')">
                                    <i class="fa-solid fa-share"></i> Chia
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const GROUPS = @json($groups);
    const CSRF = '{{ csrf_token() }}';

    // ===== Section toggle =====
    function toggleSection(header) {
        header.classList.toggle('collapsed');
    }

    // ===== RECALL SECTION =====
    function loadRecallStats() {
        const staffId = document.getElementById('recallStaffSelect').value;
        const area = document.getElementById('recallStatsArea');
        if (!staffId) {
            area.innerHTML = '<div class="dd-empty"><i class="fa-solid fa-arrow-up"></i> Chọn nhân viên để xem thống kê data</div>';
            return;
        }

        area.innerHTML = '<div class="dd-loading"><i class="fa-solid fa-spinner"></i> Đang tải...</div>';

        fetch(`{{ route('customers.dataDivisionStats') }}?staff_id=${staffId}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    area.innerHTML = '<div class="dd-empty">Không có dữ liệu</div>';
                    return;
                }
                renderRecallStats(staffId, data);
            })
            .catch(() => {
                area.innerHTML = '<div class="dd-empty" style="color:#ef4444;">Lỗi tải dữ liệu</div>';
            });
    }

    function renderRecallStats(staffId, data) {
        const area = document.getElementById('recallStatsArea');
        let html = '<div class="dd-recall-stats">';

        // Stats summary
        html += '<div class="dd-stats-row">';
        html += `<div class="dd-stat-card dd-stat-total">
                    <div class="dd-stat-value">${data.total.toLocaleString()}</div>
                    <div class="dd-stat-label">TỔNG DATA SỞ HỮU</div>
                 </div>`;
        
        html += '</div>';

        // Table with recall inputs
        html += '<div class="dd-table-wrap"><table class="dd-table"><thead><tr>';
        html += '<th>Nhóm KH</th><th>Số Lượng Hiện Có</th><th>Thu Hồi SL</th>';
        html += '</tr></thead><tbody>';

        GROUPS.forEach(g => {
            const cnt = data.stats.find(s => s.MaNhomKH == g.MaNhomKH);
            const count = cnt ? cnt.cnt : 0;
            html += `<tr>
                <td><span class="dd-badge" style="background:${g.background};color:${g.color};">${g.TenNhomKH}</span></td>
                <td><b>${count.toLocaleString()}</b></td>
                <td><input type="number" class="dd-input recall-qty" data-group="${g.MaNhomKH}" min="0" max="${count}" value="0" style="width:80px;" ${count === 0 ? 'disabled' : ''}></td>
            </tr>`;
        });

        html += '</tbody></table></div>';

        // Mode selection + button
        html += `<div class="dd-recall-actions">
            <div class="dd-form-group">
                <label>Chế Độ Thu Hồi</label>
                <div class="dd-radio-group" id="recallModeGroup">
                    <label class="dd-radio-label active-keep" onclick="setRecallMode(this, 'keep')">
                        <input type="radio" name="recall_mode" value="keep" checked>
                        <i class="fa-solid fa-shield-halved"></i> Giữ Nguyên
                    </label>
                    <label class="dd-radio-label" onclick="setRecallMode(this, 'reset')">
                        <input type="radio" name="recall_mode" value="reset">
                        <i class="fa-solid fa-rotate"></i> Reset Data
                    </label>
                </div>
            </div>
            <button class="dd-btn dd-btn-danger" onclick="executeRecall(${staffId})" style="margin-top:18px;">
                <i class="fa-solid fa-arrow-rotate-left"></i> Thu Hồi
            </button>
        </div>`;

        html += '</div>';
        area.innerHTML = html;
    }

    function setRecallMode(el, mode) {
        document.querySelectorAll('#recallModeGroup .dd-radio-label').forEach(l => {
            l.classList.remove('active-reset', 'active-keep');
        });
        el.classList.add(mode === 'reset' ? 'active-reset' : 'active-keep');
        el.querySelector('input').checked = true;
    }

    function executeRecall(staffId) {
        const mode = document.querySelector('input[name="recall_mode"]:checked')?.value || 'keep';
        const inputs = document.querySelectorAll('.recall-qty');
        let hasData = false;

        // Collect recall requests
        const requests = [];
        inputs.forEach(inp => {
            const qty = parseInt(inp.value) || 0;
            if (qty > 0) {
                hasData = true;
                const groupName = GROUPS.find(g => g.MaNhomKH == inp.dataset.group)?.TenNhomKH || '';
                requests.push({ group_id: inp.dataset.group, quantity: qty, groupName: groupName });
            }
        });

        if (!hasData) {
            Swal.fire('Thông báo', 'Vui lòng nhập số lượng thu hồi ít nhất 1 nhóm.', 'warning');
            return;
        }

        const modeText = mode === 'reset' ? 'RESET DATA (xóa toàn bộ thông tin liên hệ)' : 'GIỮ NGUYÊN thông tin';
        const staffName = document.getElementById('recallStaffSelect').selectedOptions[0].text;

        // Build detail list
        let detailHtml = requests.map(r => `Thu hồi <b>${r.quantity}</b> data <b>${r.groupName}</b>`).join('<br>');

        Swal.fire({
            title: 'Xác nhận thu hồi?',
            html: `${detailHtml}<br>của <b>${staffName}</b><br><br>Chế độ: <b>${modeText}</b>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonText: 'Hủy',
            confirmButtonText: 'Thu Hồi',
        }).then(result => {
            if (result.isConfirmed) {
                processRecallBatch(staffId, mode, requests, 0);
            }
        });
    }

    function processRecallBatch(staffId, mode, requests, index) {
        if (index >= requests.length) {
            Swal.fire('Thành công!', 'Đã thu hồi data thành công.', 'success').then(() => {
                loadRecallStats();
                location.reload();
            });
            return;
        }

        const req = requests[index];
        fetch('{{ route("customers.recallData") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                staff_id: staffId,
                group_id: req.group_id,
                quantity: req.quantity,
                mode: mode,
            }),
        })
        .then(r => {
            if (!r.ok) return r.text().then(t => { throw new Error(t); });
            return r.json();
        })
        .then(data => {
            if (data.success) {
                processRecallBatch(staffId, mode, requests, index + 1);
            } else {
                Swal.fire('Lỗi', data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(err => Swal.fire('Lỗi', err.message || 'Không thể kết nối server', 'error'));
    }

    // ===== DISTRIBUTE SECTION =====
    let currentDistMode = 'keep';

    function setDistMode(el, mode) {
        currentDistMode = mode;
        document.querySelectorAll('#distModeGroup .dd-radio-label').forEach(l => {
            l.classList.remove('active-reset', 'active-keep');
        });
        el.classList.add(mode === 'reset' ? 'active-reset' : 'active-keep');
        el.querySelector('input').checked = true;
    }

    function distributeForStaff(staffId, staffName) {
        const qtyInput = document.querySelector(`.dist-qty[data-staff="${staffId}"]`);
        const qty = parseInt(qtyInput.value) || 0;

        if (qty <= 0) {
            Swal.fire('Thông báo', 'Vui lòng nhập số lượng chia > 0.', 'warning');
            return;
        }

        const groupId = document.getElementById('distGroupSelect').value;
        const groupName = document.getElementById('distGroupSelect').selectedOptions[0].text;
        const modeText = currentDistMode === 'reset' ? 'RESET DATA (xóa toàn bộ thông tin liên hệ)' : 'GIỮ NGUYÊN thông tin';

        Swal.fire({
            title: 'Xác nhận chia data?',
            html: `Chia <b>${qty}</b> data từ <b>${groupName}</b><br>cho <b>${staffName}</b><br>Chế độ: <b>${modeText}</b>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonText: 'Hủy',
            confirmButtonText: 'Chia Data',
        }).then(result => {
            if (result.isConfirmed) {
                fetch('{{ route("customers.distributeData") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({
                        staff_id: staffId,
                        group_id: groupId,
                        quantity: qty,
                        mode: currentDistMode,
                    }),
                })
                .then(r => {
                    if (!r.ok) return r.text().then(t => { throw new Error(t); });
                    return r.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Lỗi', data.message || 'Có lỗi xảy ra', 'error');
                    }
                })
                .catch(err => Swal.fire('Lỗi', err.message || 'Không thể kết nối server', 'error'));
            }
        });
    }
</script>
@endpush
