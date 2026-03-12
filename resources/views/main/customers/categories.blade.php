@extends('main.layouts.app')

@section('title', 'Phân Loại Khách Hàng')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .cg-page {
        padding: 10px; background: white; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
    }
    .cg-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    }
    .cg-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
    .cg-header-btn {
        padding: 9px 18px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;
        font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s;
    }
    .cg-header-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.4); }

    /* Alert */
    .cg-alert {
        padding: 12px 16px; border-radius: 10px; margin-bottom: 16px;
        font-size: 14px; font-weight: 500;
    }
    .cg-alert-success { background: #dcfce7; color: #15803d; }
    .cg-alert-error { background: #fee2e2; color: #dc2626; }

    /* Table */
    .cg-table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
    .cg-table { width: 100%; border-collapse: collapse; }
    .cg-table thead th {
        padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 700;
        text-transform: uppercase; color: white; background: black;
        border-bottom: 2px solid #e2e8f0; white-space: nowrap;
    }
    .cg-table tbody td {
        padding: 14px 16px; border-bottom: 1px solid #f1f5f9;
        font-size: 15px; color: #334155; vertical-align: middle; text-align: center;
    }
    .cg-table tbody tr { transition: background 0.15s; }
    .cg-table tbody tr:nth-child(even) { background: #f1f5f9; }
    .cg-table tbody tr:hover { background: #e8edf4; }

    .cg-badge {
        display: inline-block; padding: 6px 16px; border-radius: 6px;
        font-size: 14px; font-weight: 700; white-space: nowrap;
    }
    .cg-count { font-weight: 700; font-size: 16px; }

    /* Action buttons */
    .cg-actions { display: flex; gap: 6px; justify-content: center; }
    .cg-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 36px; height: 32px; border-radius: 6px; border: none;
        cursor: pointer; font-size: 16px; transition: all 0.15s;
    }
    .cg-btn:hover { transform: scale(1.1); }
    .cg-btn-edit { background: #3b82f6; color: white; }
    .cg-btn-delete { background: #ef4444; color: white; }
    .cg-btn-disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
    .cg-btn-disabled:hover { transform: none; }

    /* Modal */
    .cg-modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;
    }
    .cg-modal-overlay.show { display: flex; }
    .cg-modal {
        background: white; border-radius: 16px; width: 480px; max-width: 95vw;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .cg-modal-header {
        padding: 18px 24px; border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .cg-modal-header h3 { margin: 0; font-size: 18px; color: #1e293b; }
    .cg-modal-close {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        background: #f1f5f9; cursor: pointer; font-size: 18px; color: #64748b;
    }
    .cg-modal-close:hover { background: #e2e8f0; }
    .cg-modal-body { padding: 24px; }
    .cg-modal-footer {
        padding: 16px 24px; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .cg-form-group { margin-bottom: 16px; }
    .cg-form-group label {
        display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;
    }
    .cg-form-group .required { color: #dc2626; }
    .cg-form-input {
        width: 100%; padding: 9px 14px; border: 2px solid #e2e8f0; border-radius: 10px;
        font-size: 14px; transition: border-color 0.2s; box-sizing: border-box;
    }
    .cg-form-input:focus { outline: none; border-color: #3b82f6; }
    .cg-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .cg-btn-submit {
        padding: 9px 20px; border-radius: 10px; border: none; font-size: 14px;
        font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .cg-btn-primary { background: #3b82f6; color: white; }
    .cg-btn-primary:hover { background: #2563eb; }
    .cg-btn-secondary { background: #f1f5f9; color: #475569; }
    .cg-btn-secondary:hover { background: #e2e8f0; }

    .cg-preview {
        display: flex; align-items: center; gap: 12px; margin-top: 12px;
    }
    .cg-preview-badge {
        display: inline-block; padding: 6px 16px; border-radius: 6px;
        font-size: 14px; font-weight: 700;
    }
    .cg-preview-label { font-size: 12px; color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="cg-page">
    <div class="cg-header">
        <h2><i class="fa-solid fa-tags" style="color:#3b82f6;"></i> Nhóm Khách Hàng</h2>
        @can('Admin')
        <button class="cg-header-btn" onclick="openCgModal()"><i class="fa-solid fa-plus"></i> Thêm Nhóm</button>
        @endcan
    </div>

    @if(session('success'))
        <div class="cg-alert cg-alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="cg-alert cg-alert-error">❌ {{ session('error') }}</div>
    @endif

    <div class="cg-table-wrap">
        <table class="cg-table">
            <thead>
                <tr>
                    <th style="width:60px;">STT</th>
                    <th>Tên Nhóm</th>
                    <th>Hiển Thị</th>
                    <th>Số Khách Hàng</th>
                    @can('Admin')
                    <th style="width:120px;">Thao Tác</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $index => $group)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight:600;">{{ $group->TenNhomKH }}</td>
                    <td>
                        <span class="cg-badge" style="background:{{ $group->background }}; color:{{ $group->color }};">
                            {{ $group->TenNhomKH }}
                        </span>
                    </td>
                    <td>
                        <span class="cg-count">{{ number_format($group->dataCount) }}</span>
                    </td>
                    @can('Admin')
                    <td>
                        <div class="cg-actions">
                            <button class="cg-btn cg-btn-edit" title="Sửa" onclick='openCgEdit(@json($group))'><i class="fa-solid fa-pen"></i></button>
                            @if($group->dataCount == 0)
                            <form action="{{ route('customers.destroyGroup', $group->MaNhomKH) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa nhóm {{ addslashes($group->TenNhomKH) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="cg-btn cg-btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                            </form>
                            @else
                            <button class="cg-btn cg-btn-disabled" title="Không thể xóa (còn {{ $group->dataCount }} KH)" disabled><i class="fa-solid fa-trash"></i></button>
                            @endif
                        </div>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:#94a3b8;">
                        Chưa có nhóm khách hàng nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== ADD/EDIT MODAL ===== --}}
@can('Admin')
<div class="cg-modal-overlay" id="cgModal">
    <div class="cg-modal">
        <div class="cg-modal-header">
            <h3 id="cgModalTitle">Thêm Nhóm Khách Hàng</h3>
            <button class="cg-modal-close" onclick="closeCgModal()">✕</button>
        </div>
        <form id="cgForm" method="POST" action="{{ route('customers.storeGroup') }}">
            @csrf
            <input type="hidden" name="_method" id="cgMethod" value="POST">
            <div class="cg-modal-body">
                <div class="cg-form-group">
                    <label>Tên Nhóm <span class="required">*</span></label>
                    <input type="text" name="TenNhomKH" id="cgTenNhom" class="cg-form-input" required placeholder="Nhập tên nhóm khách hàng" oninput="updateCgPreview()">
                </div>
                <div class="cg-form-row">
                    <div class="cg-form-group">
                        <label>Màu Nền</label>
                        <input type="color" name="background" id="cgBg" class="cg-form-input" value="#3b82f6" style="height:42px; padding:4px;" oninput="updateCgPreview()">
                    </div>
                    <div class="cg-form-group">
                        <label>Màu Chữ</label>
                        <select name="color" id="cgColor" class="cg-form-input" onchange="updateCgPreview()">
                            <option value="white">Trắng</option>
                            <option value="black">Đen</option>
                        </select>
                    </div>
                </div>
                <div class="cg-preview">
                    <span class="cg-preview-label">Xem trước:</span>
                    <span class="cg-preview-badge" id="cgPreview" style="background:#3b82f6; color:white;">Nhóm mới</span>
                </div>
            </div>
            <div class="cg-modal-footer">
                <button type="button" class="cg-btn-submit cg-btn-secondary" onclick="closeCgModal()">Hủy</button>
                <button type="submit" class="cg-btn-submit cg-btn-primary" id="cgSubmitBtn">Thêm Nhóm</button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
    function openCgModal() {
        document.getElementById('cgModalTitle').textContent = 'Thêm Nhóm Khách Hàng';
        document.getElementById('cgForm').action = '{{ route("customers.storeGroup") }}';
        document.getElementById('cgMethod').value = 'POST';
        document.getElementById('cgTenNhom').value = '';
        document.getElementById('cgBg').value = '#3b82f6';
        document.getElementById('cgColor').value = 'white';
        document.getElementById('cgSubmitBtn').textContent = 'Thêm Nhóm';
        updateCgPreview();
        document.getElementById('cgModal').classList.add('show');
    }

    function openCgEdit(g) {
        document.getElementById('cgModalTitle').textContent = 'Sửa Nhóm Khách Hàng';
        document.getElementById('cgForm').action = '/customers/groups/' + g.MaNhomKH;
        document.getElementById('cgMethod').value = 'PUT';
        document.getElementById('cgTenNhom').value = g.TenNhomKH || '';
        document.getElementById('cgBg').value = g.background || '#3b82f6';
        document.getElementById('cgColor').value = g.color || 'white';
        document.getElementById('cgSubmitBtn').textContent = 'Lưu Thay Đổi';
        updateCgPreview();
        document.getElementById('cgModal').classList.add('show');
    }

    function closeCgModal() {
        document.getElementById('cgModal').classList.remove('show');
    }

    function updateCgPreview() {
        const name = document.getElementById('cgTenNhom').value || 'Nhóm mới';
        const bg = document.getElementById('cgBg').value;
        const color = document.getElementById('cgColor').value;
        const preview = document.getElementById('cgPreview');
        preview.textContent = name;
        preview.style.background = bg;
        preview.style.color = color;
    }

    // Close on backdrop click
    document.getElementById('cgModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCgModal();
    });
</script>
@endpush
