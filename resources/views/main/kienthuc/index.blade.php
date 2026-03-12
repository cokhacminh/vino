@extends('main.layouts.app')
@section('title', 'Kiến Thức Tư Vấn')

@push('styles')
<style>
    .kttv-page { padding: 10px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .kttv-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 16px; flex-wrap: wrap; gap: 12px;
    }
    .kttv-header h2 { margin: 0; font-size: 22px; color: #1e293b; }

    /* Category Filter */
    .kttv-categories {
        display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;
        align-items: center;
    }
    .kttv-cat-btn {
        padding: 8px 18px; border-radius: 20px; border: 2px solid #e2e8f0;
        background: white; color: #475569; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
    }
    .kttv-cat-btn:hover { border-color: #6d28d9; color: #6d28d9; }
    .kttv-cat-btn.active { background: #6d28d9; color: white; border-color: #6d28d9; }

    /* Search */
    .kttv-controls {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 14px; flex-wrap: wrap; gap: 10px;
    }
    .kttv-search {
        padding: 7px 14px; border: 1px solid #d1d5db; border-radius: 8px;
        font-size: 13px; width: 300px; outline: none;
    }
    .kttv-search:focus { border-color: #6d28d9; box-shadow: 0 0 0 2px rgba(109,40,217,0.15); }

    /* Stat */
    .kttv-stat {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; background: #ede9fe; border: 1px solid #c4b5fd;
        border-radius: 8px; font-size: 14px; font-weight: 700; color: #6d28d9;
    }

    /* Add button */
    .kttv-btn-add {
        padding: 9px 20px; border: none; border-radius: 10px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white; font-size: 14px; font-weight: 700; cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3); transition: all 0.2s;
    }
    .kttv-btn-add:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109,40,217,0.4); }

    /* Cards */
    .kttv-cards { column-count: 3; column-gap: 16px; }
    .kttv-card { break-inside: avoid; margin-bottom: 30px; }
    .kttv-card {
        border-radius: 12px; border: 1px solid #e2e8f0;
        padding: 12px; transition: all 0.2s; position: relative;
    }
    .kttv-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1); }

    /* Tình Huống: Purple */
    .kttv-card.cat-tinh-huong { background: #faf5ff; border-color: #e9d5ff; }
    .kttv-card.cat-tinh-huong:hover { border-color: #a855f7; }
    .kttv-card.cat-tinh-huong .kttv-card-label { color: #7e22ce; }
    .kttv-card.cat-tinh-huong .kttv-card-badge { color: #6b21a8; }
    .kttv-card.cat-tinh-huong .kttv-card-answer { background: #f3e8ff; border-left: 3px solid #a855f7; }

    /* Tôm Giống: Warm Amber */
    .kttv-card.cat-tom-giong { background: #fffbeb; border-color: #fde68a; }
    .kttv-card.cat-tom-giong:hover { border-color: #f59e0b; }
    .kttv-card.cat-tom-giong .kttv-card-label { color: #b45309; }
    .kttv-card.cat-tom-giong .kttv-card-badge { color: #92400e; }
    .kttv-card.cat-tom-giong .kttv-card-answer { background: #fef3c7; border-left: 3px solid #f59e0b; }

    /* Thủy Sản: Cool Blue */
    .kttv-card.cat-thuy-san { background: #eff6ff; border-color: #bfdbfe; }
    .kttv-card.cat-thuy-san:hover { border-color: #3b82f6; }
    .kttv-card.cat-thuy-san .kttv-card-label { color: #1d4ed8; }
    .kttv-card.cat-thuy-san .kttv-card-badge { color: #1e40af; }
    .kttv-card.cat-thuy-san .kttv-card-answer { background: #dbeafe; border-left: 3px solid #3b82f6; }

    /* Vi Sinh: Fresh Green */
    .kttv-card.cat-vi-sinh { background: #f0fdf4; border-color: #bbf7d0; }
    .kttv-card.cat-vi-sinh:hover { border-color: #22c55e; }
    .kttv-card.cat-vi-sinh .kttv-card-label { color: #15803d; }
    .kttv-card.cat-vi-sinh .kttv-card-badge { color: #166534; }
    .kttv-card.cat-vi-sinh .kttv-card-answer { background: #dcfce7; border-left: 3px solid #22c55e; }

    /* Vật Tư: Soft Rose */
    .kttv-card.cat-vat-tu { background: #fff1f2; border-color: #fecdd3; }
    .kttv-card.cat-vat-tu:hover { border-color: #f43f5e; }
    .kttv-card.cat-vat-tu .kttv-card-label { color: #be123c; }
    .kttv-card.cat-vat-tu .kttv-card-badge { color: #9f1239; }
    .kttv-card.cat-vat-tu .kttv-card-answer { background: #ffe4e6; border-left: 3px solid #f43f5e; }

    .kttv-card-badge {
        margin-left: 4px;
        font-size: 14px;
        font-weight: 700;
    }

    .kttv-card-label { font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px; padding-left: 5px ;letter-spacing: 0.5px; }
    .kttv-card-question {
        font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 16px;
        line-height: 22px; white-space: pre-wrap;
    }
    .kttv-card-answer {
        font-size: 17px; color: #334155; line-height: 22px;
        border-radius: 8px; padding: 12px; white-space: pre-wrap;
    }

    .kttv-card-actions {
        position: absolute; top: 2px; right: 4px;
        display: flex; gap: 4px;
    }
    .kttv-card-actions button {
        width: 30px; height: 30px; border: none; border-radius: 8px;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        font-size: 13px; transition: all 0.2s;
    }
    .kttv-btn-edit { background: #eff6ff; color: #2563eb; }
    .kttv-btn-edit:hover { background: #dbeafe; }
    .kttv-btn-del { background: #fef2f2; color: #dc2626; }
    .kttv-btn-del:hover { background: #fee2e2; }

    /* Empty */
    .kttv-empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .kttv-empty .empty-icon { font-size: 48px; margin-bottom: 12px; }

    /* Modal */
    .kttv-modal-overlay {
        display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
        z-index: 1000; align-items: center; justify-content: center;
    }
    .kttv-modal-overlay.show { display: flex; }
    .kttv-modal {
        background: white; border-radius: 16px; width: 92%; max-width: 600px;
        max-height: 90vh; overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: kttvIn 0.25s ease;
    }
    @keyframes kttvIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .kttv-modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 20px 24px; border-bottom: 1px solid #f1f5f9;
        position: sticky; top: 0; background: white; z-index: 2;
    }
    .kttv-modal-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1e293b; }
    .kttv-modal-close {
        width: 32px; height: 32px; border: none; background: #f1f5f9;
        border-radius: 8px; cursor: pointer; font-size: 18px; color: #64748b;
        display: flex; align-items: center; justify-content: center;
    }
    .kttv-modal-close:hover { background: #e2e8f0; }
    .kttv-modal-body { padding: 24px; }
    .kttv-modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 16px 24px; border-top: 1px solid #f1f5f9;
        position: sticky; bottom: 0; background: white;
    }

    .kttv-form-group { margin-bottom: 16px; }
    .kttv-form-group label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; color: #374151; }
    .kttv-form-group .req { color: #dc2626; }
    .kttv-form-input {
        width: 100%; padding: 9px 12px; border: 2px solid #e2e8f0;
        border-radius: 10px; font-size: 13px; transition: border-color 0.2s; box-sizing: border-box;
    }
    .kttv-form-input:focus { outline: none; border-color: #6d28d9; box-shadow: 0 0 0 3px rgba(109,40,217,0.1); }
    select.kttv-form-input { appearance: auto; cursor: pointer; }
    textarea.kttv-form-input { resize: vertical; min-height: 100px; font-family: inherit; }

    .kttv-btn-cancel {
        padding: 10px 20px; background: #f1f5f9; color: #475569;
        border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .kttv-btn-cancel:hover { background: #e2e8f0; }
    .kttv-btn-save {
        padding: 10px 24px; background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white; border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3);
    }
    .kttv-btn-save:hover { opacity: 0.9; }
    .kttv-btn-delete-confirm {
        padding: 10px 24px; background: #dc2626; color: white;
        border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .kttv-btn-delete-confirm:hover { background: #b91c1c; }
</style>
@endpush

@section('content')
<div class="kttv-page">
    <div class="kttv-header">
        <h2><i class="fa-solid fa-book-open" style="color:#6d28d9;"></i> Kiến Thức Tư Vấn</h2>
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="kttv-stat">
                <i class="fa-solid fa-clipboard-list"></i> Tổng: {{ $items->count() }}
            </span>
            @can('Admin')
            <button class="kttv-btn-add" onclick="openKttvModal('add')">
                <i class="fa-solid fa-plus"></i> Thêm Kiến Thức
            </button>
            @endcan
        </div>
    </div>

    {{-- Category filter --}}
    <div class="kttv-categories">
        @php
            $categories = ['Tất cả' => '','Tình Huống' =>'Tình Huống', 'Tôm Giống' => 'Tôm Giống', 'Thủy Sản' => 'Thủy Sản', 'Vi Sinh' => 'Vi Sinh', 'Vật Tư' => 'Vật Tư'];
        @endphp
        @foreach($categories as $label => $val)
        <a href="{{ route('kienThucTuVan.index', array_filter(['phan_loai' => $val, 'search' => $search])) }}"
           class="kttv-cat-btn {{ $phanLoai == $val ? 'active' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Search --}}
    <div class="kttv-controls">
        <div></div>
        <form method="GET" action="{{ route('kienThucTuVan.index') }}" style="display:flex; gap:8px;">
            <input type="hidden" name="phan_loai" value="{{ $phanLoai }}">
            <input type="text" name="search" class="kttv-search" placeholder="Tìm kiếm câu hỏi, cách tư vấn..." value="{{ $search }}">
            <button type="submit" style="padding:7px 16px; border:none; border-radius:8px; background:#6d28d9; color:white; font-size:13px; font-weight:700; cursor:pointer;">
                <i class="fa-solid fa-search"></i>
            </button>
            @if($search)
            <a href="{{ route('kienThucTuVan.index', ['phan_loai' => $phanLoai]) }}" style="padding:7px 16px; border:none; border-radius:8px; background:#ef4444; color:white; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center;">
                <i class="fa-solid fa-xmark"></i>
            </a>
            @endif
        </form>
    </div>

    {{-- Cards --}}
    @if($items->count() > 0)
    <div class="kttv-cards">
        @foreach($items as $item)
        @php
            $catClass = match($item->PhanLoai) {
                'Tình Huống' => 'cat-tinh-huong',
                'Tôm Giống' => 'cat-tom-giong',
                'Thủy Sản' => 'cat-thuy-san',
                'Vi Sinh' => 'cat-vi-sinh',
                'Vật Tư' => 'cat-vat-tu',
                default => 'cat-tom-giong',
            };
        @endphp
        <div class="kttv-card {{ $catClass }}">
            @can('Admin')
            <div class="kttv-card-actions">
                <button class="kttv-btn-edit" title="Sửa" onclick='openKttvModal("edit", @json($item))'>
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button class="kttv-btn-del" title="Xóa" onclick="openKttvDelete({{ $item->id }})">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
            @endcan
            <div class="kttv-card-label">Câu hỏi <span class="kttv-card-badge">{{ $item->PhanLoai }}</span></div>
            <div class="kttv-card-question">{{ $item->CauHoiTinhHuong }}</div>
            <div class="kttv-card-label">Cách tư vấn</div>
            <div class="kttv-card-answer">{{ $item->CachTuVan }}</div>
        </div>
        @endforeach
    </div>
    @else
    <div class="kttv-empty">
        <div class="empty-icon">📚</div>
        <h3>Chưa có kiến thức nào</h3>
        <p>{{ $search || $phanLoai ? 'Không tìm thấy kết quả phù hợp' : 'Hãy thêm kiến thức tư vấn mới' }}</p>
    </div>
    @endif
</div>

{{-- ======= MODAL: THÊM / SỬA ======= --}}
<div class="kttv-modal-overlay" id="kttvFormModal">
    <div class="kttv-modal">
        <div class="kttv-modal-header">
            <h3 id="kttvFormTitle">Thêm Kiến Thức</h3>
            <button class="kttv-modal-close" onclick="closeKttvModal('kttvFormModal')">✕</button>
        </div>
        <form id="kttvForm" method="POST">
            @csrf
            <span id="kttvMethodField"></span>
            <div class="kttv-modal-body">
                <div class="kttv-form-group">
                    <label>Phân Loại <span class="req">*</span></label>
                    <select name="PhanLoai" id="kttv_PhanLoai" class="kttv-form-input" required>
                        <option value="">-- Chọn phân loại --</option>
                        <option value="Tình Huống">Tình Huống</option>
                        <option value="Tôm Giống">Tôm Giống</option>
                        <option value="Thủy Sản">Thủy Sản</option>
                        <option value="Vi Sinh">Vi Sinh</option>
                        <option value="Vật Tư">Vật Tư</option>
                    </select>
                </div>
                <div class="kttv-form-group">
                    <label>Câu Hỏi Tình Huống <span class="req">*</span></label>
                    <textarea name="CauHoiTinhHuong" id="kttv_CauHoi" class="kttv-form-input" rows="4" required placeholder="Nhập câu hỏi tình huống..."></textarea>
                </div>
                <div class="kttv-form-group">
                    <label>Cách Tư Vấn <span class="req">*</span></label>
                    <textarea name="CachTuVan" id="kttv_CachTV" class="kttv-form-input" rows="6" required placeholder="Nhập cách tư vấn..."></textarea>
                </div>
            </div>
            <div class="kttv-modal-footer">
                <button type="button" class="kttv-btn-cancel" onclick="closeKttvModal('kttvFormModal')">Hủy</button>
                <button type="submit" class="kttv-btn-save" id="kttvSaveBtn">Thêm</button>
            </div>
        </form>
    </div>
</div>

{{-- ======= MODAL: XÓA ======= --}}
<div class="kttv-modal-overlay" id="kttvDeleteModal">
    <div class="kttv-modal" style="max-width:420px;">
        <div class="kttv-modal-header">
            <h3>🗑️ Xóa Kiến Thức</h3>
            <button class="kttv-modal-close" onclick="closeKttvModal('kttvDeleteModal')">✕</button>
        </div>
        <form id="kttvDeleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="kttv-modal-body" style="text-align:center; padding:30px 24px;">
                <p style="font-size:15px; color:#475569; line-height:1.6;">
                    Bạn có chắc muốn <strong style="color:#dc2626;">xóa</strong> kiến thức này?<br>
                    Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="kttv-modal-footer">
                <button type="button" class="kttv-btn-cancel" onclick="closeKttvModal('kttvDeleteModal')">Hủy</button>
                <button type="submit" class="kttv-btn-delete-confirm">Xóa</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openKttvModal(mode, item = null) {
        const form = document.getElementById('kttvForm');
        const title = document.getElementById('kttvFormTitle');
        const saveBtn = document.getElementById('kttvSaveBtn');
        const methodField = document.getElementById('kttvMethodField');

        if (mode === 'add') {
            title.textContent = '➕ Thêm Kiến Thức Tư Vấn';
            saveBtn.textContent = 'Thêm';
            form.action = '{{ route("kienThucTuVan.store") }}';
            methodField.innerHTML = '';
            document.getElementById('kttv_PhanLoai').value = '';
            document.getElementById('kttv_CauHoi').value = '';
            document.getElementById('kttv_CachTV').value = '';
        } else {
            title.textContent = '✏️ Sửa Kiến Thức Tư Vấn';
            saveBtn.textContent = 'Lưu';
            form.action = '/kien-thuc-tu-van/' + item.id;
            methodField.innerHTML = '@method("PUT")';
            document.getElementById('kttv_PhanLoai').value = item.PhanLoai || '';
            document.getElementById('kttv_CauHoi').value = item.CauHoiTinhHuong || '';
            document.getElementById('kttv_CachTV').value = item.CachTuVan || '';
        }

        document.getElementById('kttvFormModal').classList.add('show');
    }

    function openKttvDelete(id) {
        document.getElementById('kttvDeleteForm').action = '/kien-thuc-tu-van/' + id;
        document.getElementById('kttvDeleteModal').classList.add('show');
    }

    function closeKttvModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    // Close on overlay click
    document.querySelectorAll('.kttv-modal-overlay').forEach(o => {
        o.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('show'); });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.querySelectorAll('.kttv-modal-overlay.show').forEach(m => m.classList.remove('show'));
    });

</script>
@endsection
