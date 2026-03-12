@extends('main.layouts.app')

@section('title', 'Hồ Sơ Nhân Sự')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Select2 custom */
    .select2-container--default .select2-selection--single {
        height: 40px; border: 2px solid #e2e8f0; border-radius: 10px;
        display: flex; align-items: center; padding: 0 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { top: 6px; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color: #6d28d9; }
    .select2-dropdown { border: 2px solid #e2e8f0; border-radius: 10px; z-index: 9999; }
    .select2-results__option--highlighted { background: #6d28d9 !important; }
    .select2-container { width: 100% !important; }
    /* Flatpickr custom */
    .flatpickr-input { background: white !important; }
    .page-content {
        padding: 10px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
    }
    /* Header */
    .ns-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .ns-header h2 { margin: 0; font-size: 24px; color: #1e293b; }
    .ns-stats { display: flex; gap: 12px; }
    .ns-stat {
        padding: 8px 16px; border-radius: 10px; font-size: 13px;
        font-weight: 600; display: flex; align-items: center; gap: 6px;
    }
    .ns-stat.total { background: #ede9fe; color: #6d28d9; }
    .ns-stat.thuViec { background: #fef3c7; color: #d97706; }
    .ns-stat.chinhThuc { background: #dcfce7; color: #16a34a; }

    /* Filter */
    .ns-filter {
        display: flex; gap: 12px; margin-bottom: 20px;
        flex-wrap: wrap; align-items: center;
    }
    .ns-filter .filter-input {
        padding: 8px 14px; border: 2px solid #e2e8f0; border-radius: 10px;
        font-size: 13px; min-width: 180px; transition: border-color 0.2s;
    }
    .ns-filter .filter-input:focus { outline: none; border-color: #6d28d9; }

    /* Table */
    .ns-card {
        background: white; border-radius: 16px; overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
    }
    .ns-table { width: 100%; border-collapse: collapse; }
    .ns-table thead th {
        padding: 12px 16px; text-align: center; font-size: 12px;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        color: white; background: #1e293b; border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .ns-table tbody td {
        padding: 12px 16px; border-bottom: 1px solid #f1f5f9;
        font-size: 14px; color: #334155; vertical-align: middle; text-align: center;
    }
    .ns-table tbody tr { transition: background 0.15s ease; }
    .ns-table tbody tr:hover { background: #f8fafc; }

    /* Badges */
    .badge-hd {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: 11px;
        font-weight: 600; white-space: nowrap;
    }
    .badge-thuViec { background: #fef3c7; color: #d97706; }
    .badge-chinhThuc { background: #dcfce7; color: #16a34a; }

    /* Buttons */
    .btn-actions { display: flex; gap: 4px; justify-content: center; }
    .btn-action {
        width: 32px; height: 32px; border: none; border-radius: 8px;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; font-size: 14px;
    }
    .btn-edit { background: #eff6ff; color: #2563eb; }
    .btn-edit:hover { background: #dbeafe; }
    .btn-delete { background: #fef2f2; color: #dc2626; }
    .btn-delete:hover { background: #fee2e2; }
    .btn-view { background: #f0fdf4; color: #16a34a; }
    .btn-view:hover { background: #dcfce7; }

    .btn-add {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white; border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3); transition: all 0.2s;
    }
    .btn-add:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109,40,217,0.4); }

    /* Modal */
    .modal-overlay {
        display: none; position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
        z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white; border-radius: 16px;
        width: 95%; max-width: 800px; max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        animation: modalIn 0.25s ease;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 24px; border-bottom: 1px solid #f1f5f9;
        position: sticky; top: 0; background: white; z-index: 2;
    }
    .modal-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1e293b; }
    .modal-close {
        width: 32px; height: 32px; border: none; background: #f1f5f9;
        border-radius: 8px; cursor: pointer; font-size: 18px; color: #64748b;
        display: flex; align-items: center; justify-content: center;
    }
    .modal-close:hover { background: #e2e8f0; }
    .modal-body { padding: 5px 24px; }
    .modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 16px 24px; border-top: 1px solid #f1f5f9;
        position: sticky; bottom: 0; background: white;
    }

    /* Tabs */
    .ns-tabs {
        display: flex; gap: 0; border-bottom: 2px solid #e2e8f0;
        margin-bottom: 20px; flex-wrap: wrap;
    }
    .ns-tab {
        padding: 10px 18px; cursor: pointer; font-size: 13px;
        font-weight: 600; color: #64748b; border-bottom: 2px solid transparent;
        margin-bottom: -2px; transition: all 0.2s; white-space: nowrap;
    }
    .ns-tab:hover { color: #6d28d9; }
    .ns-tab.active { color: #6d28d9; border-bottom-color: #6d28d9; }
    .ns-tab-content { display: none; }
    .ns-tab-content.active { display: block; }

    /* Form */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-row.three-col { grid-template-columns: 1fr 1fr 1fr; }
    .form-group { margin-bottom: 14px; }
    .form-group label {
        display: block; margin-bottom: 5px; font-size: 12px;
        font-weight: 600; color: #374151;
    }
    .form-group .required { color: #dc2626; }
    .form-input {
        width: 100%; padding: 9px 12px; border: 2px solid #e2e8f0;
        border-radius: 10px; font-size: 13px; transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .form-input:focus { outline: none; border-color: #6d28d9; box-shadow: 0 0 0 3px rgba(109,40,217,0.1); }
    select.form-input { appearance: auto; cursor: pointer; }
    textarea.form-input { resize: vertical; min-height: 60px; }

    .section-title {
        font-size: 14px; font-weight: 700; color: #6d28d9;
        margin-bottom: 14px; padding-bottom: 8px;
        border-bottom: 1px solid #ede9fe;
        display: flex; align-items: center; gap: 8px;
    }

    /* Buttons */
    .btn-modal-cancel {
        padding: 10px 20px; background: #f1f5f9; color: #475569;
        border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-save {
        padding: 10px 24px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white; border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3);
    }
    .btn-modal-save:hover { opacity: 0.9; }

    .confirm-text { font-size: 15px; color: #475569; line-height: 1.6; text-align: center; padding: 10px 0; }
    .confirm-text strong { color: #dc2626; }
    .btn-modal-delete {
        padding: 10px 24px; background: #dc2626; color: white;
        border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .btn-modal-delete:hover { background: #b91c1c; }

    /* View Modal */
    .view-section { margin-bottom: 20px; }
    .view-section h4 {
        font-size: 17px; font-weight: 700; color: #d94e28;
        ; padding-bottom: 6px;margin:6px; border-bottom: 1px solid #ede9fe;
    }
    .view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .view-item { display: flex; flex-direction: column; padding: 2px 0;gap:3px }
    .view-label { font-size: 13px; font-weight: 600; color: #0059b2; text-transform: uppercase; }
    .view-value { font-size: 16px; color: black; font-weight: 500; }
    .btn-edit, .btn-delete{
        margin-right: unset;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="ns-header">
        <div>
            <h2><i class="fa-solid fa-id-card-clip" style="color:#6d28d9;"></i> QUẢN LÝ NHÂN SỰ</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="ns-stats">
                <span class="ns-stat total"><i class="fa-solid fa-users"></i> Tổng: {{ count($nhansuList) }}</span>
                <span class="ns-stat chinhThuc"><i class="fa-solid fa-file-contract"></i> Chính thức: {{ $nhansuList->where('LoaiHD', 'Chính Thức')->count() }}</span>
                <span class="ns-stat thuViec"><i class="fa-solid fa-clock"></i> Thử việc: {{ $nhansuList->where('LoaiHD', 'Thử Việc')->count() }}</span>
            </div>
            <button class="btn-add" onclick="openModal('modalAddNS')">
                <i class="fa-solid fa-plus"></i> Thêm Nhân Sự
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="ns-filter">
        <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Tìm theo tên, SĐT..." oninput="filterTable()">
        <select class="filter-input" id="filterLoaiHD" onchange="filterTable()">
            <option value="">Tất cả Nhóm</option>
            <option value="Thử Việc">Thử Việc</option>
            <option value="Chính Thức">Chính Thức</option>
        </select>
        <select class="filter-input" id="filterPhongBan" onchange="filterTable()">
            <option value="">Tất cả Phòng Ban</option>
            @foreach($phongbanList as $pb)
                <option value="{{ $pb->TenPB }}">{{ $pb->TenPB }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="ns-card">
        <table class="ns-table" id="nsTable">
            <thead>
                <tr>
                    <th>STT</th>
                    <th style="text-align:left;">Họ Tên</th>
                    <th>Phòng Ban</th>
                    <th>Chức Vụ</th>
                    <th>Giới Tính</th>
                    <th>SĐT</th>
                    <th>Nhóm</th>
                    <th>Tài Khoản</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $pbMap = DB::table('phongban')->pluck('TenPB', 'MaPB');
                    $cvMap = DB::table('chucvu')->pluck('TenCV', 'MaCV');
                @endphp
                @foreach($nhansuList as $index => $ns)
                @php
                    $tenPB = ($ns->user && $ns->user->MaPB) ? ($pbMap[$ns->user->MaPB] ?? '—') : '—';
                    $tenCV = ($ns->user && $ns->user->MaCV) ? ($cvMap[$ns->user->MaCV] ?? '—') : '—';
                @endphp
                <tr data-search="{{ mb_strtolower($ns->HoTen . ' ' . $ns->SDT) }}"
                    data-loaihd="{{ $ns->LoaiHD }}"
                    data-phongban="{{ $tenPB }}">
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight:600; text-align:left; white-space:nowrap;">{{ $ns->HoTen }}</td>
                    <td style="white-space:nowrap;">{{ $tenPB }}</td>
                    <td style="white-space:nowrap;">{{ $tenCV }}</td>
                    <td>{{ $ns->GioiTinh ?? '—' }}</td>
                    <td style="white-space:nowrap;">{{ $ns->SDT ?? '—' }}</td>
                    <td>
                        @if($ns->LoaiHD)
                            <span class="badge-hd {{ $ns->LoaiHD == 'Chính Thức' ? 'badge-chinhThuc' : 'badge-thuViec' }}">
                                <i class="fa-solid {{ $ns->LoaiHD == 'Chính Thức' ? 'fa-file-contract' : 'fa-clock' }}"></i>
                                {{ $ns->LoaiHD }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $ns->user ? $ns->user->name : '—' }}</td>
                    <td>
                        <div class="btn-actions">
                            <button class="btn-action btn-view" title="Xem chi tiết" onclick='viewNS(@json($ns))'>
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Nhân Sự') || Auth::user()->can('Admin') || Auth::user()->can('Nhân Sự'))
                            <button class="btn-action btn-edit" title="Sửa" onclick='editNS(@json($ns))'>
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            @endif
                            @if(Auth::user()->hasRole('Admin') || Auth::user()->can('Admin'))
                            <button class="btn-action btn-delete" title="Xóa" onclick="deleteNS({{ $ns->id }}, '{{ addslashes($ns->HoTen) }}')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ===================== MODAL: THÊM NHÂN SỰ ===================== -->
<div class="modal-overlay" id="modalAddNS">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus"></i> Thêm Nhân Sự</h3>
            <button class="modal-close" onclick="closeModal('modalAddNS')">✕</button>
        </div>
        <form action="{{ route('nhansu.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <!-- Tabs -->
                <div class="ns-tabs" id="addTabs">
                    <div class="ns-tab active" onclick="switchTab('addTabs', 'addTab', 0)"><i class="fa-solid fa-user"></i> Cá Nhân</div>
                    <div class="ns-tab" onclick="switchTab('addTabs', 'addTab', 1)"><i class="fa-solid fa-graduation-cap"></i> Học Vấn</div>
                    <div class="ns-tab" onclick="switchTab('addTabs', 'addTab', 2)"><i class="fa-solid fa-file-signature"></i> HĐLĐ</div>
                    <div class="ns-tab" onclick="switchTab('addTabs', 'addTab', 3)"><i class="fa-solid fa-building-columns"></i> NH - BHXH - MST</div>
                </div>

                <!-- Tab 1: Thông tin cá nhân -->
                <div class="ns-tab-content active" id="addTab0">
                    <div class="form-group">
                        <label>Liên kết Tài Khoản</label>
                        <select name="user_id" class="form-input select2-user" id="add_user_id">
                            <option value="">-- Không liên kết --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ tên <span class="required">*</span></label>
                            <input type="text" name="HoTen" class="form-input" placeholder="Nhập họ tên..." required>
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="text" name="NgaySinh" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="form-row three-col">
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="GioiTinh" class="form-input">
                                <option value="">-- Chọn --</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Số CCCD</label>
                            <input type="text" name="SoCCCD" class="form-input" placeholder="Nhập số CCCD...">
                        </div>
                        <div class="form-group">
                            <label>Ngày cấp CCCD</label>
                            <input type="text" name="NgayCapCCCD" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nơi cấp CCCD</label>
                        <input type="text" name="NoiCapCCCD" class="form-input" placeholder="Nhập nơi cấp...">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="SDT" class="form-input" placeholder="Nhập SĐT...">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="Email" class="form-input" placeholder="Nhập email...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Thường trú</label>
                        <textarea name="ThuongTru" class="form-input" placeholder="Nhập địa chỉ thường trú..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ hiện tại</label>
                        <textarea name="DiaChiHienTai" class="form-input" placeholder="Nhập địa chỉ hiện tại..."></textarea>
                    </div>
                </div>

                <!-- Tab 2: Trình độ học vấn -->
                <div class="ns-tab-content" id="addTab1">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Trình độ học vấn</label>
                            <select name="TrinhDoHocVan" class="form-input">
                                <option value="">-- Chọn --</option>
                                <option value="Trung cấp">Trung cấp</option>
                                <option value="Cao đẳng">Cao đẳng</option>
                                <option value="Đại học">Đại học</option>
                                <option value="Thạc sĩ">Thạc sĩ</option>
                                <option value="Tiến sĩ">Tiến sĩ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Năm tốt nghiệp</label>
                            <input type="text" name="NamTotNghiep" class="form-input" placeholder="VD: 2020">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Trường đào tạo</label>
                        <input type="text" name="TruongDaoTao" class="form-input" placeholder="Nhập tên trường...">
                    </div>
                    <div class="form-group">
                        <label>Chuyên ngành</label>
                        <input type="text" name="ChuyenNganh" class="form-input" placeholder="Nhập chuyên ngành...">
                    </div>
                </div>

                <!-- Tab 3: Thông tin HĐLĐ -->
                <div class="ns-tab-content" id="addTab2">
                    <div class="form-group">
                        <label>Loại hợp đồng</label>
                        <select name="LoaiHD" class="form-input loaihd-select" data-target="add">
                            <option value="">-- Chọn --</option>
                            <option value="Thử Việc">Thử Việc</option>
                            <option value="Chính Thức">Chính Thức</option>
                        </select>
                    </div>
                    <div class="hd-group hd-thuViec" data-modal="add" style="display:none;">
                        <div class="section-title"><i class="fa-solid fa-file-lines"></i> HĐ Thử Việc</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ngày ký HĐ thử việc</label>
                                <input type="text" name="NgayKyHDTV" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="form-group">
                                <label>Ngày hết hạn HĐ TV</label>
                                <input type="text" name="NgayHetHanHDTV" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                    <div class="hd-group hd-chinhThuc" data-modal="add" style="display:none;">
                        <div class="section-title"><i class="fa-solid fa-file-contract"></i> HĐ Xác Định Thời Hạn</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ngày ký HĐ XĐTH</label>
                                <input type="text" name="NgayKyHDXDTH" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="form-group">
                                <label>Ngày hết hạn HĐ XĐTH</label>
                                <input type="text" name="NgayHetHanHDXDTH" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                        <div class="section-title"><i class="fa-solid fa-file-shield"></i> HĐ Không Xác Định Thời Hạn</div>
                        <div class="form-group">
                            <label>Ngày ký HĐ KXĐ</label>
                            <input type="text" name="NgayKyHDKXD" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Ngân hàng - BHXH - MST -->
                <div class="ns-tab-content" id="addTab3">
                    <div class="form-group">
                        <label>Số sổ BHXH</label>
                        <input type="text" name="SoSoBHXH" class="form-input" placeholder="Nhập số sổ BHXH...">
                    </div>
                    <div class="form-group">
                        <label>MST cá nhân</label>
                        <input type="text" name="MSTCaNhan" class="form-input" placeholder="Nhập mã số thuế cá nhân...">
                    </div>
                    <div class="form-group">
                        <label>STK Ngân hàng</label>
                        <input type="text" name="STKNganHang" class="form-input" placeholder="Nhập số tài khoản ngân hàng...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalAddNS')">Hủy</button>
                <button type="submit" class="btn-modal-save">Thêm Nhân Sự</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: SỬA NHÂN SỰ ===================== -->
<div class="modal-overlay" id="modalEditNS">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-pen"></i> Sửa Nhân Sự</h3>
            <button class="modal-close" onclick="closeModal('modalEditNS')">✕</button>
        </div>
        <form id="formEditNS" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="ns-tabs" id="editTabs">
                    <div class="ns-tab active" onclick="switchTab('editTabs', 'editTab', 0)"><i class="fa-solid fa-user"></i> Cá Nhân</div>
                    <div class="ns-tab" onclick="switchTab('editTabs', 'editTab', 1)"><i class="fa-solid fa-graduation-cap"></i> Học Vấn</div>
                    <div class="ns-tab" onclick="switchTab('editTabs', 'editTab', 2)"><i class="fa-solid fa-file-signature"></i> HĐLĐ</div>
                    <div class="ns-tab" onclick="switchTab('editTabs', 'editTab', 3)"><i class="fa-solid fa-building-columns"></i> NH - BHXH - MST</div>
                </div>

                <!-- Tab 1: Cá nhân -->
                <div class="ns-tab-content active" id="editTab0">
                    <div class="form-group">
                        <label>Liên kết Tài Khoản</label>
                        <select name="user_id" id="edit_user_id" class="form-input select2-user">
                            <option value="">-- Không liên kết --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ tên <span class="required">*</span></label>
                            <input type="text" name="HoTen" id="edit_HoTen" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="text" name="NgaySinh" id="edit_NgaySinh" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="form-row three-col">
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="GioiTinh" id="edit_GioiTinh" class="form-input">
                                <option value="">-- Chọn --</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Số CCCD</label>
                            <input type="text" name="SoCCCD" id="edit_SoCCCD" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Ngày cấp CCCD</label>
                            <input type="text" name="NgayCapCCCD" id="edit_NgayCapCCCD" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nơi cấp CCCD</label>
                        <input type="text" name="NoiCapCCCD" id="edit_NoiCapCCCD" class="form-input">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="SDT" id="edit_SDT" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="Email" id="edit_Email" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Thường trú</label>
                        <textarea name="ThuongTru" id="edit_ThuongTru" class="form-input"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ hiện tại</label>
                        <textarea name="DiaChiHienTai" id="edit_DiaChiHienTai" class="form-input"></textarea>
                    </div>
                </div>

                <!-- Tab 2: Học vấn -->
                <div class="ns-tab-content" id="editTab1">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Trình độ học vấn</label>
                            <select name="TrinhDoHocVan" id="edit_TrinhDoHocVan" class="form-input">
                                <option value="">-- Chọn --</option>
                                <option value="Trung cấp">Trung cấp</option>
                                <option value="Cao đẳng">Cao đẳng</option>
                                <option value="Đại học">Đại học</option>
                                <option value="Thạc sĩ">Thạc sĩ</option>
                                <option value="Tiến sĩ">Tiến sĩ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Năm tốt nghiệp</label>
                            <input type="text" name="NamTotNghiep" id="edit_NamTotNghiep" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Trường đào tạo</label>
                        <input type="text" name="TruongDaoTao" id="edit_TruongDaoTao" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Chuyên ngành</label>
                        <input type="text" name="ChuyenNganh" id="edit_ChuyenNganh" class="form-input">
                    </div>
                </div>

                <!-- Tab 3: HĐLĐ -->
                <div class="ns-tab-content" id="editTab2">
                    <div class="form-group">
                        <label>Loại hợp đồng</label>
                        <select name="LoaiHD" id="edit_LoaiHD" class="form-input loaihd-select" data-target="edit">
                            <option value="">-- Chọn --</option>
                            <option value="Thử Việc">Thử Việc</option>
                            <option value="Chính Thức">Chính Thức</option>
                        </select>
                    </div>
                    <div class="hd-group hd-thuViec" data-modal="edit" style="display:none;">
                        <div class="section-title"><i class="fa-solid fa-file-lines"></i> HĐ Thử Việc</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ngày ký HĐ thử việc</label>
                                <input type="text" name="NgayKyHDTV" id="edit_NgayKyHDTV" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="form-group">
                                <label>Ngày hết hạn HĐ TV</label>
                                <input type="text" name="NgayHetHanHDTV" id="edit_NgayHetHanHDTV" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                    <div class="hd-group hd-chinhThuc" data-modal="edit" style="display:none;">
                        <div class="section-title"><i class="fa-solid fa-file-contract"></i> HĐ Xác Định Thời Hạn</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ngày ký HĐ XĐTH</label>
                                <input type="text" name="NgayKyHDXDTH" id="edit_NgayKyHDXDTH" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="form-group">
                                <label>Ngày hết hạn HĐ XĐTH</label>
                                <input type="text" name="NgayHetHanHDXDTH" id="edit_NgayHetHanHDXDTH" class="form-input datepicker" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                        <div class="section-title"><i class="fa-solid fa-file-shield"></i> HĐ Không Xác Định Thời Hạn</div>
                        <div class="form-group">
                            <label>Ngày ký HĐ KXĐ</label>
                            <input type="text" name="NgayKyHDKXD" id="edit_NgayKyHDKXD" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                </div>

                <!-- Tab 4: NH - BHXH - MST -->
                <div class="ns-tab-content" id="editTab3">
                    <div class="form-group">
                        <label>Số sổ BHXH</label>
                        <input type="text" name="SoSoBHXH" id="edit_SoSoBHXH" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>MST cá nhân</label>
                        <input type="text" name="MSTCaNhan" id="edit_MSTCaNhan" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>STK Ngân hàng</label>
                        <input type="text" name="STKNganHang" id="edit_STKNganHang" class="form-input">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEditNS')">Hủy</button>
                <button type="submit" class="btn-modal-save">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: XEM CHI TIẾT ===================== -->
<div class="modal-overlay" id="modalViewNS">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-id-card"></i> Chi Tiết Nhân Sự</h3>
            <button class="modal-close" onclick="closeModal('modalViewNS')">✕</button>
        </div>
        <div class="modal-body" id="viewNSContent"></div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeModal('modalViewNS')">Đóng</button>
        </div>
    </div>
</div>

<!-- ===================== MODAL: XÓA ===================== -->
<div class="modal-overlay" id="modalDeleteNS">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-trash-can"></i> Xóa Nhân Sự</h3>
            <button class="modal-close" onclick="closeModal('modalDeleteNS')">✕</button>
        </div>
        <form id="formDeleteNS" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="confirm-text" id="deleteNS_message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalDeleteNS')">Hủy</button>
                <button type="submit" class="btn-modal-delete">Xóa Nhân Sự</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
<script>
    // ========== MODAL ==========
    function openModal(id) {
        document.getElementById(id).classList.add('show');
        // Re-init Select2 inside modal after it becomes visible
        setTimeout(() => $('#' + id + ' .select2-user').select2({ dropdownParent: $('#' + id), placeholder: '-- Không liên kết --', allowClear: true, width: '100%' }), 50);
    }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('show'); });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show'));
    });

    // ========== TABS ==========
    function switchTab(tabsId, tabPrefix, index) {
        const tabsEl = document.getElementById(tabsId);
        tabsEl.querySelectorAll('.ns-tab').forEach((t, i) => t.classList.toggle('active', i === index));
        let i = 0;
        while (document.getElementById(tabPrefix + i)) {
            document.getElementById(tabPrefix + i).classList.toggle('active', i === index);
            i++;
        }
    }

    // ========== FILTER ==========
    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const loaiHD = document.getElementById('filterLoaiHD').value;
        const phongBan = document.getElementById('filterPhongBan').value;
        document.querySelectorAll('#nsTable tbody tr').forEach(row => {
            const matchSearch = !search || row.dataset.search.includes(search);
            const matchLoaiHD = !loaiHD || row.dataset.loaihd === loaiHD;
            const matchPB = !phongBan || row.dataset.phongban === phongBan;
            row.style.display = (matchSearch && matchLoaiHD && matchPB) ? '' : 'none';
        });
    }

    // ========== HELPERS ==========
    function isoToDisplay(val) {
        if (!val || val === '0000-00-00') return '';
        const d = val.substring(0, 10).split('-');
        if (d.length !== 3) return '';
        return d[2] + '/' + d[1] + '/' + d[0];
    }
    function displayDate(val) {
        if (!val || val === '0000-00-00') return '—';
        const d = new Date(val);
        return d.toLocaleDateString('vi-VN');
    }

    // ========== LOẠI HĐ TOGGLE ==========
    function toggleLoaiHD(selectEl) {
        const target = selectEl.dataset.target;
        const val = selectEl.value;
        document.querySelectorAll('.hd-group[data-modal="' + target + '"]').forEach(g => g.style.display = 'none');
        if (val === 'Thử Việc') {
            document.querySelectorAll('.hd-thuViec[data-modal="' + target + '"]').forEach(g => g.style.display = 'block');
        } else if (val === 'Chính Thức') {
            document.querySelectorAll('.hd-chinhThuc[data-modal="' + target + '"]').forEach(g => g.style.display = 'block');
        }
    }
    document.querySelectorAll('.loaihd-select').forEach(sel => {
        sel.addEventListener('change', function() { toggleLoaiHD(this); });
    });

    // ========== FLATPICKR INIT ==========
    flatpickr('.datepicker', {
        dateFormat: 'd/m/Y',
        allowInput: true,
        locale: 'vn'
    });

    // ========== VIEW ==========
    function viewNS(ns) {
        let html = `
        <div class="view-section">
            <h4><i class="fa-solid fa-user"></i> Thông Tin Cá Nhân</h4>
            <div class="view-grid">
                <div class="view-item"><span class="view-label">Họ tên</span><span class="view-value">${ns.HoTen || '—'}</span></div>
                <div class="view-item"><span class="view-label">Ngày sinh</span><span class="view-value">${displayDate(ns.NgaySinh)}</span></div>
                <div class="view-item"><span class="view-label">Giới tính</span><span class="view-value">${ns.GioiTinh || '—'}</span></div>
                <div class="view-item"><span class="view-label">Số CCCD</span><span class="view-value">${ns.SoCCCD || '—'}</span></div>
                <div class="view-item"><span class="view-label">Ngày cấp CCCD</span><span class="view-value">${displayDate(ns.NgayCapCCCD)}</span></div>
                <div class="view-item"><span class="view-label">Nơi cấp CCCD</span><span class="view-value">${ns.NoiCapCCCD || '—'}</span></div>
                <div class="view-item"><span class="view-label">SĐT</span><span class="view-value">${ns.SDT || '—'}</span></div>
                <div class="view-item"><span class="view-label">Email</span><span class="view-value">${ns.Email || '—'}</span></div>
            </div>
            <div class="view-item" style="margin-top:8px"><span class="view-label">Thường trú</span><span class="view-value">${ns.ThuongTru || '—'}</span></div>
            <div class="view-item"><span class="view-label">Địa chỉ hiện tại</span><span class="view-value">${ns.DiaChiHienTai || '—'}</span></div>
        </div>
        <div class="view-section">
            <h4><i class="fa-solid fa-graduation-cap"></i> Trình Độ Học Vấn</h4>
            <div class="view-grid">
                <div class="view-item"><span class="view-label">Trình độ</span><span class="view-value">${ns.TrinhDoHocVan || '—'}</span></div>
                <div class="view-item"><span class="view-label">Năm Tốt Nghiệp</span><span class="view-value">${ns.NamTotNghiep || '—'}</span></div>
                <div class="view-item"><span class="view-label">Trường đào tạo</span><span class="view-value">${ns.TruongDaoTao || '—'}</span></div>
                <div class="view-item"><span class="view-label">Chuyên ngành</span><span class="view-value">${ns.ChuyenNganh || '—'}</span></div>
            </div>
        </div>
        <div class="view-section">
            <h4><i class="fa-solid fa-file-signature"></i> Thông Tin HĐLĐ</h4>
            <div class="view-grid">
                <div class="view-item"><span class="view-label">Loại Hợp Đồng</span><span class="view-value">${ns.LoaiHD || '—'}</span></div>
                <div class="view-item"><span class="view-label">Ký Hợp Đồng Thử Việc</span><span class="view-value">${displayDate(ns.NgayKyHDTV)}</span></div>
                <div class="view-item"><span class="view-label">Hết hạn Hợp Đồng Thử Việc</span><span class="view-value">${displayDate(ns.NgayHetHanHDTV)}</span></div>
                <div class="view-item"><span class="view-label">Ký Hợp Đồng Chính Thức</span><span class="view-value">${displayDate(ns.NgayKyHDXDTH)}</span></div>
                <div class="view-item"><span class="view-label">Hết hạn Hợp Đồng Chính Thức</span><span class="view-value">${displayDate(ns.NgayHetHanHDXDTH)}</span></div>
                <div class="view-item"><span class="view-label">Ký Hợp Đồng Ký Xác Định</span><span class="view-value">${displayDate(ns.NgayKyHDKXD)}</span></div>
            </div>
        </div>
        <div class="view-section">
            <h4><i class="fa-solid fa-building-columns"></i> Ngân Hàng - BHXH - MST</h4>
            <div class="view-grid">
                <div class="view-item"><span class="view-label">Số sổ BHXH</span><span class="view-value">${ns.SoSoBHXH || '—'}</span></div>
                <div class="view-item"><span class="view-label">MST cá nhân</span><span class="view-value">${ns.MSTCaNhan || '—'}</span></div>
                <div class="view-item"><span class="view-label">STK Ngân hàng</span><span class="view-value">${ns.STKNganHang || '—'}</span></div>
            </div>
        </div>`;
        document.getElementById('viewNSContent').innerHTML = html;
        openModal('modalViewNS');
    }

    // ========== EDIT ==========
    function editNS(ns) {
        document.getElementById('formEditNS').action = '/nhansu/' + ns.id;

        // Tab 1
        $('#edit_user_id').val(ns.user_id || '').trigger('change');
        document.getElementById('edit_HoTen').value = ns.HoTen || '';
        setFlatpickr('edit_NgaySinh', ns.NgaySinh);
        document.getElementById('edit_GioiTinh').value = ns.GioiTinh || '';
        document.getElementById('edit_SoCCCD').value = ns.SoCCCD || '';
        setFlatpickr('edit_NgayCapCCCD', ns.NgayCapCCCD);
        document.getElementById('edit_NoiCapCCCD').value = ns.NoiCapCCCD || '';
        document.getElementById('edit_SDT').value = ns.SDT || '';
        document.getElementById('edit_Email').value = ns.Email || '';
        document.getElementById('edit_ThuongTru').value = ns.ThuongTru || '';
        document.getElementById('edit_DiaChiHienTai').value = ns.DiaChiHienTai || '';

        // Tab 2
        document.getElementById('edit_TrinhDoHocVan').value = ns.TrinhDoHocVan || '';
        document.getElementById('edit_TruongDaoTao').value = ns.TruongDaoTao || '';
        document.getElementById('edit_ChuyenNganh').value = ns.ChuyenNganh || '';
        document.getElementById('edit_NamTotNghiep').value = ns.NamTotNghiep || '';

        // Tab 3
        document.getElementById('edit_LoaiHD').value = ns.LoaiHD || '';
        toggleLoaiHD(document.getElementById('edit_LoaiHD'));
        setFlatpickr('edit_NgayKyHDTV', ns.NgayKyHDTV);
        setFlatpickr('edit_NgayHetHanHDTV', ns.NgayHetHanHDTV);
        setFlatpickr('edit_NgayKyHDXDTH', ns.NgayKyHDXDTH);
        setFlatpickr('edit_NgayHetHanHDXDTH', ns.NgayHetHanHDXDTH);
        setFlatpickr('edit_NgayKyHDKXD', ns.NgayKyHDKXD);

        // Tab 4
        document.getElementById('edit_SoSoBHXH').value = ns.SoSoBHXH || '';
        document.getElementById('edit_MSTCaNhan').value = ns.MSTCaNhan || '';
        document.getElementById('edit_STKNganHang').value = ns.STKNganHang || '';

        switchTab('editTabs', 'editTab', 0);
        openModal('modalEditNS');
    }

    // Helper to set flatpickr value from ISO date
    function setFlatpickr(id, val) {
        const el = document.getElementById(id);
        if (el && el._flatpickr && val) {
            el._flatpickr.setDate(val.substring(0, 10), true);
        } else if (el) {
            el.value = isoToDisplay(val);
        }
    }

    // ========== DELETE ==========
    function deleteNS(id, name) {
        document.getElementById('formDeleteNS').action = '/nhansu/' + id;
        document.getElementById('deleteNS_message').innerHTML =
            'Bạn có chắc muốn xóa nhân sự <strong>' + name + '</strong>?<br>Hành động này không thể hoàn tác.';
        openModal('modalDeleteNS');
    }
</script>
@endpush
