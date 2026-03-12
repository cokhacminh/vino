@extends('main.layouts.app')

@section('title', 'Danh Sách Khách Hàng')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    .cl-page {
        padding: 10px; background: white; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
    }
    .cl-header {
        background: #1e293b; color: white; padding: 10px 16px;
        border-radius: 8px 8px 0 0; font-size: 16px; font-weight: 700;
        display: flex; justify-content: space-between; align-items: center;
    }
    .cl-total {
        font-size: 14px; font-weight: 700; color: white;
        background: #dc2626; padding: 4px 12px; border-radius: 6px;
    }
    .cl-tabs { display: flex; gap: 6px; padding: 12px 0; flex-wrap: wrap; align-items: center; }
    .cl-tab {
        padding: 6px 14px; border-radius: 6px; cursor: pointer;
        font-size: 12px; font-weight: 700; border: none;
        transition: all 0.2s; text-decoration: none; display: inline-block; white-space: nowrap;
    }
    .cl-tab:hover { opacity: 0.85; transform: translateY(-1px); }
    .cl-tab.active { box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor; }
    .cl-staff-select {
        padding: 7px 10px; border: 2px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; min-width: 160px; margin-bottom: 10px;
    }

    /* DataTables overrides */
    .dataTables_wrapper { font-size: 13px; }
    .dataTables_wrapper .dataTables_filter input {
        padding: 6px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 13px; margin-left: 6px;
    }
    .dataTables_wrapper .dataTables_filter input:focus { outline: none; border-color: #3b82f6; }
    .dataTables_wrapper .dataTables_length select { padding: 4px 8px; border: 2px solid #e2e8f0; border-radius: 6px; }
    table.dataTable thead th {
        padding: 10px 12px !important; text-align: left; font-size: 12px; font-weight: 700;
        text-transform: uppercase; color: white !important; background: #334155 !important;
        border-bottom: 2px solid #475569 !important; white-space: nowrap;
    }
    table.dataTable tbody td {
        padding: 8px 12px !important; font-size: 14px; color: #334155; vertical-align: middle;
    }
    table.dataTable tbody tr:nth-child(even) { background: #f8fafc; }
    table.dataTable tbody tr:hover { background: #e8edf4 !important; }
    table.dataTable.no-footer { border-bottom: 1px solid #e2e8f0 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 4px 10px !important; border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important; margin: 0 2px !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important; color: white !important; border-color: #3b82f6 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #eff6ff !important; color: #2563eb !important; border-color: #3b82f6 !important;
    }
    .dataTables_wrapper .dataTables_info { font-size: 13px; color: #64748b; }

    /* Action buttons in TT column */
    .cl-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 28px; border-radius: 6px; border: none;
        cursor: pointer; font-size: 13px; transition: all 0.15s;
    }
    .cl-btn:hover { transform: scale(1.1); }
    .cl-btn-update { background: #3b82f6; color: white; }
    .cl-btn-history { background: #8b5cf6; color: white; }
    .cl-btn-add {
        padding: 7px 16px; border-radius: 8px; border: none; cursor: pointer;
        background: #22c55e; color: white; font-size: 13px; font-weight: 700;
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
    }
    .cl-btn-add:hover { background: #16a34a; transform: translateY(-1px); }

    /* Warning animation */
    .cl-warn {
        display: inline-flex; align-items: center; gap: 3px;
        color: #f59e0b; font-size: 13px;
        animation: warnPulse 1.5s ease-in-out infinite;
    }
    .cl-warn b { font-size: 14px; }
    @keyframes warnPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.15); }
    }

    /* Modal */
    .cl-modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;
    }
    .cl-modal-overlay.show { display: flex; }
    .cl-modal {
        background: white; border-radius: 16px; max-width: 95vw;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .cl-modal-sm { width: 520px; }
    .cl-modal-lg { width: 700px; max-height: 85vh; display: flex; flex-direction: column; }
    .cl-modal-header {
        padding: 18px 24px; border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .cl-modal-header h3 { margin: 0; font-size: 18px; color: #1e293b; }
    .cl-modal-close {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        background: #f1f5f9; cursor: pointer; font-size: 18px; color: #64748b;
    }
    .cl-modal-close:hover { background: #e2e8f0; }
    .cl-modal-body { padding: 24px; overflow-y: auto; }
    .cl-modal-footer {
        padding: 16px 24px; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .cl-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .cl-form-group { margin-bottom: 16px; }
    .cl-form-group label { display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px; }
    .cl-form-input, .cl-form-select, .cl-form-textarea {
        width: 100%; padding: 9px 14px; border: 2px solid #e2e8f0; border-radius: 6px;
        font-size: 14px; transition: border-color 0.2s; box-sizing: border-box;
    }
    .cl-form-input:focus, .cl-form-select:focus, .cl-form-textarea:focus { outline: none; border-color: #3b82f6; }
    .cl-form-textarea { resize: vertical; min-height: 80px; }
    .cl-btn-submit {
        padding: 9px 20px; border-radius: 10px; border: none;
        font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .cl-btn-primary { background: #3b82f6; color: white; }
    .cl-btn-primary:hover { background: #2563eb; }
    .cl-btn-danger { background: #ef4444; color: white; }
    .cl-btn-danger:hover { background: #dc2626; }
    .cl-btn-secondary { background: #f1f5f9; color: #475569; }
    .cl-btn-secondary:hover { background: #e2e8f0; }

    /* History table */
    .hs-table { width: 100%; border-collapse: collapse; }
    .hs-table th {
        padding: 10px 12px; background: #f1f5f9; font-size: 12px;
        font-weight: 700; text-transform: uppercase; color: #475569;
        border-bottom: 2px solid #e2e8f0; text-align: left;
    }
    .hs-table td {
        padding: 10px 12px; border-bottom: 1px solid #f1f5f9;
        font-size: 13px; color: #334155; vertical-align: top;
    }
    .hs-table tr:hover { background: #f8fafc; }
    .hs-empty { text-align: center; padding: 30px; color: #94a3b8; }
    .hs-del-btn {
        padding: 4px 10px; border-radius: 6px; border: none;
        background: #fee2e2; color: #dc2626; font-size: 12px; cursor: pointer;
    }
    .hs-del-btn:hover { background: #ef4444; color: white; }
    .hs-loading { text-align: center; padding: 30px; color: #64748b; }

    /* Call Panel */
    .call-panel {
        position: fixed; bottom: 20px; right: 20px; z-index: 10000;
        background: #1e293b; color: white; border-radius: 16px;
        padding: 16px 20px; width: 320px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.4);
        display: none; font-family: 'Inter', sans-serif;
    }
    .call-panel.show { display: block; animation: slideUp 0.3s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .call-panel-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .call-panel-avatar {
        width: 42px; height: 42px; border-radius: 50%; background: #3b82f6;
        display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    .call-panel-info { flex: 1; }
    .call-panel-number { font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
    .call-panel-status { font-size: 12px; color: #94a3b8; margin-top: 2px; }
    .call-panel-status.connected { color: #22c55e; }
    .call-panel-status.ringing { color: #f59e0b; }
    .call-panel-timer {
        font-size: 24px; font-weight: 700; text-align: center;
        font-family: monospace; letter-spacing: 2px; margin: 10px 0;
        display: none;
    }
    .call-panel-timer.show { display: block; }
    .call-panel-actions { display: flex; justify-content: center; gap: 16px; margin-top: 12px; }
    .call-action-btn {
        width: 50px; height: 50px; border-radius: 50%; border: none;
        cursor: pointer; font-size: 20px; color: white;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .call-action-btn:hover { transform: scale(1.1); }
    .call-btn-hangup { background: #ef4444; }
    .call-btn-hangup:hover { background: #dc2626; }
    .call-btn-answer { background: #22c55e; }
    .call-btn-answer:hover { background: #16a34a; }
    .call-btn-mute { background: #475569; }
    .call-btn-mute.active { background: #f59e0b; }

    /* SIP Status indicator */
    .sip-status {
        position: fixed; bottom: 20px; right: 20px; z-index: 9998;
        padding: 8px 16px; border-radius: 30px; font-size: 12px; font-weight: 700;
        display: flex; align-items: center; gap: 8px; cursor: default;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .sip-status .dot {
        width: 8px; height: 8px; border-radius: 50%; display: inline-block;
    }
    .sip-status.registered { background: #dcfce7; color: #15803d; }
    .sip-status.registered .dot { background: #22c55e; animation: pulse 2s infinite; }
    .sip-status.unregistered { background: #fee2e2; color: #dc2626; }
    .sip-status.unregistered .dot { background: #ef4444; }
    .sip-status.connecting { background: #fef3c7; color: #92400e; }
    .sip-status.connecting .dot { background: #f59e0b; animation: pulse 1s infinite; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
</style>
<script src="{{ asset('js/jssip.min.js') }}"></script>
@endpush

@section('content')
<div class="cl-page">
    <div class="cl-header">
        <span><i class="fa-solid fa-database"></i> DANH SÁCH KHÁCH HÀNG</span>
        <span class="cl-total">Tổng: {{ number_format($totalCount) }} số</span>
    </div>

    <div class="cl-tabs">
        @foreach($groups as $g)
        <a href="{{ route('customers.list', array_filter(['group' => $g->MaNhomKH, 'staff' => $staffFilter])) }}"
           class="cl-tab {{ $groupFilter == $g->MaNhomKH ? 'active' : '' }}"
           style="background:{{ $g->background }}; color:{{ $g->color }};">
            {{ $g->TenNhomKH }} ({{ $groupCounts[$g->MaNhomKH] ?? 0 }})
        </a>
        @endforeach
        <button class="cl-tab" style="background:#1e40af;color:white;" onclick="openSearchModal()">
            <i class="fa-solid fa-magnifying-glass"></i> Tìm Khách Hàng
        </button>
    </div>

    @if($isAdmin)
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
        <select class="cl-staff-select" onchange="filterByStaff(this.value)" style="margin-bottom:0;">
            @foreach($staffList as $staff)
            <option value="{{ $staff->id }}" {{ $staffFilter == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
            @endforeach
        </select>
        <button class="cl-btn-add" onclick="openAddModal()">
            <i class="fa-solid fa-user-plus"></i> Thêm Khách Hàng
        </button>
    </div>
    @endif

    <table id="clTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Tên KH</th>
                <th>Số Điện Thoại</th>
                <th>Nhóm</th>
                <th>Telesale</th>
                <th>Ghi Chú Gần Nhất</th>
                <th>Tỉnh</th>
                <th>Gọi Cuối</th>
                <th>Đã Gọi</th>
                <th>TT</th>
            </tr>
        </thead>
    </table>
</div>

@can('Admin')
{{-- ===== ADD CUSTOMER MODAL ===== --}}
<div class="cl-modal-overlay" id="addModal">
    <div class="cl-modal cl-modal-sm">
        <div class="cl-modal-header">
            <h3>THÊM KHÁCH HÀNG MỚI</h3>
            <button class="cl-modal-close" onclick="closeAddModal()">✕</button>
        </div>
        <div class="cl-modal-body">
            <div class="cl-form-row">
                <div class="cl-form-group">
                    <label>Tên KH <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="addTenKH" class="cl-form-input" placeholder="Nhập tên khách hàng">
                </div>
                <div class="cl-form-group">
                    <label>Số Điện Thoại <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="addSoDienThoai" class="cl-form-input" placeholder="Nhập số điện thoại">
                </div>
            </div>
            <div class="cl-form-row">
                <div class="cl-form-group">
                    <label>Tỉnh Thành</label>
                    <select id="addTinh" class="cl-form-select" style="width:100%;">
                        <option value="">-- Chọn tỉnh thành --</option>
                        @foreach($provinces as $tinh)
                        <option value="{{ $tinh }}">{{ $tinh }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="cl-form-group">
                    <label>Nhóm Khách Hàng</label>
                    <select id="addMaNhomKH" class="cl-form-select">
                        @foreach($groups as $g)
                        <option value="{{ $g->MaNhomKH }}">{{ $g->TenNhomKH }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="cl-form-group">
                <label>Nhân Viên Phụ Trách <span style="color:#ef4444;">*</span></label>
                <select id="addMaNV" class="cl-form-select" style="width:100%;">
                    @foreach($staffList as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="cl-modal-footer">
            <button class="cl-btn-submit cl-btn-danger" onclick="closeAddModal()">HỦY</button>
            <button class="cl-btn-submit cl-btn-primary" onclick="submitAdd()">THÊM</button>
        </div>
    </div>
</div>
@endcan

<div class="cl-modal-overlay" id="updateModal">
    <div class="cl-modal cl-modal-sm">
        <div class="cl-modal-header">
            <h3>UPDATE THÔNG TIN KHÁCH HÀNG</h3>
            <button class="cl-modal-close" onclick="closeUpdateModal()">✕</button>
        </div>
        <div class="cl-modal-body">
            <input type="hidden" id="upMaKH">
            <div class="cl-form-row">
                <div class="cl-form-group">
                    <label>Tên KH</label>
                    <input type="text" id="upTenKH" class="cl-form-input">
                </div>
                <div class="cl-form-group">
                    <label>Số Điện Thoại</label>
                    <input type="text" id="upSoDienThoai" class="cl-form-input" readonly style="background:#f1f5f9;cursor:not-allowed;">
                </div>
            </div>
            <div class="cl-form-row">
                <div class="cl-form-group">
                    <label>Tỉnh Thành</label>
                    <select id="upTinh" class="cl-form-select" style="width:100%;">
                        <option value="">-- Chọn tỉnh thành --</option>
                        @foreach($provinces as $tinh)
                        <option value="{{ $tinh }}">{{ $tinh }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="cl-form-group">
                    <label>Nhóm Khách Hàng</label>
                    <select id="upMaNhomKH" class="cl-form-select">
                        @foreach($groups as $g)
                        <option value="{{ $g->MaNhomKH }}">{{ $g->TenNhomKH }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="cl-form-group">
                <label>Ghi Chú Khách Hàng</label>
                <textarea id="upCallNote" class="cl-form-textarea" rows="3"></textarea>
            </div>
        </div>
        <div class="cl-modal-footer">
            <button class="cl-btn-submit cl-btn-danger" onclick="closeUpdateModal()">HỦY</button>
            <button class="cl-btn-submit cl-btn-primary" onclick="submitUpdate()">CẬP NHẬT</button>
        </div>
    </div>
</div>

{{-- ===== HISTORY MODAL ===== --}}
<div class="cl-modal-overlay" id="historyModal">
    <div class="cl-modal cl-modal-lg">
        <div class="cl-modal-header">
            <h3 id="hsTitle">LỊCH SỬ LIÊN HỆ</h3>
            <button class="cl-modal-close" onclick="closeHistoryModal()">✕</button>
        </div>
        <div class="cl-modal-body" id="hsBody">
            <div class="hs-loading">Đang tải...</div>
        </div>
    </div>
</div>

{{-- ===== SEARCH DATA MODAL ===== --}}
<div class="cl-modal-overlay" id="searchDataModal">
    <div class="cl-modal cl-modal-sm">
        <div class="cl-modal-header">
            <h3>TÌM KIẾM DATA KHÁCH HÀNG</h3>
            <button class="cl-modal-close" onclick="closeSearchModal()">✕</button>
        </div>
        <div class="cl-modal-body">
            <div style="display:flex;gap:8px;margin-bottom:16px;">
                <input type="text" id="searchDataInput" class="cl-form-input" placeholder="Nhập số điện thoại hoặc tên KH..." style="flex:1;" onkeydown="if(event.key==='Enter') doSearchData()">
                <button class="cl-btn-submit" style="background:#22c55e;color:white;padding:9px 20px;white-space:nowrap;" onclick="doSearchData()">TÌM</button>
            </div>
            <div id="searchDataResult"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
    let clTable;

    $(document).ready(function() {
        // Init Select2 cho Tỉnh Thành (Update modal)
        $('#upTinh').select2({ placeholder: '-- Chọn tỉnh thành --', allowClear: true, width: '100%', dropdownParent: $('#updateModal') });

        // Init Select2 cho Add modal
        if (document.getElementById('addModal')) {
            $('#addTinh').select2({ placeholder: '-- Chọn tỉnh thành --', allowClear: true, width: '100%', dropdownParent: $('#addModal') });
            $('#addMaNV').select2({ placeholder: '-- Chọn nhân viên --', width: '100%', dropdownParent: $('#addModal') });
        }

        clTable = $('#clTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("customers.listData") }}',
                data: function(d) {
                    d.staff = '{{ $staffFilter }}';
                    d.group = '{{ $groupFilter }}';
                }
            },
            columns: [
                { title: 'Tên KH' },
                { title: 'Số Điện Thoại' },
                { title: 'Nhóm' },
                { title: 'Telesale' },
                { title: 'Ghi Chú Gần Nhất' },
                { title: 'Tỉnh', className: 'dt-center' },
                { title: 'Gọi Cuối', className: 'dt-center' },
                { title: 'Đã Gọi', className: 'dt-center' },
                { title: 'TT', orderable: false, searchable: false, className: 'dt-center' },
            ],
            order: [[6, 'desc']],
            pageLength: 25,
            lengthMenu: [25, 50, 100, 200],
            language: {
                processing: 'Đang tải...',
                search: 'Tìm kiếm:',
                lengthMenu: 'Hiển thị _MENU_ dòng',
                info: 'Hiển thị _START_ - _END_ / _TOTAL_ khách hàng',
                infoEmpty: 'Không có dữ liệu',
                infoFiltered: '(lọc từ _MAX_ khách hàng)',
                zeroRecords: 'Không tìm thấy khách hàng nào',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' }
            }
        });
    });

    function filterByStaff(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('staff', val);
        url.searchParams.delete('group');
        window.location.href = url.toString();
    }

    // ===== UPDATE MODAL =====
    function openUpdate(data) {
        document.getElementById('upMaKH').value = data.MaKH;
        document.getElementById('upTenKH').value = data.TenKH || '';
        document.getElementById('upSoDienThoai').value = data.SoDienThoai || '';
        $('#upTinh').val(data.Tinh || '').trigger('change');
        document.getElementById('upMaNhomKH').value = data.MaNhomKH || '';
        document.getElementById('upCallNote').value = '';
        document.getElementById('updateModal').classList.add('show');
    }

    function closeUpdateModal() {
        document.getElementById('updateModal').classList.remove('show');
    }

    function submitUpdate() {
        const id = document.getElementById('upMaKH').value;
        const payload = {
            TenKH: document.getElementById('upTenKH').value,
            SoDienThoai: document.getElementById('upSoDienThoai').value,
            Tinh: document.getElementById('upTinh').value,
            MaNhomKH: document.getElementById('upMaNhomKH').value,
            call_note: document.getElementById('upCallNote').value,
        };

        fetch('/customers/update/' + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                closeUpdateModal();
                clTable.ajax.reload(null, false);
            } else {
                alert(res.message || 'Lỗi cập nhật');
            }
        })
        .catch(() => alert('Lỗi kết nối'));
    }

    // ===== HISTORY MODAL =====
    function openHistory(phone, name) {
        document.getElementById('hsTitle').textContent = 'LỊCH SỬ LIÊN HỆ - ' + (name || phone);
        document.getElementById('hsBody').innerHTML = '<div class="hs-loading"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>';
        document.getElementById('historyModal').classList.add('show');

        fetch('/customers/history?phone=' + encodeURIComponent(phone))
        .then(r => r.json())
        .then(res => {
            if (!res.data || res.data.length === 0) {
                document.getElementById('hsBody').innerHTML = '<div class="hs-empty">Chưa có lịch sử liên hệ</div>';
                return;
            }
            let html = '<table class="hs-table"><thead><tr><th>Thời Gian</th><th>Nhân Viên</th><th>Nội Dung</th>';
            if (isAdmin) html += '<th style="width:60px;"></th>';
            html += '</tr></thead><tbody>';
            res.data.forEach(log => {
                const t = log.time ? new Date(log.time).toLocaleString('vi-VN') : '';
                html += '<tr id="hs-row-'+log.id+'">';
                html += '<td style="white-space:nowrap;">'+t+'</td>';
                html += '<td><b>'+(log.staffName||'—')+'</b></td>';
                html += '<td>'+(log.call_note||'')+'</td>';
                if (isAdmin) {
                    html += '<td><button class="hs-del-btn" onclick="deleteHistory('+log.id+')"><i class="fa-solid fa-trash"></i></button></td>';
                }
                html += '</tr>';
            });
            html += '</tbody></table>';
            document.getElementById('hsBody').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('hsBody').innerHTML = '<div class="hs-empty" style="color:#ef4444;">Lỗi kết nối</div>';
        });
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.remove('show');
    }

    function deleteHistory(id) {
        if (!confirm('Xóa lịch sử liên hệ này?')) return;
        fetch('/customers/history/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                const row = document.getElementById('hs-row-' + id);
                if (row) row.remove();
            }
        });
    }

    // ===== ADD CUSTOMER MODAL =====
    function openAddModal() {
        document.getElementById('addTenKH').value = '';
        document.getElementById('addSoDienThoai').value = '';
        $('#addTinh').val('').trigger('change');
        document.getElementById('addMaNhomKH').value = '';
        document.getElementById('addModal').classList.add('show');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.remove('show');
    }

    function submitAdd() {
        const tenKH = document.getElementById('addTenKH').value.trim();
        const sdt = document.getElementById('addSoDienThoai').value.trim();
        const maNV = document.getElementById('addMaNV').value;

        if (!tenKH) { alert('Vui lòng nhập tên khách hàng!'); return; }
        if (!sdt) { alert('Vui lòng nhập số điện thoại!'); return; }
        if (!maNV) { alert('Vui lòng chọn nhân viên phụ trách!'); return; }

        const payload = {
            TenKH: tenKH,
            SoDienThoai: sdt,
            Tinh: $('#addTinh').val() || '',
            MaNhomKH: document.getElementById('addMaNhomKH').value,
            MaNV: maNV,
        };

        fetch('/customers/store', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                closeAddModal();
                clTable.ajax.reload(null, false);
                alert('Thêm khách hàng thành công!');
            } else {
                alert(res.message || 'Có lỗi xảy ra!');
            }
        })
        .catch(err => { console.error(err); alert('Lỗi kết nối!'); });
    }

    // ===== SEARCH DATA MODAL =====
    function openSearchModal() {
        document.getElementById('searchDataInput').value = '';
        document.getElementById('searchDataResult').innerHTML = '';
        document.getElementById('searchDataModal').classList.add('show');
        setTimeout(() => document.getElementById('searchDataInput').focus(), 200);
    }
    function closeSearchModal() {
        document.getElementById('searchDataModal').classList.remove('show');
    }
    function doSearchData() {
        const keyword = document.getElementById('searchDataInput').value.trim();
        if (!keyword || keyword.length < 3) {
            document.getElementById('searchDataResult').innerHTML = '<div style="text-align:center;color:#94a3b8;padding:12px;">Nhập ít nhất 3 ký tự</div>';
            return;
        }
        document.getElementById('searchDataResult').innerHTML = '<div style="text-align:center;color:#64748b;padding:12px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tìm...</div>';

        fetch('/customers/search-data?keyword=' + encodeURIComponent(keyword))
        .then(r => r.json())
        .then(res => {
            if (!res.found) {
                document.getElementById('searchDataResult').innerHTML = '<div style="text-align:center;color:#ef4444;padding:12px;">' + (res.message || 'Không tìm thấy') + '</div>';
                return;
            }
            let html = '';
            res.data.forEach(item => {
                html += '<div style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:14px;line-height:1.6;">';
                html += '<b style="color:#1e293b;">' + (item.TenKH || '') + '</b> ';
                html += '<span style="color:#3b82f6;">' + (item.SoDienThoai || '') + '</span>';
                if (item.staffName) {
                    html += ' của telesales : <b style="color:#6d28d9;">' + item.staffName + '</b>';
                }
                if (item.TenNhomKH) {
                    html += '<div style="text-align:center;color:#475569;font-size:13px;margin-top:2px;">Thuộc nhóm : <b>' + item.TenNhomKH + '</b></div>';
                }
                html += '</div>';
            });
            document.getElementById('searchDataResult').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('searchDataResult').innerHTML = '<div style="text-align:center;color:#ef4444;padding:12px;">Lỗi kết nối</div>';
        });
    }

    // Close modals on backdrop click
    document.getElementById('updateModal').addEventListener('click', function(e) { if (e.target === this) closeUpdateModal(); });
    document.getElementById('historyModal').addEventListener('click', function(e) { if (e.target === this) closeHistoryModal(); });
    document.getElementById('searchDataModal').addEventListener('click', function(e) { if (e.target === this) closeSearchModal(); });
    if (document.getElementById('addModal')) {
        document.getElementById('addModal').addEventListener('click', function(e) { if (e.target === this) closeAddModal(); });
    }
</script>

{{-- JsSIP loaded via @push('styles') in head --}}

<script>
(function() {
    // SIP Config from server
    const sipSocket = {!! json_encode($callSetting->sockets ?? '') !!};
    const sipUri = {!! json_encode($callSetting->uri ?? '') !!};
    const sipExt = {!! json_encode($userPhone->extension ?? '') !!};
    const sipPass = {!! json_encode($userPhone->password ?? '') !!};
    const sipDauSo = {!! json_encode($userPhone->DauSo ?? '') !!};
    const callDurationLimit = {!! json_encode(($callSetting->call_duration ?? 5) * 60) !!}; // giây

    if (!sipSocket || !sipUri || !sipExt || !sipPass) {
        console.warn('[SIP] Config incomplete - calls disabled');
        return;
    }

    // Build SIP URI: extension@domain (giống cấu hình cũ)
    const sipDomain = sipUri;
    const fullUri = sipExt + '@' + sipDomain;

    console.log('[SIP] Config:', { sipSocket, sipDomain, sipExt, fullUri, sipDauSo });

    // Remote audio element cho phát âm thanh cuộc gọi
    const remoteAudio = document.createElement('audio');
    remoteAudio.id = 'sipRemoteAudio';
    document.body.appendChild(remoteAudio);

    // Status indicator
    const statusEl = document.createElement('div');
    statusEl.className = 'sip-status connecting';
    statusEl.innerHTML = '<span class="dot"></span> Đang kết nối...';
    document.body.appendChild(statusEl);

    // ===== Call Panel HTML (gọi đi + gọi đến) =====
    const panelHtml = `
    <div class="call-panel" id="callPanel">
        <div class="call-panel-top">
            <div class="call-panel-avatar" id="callAvatar"><i class="fa-solid fa-phone-volume"></i></div>
            <div class="call-panel-info">
                <div class="call-panel-label" id="callLabel">Đang gọi đến</div>
                <div class="call-panel-name" id="callName"></div>
                <div class="call-panel-number" id="callNumber"></div>
                <div class="call-panel-staff" id="callStaff"></div>
                <div class="call-panel-status" id="callStatus">Đang kết nối...</div>
            </div>
        </div>
        <div class="call-panel-timer" id="callTimer">00:00</div>
        <div class="call-panel-actions" id="callActions">
            <button class="call-action-btn call-btn-mute" id="btnMute" onclick="toggleMute()" style="display:none;" title="Tắt mic">
                <i class="fa-solid fa-microphone"></i>
            </button>
            <button class="call-action-btn call-btn-hangup" id="btnHangup" onclick="hangupCall()" title="Kết thúc">
                <i class="fa-solid fa-phone-slash"></i>
            </button>
        </div>
        <div class="call-panel-actions" id="incomingActions" style="display:none;">
            <button class="call-action-btn call-btn-answer" id="btnAnswer" onclick="answerCall()" title="Nhận cuộc gọi">
                <i class="fa-solid fa-phone"></i> Nhận
            </button>
            <button class="call-action-btn call-btn-reject" id="btnReject" onclick="rejectCall()" title="Từ chối">
                <i class="fa-solid fa-phone-slash"></i> Từ chối
            </button>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', panelHtml);

    // ===== Call Panel Styles =====
    const callStyles = document.createElement('style');
    callStyles.textContent = `
        .call-panel-label { font-size:12px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; }
        .call-panel-name { font-size:16px; font-weight:700; color:#f8fafc; margin-bottom:2px; }
        .call-panel-staff { font-size:12px; color:#60a5fa; margin-top:2px; }
        .call-panel.incoming .call-panel-avatar { background:linear-gradient(135deg,#3b82f6,#06b6d4); }
        .call-btn-answer { background:#22c55e !important; color:white; font-size:13px; padding:10px 20px !important; border-radius:25px !important; gap:6px; display:flex; align-items:center; }
        .call-btn-answer:hover { background:#16a34a !important; }
        .call-btn-reject { background:#ef4444 !important; color:white; font-size:13px; padding:10px 20px !important; border-radius:25px !important; gap:6px; display:flex; align-items:center; }
        .call-btn-reject:hover { background:#dc2626 !important; }
        @keyframes incomingPulse { 0%,100% { box-shadow:0 0 0 0 rgba(59,130,246,0.5); } 50% { box-shadow:0 0 20px 10px rgba(59,130,246,0.2); } }
        .call-panel.incoming { animation:incomingPulse 1.5s infinite; }
    `;
    document.head.appendChild(callStyles);

    // JsSIP setup
    const socket = new JsSIP.WebSocketInterface(sipSocket);
    const config = {
        sockets: [socket],
        uri: fullUri,
        password: sipPass,
        username: fullUri,
        register: true,
    };
    console.log('[SIP] UA config:', JSON.stringify(config, null, 2));

    // Bật JsSIP debug
    JsSIP.debug.enable('JsSIP:*');

    const ua = new JsSIP.UA(config);
    let currentSession = null;
    let callTimerInterval = null;
    let callSeconds = 0;
    let isMuted = false;
    let incomingSession = null;

    // ===== XỬ LÝ CUỘC GỌI ĐẾN =====
    ua.on('newRTCSession', function(e) {
        console.log('[SIP] newRTCSession:', e.originator);

        if (e.originator === 'remote') {
            // Cuộc gọi đến
            if (currentSession) {
                console.log('[SIP] Đang bận, từ chối cuộc gọi đến');
                e.session.terminate();
                return;
            }

            incomingSession = e.session;
            const caller = e.session.remote_identity.uri.user || '';
            console.log('[SIP] Cuộc gọi đến từ:', caller);

            // Hiển thị popup gọi đến
            showIncomingCall(caller);

            // Tra cứu thông tin KH
            fetch('/customers/lookup-phone?phone=' + encodeURIComponent(caller))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.found) {
                        document.getElementById('callName').textContent = data.TenKH;
                        if (data.staffName) {
                            document.getElementById('callStaff').textContent = 'NV phụ trách: ' + data.staffName;
                        }
                    } else {
                        document.getElementById('callName').textContent = 'Số mới';
                    }
                })
                .catch(function(err) { console.error('[SIP] Lookup error:', err); });

            // Lắng nghe sự kiện trên incoming session
            incomingSession.on('ended', function() {
                console.log('[SIP] Incoming call ended');
                endCall('Cuộc gọi kết thúc');
            });

            incomingSession.on('failed', function(ev) {
                console.log('[SIP] Incoming call failed:', ev.cause);
                endCall('Cuộc gọi kết thúc');
            });

            incomingSession.on('accepted', function() {
                console.log('[SIP] Incoming call accepted');
                updateCallStatus('Đã kết nối', 'connected');
                startTimer();
                document.getElementById('btnMute').style.display = '';
            });

            incomingSession.on('peerconnection', function(ev) {
                ev.peerconnection.addEventListener('addstream', function(event) {
                    console.log('[SIP] Incoming remote stream');
                    remoteAudio.srcObject = event.stream;
                    remoteAudio.play().catch(function(err) { console.warn('[SIP] Audio error:', err); });
                });
            });
        }
    });

    // Nhận cuộc gọi đến
    window.answerCall = function() {
        if (!incomingSession) return;
        currentSession = incomingSession;
        incomingSession = null;

        // Chuyển sang giao diện đang gọi
        document.getElementById('incomingActions').style.display = 'none';
        document.getElementById('callActions').style.display = '';
        document.getElementById('callPanel').classList.remove('incoming');
        document.getElementById('callLabel').textContent = 'Đang kết nối';

        currentSession.answer({
            mediaConstraints: { audio: true, video: false },
        });
    };

    // Từ chối cuộc gọi đến
    window.rejectCall = function() {
        if (incomingSession) {
            incomingSession.terminate();
            incomingSession = null;
        }
        endCall('Đã từ chối');
    };

    function showIncomingCall(phone) {
        const panel = document.getElementById('callPanel');
        panel.classList.add('show', 'incoming');
        document.getElementById('callLabel').textContent = 'Khách đang gọi đến';
        document.getElementById('callName').textContent = 'Đang tìm...';
        document.getElementById('callNumber').textContent = phone;
        document.getElementById('callStaff').textContent = '';
        document.getElementById('callAvatar').innerHTML = '<i class="fa-solid fa-phone-volume"></i>';
        document.getElementById('callActions').style.display = 'none';
        document.getElementById('incomingActions').style.display = '';
        statusEl.style.display = 'none';
    }

    ua.on('registered', function() {
        statusEl.className = 'sip-status registered';
        statusEl.innerHTML = '<span class="dot"></span> SIP: Sẵn sàng (' + sipExt + ')';
    });

    ua.on('unregistered', function() {
        statusEl.className = 'sip-status unregistered';
        statusEl.innerHTML = '<span class="dot"></span> SIP: Mất kết nối';
    });

    ua.on('registrationFailed', function(e) {
        statusEl.className = 'sip-status unregistered';
        statusEl.innerHTML = '<span class="dot"></span> SIP: Lỗi đăng ký';
        console.error('SIP registration failed:', e.cause);
    });

    ua.start();

    // ===== GỌI ĐI =====
    window.makeCall = function(phone, customerName) {
        console.log('[SIP] makeCall:', phone, customerName);

        if (currentSession) {
            alert('Đang có cuộc gọi khác!');
            return;
        }

        let target = 'sip:' + phone + '@' + sipDomain;

        const eventHandlers = {
            'peerconnection': function(e) {
                console.log('[SIP] PeerConnection created');
                e.peerconnection.addEventListener('addstream', function(event) {
                    remoteAudio.srcObject = event.stream;
                    remoteAudio.play().catch(function(err) { console.warn('[SIP] Audio error:', err); });
                });
            },
            'progress': function() {
                updateCallStatus('Đang đổ chuông...', 'ringing');
            },
            'accepted': function() {
                updateCallStatus('Đã kết nối', 'connected');
                startTimer();
                document.getElementById('btnMute').style.display = '';
            },
            'ended': function() {
                endCall('Cuộc gọi kết thúc');
            },
            'failed': function(e) {
                endCall('Cuộc gọi thất bại: ' + (e.cause || ''));
            },
        };

        const callOptions = {
            eventHandlers: eventHandlers,
            mediaConstraints: { audio: true, video: false },
            rtcOfferConstraints: { offerToReceiveAudio: true, offerToReceiveVideo: false },
            extraHeaders: sipDauSo ? ['X-Accountcode: ' + sipDauSo] : [],
        };

        try {
            currentSession = ua.call(target, callOptions);
        } catch (err) {
            console.error('[SIP] Error:', err);
            alert('Lỗi khi gọi: ' + err.message);
            return;
        }

        // Hiển thị popup gọi đi
        showOutgoingCall(phone, customerName || '');
    };

    function showOutgoingCall(phone, name) {
        const panel = document.getElementById('callPanel');
        panel.classList.add('show');
        panel.classList.remove('incoming');
        document.getElementById('callLabel').textContent = 'Đang gọi đến';
        document.getElementById('callName').textContent = name || phone;
        document.getElementById('callNumber').textContent = phone;
        document.getElementById('callStaff').textContent = '';
        document.getElementById('callAvatar').innerHTML = '<i class="fa-solid fa-phone-volume"></i>';
        document.getElementById('callActions').style.display = '';
        document.getElementById('incomingActions').style.display = 'none';
        updateCallStatus('Đang gọi...', 'ringing');
        statusEl.style.display = 'none';
    }

    function updateCallStatus(text, cls) {
        const el = document.getElementById('callStatus');
        el.textContent = text;
        el.className = 'call-panel-status ' + (cls || '');
    }

    function startTimer() {
        callSeconds = 0;
        const timerEl = document.getElementById('callTimer');
        const limitMinutes = Math.floor(callDurationLimit / 60);
        timerEl.classList.add('show');
        callTimerInterval = setInterval(function() {
            callSeconds++;
            const m = String(Math.floor(callSeconds / 60)).padStart(2, '0');
            const s = String(callSeconds % 60).padStart(2, '0');
            timerEl.textContent = m + ':' + s + ' / ' + limitMinutes + ':00';

            // Cảnh báo trước 30 giây
            const remaining = callDurationLimit - callSeconds;
            if (remaining <= 30 && remaining > 0) {
                timerEl.style.color = '#f59e0b';
                updateCallStatus('Sắp hết thời gian! Còn ' + remaining + 's', 'warning');
            }

            // Tự ngắt khi quá thời gian
            if (callSeconds >= callDurationLimit) {
                console.log('[SIP] Auto-hangup: exceeded ' + limitMinutes + ' minutes');
                if (currentSession) {
                    currentSession.terminate();
                }
            }
        }, 1000);
    }

    function endCall(msg) {
        if (callTimerInterval) clearInterval(callTimerInterval);
        updateCallStatus(msg, '');
        document.getElementById('btnMute').style.display = 'none';
        isMuted = false;
        setTimeout(function() {
            document.getElementById('callPanel').classList.remove('show', 'incoming');
            document.getElementById('callTimer').classList.remove('show');
            document.getElementById('callTimer').textContent = '00:00';
            document.getElementById('callTimer').style.color = '';
            statusEl.style.display = '';
            currentSession = null;
            incomingSession = null;
        }, 2000);
    }

    window.hangupCall = function() {
        if (currentSession) {
            currentSession.terminate();
        }
    };

    window.toggleMute = function() {
        if (!currentSession) return;
        isMuted = !isMuted;
        if (isMuted) {
            currentSession.mute({ audio: true });
            document.getElementById('btnMute').classList.add('active');
            document.getElementById('btnMute').innerHTML = '<i class="fa-solid fa-microphone-slash"></i>';
        } else {
            currentSession.unmute({ audio: true });
            document.getElementById('btnMute').classList.remove('active');
            document.getElementById('btnMute').innerHTML = '<i class="fa-solid fa-microphone"></i>';
        }
    };
})();
</script>
@endpush
