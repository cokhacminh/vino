@extends('main.layouts.app')
@section('title', 'Quản Lý Combo')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .sys-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; flex-wrap:wrap; gap:14px; }
    .sys-header h2 { margin:0; font-size:23px; color:#1e293b; }
    .sys-header-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .stat-badge { padding:7px 15px; border-radius:10px; font-size:13px; font-weight:600; background:#f1f5f9; color:#475569; }
    .btn-add-main { padding:9px 18px; border-radius:10px; border:none; background:#6d28d9; color:white; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.2s; }
    .btn-add-main:hover { background:#5b21b6; }
    .sys-filter { display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; align-items:center; }
    .filter-input { padding:8px 14px; border:2px solid #e2e8f0; border-radius:10px; font-size:13px; min-width:200px; transition:border-color 0.2s; background:white; }
    .filter-input:focus { outline:none; border-color:#6d28d9; }
    .sys-card { background:white; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04); overflow:hidden; }
    .sys-table { width:100%; border-collapse:collapse; }
    .sys-table thead th { padding:12px 16px; text-align:left; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#fff; background:black; white-space:nowrap; }
    .sys-table tbody td { padding:12px 16px; border-bottom:1px solid #f1f5f9; font-size:14px; color:#334155; vertical-align:middle; }
    .sys-table tbody tr:nth-child(even) { background:#f8fafc; }
    .sys-table tbody tr:hover { background:#eef2f7; }
    .sys-table-wrap { overflow-x:auto; }
    .badge-active { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; background:#dcfce7; color:#15803d; }
    .badge-pause  { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; background:#fee2e2; color:#dc2626; }
    .yc-tag { display:inline-block; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:600; background:#ede9fe; color:#5b21b6; margin:2px; }
    .discount-chip { font-weight:700; color:#dc2626; font-size:14px; }
    .btn-actions { display:flex; gap:6px; }
    .btn-action { width:32px; height:32px; border-radius:8px; border:none; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; transition:all 0.18s; }
    .btn-edit { background:#f1f5f9; color:#475569; } .btn-edit:hover { background:#e2e8f0; }
    .btn-delete { background:#fee2e2; color:#dc2626; } .btn-delete:hover { background:#fecaca; }
    .sys-empty { text-align:center; padding:40px 20px; color:#94a3b8; }
    .sys-empty .empty-icon { font-size:36px; margin-bottom:12px; }
    .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center; }
    .modal-overlay.show { display:flex; }
    .modal-box { background:white; border-radius:18px; width:600px; max-width:96vw; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(0,0,0,0.18); }
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
    .form-input:focus { outline:none; border-color:#6d28d9; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .btn-submit { padding:9px 20px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; }
    .btn-primary { background:#6d28d9; color:white; } .btn-primary:hover { background:#5b21b6; }
    .btn-secondary { background:#f1f5f9; color:#475569; } .btn-secondary:hover { background:#e2e8f0; }
    .sp-rows { display:flex; flex-direction:column; gap:8px; margin-bottom:10px; }
    .sp-row { display:flex; gap:8px; align-items:center; }
    .sp-row .sp-select-wrap { flex:1; }
    .sp-row input[type="number"] { width:80px; padding:8px 10px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px; }
    .sp-row input[type="number"]:focus { outline:none; border-color:#6d28d9; }
    .btn-rm-row { width:28px; height:28px; border-radius:6px; border:none; background:#fee2e2; color:#dc2626; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .btn-add-row { padding:6px 13px; border-radius:8px; border:none; background:#ede9fe; color:#5b21b6; font-size:12px; font-weight:600; cursor:pointer; }
    .btn-add-row:hover { background:#ddd6fe; }
    .alert-msg { padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px; font-weight:500; }
    .alert-success { background:#dcfce7; color:#15803d; }
    .alert-error   { background:#fee2e2; color:#dc2626; }

    /* Select2 overrides for modals */
    .sp-row .select2-container { width:100% !important; }
    .sp-row .select2-selection--single { height:38px; border:2px solid #e2e8f0; border-radius:8px; padding:4px 8px; font-size:13px; }
    .sp-row .select2-selection__arrow { top:5px; }
    .select2-dropdown { z-index:99999; }
</style>
@endpush

@section('content')
<div style="padding:10px;background:white;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04);">

    <div class="sys-header">
        <h2>🎁 Quản Lý Combo</h2>
        <div class="sys-header-actions">
            <span class="stat-badge">{{ $combos->count() }} combo</span>
            <span class="stat-badge" style="background:#dcfce7;color:#15803d;">
                {{ $combos->where('TinhTrang','Đang Hoạt Động')->count() }} đang hoạt động
            </span>
            @can('Admin')
            <button class="btn-add-main" onclick="openAddCombo()">+ Thêm Combo</button>
            @endcan
        </div>
    </div>

    <div class="sys-filter">
        <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Tìm theo tên combo..." oninput="filterTable()">
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
            <table class="sys-table" id="comboTable">
                <thead>
                    <tr>
                        <th style="width:50px;">STT</th>
                        <th>Tên Combo</th>
                        <th>Yêu Cầu (Sản Phẩm × SL)</th>
                        <th style="width:140px;">Giảm Giá</th>
                        <th style="width:155px;">Tình Trạng</th>
                        @can('Admin')<th style="width:100px;">Thao Tác</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($combos as $i => $combo)
                    @php $yeuCau = json_decode($combo->YeuCau, true) ?? []; @endphp
                    <tr data-search="{{ mb_strtolower($combo->TenCombo) }}" data-status="{{ $combo->TinhTrang }}">
                        <td style="text-align:center;color:#94a3b8;font-size:13px;">{{ $i+1 }}</td>
                        <td><strong>{{ $combo->TenCombo }}</strong></td>
                        <td>
                            @foreach($yeuCau as $maSP => $qty)
                            @php $sp = $sanphams->firstWhere('MaSP', $maSP); @endphp
                            <span class="yc-tag">{{ $sp ? $sp->TenSP : $maSP }} × {{ $qty }}</span>
                            @endforeach
                        </td>
                        <td class="discount-chip">-{{ number_format($combo->GiamGia, 0, ',', '.') }}đ</td>
                        <td>
                            @if($combo->TinhTrang === 'Đang Hoạt Động')
                                <span class="badge-active">✅ Đang Hoạt Động</span>
                            @else
                                <span class="badge-pause">⏸ Tạm Dừng</span>
                            @endif
                        </td>
                        @can('Admin')
                        <td>
                            <div class="btn-actions">
                                <button class="btn-action btn-edit" title="Sửa"
                                    onclick='openEditCombo(@json($combo), @json($yeuCau))'>✏️</button>
                                <form action="{{ route('system.combos.destroy', $combo->id) }}" method="POST"
                                    onsubmit="return confirm('Xóa combo này?')">
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
                            <div class="empty-icon">🎁</div>
                            <h3>Chưa có combo nào</h3>
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
<div class="modal-overlay" id="modalAddCombo">
    <div class="modal-box">
        <div class="modal-header">
            <h3>🎁 Thêm Combo Mới</h3>
            <button class="modal-close" onclick="closeModal('modalAddCombo')">✕</button>
        </div>
        <form action="{{ route('system.combos.store') }}" method="POST" id="formAddCombo">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Combo <span class="req">*</span></label>
                    <input type="text" name="TenCombo" class="form-input" required placeholder="VD: Combo 4 Multi">
                </div>
                <div class="form-group">
                    <label>Sản Phẩm Yêu Cầu <span class="req">*</span></label>
                    <div class="sp-rows" id="addSpRows"></div>
                    <button type="button" class="btn-add-row" onclick="addSpRow('addSpRows')">+ Thêm sản phẩm</button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Giảm Giá (đ) <span class="req">*</span></label>
                        <input type="text" class="form-input fmt-number" id="add_GiamGia_display" required placeholder="0" oninput="fmtNum(this, 'add_GiamGia_raw')">
                        <input type="hidden" name="GiamGia" id="add_GiamGia_raw">
                    </div>
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
                <button type="button" class="btn-submit btn-secondary" onclick="closeModal('modalAddCombo')">Hủy</button>
                <button type="submit" class="btn-submit btn-primary">Thêm Combo</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Sửa --}}
<div class="modal-overlay" id="modalEditCombo">
    <div class="modal-box">
        <div class="modal-header">
            <h3>✏️ Sửa Combo</h3>
            <button class="modal-close" onclick="closeModal('modalEditCombo')">✕</button>
        </div>
        <form id="formEditCombo" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên Combo <span class="req">*</span></label>
                    <input type="text" name="TenCombo" id="edit_TenCombo" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Sản Phẩm Yêu Cầu <span class="req">*</span></label>
                    <div class="sp-rows" id="editSpRows"></div>
                    <button type="button" class="btn-add-row" onclick="addSpRow('editSpRows')">+ Thêm sản phẩm</button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Giảm Giá (đ) <span class="req">*</span></label>
                        <input type="text" class="form-input fmt-number" id="edit_GiamGia_display" required oninput="fmtNum(this, 'edit_GiamGia_raw')">
                        <input type="hidden" name="GiamGia" id="edit_GiamGia_raw">
                    </div>
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
                <button type="button" class="btn-submit btn-secondary" onclick="closeModal('modalEditCombo')">Hủy</button>
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
    allRows = Array.from(document.querySelectorAll('#comboTable tbody tr[data-search]'));
    addSpRow('addSpRows');
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
        sanphams.map(sp => `<option value="${sp.MaSP}"${sp.MaSP===sel?' selected':''}>${sp.TenSP} (${sp.MaSP})</option>`).join('');
}

function addSpRow(containerId, maSP = '', qty = 1) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'sp-row';
    div.innerHTML = `<div class="sp-select-wrap"><select name="sp_ma[]" class="sp-select">${buildSpOptions(maSP)}</select></div>
        <input type="number" name="sp_qty[]" value="${qty}" min="1" placeholder="SL">
        <button type="button" class="btn-rm-row" onclick="removeSpRow(this)">×</button>`;
    container.appendChild(div);
    // Init Select2 on the new select
    const modalId = container.closest('.modal-overlay')?.id;
    if (modalId && $.fn.select2) {
        $(div).find('.sp-select').select2({
            dropdownParent: $('#' + modalId),
            placeholder: '-- Chọn sản phẩm --',
            width: '100%'
        });
    }
}

function removeSpRow(btn) {
    const row = btn.closest('.sp-row');
    $(row).find('.sp-select').select2('destroy');
    row.remove();
}

// Number format for GiamGia
function fmtNum(el, rawId) {
    let raw = el.value.replace(/[^\d]/g, '');
    raw = raw.replace(/^0+(?=\d)/, '');
    document.getElementById(rawId).value = raw || '0';
    el.value = raw ? parseInt(raw).toLocaleString('vi-VN') : '';
}

function setFmtNum(displayId, rawId, value) {
    const el = document.getElementById(displayId);
    const raw = document.getElementById(rawId);
    const v = parseInt(value) || 0;
    raw.value = v;
    el.value = v ? v.toLocaleString('vi-VN') : '0';
}

function openAddCombo() {
    const rows = document.getElementById('addSpRows');
    // Destroy existing select2 instances
    $(rows).find('.sp-select').each(function() { if ($(this).data('select2')) $(this).select2('destroy'); });
    rows.innerHTML = '';
    document.getElementById('formAddCombo').reset();
    document.getElementById('add_GiamGia_display').value = '';
    document.getElementById('add_GiamGia_raw').value = '';
    addSpRow('addSpRows');
    document.getElementById('modalAddCombo').classList.add('show');
}

function openEditCombo(combo, yeuCau) {
    document.getElementById('formEditCombo').action = '/system/combos/' + combo.id;
    document.getElementById('edit_TenCombo').value  = combo.TenCombo;
    document.getElementById('edit_TinhTrang').value = combo.TinhTrang;
    setFmtNum('edit_GiamGia_display', 'edit_GiamGia_raw', combo.GiamGia);

    const rows = document.getElementById('editSpRows');
    $(rows).find('.sp-select').each(function() { if ($(this).data('select2')) $(this).select2('destroy'); });
    rows.innerHTML = '';
    const entries = Object.entries(yeuCau);
    if (entries.length === 0) addSpRow('editSpRows');
    else entries.forEach(([ma, qty]) => addSpRow('editSpRows', ma, qty));
    document.getElementById('modalEditCombo').classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target===m) closeModal(m.id); });
});
</script>
@endpush
