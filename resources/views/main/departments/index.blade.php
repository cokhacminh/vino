@extends('main.layouts.app')

@section('title', 'Phòng Ban - Chức Vụ')

@push('styles')
<style>
    /* Tabs */
    .dept-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 24px;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 4px;
    }
    .dept-tab {
        flex: 1;
        padding: 12px 24px;
        border: none;
        background: none;
        font-size: 15px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-radius: 10px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .dept-tab:hover { color: #334155; background: rgba(255,255,255,0.5); }
    .dept-tab.active {
        background: white;
        color: #6d28d9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .dept-tab .tab-count {
        background: #e2e8f0;
        color: #475569;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
    }
    .dept-tab.active .tab-count {
        background: #ede9fe;
        color: #6d28d9;
    }

    /* Tab Content */
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* Card */
    .dept-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .dept-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .dept-card-header h3 {
        margin: 0;
        font-size: 18px;
        color: #1e293b;
        font-weight: 700;
    }
    .dept-card-body { padding: 0; }

    /* Table */
    .dept-table {
        width: 100%;
        border-collapse: collapse;
    }
    .dept-table thead th {
        padding: 12px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .dept-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
    }
    .dept-table tbody tr { transition: background 0.15s ease; }
    .dept-table tbody tr:hover { background: #f8fafc; }
    .dept-table tbody tr:last-child td { border-bottom: none; }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-active {
        background: #dcfce7;
        color: #16a34a;
    }
    .status-inactive {
        background: #fee2e2;
        color: #dc2626;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    /* Permission Tags */
    .perm-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .perm-tag {
        display: inline-block;
        padding: 2px 8px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
    }
    .perm-tag.admin {
        background: #fef3c7;
        color: #d97706;
    }

    /* Action Buttons */
    .btn-actions {
        display: flex;
        gap: 6px;
    }
    .btn-action {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 14px;
    }
    .btn-edit {
        background: #eff6ff;
        color: #2563eb;
    }
    .btn-edit:hover { background: #dbeafe; }
    .btn-delete {
        background: #fef2f2;
        color: #dc2626;
    }
    .btn-delete:hover { background: #fee2e2; }

    /* Add Button */
    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(109, 40, 217, 0.3);
    }
    .btn-add:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(109, 40, 217, 0.4);
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 520px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        animation: modalIn 0.25s ease;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }
    .modal-close {
        width: 32px; height: 32px;
        border: none;
        background: #f1f5f9;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        color: #64748b;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .modal-close:hover { background: #e2e8f0; }
    .modal-body { padding: 24px; }
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
    }

    /* Form */
    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }
    .form-group label .required { color: #dc2626; }
    .form-input {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .form-input:focus {
        outline: none;
        border-color: #6d28d9;
        box-shadow: 0 0 0 3px rgba(109,40,217,0.1);
    }
    select.form-input {
        appearance: auto;
        cursor: pointer;
    }

    /* Checkbox Grid for Permissions */
    .perm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        max-height: 200px;
        overflow-y: auto;
        padding: 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
    }
    .perm-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #334155;
        cursor: pointer;
    }
    .perm-checkbox input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #6d28d9;
        cursor: pointer;
    }

    /* Buttons */
    .btn-modal-cancel {
        padding: 10px 20px;
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-save {
        padding: 10px 24px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3);
    }
    .btn-modal-save:hover { opacity: 0.9; }

    /* Dept Name in ChucVu table */
    .dept-name-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        background: #f0fdf4;
        color: #15803d;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; }
    .empty-state p { margin: 0; font-size: 15px; }

    /* Responsive */
    @media (max-width: 768px) {
        .perm-grid { grid-template-columns: 1fr; }
        .dept-table thead { display: none; }
        .dept-table tbody td {
            display: block;
            padding: 8px 20px;
            text-align: right;
        }
        .dept-table tbody td::before {
            content: attr(data-label);
            float: left;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            font-size: 11px;
        }
        .dept-table tbody tr {
            border-bottom: 2px solid #e2e8f0;
            padding: 8px 0;
        }
    }

    /* Confirm Delete */
    .confirm-text {
        font-size: 15px;
        color: #475569;
        line-height: 1.6;
        text-align: center;
        padding: 10px 0;
    }
    .confirm-text strong { color: #dc2626; }
    .btn-modal-delete {
        padding: 10px 24px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-delete:hover { background: #b91c1c; }
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="page-header" style="margin-bottom: 24px;">
        <h2 style="margin: 0; font-size: 24px; color: #1e293b;">🏢 QUẢN LÝ PHÒNG BAN & CHỨC VỤ</h2>
    </div>

    <!-- Tabs -->
    <div class="dept-tabs">
        <button class="dept-tab active" onclick="switchTab('phongban')">
            🏢 Phòng Ban
            <span class="tab-count">{{ count($phongBans) }}</span>
        </button>
        <button class="dept-tab" onclick="switchTab('chucvu')">
            👔 Chức Vụ
            <span class="tab-count">{{ count($chucVus) }}</span>
        </button>
    </div>

    <!-- ===================== TAB PHÒNG BAN ===================== -->
    <div id="tab-phongban" class="tab-content active">
        <div class="dept-card">
            <div class="dept-card-header">
                <h3>Danh Sách Phòng Ban</h3>
                <button class="btn-add" onclick="openModal('modalAddPB')">
                    ➕ Thêm Phòng Ban
                </button>
            </div>
            <div class="dept-card-body">
                @if(count($phongBans) > 0)
                <table class="dept-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Tên Phòng Ban</th>
                            <th style="width: 120px;">Số Chức Vụ</th>
                            <th style="width: 120px;">Trạng Thái</th>
                            <th style="width: 100px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($phongBans as $index => $pb)
                        <tr>
                            <td data-label="#">{{ $index + 1 }}</td>
                            <td data-label="Tên PB" style="font-weight: 600; color: #1e293b;">{{ $pb->TenPB }}</td>
                            <td data-label="Số CV">
                                <span style="background: #ede9fe; color: #6d28d9; padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 13px;">
                                    {{ $pb->chuc_vus_count }}
                                </span>
                            </td>
                            <td data-label="Trạng Thái">
                                <span class="status-badge {{ $pb->TrangThai ? 'status-active' : 'status-inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $pb->TrangThai ? 'Hoạt động' : 'Ngừng' }}
                                </span>
                            </td>
                            <td data-label="Thao Tác">
                                <div class="btn-actions">
                                    <button class="btn-action btn-edit" title="Sửa"
                                        onclick="openEditPB({{ $pb->MaPB }}, '{{ addslashes($pb->TenPB) }}', {{ $pb->TrangThai }})">
                                        ✏️
                                    </button>
                                    <button class="btn-action btn-delete" title="Xóa"
                                        onclick="openDeletePB({{ $pb->MaPB }}, '{{ addslashes($pb->TenPB) }}', {{ $pb->chuc_vus_count }})">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <div class="empty-icon">🏢</div>
                    <p>Chưa có phòng ban nào. Nhấn "Thêm Phòng Ban" để bắt đầu.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ===================== TAB CHỨC VỤ ===================== -->
    <div id="tab-chucvu" class="tab-content">
        <div class="dept-card">
            <div class="dept-card-header">
                <h3>Danh Sách Chức Vụ</h3>
                <button class="btn-add" onclick="openModal('modalAddCV')">
                    ➕ Thêm Chức Vụ
                </button>
            </div>
            <div class="dept-card-body">
                @if(count($chucVus) > 0)
                <table class="dept-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Tên Chức Vụ</th>
                            <th>Phòng Ban</th>
                            <th>Quyền Hạn</th>
                            <th style="width: 100px;">Trạng Thái</th>
                            <th style="width: 100px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chucVus as $index => $cv)
                        <tr>
                            <td data-label="#">{{ $index + 1 }}</td>
                            <td data-label="Tên CV" style="font-weight: 600; color: #1e293b;">{{ $cv->TenCV }}</td>
                            <td data-label="Phòng Ban">
                                <span class="dept-name-chip">
                                    🏢 {{ $cv->phongBan ? $cv->phongBan->TenPB : 'N/A' }}
                                </span>
                            </td>
                            <td data-label="Quyền Hạn">
                                <div class="perm-tags">
                                    @if(count($cv->spatiePermissions) > 0)
                                        @foreach($cv->spatiePermissions as $quyen)
                                            <span class="perm-tag {{ $quyen == 'Admin' ? 'admin' : '' }}">{{ $quyen }}</span>
                                        @endforeach
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px;">—</span>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Trạng Thái">
                                <span class="status-badge {{ $cv->TrangThai ? 'status-active' : 'status-inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $cv->TrangThai ? 'Hoạt động' : 'Ngừng' }}
                                </span>
                            </td>
                            <td data-label="Thao Tác">
                                <div class="btn-actions">
                                    <button class="btn-action btn-edit" title="Sửa"
                                        onclick='openEditCV({{ $cv->MaCV }}, "{{ addslashes($cv->TenCV) }}", {{ $cv->MaPB }}, @json($cv->spatiePermissions), {{ $cv->TrangThai }})'>
                                        ✏️
                                    </button>
                                    <button class="btn-action btn-delete" title="Xóa"
                                        onclick="openDeleteCV({{ $cv->MaCV }}, '{{ addslashes($cv->TenCV) }}')">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <div class="empty-icon">👔</div>
                    <p>Chưa có chức vụ nào. Nhấn "Thêm Chức Vụ" để bắt đầu.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL: THÊM PHÒNG BAN ===================== -->
<div class="modal-overlay" id="modalAddPB">
    <div class="modal-box">
        <div class="modal-header">
            <h3>➕ Thêm Phòng Ban</h3>
            <button class="modal-close" onclick="closeModal('modalAddPB')">✕</button>
        </div>
        <form action="{{ route('phongban.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Phòng Ban <span class="required">*</span></label>
                    <input type="text" name="TenPB" class="form-input" placeholder="Nhập tên phòng ban..." required maxlength="200">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalAddPB')">Hủy</button>
                <button type="submit" class="btn-modal-save">Thêm Phòng Ban</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: SỬA PHÒNG BAN ===================== -->
<div class="modal-overlay" id="modalEditPB">
    <div class="modal-box">
        <div class="modal-header">
            <h3>✏️ Sửa Phòng Ban</h3>
            <button class="modal-close" onclick="closeModal('modalEditPB')">✕</button>
        </div>
        <form id="formEditPB" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Phòng Ban <span class="required">*</span></label>
                    <input type="text" name="TenPB" id="editPB_TenPB" class="form-input" required maxlength="200">
                </div>
                <div class="form-group">
                    <label>Trạng Thái</label>
                    <select name="TrangThai" id="editPB_TrangThai" class="form-input">
                        <option value="1">Hoạt động</option>
                        <option value="0">Ngừng hoạt động</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEditPB')">Hủy</button>
                <button type="submit" class="btn-modal-save">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: XÓA PHÒNG BAN ===================== -->
<div class="modal-overlay" id="modalDeletePB">
    <div class="modal-box">
        <div class="modal-header">
            <h3>🗑️ Xóa Phòng Ban</h3>
            <button class="modal-close" onclick="closeModal('modalDeletePB')">✕</button>
        </div>
        <form id="formDeletePB" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="confirm-text" id="deletePB_message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalDeletePB')">Hủy</button>
                <button type="submit" class="btn-modal-delete" id="btnDeletePB">Xóa</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: THÊM CHỨC VỤ ===================== -->
<div class="modal-overlay" id="modalAddCV">
    <div class="modal-box">
        <div class="modal-header">
            <h3>➕ Thêm Chức Vụ</h3>
            <button class="modal-close" onclick="closeModal('modalAddCV')">✕</button>
        </div>
        <form action="{{ route('chucvu.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Chức Vụ <span class="required">*</span></label>
                    <input type="text" name="TenCV" class="form-input" placeholder="Nhập tên chức vụ..." required maxlength="50">
                </div>
                <div class="form-group">
                    <label>Phòng Ban <span class="required">*</span></label>
                    <select name="MaPB" class="form-input" required>
                        <option value="">-- Chọn Phòng Ban --</option>
                        @foreach($phongBans as $pb)
                            <option value="{{ $pb->MaPB }}">{{ $pb->TenPB }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Quyền Hạn</label>
                    <div class="perm-grid">
                        @foreach($danhSachQuyen as $quyen)
                        <label class="perm-checkbox">
                            <input type="checkbox" name="QuyenHan[]" value="{{ $quyen }}">
                            {{ $quyen }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalAddCV')">Hủy</button>
                <button type="submit" class="btn-modal-save">Thêm Chức Vụ</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: SỬA CHỨC VỤ ===================== -->
<div class="modal-overlay" id="modalEditCV">
    <div class="modal-box">
        <div class="modal-header">
            <h3>✏️ Sửa Chức Vụ</h3>
            <button class="modal-close" onclick="closeModal('modalEditCV')">✕</button>
        </div>
        <form id="formEditCV" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Chức Vụ <span class="required">*</span></label>
                    <input type="text" name="TenCV" id="editCV_TenCV" class="form-input" required maxlength="50">
                </div>
                <div class="form-group">
                    <label>Phòng Ban <span class="required">*</span></label>
                    <select name="MaPB" id="editCV_MaPB" class="form-input" required>
                        <option value="">-- Chọn Phòng Ban --</option>
                        @foreach($phongBans as $pb)
                            <option value="{{ $pb->MaPB }}">{{ $pb->TenPB }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Quyền Hạn</label>
                    <div class="perm-grid" id="editCV_QuyenHan">
                        @foreach($danhSachQuyen as $quyen)
                        <label class="perm-checkbox">
                            <input type="checkbox" name="QuyenHan[]" value="{{ $quyen }}">
                            {{ $quyen }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label>Trạng Thái</label>
                    <select name="TrangThai" id="editCV_TrangThai" class="form-input">
                        <option value="1">Hoạt động</option>
                        <option value="0">Ngừng hoạt động</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEditCV')">Hủy</button>
                <button type="submit" class="btn-modal-save">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: XÓA CHỨC VỤ ===================== -->
<div class="modal-overlay" id="modalDeleteCV">
    <div class="modal-box">
        <div class="modal-header">
            <h3>🗑️ Xóa Chức Vụ</h3>
            <button class="modal-close" onclick="closeModal('modalDeleteCV')">✕</button>
        </div>
        <form id="formDeleteCV" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="confirm-text" id="deleteCV_message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalDeleteCV')">Hủy</button>
                <button type="submit" class="btn-modal-delete">Xóa Chức Vụ</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ========== TABS ==========
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.dept-tab').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        event.currentTarget.classList.add('active');
    }

    // ========== MODAL ==========
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show'));
        }
    });

    // ========== PHÒNG BAN ==========
    function openEditPB(id, tenPB, trangThai) {
        document.getElementById('editPB_TenPB').value = tenPB;
        document.getElementById('editPB_TrangThai').value = trangThai;
        document.getElementById('formEditPB').action = '/phong-ban/' + id;
        openModal('modalEditPB');
    }

    function openDeletePB(id, tenPB, soCV) {
        const form = document.getElementById('formDeletePB');
        const message = document.getElementById('deletePB_message');
        const btn = document.getElementById('btnDeletePB');

        form.action = '/phong-ban/' + id;

        if (soCV > 0) {
            message.innerHTML = '⚠️ Phòng ban <strong>' + tenPB + '</strong> đang có <strong>' + soCV + ' chức vụ</strong> liên kết.<br>Không thể xóa! Vui lòng xóa các chức vụ trước.';
            btn.disabled = true;
            btn.style.opacity = '0.5';
        } else {
            message.innerHTML = 'Bạn có chắc muốn xóa phòng ban <strong>' + tenPB + '</strong>?<br>Hành động này không thể hoàn tác.';
            btn.disabled = false;
            btn.style.opacity = '1';
        }
        openModal('modalDeletePB');
    }

    // ========== CHỨC VỤ ==========
    function openEditCV(id, tenCV, maPB, permArray, trangThai) {
        document.getElementById('editCV_TenCV').value = tenCV;
        document.getElementById('editCV_MaPB').value = maPB;
        document.getElementById('editCV_TrangThai').value = trangThai;
        document.getElementById('formEditCV').action = '/chuc-vu/' + id;

        // Reset all checkboxes
        document.querySelectorAll('#editCV_QuyenHan input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });

        // Check matching permissions from Spatie
        if (permArray && permArray.length > 0) {
            document.querySelectorAll('#editCV_QuyenHan input[type="checkbox"]').forEach(cb => {
                if (permArray.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        }

        openModal('modalEditCV');
    }

    function openDeleteCV(id, tenCV) {
        document.getElementById('formDeleteCV').action = '/chuc-vu/' + id;
        document.getElementById('deleteCV_message').innerHTML = 'Bạn có chắc muốn xóa chức vụ <strong>' + tenCV + '</strong>?<br>Hành động này không thể hoàn tác.';
        openModal('modalDeleteCV');
    }
</script>
@endpush
