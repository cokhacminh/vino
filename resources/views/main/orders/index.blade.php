@extends('main.layouts.app')
@section('title', 'Quản Lý Đơn Hàng')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;align-items:center}
.dt-wrap{background:#fff;border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow-sm);padding:16px;overflow-x:auto}
/* DataTable styling override for light theme */
table.dataTable{width:100%!important;border-collapse:separate!important;border-spacing:0!important}
table.dataTable thead th{background:#f8fafc!important;color:var(--text-secondary)!important;font-weight:600!important;font-size:11px!important;padding:10px 12px!important;text-align:left!important;border-bottom:2px solid var(--border)!important;white-space:nowrap;text-transform:uppercase;cursor:pointer}
table.dataTable thead th:nth-child(4),
table.dataTable thead th:nth-child(5){
    text-align: center !important;
}
table.dataTable thead th.sorting::after,table.dataTable thead th.sorting_asc::after,table.dataTable thead th.sorting_desc::after{opacity:.5;font-size:10px}
table.dataTable tbody td{padding:8px 12px!important;color:var(--text)!important;font-size:13px!important;border-bottom:1px solid var(--border-light)!important;vertical-align:middle}
table.dataTable tbody tr:hover td{background:#f8fafc!important}
.dataTables_wrapper .dataTables_length select{padding:4px 8px;border:1px solid var(--border);border-radius:6px;font-size:13px;background:#fff;color:var(--text)}
.dataTables_wrapper .dataTables_filter input{padding:6px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;outline:none;transition:border .2s;font-family:'Inter',sans-serif}
.dataTables_wrapper .dataTables_filter input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.dataTables_wrapper .dataTables_info{font-size:12px;color:var(--text-secondary);padding-top:12px}
.dataTables_wrapper .dataTables_paginate{padding-top:12px}
.dataTables_wrapper .dataTables_paginate .paginate_button{padding:4px 10px!important;margin:0 2px;border:1px solid var(--border)!important;border-radius:6px!important;background:#fff!important;color:var(--text-secondary)!important;font-size:12px!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:var(--primary)!important;color:#fff!important;border-color:var(--primary)!important}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current){background:#f1f5f9!important;color:var(--text)!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled{opacity:.4!important}
.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{margin-bottom:12px;font-size:13px;color:var(--text-secondary)}
.modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.show{display:flex}
.modal-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);width:100%;max-width:700px;max-height:90vh;overflow-y:auto;padding:28px;box-shadow:var(--shadow-lg)}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-header h3{font-size:18px;font-weight:700;color:var(--text)}
.modal-header h3 i{color:var(--primary)}
.modal-close{background:none;border:none;color:var(--text-muted);font-size:22px;cursor:pointer}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:5px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.item-row{display:flex;gap:8px;align-items:center;margin-bottom:8px;padding:8px;background:#f8fafc;border-radius:8px;border:1px solid var(--border)}
.item-row select,.item-row input{flex:1;padding:6px 8px;background:#fff;border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:13px}
.item-remove{background:#ef4444;color:#fff;border:none;border-radius:6px;padding:6px 10px;cursor:pointer}
code{color:var(--primary);background:var(--primary-bg);padding:2px 8px;border-radius:6px;font-size:12px}
.sp-list{font-size:12px;line-height:1.6;max-width:250px;text-align:center}
.sp-list span{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sp-list span b{color:var(--primary);font-weight:600}
.addr-cell{max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.flatpickr-calendar{border-radius:12px!important;box-shadow:0 8px 24px rgba(0,0,0,.12)!important;border:1.5px solid var(--border)!important}
</style>
@endpush
@section('content')
<div class="orders-page">
<div class="page-header">
    <h2><i class="fa-solid fa-clipboard-list"></i> QUẢN LÝ ĐƠN HÀNG</h2>
    <div style="display:flex;gap:8px">
        <button class="btn-primary" style="background:linear-gradient(135deg,#059669,#10b981)" onclick="exportExcel()"><i class="fa-solid fa-file-excel"></i> Xuất Excel</button>
        <button class="btn-primary" onclick="document.getElementById('addOrderModal').classList.add('show')"><i class="fa-solid fa-plus"></i> Tạo Đơn</button>
    </div>
</div>

<form method="GET" class="filter-bar" id="filterForm">
    @if(in_array($user->Permission, ['Admin','Kế Toán']))
    <select name="manv" class="form-select" style="width:150px"><option value="">Tất cả NV</option>@foreach($employees as $e)<option value="{{ $e->id }}" {{ $maNV==$e->id?'selected':'' }}>{{ $e->name }}</option>@endforeach</select>
    @endif
    <input type="text" name="date_from" class="form-input fp-date" value="{{ $dateFrom }}" placeholder="Từ ngày" style="width:130px" readonly>
    <input type="text" name="date_to" class="form-input fp-date" value="{{ $dateTo }}" placeholder="Đến ngày" style="width:130px" readonly>
    <button type="submit" class="btn-primary btn-sm"><i class="fa-solid fa-filter"></i> Lọc</button>
    @if($dateFrom || $dateTo || $maNV)
    <a href="{{ route('orders.index') }}" class="btn-primary btn-sm" style="background:linear-gradient(135deg,#64748b,#94a3b8)"><i class="fa-solid fa-xmark"></i> Xóa lọc</a>
    @endif
</form>

<div class="dt-wrap">
<table id="ordersTable" class="display" style="width:100%">
<thead><tr>
    <th>Đơn Hàng</th><th>Khách Hàng</th><th>Địa Chỉ</th><th>Sản Phẩm</th><th>Thanh Toán</th><th style="width:70px"></th>
</tr></thead>
<tbody>
@foreach($orders as $o)
@php
    $addr = implode(', ', array_filter([$o->DiaChi, $o->Xa, $o->Huyen, $o->Tinh]));
    $sdt = $o->SoDienThoai ?? '';
    $sdtMask = strlen($sdt) >= 7 ? substr($sdt,0,4).'***'.substr($sdt,-3) : $sdt;
@endphp
<tr>
    <td>
        <code>{{ $o->MaDH }}</code><br>
        <span style="font-size:11px;color:var(--text-secondary)">Nv: {{ $o->TenNV ?? '-' }}</span>
    </td>
    <td>
        <strong>{{ $o->TenKH ?? '-' }}</strong><br>
        <span style="font-size:12px;color:var(--text-secondary)">{{ $sdtMask }}</span>
    </td>
    <td class="addr-cell" title="{{ $addr }}">{{ $addr ?: '-' }}</td>
    <td>
        <div class="sp-list">
            @if($o->ChiTietSP)
                @foreach(explode("\n", $o->ChiTietSP) as $line)
                    <span>{{ $line }}</span>
                @endforeach
            @else
                <span style="color:var(--text-muted)">-</span>
            @endif
        </div>
    </td>
    <td style="text-align:center">
        <strong style="color:#059669">{{ number_format($o->TongTien ?? 0) }}đ</strong>
        @if(($o->GiamGia ?? 0) > 0)<br><span style="font-size:11px;color:#dc2626">Giảm -{{ number_format($o->GiamGia) }}đ</span>@endif
    </td>
    <td style="white-space:nowrap">
        <button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#d97706,#f59e0b)" onclick="editOrder({{ $o->id }})"><i class="fa-solid fa-pen"></i></button>
        @if($user->Permission === 'Admin')<button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#dc2626,#ef4444)" onclick="deleteOrder({{ $o->id }})"><i class="fa-solid fa-trash"></i></button>@endif
    </td>
</tr>
@endforeach
</tbody></table>
</div>
</div>

<!-- Add Order Modal -->
<div class="modal-overlay" id="addOrderModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-file-invoice"></i> Tạo Đơn Hàng Mới</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<form method="POST" action="{{ route('orders.store') }}">@csrf
<div class="form-row"><div class="form-group"><label>Tên KH *</label><input type="text" name="TenKH" class="form-input" required></div><div class="form-group"><label>SĐT *</label><input type="text" name="SoDienThoai" class="form-input" required></div></div>
<div class="form-group"><label>Địa Chỉ</label><input type="text" name="DiaChi" class="form-input"></div>
<div class="form-row-3"><div class="form-group"><label>Tỉnh</label><input type="text" name="Tinh" class="form-input"></div><div class="form-group"><label>Huyện</label><input type="text" name="Huyen" class="form-input"></div><div class="form-group"><label>Xã</label><input type="text" name="Xa" class="form-input"></div></div>
<div style="font-size:15px;font-weight:700;color:var(--text);margin:12px 0 8px"><i class="fa-solid fa-box" style="color:var(--primary)"></i> Sản Phẩm</div>
<div id="itemList"><div class="item-row"><select name="items[0][MaSP]" required style="flex:2"><option value="">Chọn SP</option>@foreach($products as $p)<option value="{{ $p->MaSP }}">{{ $p->MaSP }} - {{ $p->TenSP }}</option>@endforeach</select><input type="number" name="items[0][SoLuong]" placeholder="SL" value="1" min="1" step="0.1" required style="width:60px"><input type="number" name="items[0][GiaBan]" placeholder="Giá" style="width:100px"><button type="button" class="item-remove" onclick="this.closest('.item-row').remove()">×</button></div></div>
<button type="button" class="btn-primary btn-sm" onclick="addItem()" style="margin-bottom:12px"><i class="fa-solid fa-plus"></i> Thêm SP</button>
<div class="form-row"><div class="form-group"><label>ĐVGH</label><select name="DonviGH" class="form-select">@foreach($dvghs as $d)<option value="{{ $d->MaDV ?? '' }}">{{ $d->TenDV ?? $d->MaDV ?? '' }}</option>@endforeach</select></div><div class="form-group"><label>Giảm Giá</label><input type="number" name="GiamGia" class="form-input" value="0"></div></div>
<div class="form-group"><label>Ghi Chú</label><textarea name="GhiChu" class="form-input" rows="2"></textarea></div>
<button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px"><i class="fa-solid fa-check"></i> Tạo Đơn</button>
</form></div></div>

<!-- Edit Order Modal -->
<div class="modal-overlay" id="editOrderModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-pen-to-square"></i> Sửa Đơn Hàng</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<form id="editOrderForm"><input type="hidden" id="editOrderId">
<div class="form-row"><div class="form-group"><label>Tên KH</label><input type="text" id="eoTenKH" name="TenKH" class="form-input"></div><div class="form-group"><label>SĐT</label><input type="text" id="eoSoDT" name="SoDienThoai" class="form-input"></div></div>
<div class="form-group"><label>Địa Chỉ</label><input type="text" id="eoDC" name="DiaChi" class="form-input"></div>
<div class="form-row-3">
    <div class="form-group"><label>Tổng Tiền (đơn)</label><input type="text" id="eoTT" class="form-input" readonly style="background:#f1f5f9;font-weight:700;color:#059669"></div>
    <div class="form-group"><label>Giảm Giá</label><input type="text" id="eoGG" class="form-input" readonly style="background:#f1f5f9;font-weight:700;color:#dc2626"></div>
    <div class="form-group"><label>Ghi Chú</label><input type="text" id="eoDH" name="DonHang" class="form-input"></div>
</div>
<div id="eoItemsWrap" style="margin:12px 0">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <span style="font-weight:600;font-size:13px;color:var(--text-secondary)"><i class="fa-solid fa-box"></i> Sản phẩm trong đơn</span>
        <button type="button" class="btn-primary btn-sm" onclick="addEditItem()" style="font-size:11px"><i class="fa-solid fa-plus"></i> Thêm SP</button>
    </div>
    <div id="eoItemsList"></div>
    <div style="text-align:right;font-weight:700;font-size:14px;color:#1e40af;margin-top:8px">Tổng giá trị SP: <span id="eoSPTotal">0</span></div>
</div>
<button type="button" class="btn-primary" id="btnSaveOrder" onclick="saveOrderEdit()" style="width:100%;justify-content:center;background:linear-gradient(135deg,#059669,#10b981)" disabled><i class="fa-solid fa-save"></i> Lưu</button>
<div id="eoWarning" style="text-align:center;color:#dc2626;font-size:12px;margin-top:6px;font-weight:600">Tổng giá trị SP phải ≥ Tổng Tiền đơn hàng</div>
</form></div></div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
const csrfToken=document.querySelector('meta[name="csrf-token"]').content;
let idx=1;

$(document).ready(function(){
    $('#ordersTable').DataTable({
        order: [[0,'desc']],
        pageLength: 25,
        language: {
            search: "Tìm kiếm:",
            lengthMenu: "Hiển thị _MENU_ dòng",
            info: "Hiện _START_ - _END_ / _TOTAL_ đơn hàng",
            infoEmpty: "Không có dữ liệu",
            infoFiltered: "(lọc từ _MAX_ đơn)",
            zeroRecords: "Không tìm thấy đơn hàng nào",
            paginate: { first:"Đầu", last:"Cuối", next:"›", previous:"‹" }
        },
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });
});

// Flatpickr
document.querySelectorAll('.fp-date').forEach(el=>{
    flatpickr(el,{dateFormat:'d/m/Y',locale:'vn',allowInput:true});
});

function exportExcel(){
    const params=new URLSearchParams(window.location.search);
    window.location.href='/orders/export?'+params.toString();
}

function addItem(){const h=`<div class="item-row"><select name="items[${idx}][MaSP]" required style="flex:2"><option value="">Chọn SP</option>@foreach($products as $p)<option value="{{ $p->MaSP }}">{{ $p->MaSP }} - {{ $p->TenSP }}</option>@endforeach</select><input type="number" name="items[${idx}][SoLuong]" placeholder="SL" value="1" min="1" step="0.1" required style="width:60px"><input type="number" name="items[${idx}][GiaBan]" placeholder="Giá" style="width:100px"><button type="button" class="item-remove" onclick="this.closest('.item-row').remove()">×</button></div>`;document.getElementById('itemList').insertAdjacentHTML('beforeend',h);idx++}

let editProducts = [];
let editOrderTongTien = 0;

function editOrder(id){
    fetch(`/orders/${id}/edit-data`).then(r=>r.json()).then(o=>{
        document.getElementById('editOrderId').value=id;
        document.getElementById('eoTenKH').value=o.TenKH||'';
        document.getElementById('eoSoDT').value=o.SoDienThoai||'';
        document.getElementById('eoDC').value=o.DiaChi||'';
        editOrderTongTien = Number(o.TongTien||0);
        document.getElementById('eoTT').value=editOrderTongTien.toLocaleString('vi-VN')+'\u0111';
        document.getElementById('eoDH').value=o.DonHang||'';
        editProducts = o.products || [];

        const list = document.getElementById('eoItemsList');
        list.innerHTML = '';
        if(o.items && o.items.length){
            o.items.forEach((it, i) => {
                addEditItemRow(it.MaSP, Number(it.SoLuong||1), Number(it.GiaBan||0));
            });
        }
        calcEditTotal();
        document.getElementById('editOrderModal').classList.add('show');
    })
}

function buildProductOptions(selectedMaSP) {
    let opts = '<option value="">Ch\u1ecdn SP</option>';
    editProducts.forEach(p => {
        const sel = p.MaSP === selectedMaSP ? 'selected' : '';
        opts += `<option value="${p.MaSP}" data-gia="${p.GiaBan_SG||0}" ${sel}>${p.MaSP} - ${p.TenSP}</option>`;
    });
    return opts;
}

function addEditItemRow(maSP, soLuong, giaBan) {
    const list = document.getElementById('eoItemsList');
    const row = document.createElement('div');
    row.className = 'item-row';
    const fmtGia = Number(giaBan||0).toLocaleString('vi-VN');
    row.innerHTML = `<select onchange="onEditSPChange(this)" style="flex:2">${buildProductOptions(maSP||'')}</select>`
        + `<input type="number" value="${soLuong||1}" min="1" step="1" style="width:60px" onchange="calcEditTotal()" oninput="calcEditTotal()">`
        + `<input type="text" value="${fmtGia}" class="fmt-gia" style="width:100px;text-align:right" oninput="fmtGiaInput(this)">`
        + `<button type="button" class="item-remove" onclick="this.closest('.item-row').remove();calcEditTotal()">&times;</button>`;
    list.appendChild(row);
}

function addEditItem() {
    addEditItemRow('', 1, 0);
}

function onEditSPChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    const row = sel.closest('.item-row');
    const giaInput = row.querySelector('.fmt-gia');
    if (opt && opt.value) {
        giaInput.value = Number(opt.dataset.gia || 0).toLocaleString('vi-VN');
    }
    calcEditTotal();
}

function parseGia(s) { return Number(String(s||0).replace(/[^0-9]/g,'')) || 0; }
function fmtGiaInput(el) {
    const raw = parseGia(el.value);
    const pos = el.selectionStart;
    const oldLen = el.value.length;
    el.value = raw ? raw.toLocaleString('vi-VN') : '';
    const newLen = el.value.length;
    el.setSelectionRange(pos + newLen - oldLen, pos + newLen - oldLen);
    calcEditTotal();
}

function calcEditTotal() {
    let total = 0;
    document.querySelectorAll('#eoItemsList .item-row').forEach(row => {
        const slInput = row.querySelector('input[type=number]');
        const giaInput = row.querySelector('.fmt-gia');
        const sl = Number(slInput.value || 0);
        const gia = parseGia(giaInput.value);
        total += sl * gia;
    });
    document.getElementById('eoSPTotal').textContent = total.toLocaleString('vi-VN') + '\u0111';
    const giamGia = total > editOrderTongTien ? (total - editOrderTongTien) : 0;
    document.getElementById('eoGG').value = giamGia > 0 ? giamGia.toLocaleString('vi-VN')+'\u0111' : '0';
    // Enable/disable save button
    const canSave = total >= editOrderTongTien && editOrderTongTien > 0;
    document.getElementById('btnSaveOrder').disabled = !canSave;
    document.getElementById('eoWarning').style.display = canSave ? 'none' : 'block';
}

function saveOrderEdit(){
    const id = document.getElementById('editOrderId').value;
    const items = [];
    document.querySelectorAll('#eoItemsList .item-row').forEach(row => {
        const sel = row.querySelector('select');
        const slInput = row.querySelector('input[type=number]');
        const giaInput = row.querySelector('.fmt-gia');
        if (sel.value) {
            items.push({
                MaSP: sel.value,
                SoLuong: Number(slInput.value || 1),
                GiaBan: parseGia(giaInput.value)
            });
        }
    });
    const d = {
        TenKH: document.getElementById('eoTenKH').value,
        SoDienThoai: document.getElementById('eoSoDT').value,
        DiaChi: document.getElementById('eoDC').value,
        DonHang: document.getElementById('eoDH').value,
        items: items
    };
    fetch(`/orders/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify(d)
    }).then(r=>r.json()).then(r=>{
        if(r.success){alert(r.message);location.reload()}
        else alert(r.message)
    })
}

function deleteOrder(id){if(!confirm('X\u00f3a \u0111\u01a1n h\u00e0ng?'))return;fetch(`/orders/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrfToken}}).then(r=>r.json()).then(r=>{if(r.success){alert(r.message);location.reload()}else alert(r.message)})}

document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('show')}));
</script>
@endpush
