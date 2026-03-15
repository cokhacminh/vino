@extends('main.layouts.app')
@section('title', 'Quản Lý Kho')
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
.inv-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;align-items:start}
.inv-section{background:#fff;border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden}
.inv-header{background:linear-gradient(135deg,#1e293b,#334155);color:#fff;padding:10px 16px;font-weight:700;font-size:13px;display:flex;justify-content:space-between;align-items:center}
.inv-header i{margin-right:6px}
.inv-body{padding:12px;overflow-x:auto}
.inv-date{display:flex;gap:6px;align-items:center;padding:8px 12px;background:#f8fafc;border-bottom:1px solid var(--border);flex-wrap:wrap}
.inv-date input{padding:5px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:200px;background:#fff;cursor:pointer}
.inv-date .btn-xem{padding:5px 14px;background:linear-gradient(135deg,var(--primary),#818cf8);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer}
.inv-date .btn-nhap{padding:5px 14px;background:linear-gradient(135deg,#059669,#10b981);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer}
.inv-left{grid-column:1;display:flex;flex-direction:column;gap:16px}
.inv-mid{grid-column:2;display:flex;flex-direction:column;gap:16px}
.inv-right{grid-column:3}
.sum-table{width:100%;border-collapse:collapse;font-size:12px}
.sum-table th{background:#1e3a5f;color:#fff;padding:6px 10px;text-align:left;font-size:11px;white-space:nowrap}
.sum-table td{padding:5px 10px;border-bottom:1px solid var(--border-light)}
.sum-table tr:hover td{background:#f8fafc}
.sum-date{text-align:center;font-size:11px;font-weight:700;color:var(--primary);padding:6px;background:#eef2ff;border-bottom:1px solid var(--border)}
.ton-table{width:100%;border-collapse:collapse;font-size:12px}
.ton-table th{background:#1e3a5f;color:#fff;padding:6px 10px;text-align:left;font-size:11px;white-space:nowrap}
.ton-table td{padding:5px 10px;border-bottom:1px solid var(--border-light)}
.ton-table tr:hover td{background:#f8fafc}
.ton-table tr.low td{background:#fef2f2;color:#dc2626;font-weight:600}
.total-row{padding:10px 16px;background:#f8fafc;border-top:2px solid var(--border);font-weight:800;font-size:14px;text-align:right;color:var(--text)}
.btn-add-inv{display:inline-flex;align-items:center;gap:4px;padding:6px 14px;background:linear-gradient(135deg,var(--primary),#818cf8);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;margin:8px 12px 12px}
table.dataTable{width:100%!important;border-collapse:separate!important;border-spacing:0!important}
table.dataTable thead th{background:#f8fafc!important;color:var(--text-secondary)!important;font-weight:600!important;font-size:10px!important;padding:8px 8px!important;text-align:left!important;border-bottom:2px solid var(--border)!important;white-space:nowrap;text-transform:uppercase}
table.dataTable tbody td{padding:6px 8px!important;color:var(--text)!important;font-size:12px!important;border-bottom:1px solid var(--border-light)!important;vertical-align:middle}
table.dataTable tbody tr:hover td{background:#f8fafc!important}
.dataTables_wrapper .dataTables_length select{padding:3px 6px;border:1px solid var(--border);border-radius:6px;font-size:12px}
.dataTables_wrapper .dataTables_filter input{padding:4px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;outline:none}
.dataTables_wrapper .dataTables_filter input:focus{border-color:var(--primary)}
.dataTables_wrapper .dataTables_info{font-size:11px;color:var(--text-secondary);padding-top:8px}
.dataTables_wrapper .dataTables_paginate{padding-top:8px}
.dataTables_wrapper .dataTables_paginate .paginate_button{padding:3px 8px!important;margin:0 1px;border:1px solid var(--border)!important;border-radius:5px!important;background:#fff!important;color:var(--text-secondary)!important;font-size:11px!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:var(--primary)!important;color:#fff!important;border-color:var(--primary)!important}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current){background:#f1f5f9!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled{opacity:.4!important}
.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{margin-bottom:8px;font-size:12px;color:var(--text-secondary)}
code{color:var(--primary);background:var(--primary-bg);padding:2px 6px;border-radius:5px;font-size:11px}
.modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.show{display:flex}
.modal-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;padding:28px;box-shadow:var(--shadow-lg)}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-header h3{font-size:18px;font-weight:700;color:var(--text)}
.modal-header h3 i{color:var(--primary)}
.modal-close{background:none;border:none;color:var(--text-muted);font-size:22px;cursor:pointer}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:5px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.flatpickr-calendar{border-radius:12px!important;box-shadow:0 8px 24px rgba(0,0,0,.12)!important;border:1.5px solid var(--border)!important}
.btn-del{background:#ef4444;color:#fff;border:none;border-radius:5px;padding:3px 7px;cursor:pointer;font-size:11px}
.btn-edit{background:#d97706;color:#fff;border:none;border-radius:5px;padding:3px 7px;cursor:pointer;font-size:11px}
.btn-reset{padding:6px 14px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer}
.btn-reset:hover{opacity:.9}
.loading-overlay{position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;z-index:5;font-size:13px;color:var(--text-secondary)}
.inv-section{position:relative}
@media(max-width:1200px){.inv-grid{grid-template-columns:1fr;}.inv-left,.inv-mid,.inv-right{grid-column:1}}
</style>
@endpush
@section('content')
<div class="page-header" style="margin-bottom:16px">
    <h2><i class="fa-solid fa-warehouse"></i> QUẢN LÝ KHO</h2>
</div>

<div class="inv-grid">
{{-- LEFT COLUMN --}}
<div class="inv-left">
    {{-- DANH SÁCH XUẤT KHO --}}
    <div class="inv-section">
        <div class="inv-header"><span><i class="fa-solid fa-arrow-up-from-bracket"></i> DANH SÁCH XUẤT KHO</span></div>
        <div class="inv-date">
            <input type="text" id="dateRangeXuat" readonly placeholder="Chọn khoảng thời gian">
            <button type="button" class="btn-xem" onclick="loadData()">XEM</button>
        </div>
        <div class="inv-body">
            <table id="xuatTable" class="display" style="width:100%">
            <thead><tr><th>Ngày</th><th>Mã Đơn</th><th>Sản Phẩm</th><th>Số Lượng</th><th>Giá Nhập</th><th>Giá Bán</th></tr></thead>
            <tbody></tbody></table>
        </div>
    </div>

    {{-- DANH SÁCH NHẬP KHO --}}
    <div class="inv-section">
        <div class="inv-header"><span><i class="fa-solid fa-arrow-down-to-bracket"></i> DANH SÁCH NHẬP KHO</span></div>
        <div class="inv-date">
            <input type="text" id="dateRangeNhap" readonly placeholder="Chọn khoảng thời gian">
            <button type="button" class="btn-xem" onclick="loadData()">XEM</button>
            <button type="button" class="btn-nhap" onclick="document.getElementById('importModal').classList.add('show')"><i class="fa-solid fa-plus"></i> Nhập Kho</button>
        </div>
        <div class="inv-body">
            <table id="nhapTable" class="display" style="width:100%">
            <thead><tr><th>Ngày</th><th>Sản Phẩm</th><th>Số Lượng</th><th>Giá Nhập</th><th style="width:60px"></th></tr></thead>
            <tbody></tbody></table>
        </div>
    </div>
</div>

{{-- MIDDLE COLUMN --}}
<div class="inv-mid">
    <div class="inv-section">
        <div class="inv-header"><span><i class="fa-solid fa-chart-bar"></i> TỔNG XUẤT KHO</span></div>
        <div class="sum-date" id="tongXuatDate"></div>
        <table class="sum-table"><thead><tr><th>Sản Phẩm</th><th style="text-align:right">Số Lượng</th></tr></thead>
        <tbody id="tongXuatBody"></tbody></table>
    </div>
    <div class="inv-section">
        <div class="inv-header"><span><i class="fa-solid fa-chart-bar"></i> TỔNG NHẬP KHO</span></div>
        <div class="sum-date" id="tongNhapDate"></div>
        <table class="sum-table"><thead><tr><th>Sản Phẩm</th><th style="text-align:right">Số Lượng</th></tr></thead>
        <tbody id="tongNhapBody"></tbody></table>
    </div>
</div>

{{-- RIGHT COLUMN --}}
<div class="inv-right">
    <div class="inv-section">
        <div class="inv-header"><span><i class="fa-solid fa-boxes-stacked"></i> DANH SÁCH TỒN KHO</span></div>
        <table class="ton-table"><thead><tr><th>Sản Phẩm</th><th style="text-align:right">Số Lượng</th><th style="text-align:right">Tiền Hàng</th></tr></thead>
        <tbody id="tonKhoBody"></tbody></table>
        <div class="total-row" id="totalValueRow">TỔNG GIÁ TRỊ TỒN : 0</div>
        @if(auth()->user()->Permission === 'Admin')
        <button class="btn-reset" style="margin:8px 12px 12px" onclick="document.getElementById('resetModal').classList.add('show')"><i class="fa-solid fa-rotate-left"></i> Reset Dữ Liệu</button>
        @endif
    </div>
</div>
</div>

{{-- Modal Nhập Kho --}}
<div class="modal-overlay" id="importModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-arrow-down-to-bracket"></i> Nhập Kho</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<form id="importForm">

<div class="form-group"><label>Sản Phẩm *</label><select name="MaSP" id="spSelect" class="form-select" required onchange="autoFillPrice(this)"><option value="">Chọn SP</option>@foreach($products as $p)<option value="{{ $p->MaSP }}" data-gia="{{ $p->GiaNhap ?? 0 }}">{{ $p->TenSP }}</option>@endforeach</select></div>
<div class="form-row"><div class="form-group"><label>Số Lượng *</label><input type="text" name="SoLuong" class="form-input fmt-num" required placeholder="0"></div><div class="form-group"><label>Giá Nhập</label><input type="text" name="GiaNhap" id="giaNhapInput" class="form-input fmt-num" value="0" placeholder="0"></div></div>
<div class="form-group"><label>Ngày</label><input type="text" name="Ngay" id="importDate" class="form-input" readonly></div>
<button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px"><i class="fa-solid fa-check"></i> Xác Nhận Nhập Kho</button>
</form></div></div>

{{-- Modal Sửa Phiếu Nhập --}}
<div class="modal-overlay" id="editNhapModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-pen-to-square"></i> Sửa Phiếu Nhập</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<form id="editNhapForm">
<input type="hidden" id="enId">
<div class="form-group"><label>Sản Phẩm</label><input type="text" id="enTenSP" class="form-input" readonly style="background:#f1f5f9"></div>
<div class="form-group"><label>Số Lượng Cũ</label><input type="text" id="enSLCu" class="form-input" readonly style="background:#f1f5f9"></div>
<div class="form-group"><label>Số Lượng Mới *</label><input type="text" id="enSLMoi" class="form-input fmt-num" required placeholder="0"></div>
<button type="button" class="btn-primary" onclick="saveEditNhap()" style="width:100%;justify-content:center;margin-top:8px;background:linear-gradient(135deg,#059669,#10b981)"><i class="fa-solid fa-save"></i> Lưu Thay Đổi</button>
</form></div></div>

{{-- Modal Reset Dữ Liệu --}}
@if(auth()->user()->Permission === 'Admin')
<div class="modal-overlay" id="resetModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-rotate-left" style="color:#dc2626"></i> Reset Dữ Liệu</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<div style="padding:20px">
    <div class="form-group">
        <label>Chọn kiểu reset</label>
        <div style="display:flex;gap:8px;margin-top:6px">
            <button type="button" class="btn-xem" id="btnModeThang" onclick="switchResetMode('thang')" style="flex:1">📅 Theo Tháng</button>
            <button type="button" class="btn-xem" id="btnModeNgay" onclick="switchResetMode('ngay')" style="flex:1;opacity:.5">📆 Theo Ngày</button>
        </div>
    </div>
    <div class="form-group" id="resetThangGroup">
        <label>Chọn tháng</label>
        <input type="text" id="resetMonthPicker" class="form-input" readonly placeholder="Chọn tháng">
    </div>
    <div class="form-group" id="resetNgayGroup" style="display:none">
        <label>Chọn ngày</label>
        <input type="text" id="resetDatePicker" class="form-input" readonly placeholder="Chọn ngày">
    </div>
    <div class="form-group">
        <label>Kiểu Reset</label>
        <select id="resetType" class="form-select" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px">
            <option value="xuat">Reset Xuất Kho (Xóa đơn hàng + bù tồn kho)</option>
            <option value="nhap">Reset Nhập Kho (Xóa phiếu nhập + trừ tồn kho)</option>
            <option value="all">Reset Toàn Bộ (Xuất trước, Nhập sau)</option>
        </select>
    </div>
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px;margin:12px 0;font-size:12px;color:#991b1b">
        <i class="fa-solid fa-triangle-exclamation"></i> <b>Cảnh báo:</b> Thao tác này sẽ xóa dữ liệu vĩnh viễn và không thể hoàn tác!
    </div>
    <button type="button" class="btn-primary" onclick="executeReset()" style="width:100%;justify-content:center;background:linear-gradient(135deg,#dc2626,#ef4444)">
        <i class="fa-solid fa-rotate-left"></i> Xác Nhận Reset
    </button>
</div>
</div></div>
@endif

@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
const csrfToken=window.csrfToken||document.querySelector('meta[name="csrf-token"]').content;
const dtLang={search:"Tìm:",lengthMenu:"Hiện _MENU_",info:"_START_-_END_/_TOTAL_",infoEmpty:"Trống",zeroRecords:"Không có",paginate:{next:"›",previous:"‹"}};
let xuatDT, nhapDT;

// Daterange pickers (independent)
const defaultFrom='01/{{ now()->format("m/Y") }}';
const defaultTo='{{ now()->format("d/m/Y") }}';
function parseDMY(s){const p=s.split('/');return p.length===3?new Date(p[2],p[1]-1,p[0]):new Date();}
const fmtDt=dt=>`${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()}`;

const fpXuat=flatpickr('#dateRangeXuat',{
    mode:'range',dateFormat:'d/m/Y',locale:'vn',
    defaultDate:[parseDMY(defaultFrom),parseDMY(defaultTo)],
    allowInput:false
});
const fpNhap=flatpickr('#dateRangeNhap',{
    mode:'range',dateFormat:'d/m/Y',locale:'vn',
    defaultDate:[parseDMY(defaultFrom),parseDMY(defaultTo)],
    allowInput:false
});

flatpickr('#importDate',{dateFormat:'d/m/Y',locale:'vn',defaultDate:new Date()});

function getParams(){
    const xd=fpXuat.selectedDates, nd=fpNhap.selectedDates;
    return {
        xuat_from: xd.length>=1?fmtDt(xd[0]):defaultFrom,
        xuat_to: xd.length>=2?fmtDt(xd[1]):defaultTo,
        nhap_from: nd.length>=1?fmtDt(nd[0]):defaultFrom,
        nhap_to: nd.length>=2?fmtDt(nd[1]):defaultTo,
    };
}

function fmtN(n){return Number(n||0).toLocaleString('vi-VN');}
function fmtDate(d){
    if(!d)return'';
    const dt=new Date(d);
    return `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${String(dt.getFullYear()).slice(-2)}`;
}

function loadData(){
    const params=getParams();
    $.getJSON('/inventory/data',params,function(data){
        // Xuất Table
        if(xuatDT){xuatDT.destroy();$('#xuatTable tbody').empty();}
        let xHtml='';
        data.xuatList.forEach(x=>{
            xHtml+=`<tr>
                <td style="white-space:nowrap">${fmtDate(x.Ngay)}</td>
                <td><code>${x.MaDH||''}</code></td>
                <td>${x.TenSP||''}</td>
                <td style="text-align:center">${fmtN(Math.round(x.SoLuong))}</td>
                <td style="text-align:right;color:#6366f1">${fmtN(x.GiaNhap)}</td>
                <td style="text-align:right;color:#059669;font-weight:600">${fmtN(x.GiaBan)}</td>
            </tr>`;
        });
        $('#xuatTable tbody').html(xHtml);
        xuatDT=$('#xuatTable').DataTable({order:[[0,'desc']],pageLength:10,language:dtLang,destroy:true});

        // Nhập Table
        if(nhapDT){nhapDT.destroy();$('#nhapTable tbody').empty();}
        let nHtml='';
        data.nhapList.forEach(n=>{
            const tenSP=(n.TenSP||n.MaSP||'').replace(/'/g,"\\'");
            nHtml+=`<tr>
                <td style="white-space:nowrap">${fmtDate(n.Ngay)}</td>
                <td>${n.TenSP||n.MaSP||''}</td>
                <td style="text-align:center">${fmtN(Math.round(n.SoLuong))}</td>
                <td style="text-align:right;color:#6366f1">${fmtN(n.GiaNhap)}</td>
                <td style="white-space:nowrap">
                    <button class="btn-edit" onclick="editNhap(${n.id},'${tenSP}',${Math.round(n.SoLuong)})"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn-del" onclick="deleteNhap(${n.id})"><i class="fa-solid fa-ban"></i></button>
                </td>
            </tr>`;
        });
        $('#nhapTable tbody').html(nHtml);
        nhapDT=$('#nhapTable').DataTable({order:[[0,'desc']],pageLength:10,language:dtLang,destroy:true,columnDefs:[{orderable:false,targets:[4]}]});

        // Tổng Xuất (uses xuất dates)
        $('#tongXuatDate').text(`Từ ${data.xuatFrom} Đến ${data.xuatTo}`);
        let txHtml='';
        data.tongXuat.forEach(t=>{txHtml+=`<tr><td>${t.TenSP||'-'}</td><td style="text-align:right;font-weight:600">${fmtN(t.TongSL)}</td></tr>`;});
        $('#tongXuatBody').html(txHtml||'<tr><td colspan="2" style="text-align:center;color:#999;padding:12px">Không có dữ liệu</td></tr>');

        // Tổng Nhập (uses nhập dates)
        $('#tongNhapDate').text(`Từ ${data.nhapFrom} Đến ${data.nhapTo}`);
        let tnHtml='';
        data.tongNhap.forEach(t=>{tnHtml+=`<tr><td>${t.TenSP||'-'}</td><td style="text-align:right;font-weight:600">${fmtN(t.TongSL)}</td></tr>`;});
        $('#tongNhapBody').html(tnHtml||'<tr><td colspan="2" style="text-align:center;color:#999;padding:12px">Không có dữ liệu</td></tr>');

        // Tồn Kho
        let tkHtml='';
        data.tonKho.forEach(t=>{
            const tien=Math.round((t.SoLuong||0)*(t.GiaNhap||0));
            const cls=t.SoLuong<=0?'class="low"':'';
            tkHtml+=`<tr ${cls}><td>${t.TenSP||t.MaSP}</td><td style="text-align:right;font-weight:600">${fmtN(t.SoLuong)}</td><td style="text-align:right;color:#059669;font-weight:600">${fmtN(tien)}</td></tr>`;
        });
        $('#tonKhoBody').html(tkHtml||'<tr><td colspan="3" style="text-align:center;color:#999;padding:12px">Trống</td></tr>');
        $('#totalValueRow').text('TỔNG GIÁ TRỊ TỒN : '+fmtN(Math.round(data.totalValue)));
    });
}

// Auto-fill giá nhập
function autoFillPrice(sel){
    const opt=sel.options[sel.selectedIndex];
    const gia=opt?opt.getAttribute('data-gia')||'0':'0';
    document.getElementById('giaNhapInput').value=parseInt(gia).toLocaleString('vi-VN');
}

// Submit nhập kho via Ajax
$('#importForm').on('submit',function(e){
    e.preventDefault();
    const fd=new FormData(this);
    // Strip number formatting
    fd.set('SoLuong',(fd.get('SoLuong')||'').replace(/[^\d]/g,''));
    fd.set('GiaNhap',(fd.get('GiaNhap')||'').replace(/[^\d]/g,''));
    $.ajax({
        url:'/inventory/import-export',method:'POST',
        data:Object.fromEntries(fd),
        headers:{'X-CSRF-TOKEN':csrfToken},
        success:function(){
            document.getElementById('importModal').classList.remove('show');
            $('#importForm')[0].reset();
            loadData();
        },
        error:function(xhr){alert(xhr.responseJSON?.message||'Lỗi nhập kho');}
    });
});

function deleteNhap(id){
    if(!confirm('Xóa phiếu nhập kho này?'))return;
    $.ajax({url:'/inventory/nhap/'+id,method:'DELETE',headers:{'X-CSRF-TOKEN':csrfToken},
        success:function(r){if(r.success)loadData();else alert(r.message)},
        error:function(){alert('Lỗi xóa')}
    });
}

function editNhap(id,tenSP,sl){
    document.getElementById('enId').value=id;
    document.getElementById('enTenSP').value=tenSP;
    document.getElementById('enSLCu').value=sl;
    document.getElementById('enSLMoi').value='';
    document.getElementById('editNhapModal').classList.add('show');
}

function saveEditNhap(){
    const id=document.getElementById('enId').value;
    const sl=(document.getElementById('enSLMoi').value||'').replace(/[^\d]/g,'');
    if(!sl||sl<=0){alert('Vui lòng nhập số lượng mới');return}
    $.ajax({url:'/inventory/nhap/'+id,method:'PUT',contentType:'application/json',
        headers:{'X-CSRF-TOKEN':csrfToken},data:JSON.stringify({SoLuong:sl}),
        success:function(r){
            if(r.success){document.getElementById('editNhapModal').classList.remove('show');loadData();}
            else alert(r.message);
        }
    });
}

// Format number inputs
document.querySelectorAll('.fmt-num').forEach(el=>{
    el.addEventListener('input',function(){
        let v=this.value.replace(/[^\d]/g,'');
        this.value=v?parseInt(v).toLocaleString('vi-VN'):'';
    });
});

// Modal close on overlay click
document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('show')}));

// Initial load
$(document).ready(function(){loadData();});

// ====== RESET DỮ LIỆU ======
let resetMode = 'thang';

let fpResetMonth, fpResetDate;
setTimeout(function() {
    if (document.getElementById('resetMonthPicker')) {
        fpResetMonth = flatpickr('#resetMonthPicker', {
            locale: 'vn',
            plugins: [new monthSelectPlugin({shorthand: true, dateFormat: 'm/Y', altFormat: 'F Y'})],
            defaultDate: new Date(),
        });
    }
    if (document.getElementById('resetDatePicker')) {
        fpResetDate = flatpickr('#resetDatePicker', {
            dateFormat: 'd/m/Y', locale: 'vn', defaultDate: new Date(),
        });
    }
}, 0);

function switchResetMode(mode) {
    resetMode = mode;
    document.getElementById('resetThangGroup').style.display = mode === 'thang' ? '' : 'none';
    document.getElementById('resetNgayGroup').style.display = mode === 'ngay' ? '' : 'none';
    document.getElementById('btnModeThang').style.opacity = mode === 'thang' ? '1' : '.5';
    document.getElementById('btnModeNgay').style.opacity = mode === 'ngay' ? '1' : '.5';
}

function executeReset() {
    const type = document.getElementById('resetType').value;
    let value;
    if (resetMode === 'thang') {
        value = document.getElementById('resetMonthPicker').value;
    } else {
        value = document.getElementById('resetDatePicker').value;
    }
    if (!value) { alert('Vui lòng chọn thời gian'); return; }

    const typeLabel = {xuat:'Xuất Kho',nhap:'Nhập Kho',all:'Toàn Bộ'};
    const modeLabel = resetMode === 'thang' ? 'tháng ' + value : 'ngày ' + value;
    if (!confirm(`Bạn chắc chắn muốn RESET ${typeLabel[type]} cho ${modeLabel}?\n\nThao tác này KHÔNG THỂ hoàn tác!`)) return;

    $.ajax({
        url: '/inventory/reset-data',
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrfToken},
        contentType: 'application/json',
        data: JSON.stringify({ mode: resetMode, type: type, value: value }),
        success: function(r) {
            if (r.success) {
                alert(r.message);
                document.getElementById('resetModal').classList.remove('show');
                loadData();
            } else {
                alert('Lỗi: ' + r.message);
            }
        },
        error: function(xhr) { alert('Lỗi: ' + (xhr.responseJSON?.message || 'Kết nối thất bại')); }
    });
}
</script>
@endpush
