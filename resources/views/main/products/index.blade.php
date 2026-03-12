@extends('main.layouts.app')
@section('title', 'Quản Lý Sản Phẩm')
@push('styles')
<style>
.tab-bar{display:flex;gap:4px;margin-bottom:16px;background:#f1f5f9;border-radius:10px;padding:4px}
.tab-btn{padding:8px 18px;border:none;background:transparent;color:var(--text-secondary);font-weight:600;cursor:pointer;border-radius:8px;transition:all .2s;font-size:14px;text-decoration:none}
.tab-btn.active{background:#fff;color:var(--primary);box-shadow:var(--shadow-sm)}
.data-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);box-shadow:var(--shadow-sm)}
.data-table th{background:#f8fafc;color:var(--text-secondary);font-weight:600;font-size:11px;padding:10px 12px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap}
.data-table td{padding:8px 12px;color:var(--text);font-size:13px;border-bottom:1px solid var(--border-light)}
.data-table tr:hover td{background:#f8fafc}
.modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.show{display:flex}
.modal-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);width:100%;max-width:560px;max-height:90vh;overflow-y:auto;padding:28px;box-shadow:var(--shadow-lg)}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-header h3{font-size:18px;font-weight:700;color:var(--text)}
.modal-header h3 i{color:var(--primary)}
.modal-close{background:none;border:none;color:var(--text-muted);font-size:22px;cursor:pointer}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:5px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.search-bar{display:flex;gap:10px;margin-bottom:16px}
.search-bar input{flex:1}
.pg-wrap{display:flex;justify-content:center;margin-top:16px;gap:4px}
.pg-wrap a{padding:6px 12px;background:#fff;color:var(--text-secondary);border:1px solid var(--border);border-radius:6px;text-decoration:none;font-size:13px}
.pg-wrap a.active{background:var(--primary);color:#fff;border-color:var(--primary)}
code{color:var(--primary);background:var(--primary-bg);padding:2px 8px;border-radius:6px;font-size:12px}
</style>
@endpush
@section('content')
<div class="products-page">
<div class="page-header">
    <h2><i class="fa-solid fa-cubes"></i> QUẢN LÝ SẢN PHẨM</h2>
    <div style="display:flex;gap:8px">
        <button class="btn-primary" onclick="document.getElementById('addProductModal').classList.add('show')"><i class="fa-solid fa-plus"></i> Thêm SP</button>
        <button class="btn-primary" style="background:linear-gradient(135deg,#059669,#10b981)" onclick="document.getElementById('addCatModal').classList.add('show')"><i class="fa-solid fa-folder-plus"></i> Thêm DM</button>
    </div>
</div>
<div class="tab-bar">
    <a href="?tab=products&search={{ $search }}" class="tab-btn {{ $tab=='products'?'active':'' }}"><i class="fa-solid fa-box"></i> Sản Phẩm</a>
    <a href="?tab=categories" class="tab-btn {{ $tab=='categories'?'active':'' }}"><i class="fa-solid fa-folder"></i> Danh Mục</a>
</div>
@if($tab == 'products')
<form method="GET" class="search-bar"><input type="hidden" name="tab" value="products"><input type="text" name="search" class="form-input" placeholder="Tìm mã SP, tên SP..." value="{{ $search }}"><button type="submit" class="btn-primary btn-sm"><i class="fa-solid fa-search"></i></button></form>
<table class="data-table">
<thead><tr><th>Mã SP</th><th>Tên SP</th><th>ĐVT</th><th>TL</th><th>Giá Nhập</th><th>Giá Bán</th><th>Danh Mục</th><th></th></tr></thead>
<tbody>
@forelse($products as $p)
<tr>
    <td><code>{{ $p->MaSP }}</code></td>
    <td><strong>{{ $p->TenSP }}</strong></td>
    <td>{{ $p->DonViTinh??'-' }}</td>
    <td>{{ $p->TrongLuong??0 }}</td>
    <td>{{ number_format($p->GiaNhap??0) }}đ</td>
    <td style="color:#059669;font-weight:600">{{ number_format($p->GiaBan_SG??0) }}đ</td>
    <td>{{ $p->TenDMSP??'-' }}</td>
    <td><button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#d97706,#f59e0b)" onclick='editProduct(@json($p))'><i class="fa-solid fa-pen"></i></button></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:20px">Chưa có sản phẩm</td></tr>
@endforelse
</tbody></table>
@if($products->hasPages())<div class="pg-wrap">@foreach($products->appends(request()->query())->getUrlRange(1,$products->lastPage()) as $pg=>$url)<a href="{{ $url }}" class="{{ $products->currentPage()==$pg?'active':'' }}">{{ $pg }}</a>@endforeach</div>@endif
@else
<table class="data-table">
<thead><tr><th>Mã DM</th><th>Tên Danh Mục</th><th>Nhóm SP</th><th>Ghi Chú</th><th></th></tr></thead>
<tbody>
@forelse($categories as $c)
<tr>
    <td>{{ $c->MaDMSP }}</td><td><strong>{{ $c->TenDMSP }}</strong></td><td>{{ $c->NhomSanPham??'-' }}</td><td>{{ $c->GhiChu??'-' }}</td>
    <td style="white-space:nowrap;display:flex;gap:6px">
        <button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#d97706,#f59e0b)" onclick='editCat(@json($c))'><i class="fa-solid fa-pen"></i></button>
        <button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#dc2626,#ef4444)" onclick="deleteCat({{ $c->MaDMSP }})"><i class="fa-solid fa-trash"></i></button>
    </td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px">Chưa có danh mục</td></tr>
@endforelse
</tbody></table>
@endif
</div>
<div class="modal-overlay" id="addProductModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-box"></i> Thêm Sản Phẩm</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<form method="POST" action="{{ route('products.store') }}">@csrf
<div class="form-row"><div class="form-group"><label>Mã SP *</label><input type="text" name="MaSP" class="form-input" required></div><div class="form-group"><label>Tên SP *</label><input type="text" name="TenSP" class="form-input" required></div></div>
<div class="form-row-3"><div class="form-group"><label>ĐVT</label><input type="text" name="DonViTinh" class="form-input" value="Kg"></div><div class="form-group"><label>Trọng Lượng</label><input type="number" name="TrongLuong" class="form-input" value="0" step="0.1"></div><div class="form-group"><label>Giá Nhập</label><input type="text" name="GiaNhap" class="form-input fmt-num" value="0"></div></div>
<div class="form-group"><label>Giá Bán</label><input type="text" name="GiaBan" class="form-input fmt-num" value="0"></div>
<div class="form-row"><div class="form-group"><label>Danh Mục</label><select name="DanhMucSP" class="form-select"><option value="">Chọn</option>@foreach($categories as $c)<option value="{{ $c->MaDMSP }}">{{ $c->TenDMSP }}</option>@endforeach</select></div><div class="form-group"><label>Nhóm SP</label><select name="NhomSP" class="form-select"><option value="">Chọn</option>@foreach($groups as $g)<option value="{{ $g->MaNhom }}">{{ $g->TenNhom }}</option>@endforeach</select></div></div>
<button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px"><i class="fa-solid fa-check"></i> Thêm</button>
</form></div></div>
<div class="modal-overlay" id="editProductModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-pen"></i> Sửa Sản Phẩm</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<form id="editProductForm"><input type="hidden" id="epId">
<div class="form-row"><div class="form-group"><label>Mã SP</label><input type="text" id="epMaSP" class="form-input" readonly></div><div class="form-group"><label>Tên SP *</label><input type="text" id="epTenSP" name="TenSP" class="form-input" required></div></div>
<div class="form-row-3"><div class="form-group"><label>ĐVT</label><input type="text" id="epDVT" name="DonViTinh" class="form-input"></div><div class="form-group"><label>TL</label><input type="number" id="epTL" name="TrongLuong" class="form-input" step="0.1"></div><div class="form-group"><label>Giá Nhập</label><input type="text" id="epGN" name="GiaNhap" class="form-input fmt-num"></div></div>
<div class="form-group"><label>Giá Bán</label><input type="text" id="epGB" name="GiaBan" class="form-input fmt-num"></div>
<button type="button" class="btn-primary" onclick="saveProductEdit()" style="width:100%;justify-content:center;background:linear-gradient(135deg,#059669,#10b981)"><i class="fa-solid fa-save"></i> Lưu</button>
</form></div></div>
<div class="modal-overlay" id="addCatModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-folder-plus"></i> Thêm Danh Mục</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">&times;</button></div>
<form method="POST" action="{{ route('products.storeCategory') }}">@csrf
<div class="form-group"><label>Tên Danh Mục *</label><input type="text" name="TenDMSP" class="form-input" required></div>
<div class="form-group"><label>Ghi Chú</label><input type="text" name="GhiChu" class="form-input"></div>
<button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px"><i class="fa-solid fa-check"></i> Thêm</button>
</form></div></div>
@endsection
@push('scripts')
<script>
const csrfToken=document.querySelector('meta[name="csrf-token"]').content;

// Number formatting
function fmtNum(v){return Number(String(v).replace(/[^0-9]/g,'')).toLocaleString('vi-VN')}
function rawNum(v){return String(v).replace(/[^0-9]/g,'')}
document.addEventListener('input',function(e){if(e.target.classList.contains('fmt-num')){const pos=e.target.selectionStart;const old=e.target.value.length;e.target.value=fmtNum(e.target.value);const diff=e.target.value.length-old;e.target.setSelectionRange(pos+diff,pos+diff)}});
// Strip format before form submit
document.querySelectorAll('form').forEach(f=>f.addEventListener('submit',function(){f.querySelectorAll('.fmt-num').forEach(i=>i.value=rawNum(i.value))}));

function editProduct(p){document.getElementById('epId').value=p.ID;document.getElementById('epMaSP').value=p.MaSP;document.getElementById('epTenSP').value=p.TenSP;document.getElementById('epDVT').value=p.DonViTinh||'';document.getElementById('epTL').value=p.TrongLuong||0;document.getElementById('epGN').value=fmtNum(p.GiaNhap||0);document.getElementById('epGB').value=fmtNum(p.GiaBan_SG||0);document.getElementById('editProductModal').classList.add('show')}
function saveProductEdit(){const id=document.getElementById('epId').value;const fd=new FormData(document.getElementById('editProductForm'));const d={};fd.forEach((v,k)=>d[k]=k==='GiaNhap'||k==='GiaBan'?rawNum(v):v);fetch(`/products/${id}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify(d)}).then(r=>r.json()).then(r=>{if(r.success){alert(r.message);location.reload()}else alert(r.message)})}
function editCat(c){const n=prompt('Tên danh mục:',c.TenDMSP);if(!n)return;fetch(`/products/category/${c.MaDMSP}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({TenDMSP:n})}).then(r=>r.json()).then(r=>{if(r.success){alert(r.message);location.reload()}else alert(r.message)})}
function deleteCat(id){if(!confirm('Xóa danh mục?'))return;fetch(`/products/category/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrfToken}}).then(r=>r.json()).then(r=>{if(r.success){alert(r.message);location.reload()}else alert(r.message)})}
document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('show')}));
</script>
@endpush
