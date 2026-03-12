@extends('main.layouts.app')

@section('title', 'Quản Lý Quyền Hạn')

@push('styles')
<style>
    /* Header */
    .perm-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .perm-header h2 { margin: 0; font-size: 24px; color: #1e293b; }
    .perm-stats {
        display: flex;
        gap: 12px;
    }
    .perm-stat {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .perm-stat.total { background: #ede9fe; color: #6d28d9; }
    .perm-stat.fixed { background: #fef3c7; color: #d97706; }
    .perm-stat.custom { background: #dcfce7; color: #16a34a; }

    /* Card */
    .perm-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .perm-card-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .perm-card-header h3 { margin: 0; font-size: 16px; color: #1e293b; font-weight: 700; }

    /* Table */
    .perm-table {
        width: 100%;
        border-collapse: collapse;
    }
    .perm-table thead th {
        padding: 12px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .perm-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
    }
    .perm-table tbody tr { transition: background 0.15s ease; }
    .perm-table tbody tr:hover { background: #f8fafc; }

    /* Permission name badges */
    .perm-name-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
    }
    .perm-name-badge.admin { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .perm-name-badge.sale { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
    .perm-name-badge.salemanager { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0c4a6e; }
    .perm-name-badge.ketoan { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #166534; }
    .perm-name-badge.nhansu { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #5b21b6; }
    .perm-name-badge.custom { background: #f1f5f9; color: #475569; }

    .fixed-badge {
        font-size: 9px;
        padding: 2px 6px;
        background: #dc2626;
        color: white;
        border-radius: 4px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    /* Counter chips */
    .count-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
    }
    .count-chip.roles { background: #ede9fe; color: #6d28d9; }
    .count-chip.users { background: #dbeafe; color: #2563eb; }

    /* Actions */
    .btn-actions { display: flex; gap: 4px; }
    .btn-action {
        width: 32px; height: 32px;
        border: none; border-radius: 8px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; font-size: 13px;
    }
    .btn-edit { background: #eff6ff; color: #2563eb; }
    .btn-edit:hover { background: #dbeafe; }
    .btn-delete { background: #fef2f2; color: #dc2626; }
    .btn-delete:hover { background: #fee2e2; }
    .btn-action:disabled { opacity: 0.3; cursor: not-allowed; }

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
        box-shadow: 0 2px 8px rgba(109,40,217,0.3);
        transition: all 0.2s;
    }
    .btn-add:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109,40,217,0.4); }

    /* Matrix Card */
    .matrix-grid {
        overflow-x: auto;
    }
    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .matrix-table thead th {
        padding: 10px 12px;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        white-space: nowrap;
    }
    .matrix-table thead th:first-child { text-align: left; }
    .matrix-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
        vertical-align: middle;
    }
    .matrix-table tbody td:first-child {
        text-align: left;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
    }
    .matrix-check { color: #16a34a; font-size: 16px; }
    .matrix-empty { color: #e2e8f0; font-size: 14px; }

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
        width: 92%;
        max-width: 450px;
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
    .modal-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1e293b; }
    .modal-close {
        width: 32px; height: 32px;
        border: none; background: #f1f5f9;
        border-radius: 8px; cursor: pointer;
        font-size: 18px; color: #64748b;
        display: flex; align-items: center; justify-content: center;
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
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }
    .form-group .required { color: #dc2626; }
    .form-input {
        width: 100%;
        padding: 9px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .form-input:focus {
        outline: none;
        border-color: #6d28d9;
        box-shadow: 0 0 0 3px rgba(109,40,217,0.1);
    }
    .btn-modal-cancel {
        padding: 10px 20px;
        background: #f1f5f9;
        color: #475569;
        border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-save {
        padding: 10px 24px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white; border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3);
    }
    .btn-modal-save:hover { opacity: 0.9; }
    .btn-modal-delete {
        padding: 10px 24px;
        background: #dc2626; color: white;
        border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-delete:hover { background: #b91c1c; }
    .confirm-text {
        font-size: 15px; color: #475569;
        line-height: 1.6; text-align: center;
        padding: 10px 0;
    }
    .confirm-text strong { color: #dc2626; }

    /* Info box */
    .info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        color: #1d4ed8;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        line-height: 1.5;
    }
    .info-box .info-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
</style>
@endpush

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="perm-header">
        <div>
            <h2>🔐 QUẢN LÝ QUYỀN HẠN</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="perm-stats">
                <span class="perm-stat total">🔑 Tổng: {{ count($permissions) }}</span>
                <span class="perm-stat fixed">🔒 Cố định: {{ $permissions->where('is_fixed', true)->count() }}</span>
                <span class="perm-stat custom">✨ Tùy chỉnh: {{ $permissions->where('is_fixed', false)->count() }}</span>
            </div>
            <button class="btn-add" onclick="openModal('modalAddPerm')">
                ➕ Thêm Quyền Hạn
            </button>
        </div>
    </div>

    <!-- Info -->
    <div class="info-box">
        <span class="info-icon">💡</span>
        <div>
            <strong>4 quyền cố định</strong> (Admin, Sale, Kế Toán, Nhân Sự) không thể sửa hoặc xóa.
            Quyền <strong>Admin</strong> có nghĩa được cấp <strong>toàn bộ quyền hạn</strong> trong hệ thống.
        </div>
    </div>

    <!-- Danh sách quyền hạn -->
    <div class="perm-card">
        <div class="perm-card-header">
            <h3>📋 Danh Sách Quyền Hạn</h3>
        </div>
        <table class="perm-table">
            <thead>
                <tr>
                    <th>Quyền Hạn</th>
                    <th>Loại</th>
                    <th>Số Roles</th>
                    <th>Số Users</th>
                    <th>Ngày Tạo</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $perm)
                <tr>
                    <td>
                        @php
                            $badgeClass = 'custom';
                            if ($perm->name === 'Admin') $badgeClass = 'admin';
                            elseif ($perm->name === 'Sale') $badgeClass = 'sale';
                            elseif ($perm->name === 'Sale Manager') $badgeClass = 'salemanager';
                            elseif ($perm->name === 'Kế Toán') $badgeClass = 'ketoan';
                            elseif ($perm->name === 'Nhân Sự') $badgeClass = 'nhansu';
                        @endphp
                        <span class="perm-name-badge {{ $badgeClass }}">
                            {{ $perm->name }}
                        </span>
                    </td>
                    <td>
                        @if($perm->is_fixed)
                            <span class="fixed-badge">CỐ ĐỊNH</span>
                        @else
                            <span style="font-size: 11px; color: #64748b;">Tùy chỉnh</span>
                        @endif
                    </td>
                    <td><span class="count-chip roles">{{ $perm->roles_count }}</span></td>
                    <td><span class="count-chip users">{{ $perm->users_count }}</span></td>
                    <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                        {{ $perm->created_at ? $perm->created_at->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td>
                        <div class="btn-actions">
                            <button class="btn-action btn-edit" title="Sửa"
                                {{ $perm->is_fixed ? 'disabled' : '' }}
                                onclick="openEditPerm({{ $perm->id }}, '{{ addslashes($perm->name) }}')">
                                ✏️
                            </button>
                            <button class="btn-action btn-delete" title="Xóa"
                                {{ $perm->is_fixed ? 'disabled' : '' }}
                                onclick="openDeletePerm({{ $perm->id }}, '{{ addslashes($perm->name) }}')">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Ma trận Role - Permission -->
    <div class="perm-card">
        <div class="perm-card-header">
            <h3>🔗 Ma Trận Quyền Hạn Theo Chức Vụ</h3>
        </div>
        <div class="matrix-grid">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Chức Vụ / Quyền Hạn</th>
                        @foreach($permissions as $perm)
                            <th>{{ $perm->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        @foreach($permissions as $perm)
                            <td>
                                @if($role->hasPermissionTo($perm->name))
                                    <span class="matrix-check">✅</span>
                                @else
                                    <span class="matrix-empty">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===================== MODAL: THÊM QUYỀN HẠN ===================== -->
<div class="modal-overlay" id="modalAddPerm">
    <div class="modal-box">
        <div class="modal-header">
            <h3>➕ Thêm Quyền Hạn</h3>
            <button class="modal-close" onclick="closeModal('modalAddPerm')">✕</button>
        </div>
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên quyền hạn <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="VD: Quản Lý Kho, Xem Báo Cáo..." required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalAddPerm')">Hủy</button>
                <button type="submit" class="btn-modal-save">Thêm Quyền Hạn</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: SỬA QUYỀN HẠN ===================== -->
<div class="modal-overlay" id="modalEditPerm">
    <div class="modal-box">
        <div class="modal-header">
            <h3>✏️ Sửa Quyền Hạn</h3>
            <button class="modal-close" onclick="closeModal('modalEditPerm')">✕</button>
        </div>
        <form id="formEditPerm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên quyền hạn <span class="required">*</span></label>
                    <input type="text" name="name" id="edit_perm_name" class="form-input" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEditPerm')">Hủy</button>
                <button type="submit" class="btn-modal-save">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL: XÓA QUYỀN HẠN ===================== -->
<div class="modal-overlay" id="modalDeletePerm">
    <div class="modal-box">
        <div class="modal-header">
            <h3>🗑️ Xóa Quyền Hạn</h3>
            <button class="modal-close" onclick="closeModal('modalDeletePerm')">✕</button>
        </div>
        <form id="formDeletePerm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="confirm-text" id="deletePerm_message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalDeletePerm')">Hủy</button>
                <button type="submit" class="btn-modal-delete">Xóa Quyền Hạn</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('show'); });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show'));
    });

    function openEditPerm(id, name) {
        document.getElementById('formEditPerm').action = '/permissions/' + id;
        document.getElementById('edit_perm_name').value = name;
        openModal('modalEditPerm');
    }

    function openDeletePerm(id, name) {
        document.getElementById('formDeletePerm').action = '/permissions/' + id;
        document.getElementById('deletePerm_message').innerHTML =
            'Bạn có chắc muốn xóa quyền hạn <strong>' + name + '</strong>?<br>Tất cả roles và users đang có quyền này sẽ bị mất.';
        openModal('modalDeletePerm');
    }
</script>
@endpush
