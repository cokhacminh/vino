@extends('main.layouts.app')
@section('title', 'Quản Lý Khuyến Mãi')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .sys-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; flex-wrap:wrap; gap:14px; }
    .sys-header h2 { margin:0; font-size:23px; color:#1e293b; }
    .sys-header-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .stat-badge { padding:7px 15px; border-radius:10px; font-size:13px; font-weight:600; background:#f1f5f9; color:#475569; }
    .btn-add-main { padding:9px 18px; border-radius:10px; border:none; background:#059669; color:white; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.2s; }
    .btn-add-main:hover { background:#047857; }
    .sys-filter { display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; align-items:center; }
    .filter-input { padding:8px 14px; border:2px solid #e2e8f0; border-radius:10px; font-size:13px; min-width:200px; transition:border-color 0.2s; background:white; }
    .filter-input:focus { outline:none; border-color:#059669; }
    .sys-card { background:white; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04); overflow:hidden; }
    .sys-table { width:100%; border-collapse:collapse; }
    .sys-table thead th { padding:12px 16px; text-align:left; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#fff; background:black; white-space:nowrap; }
    .sys-table tbody td { padding:12px 16px; border-bottom:1px solid #f1f5f9; font-size:14px; color:#334155; vertical-align:middle; }
    .sys-table tbody tr:nth-child(even) { background:#f8fafc; }
    .sys-table tbody tr:hover { background:#eef2f7; }
    .sys-table-wrap { overflow-x:auto; }
    .badge-active { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; background:#dcfce7; color:#15803d; }
    .badge-pause  { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; background:#fee2e2; color:#dc2626; }
    .yc-tag { display:inline-block; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:600; background:#dbeafe; color:#1d4ed8; margin:2px; }
    .qt-tag { display:inline-block; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:600; background:#fef3c7; color:#b45309; margin:2px; }
    .or-divider { display:inline-block; padding:2px 6px; font-size:10px; font-weight:700; color:#94a3b8; }
    .btn-actions { display:flex; gap:6px; }
    .btn-action { width:32px; height:32px; border-radius:8px; border:none; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; transition:all 0.18s; }
    .btn-edit { background:#f1f5f9; color:#475569; } .btn-edit:hover { background:#e2e8f0; }
    .btn-delete { background:#fee2e2; color:#dc2626; } .btn-delete:hover { background:#fecaca; }
    .sys-empty { text-align:center; padding:40px 20px; color:#94a3b8; }
    .sys-empty .empty-icon { font-size:36px; margin-bottom:12px; }
    .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center; }
    .modal-overlay.show { display:flex; }
    .modal-box { background:white; border-radius:18px; width:620px; max-width:96vw; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(0,0,0,0.18); }
    .modal-header { padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:white; z-index:1; }
    .modal-header h3 { margin:0; font-size:18px; color:#1e293b; }
    .modal-close { width:32px; height:32px; border-radius:8px; border:none; background:#f1f5f9; cursor:pointer; font-size:18px; color:#64748b; }
    .modal-close:hover { background:#e2e8f0; }
    .modal-body { padding:24px; }
    .modal-footer { padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px; position:sticky; bottom:0; background:white; }
    .form-group { margin-bottom:16px; }
    .form-group label { display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:6px; }
    .req { color:#dc2626; }
    .form-input { width:100%; padding:9px 14px; border:2px solid #e2e8f0; border-radius:10px; font-size:13px; transition:border-color 0.2s; box-sizing:border-box; }
    .form-input:focus { outline:none; border-color:#059669; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .btn-submit { padding:9px 20px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; }
    .btn-primary { background:#059669; color:white; } .btn-primary:hover { background:#047857; }
    .btn-secondary { background:#f1f5f9; color:#475569; } .btn-secondary:hover { background:#e2e8f0; }
    .sp-rows { display:flex; flex-direction:column; gap:8px; margin-bottom:10px; }
    .sp-row { display:flex; gap:8px; align-items:center; }
    .sp-row .sp-select-wrap { flex:1; }
    .sp-row input[type="number"] { width:80px; padding:8px 10px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px; }
    .sp-row input[type="number"]:focus { outline:none; border-color:#059669; }
    .btn-rm-row { width:28px; height:28px; border-radius:6px; border:none; background:#fee2e2; color:#dc2626; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .btn-add-row { padding:6px 13px; border-radius:8px; border:none; background:#d1fae5; color:#059669; font-size:12px; font-weight:600; cursor:pointer; }
    .btn-add-row:hover { background:#a7f3d0; }
    .section-label { font-size:12px; font-weight:700; color:#059669; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; }
    .alert-msg { padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px; font-weight:500; }
    .alert-success { background:#dcfce7; color:#15803d; }
    .alert-error   { background:#fee2e2; color:#dc2626; }

    /* Select2 overrides */
    .sp-row .select2-container { width:100% !important; }
    .sp-row .select2-selection--single { height:38px; border:2px solid #e2e8f0; border-radius:8px; padding:4px 8px; font-size:13px; }
    .sp-row .select2-selection__arrow { top:5px; }
    .select2-dropdown { z-index:99999; }

    /* OR separator between gift rows */
    .qt-or-separator {
        text-align:center; padding:4px 0; font-size:12px; font-weight:700;
        color:#f59e0b; letter-spacing:1px;
    }
</style>
@endpush

@section('content')
<div style="padding:10px;background:white;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04);">

    <div class="sys-header">
        <h2>🎉 Quản Lý Khuyến Mãi</h2>
        <div class="sys-header-actions">
            <span class="stat-badge">{{ $khuyenmais->count() }} chương trình</span>
            <span class="stat-badge" style="background:#dcfce7;color:#15803d;">
                {{ $khuyenmais->where('TinhTrang','Đang Hoạt Động')->count() }} đang hoạt động
            </span>
            @can('Admin')
            <button class="btn-add-main" onclick="openAddKM()">+ Thêm Khuyến Mãi</button>
            @endcan
        </div>
    </div>

    <div class="sys-filter">
        <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Tìm theo tên chương trình..." oninput="filterTable()">
        <select class="filter-input" id="filterStatus" onchange="filterTable()" style="min-width:170px;">
            <option value="">Tất cả tình trạng</option>
            <option value="Đang Hoạt Động">Đang Hoạt Động</option>
            <option value="Tạm Dừng">Tạm Dừng</option>
        </select>
    </div>

    @if(session('success'))
        <div class="alert-msg alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-msg alert-error">❌ {{ session('error') }}</div>
    @endif

    <div class="sys-card">
        <div class="sys-table-wrap">
            <table class="sys-table" id="kmTable">
                <thead>
                    <tr>
                        <th style="width:45px;">STT</th>
                        <th style="min-width:180px;">Tên Chương Trình</th>
                        <th style="min-width:200px;">Điều Kiện (SP × SL)</th>
                        <th style="min-width:200px;">Quà Tặng (chọn 1)</th>
                        <th style="width:155px;">Tình Trạng</th>
                        @can('Admin')<th style="width:100px;">Thao Tác</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($khuyenmais as $i => $km)
                    @php
                        $yeuCau  = json_decode($km->YeuCau, true) ?? [];
                        $quaTang = json_decode($km->QuaTang, true) ?? [];
                        // YeuCau can be array or object depending on data
                        $isYcObj = is_array($yeuCau) && count($yeuCau) > 0 && !isset($yeuCau[0]);
                    @endphp
                    <tr data-search="{{ mb_strtolower($km->TenChuongTrinh) }}" data-status="{{ $km->TinhTrang }}">
                        <td style="text-align:center;color:#94a3b8;font-size:13px;">{{ $i+1 }}</td>
                        <td><strong>{{ $km->TenChuongTrinh }}</strong></td>
                        <td>
                            @if($isYcObj)
                                @foreach($yeuCau as $maSP => $qty)
                                @php $sp = $sanphams->firstWhere('MaSP', $maSP); @endphp
                                <span class="yc-tag">{{ $sp ? $sp->TenSP : $maSP }} × {{ $qty }}</span>
                                @endforeach
                            @else
                                @foreach($yeuCau as $maSP)
                                @php $sp = $sanphams->firstWhere('MaSP', $maSP); @endphp
                                <span class="yc-tag">{{ $sp ? $sp->TenSP : $maSP }} × {{ $km->YeuCau_SoLuong }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @php $qtEntries = is_array($quaTang) ? $quaTang : []; $qtIdx = 0; @endphp
                            @foreach($qtEntries as $maSP => $qty)
                            @php $sp = $sanphams->firstWhere('MaSP', $maSP); $qtIdx++; @endphp
                            @if($qtIdx > 1)<span class="or-divider">hoặc</span>@endif
                            <span class="qt-tag">{{ $sp ? $sp->TenSP : $maSP }} × {{ $qty }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($km->TinhTrang === 'Đang Hoạt Động')
                                <span class="badge-active">✅ Đang Hoạt Động</span>
                            @else
                                <span class="badge-pause">⏸ Tạm Dừng</span>
                            @endif
                        </td>
                        @can('Admin')
                        <td>
                            <div class="btn-actions">
                                <button class="btn-action btn-edit" title="Sửa"
                                    onclick='openEditKM(@json($km), @json($yeuCau), @json($quaTang))'>✏️</button>
                                <form action="{{ route('system.khuyenMai.destroy', $km->id) }}" method="POST"
                                    onsubmit="return confirm('Xóa chương trình khuyến mãi này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-action btn-delete" type="submit" title="Xóa">🗑️</button>
                                </form>
                            </div>
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr><td colspan="6">
                        <div class="sys-empty">
                            <div class="empty-icon">🎉</div>
                            <h3>Chưa có chương trình khuyến mãi nào</h3>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@can('Admin')
{{-- Modal Thêm --}}
<div class="modal-overlay" id="modalAddKM">
    <div class="modal-box">
        <div class="modal-header">
            <h3>🎉 Thêm Khuyến Mãi Mới</h3>
            <button class="modal-close" onclick="closeModal('modalAddKM')">✕</button>
        </div>
        <form action="{{ route('system.khuyenMai.store') }}" method="POST" id="formAddKM">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Chương Trình <span class="req">*</span></label>
                    <input type="text" name="TenChuongTrinh" class="form-input" required placeholder="VD: 4 Tặng 1">
                </div>

                <div class="form-group">
                    <div class="section-label">📦 Sản Phẩm Điều Kiện</div>
                    <div class="sp-rows" id="addYcRows"></div>
                    <button type="button" class="btn-add-row" onclick="addYcRow('addYcRows')">+ Thêm sản phẩm</button>
                </div>

                <div class="form-group">
                    <div class="section-label">🎁 Quà Tặng <span style="font-size:11px;font-weight:400;color:#94a3b8;">(khách chọn 1 trong các quà bên dưới)</span></div>
                    <div class="sp-rows" id="addQtRows"></div>
                    <button type="button" class="btn-add-row" onclick="addQtRow('addQtRows')">+ Thêm hoặc quà tặng</button>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tình Trạng <span class="req">*</span></label>
                        <select name="TinhTrang" class="form-input">
                            <option value="Đang Hoạt Động">Đang Hoạt Động</option>
                            <option value="Tạm Dừng">Tạm Dừng</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-submit btn-secondary" onclick="closeModal('modalAddKM')">Hủy</button>
                <button type="submit" class="btn-submit btn-primary">Thêm Khuyến Mãi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Sửa --}}
<div class="modal-overlay" id="modalEditKM">
    <div class="modal-box">
        <div class="modal-header">
            <h3>✏️ Sửa Khuyến Mãi</h3>
            <button class="modal-close" onclick="closeModal('modalEditKM')">✕</button>
        </div>
        <form id="formEditKM" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Chương Trình <span class="req">*</span></label>
                    <input type="text" name="TenChuongTrinh" id="edit_TenCT" class="form-input" required>
                </div>

                <div class="form-group">
                    <div class="section-label">📦 Sản Phẩm Điều Kiện</div>
                    <div class="sp-rows" id="editYcRows"></div>
                    <button type="button" class="btn-add-row" onclick="addYcRow('editYcRows')">+ Thêm sản phẩm</button>
                </div>

                <div class="form-group">
                    <div class="section-label">🎁 Quà Tặng <span style="font-size:11px;font-weight:400;color:#94a3b8;">(khách chọn 1 trong các quà bên dưới)</span></div>
                    <div class="sp-rows" id="editQtRows"></div>
                    <button type="button" class="btn-add-row" onclick="addQtRow('editQtRows')">+ Thêm hoặc quà tặng</button>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tình Trạng <span class="req">*</span></label>
                        <select name="TinhTrang" id="edit_TinhTrang" class="form-input">
                            <option value="Đang Hoạt Động">Đang Hoạt Động</option>
                            <option value="Tạm Dừng">Tạm Dừng</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-submit btn-secondary" onclick="closeModal('modalEditKM')">Hủy</button>
                <button type="submit" class="btn-submit btn-primary">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const sanphams = @json($sanphams);
let allRows = [];

document.addEventListener('DOMContentLoaded', () => {
    allRows = Array.from(document.querySelectorAll('#kmTable tbody tr[data-search]'));
    addYcRow('addYcRows');
    addQtRow('addQtRows');
});

function filterTable() {
    const s = document.getElementById('searchInput').value.toLowerCase().trim();
    const st = document.getElementById('filterStatus').value;
    allRows.forEach(r => {
        r.style.display = (!s || r.dataset.search.includes(s)) && (!st || r.dataset.status === st) ? '' : 'none';
    });
}

function buildSpOptions(sel = '') {
    return `<option value="">-- Chọn sản phẩm --</option>` +
        sanphams.map(sp => `<option value="${sp.MaSP}"${sp.MaSP===sel?' selected':''}>${sp.TenSP}</option>`).join('');
}

// YeuCau row: select + qty (like combo)
function addYcRow(containerId, maSP = '', qty = 1) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'sp-row';
    div.innerHTML = `<div class="sp-select-wrap"><select name="yc_ma[]" class="sp-select">${buildSpOptions(maSP)}</select></div>
        <input type="number" name="yc_qty[]" value="${qty}" min="1" placeholder="SL">
        <button type="button" class="btn-rm-row" onclick="removeRow(this)">×</button>`;
    container.appendChild(div);
    initSelect2InRow(div, containerId);
}

// QuaTang row: select + qty + "hoặc" separator
function addQtRow(containerId, maSP = '', qty = 1) {
    const container = document.getElementById(containerId);
    // Add "hoặc" separator if not first row
    if (container.children.length > 0) {
        const sep = document.createElement('div');
        sep.className = 'qt-or-separator';
        sep.textContent = '— hoặc —';
        container.appendChild(sep);
    }
    const div = document.createElement('div');
    div.className = 'sp-row';
    div.innerHTML = `<div class="sp-select-wrap"><select name="qt_ma[]" class="sp-select">${buildSpOptions(maSP)}</select></div>
        <input type="number" name="qt_qty[]" value="${qty}" min="1" placeholder="SL">
        <button type="button" class="btn-rm-row" onclick="removeQtRow(this)">×</button>`;
    container.appendChild(div);
    initSelect2InRow(div, containerId);
}

function initSelect2InRow(div, containerId) {
    const modal = document.getElementById(containerId).closest('.modal-overlay');
    if (modal && $.fn.select2) {
        $(div).find('.sp-select').select2({
            dropdownParent: $(modal),
            placeholder: '-- Chọn sản phẩm --',
            width: '100%'
        });
    }
}

function removeRow(btn) {
    const row = btn.closest('.sp-row');
    $(row).find('.sp-select').each(function() { if ($(this).data('select2')) $(this).select2('destroy'); });
    row.remove();
}

function removeQtRow(btn) {
    const row = btn.closest('.sp-row');
    const container = row.parentElement;
    // Remove the preceding "hoặc" separator if exists
    const prev = row.previousElementSibling;
    $(row).find('.sp-select').each(function() { if ($(this).data('select2')) $(this).select2('destroy'); });
    row.remove();
    if (prev && prev.classList.contains('qt-or-separator')) prev.remove();
    // If the first remaining element is a separator, remove it
    if (container.firstElementChild && container.firstElementChild.classList.contains('qt-or-separator')) {
        container.firstElementChild.remove();
    }
}

function destroyAllSelect2(containerId) {
    const container = document.getElementById(containerId);
    $(container).find('.sp-select').each(function() { if ($(this).data('select2')) $(this).select2('destroy'); });
    container.innerHTML = '';
}

function openAddKM() {
    destroyAllSelect2('addYcRows');
    destroyAllSelect2('addQtRows');
    document.getElementById('formAddKM').reset();
    addYcRow('addYcRows');
    addQtRow('addQtRows');
    document.getElementById('modalAddKM').classList.add('show');
}

function openEditKM(km, yeuCau, quaTang) {
    document.getElementById('formEditKM').action = '/system/khuyen-mai/' + km.id;
    document.getElementById('edit_TenCT').value     = km.TenChuongTrinh;
    document.getElementById('edit_TinhTrang').value = km.TinhTrang;

    // YeuCau (conditions) - can be array (old) or object (new)
    destroyAllSelect2('editYcRows');
    if (Array.isArray(yeuCau)) {
        // Old format: array of MaSP with shared YeuCau_SoLuong
        if (yeuCau.length === 0) {
            addYcRow('editYcRows');
        } else {
            yeuCau.forEach(ma => addYcRow('editYcRows', ma, km.YeuCau_SoLuong || 1));
        }
    } else {
        // New format: object {MaSP: qty}
        const entries = Object.entries(yeuCau || {});
        if (entries.length === 0) addYcRow('editYcRows');
        else entries.forEach(([ma, qty]) => addYcRow('editYcRows', ma, qty));
    }

    // QuaTang (gifts)
    destroyAllSelect2('editQtRows');
    const qtEntries = Object.entries(quaTang || {});
    if (qtEntries.length === 0) addQtRow('editQtRows');
    else qtEntries.forEach(([ma, qty]) => addQtRow('editQtRows', ma, qty));

    document.getElementById('modalEditKM').classList.add('show');
}

function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target===m) closeModal(m.id); });
});
</script>
@endpush
