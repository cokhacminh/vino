@extends('main.layouts.app')
@section('title', 'Quản Lý Tài Khoản')
@push('styles')
<style>
.data-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);box-shadow:var(--shadow-sm)}
.data-table th{background:#f8fafc;color:var(--text-secondary);font-weight:600;font-size:12px;padding:12px 14px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap}
.data-table td{padding:10px 14px;color:var(--text);font-size:13px;border-bottom:1px solid var(--border-light)}
.data-table tr:hover td{background:#f8fafc}
.data-table tr:last-child td{border-bottom:none}
.badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600}
.badge-admin{background:#fef2f2;color:#dc2626}
.badge-ketoan{background:#eff6ff;color:#2563eb}
.badge-nhanvien{background:#f0fdf4;color:#16a34a}
.badge-active{background:#f0fdf4;color:#16a34a}
.badge-inactive{background:#fef2f2;color:#dc2626}
.modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.show{display:flex}
.modal-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;padding:28px;box-shadow:var(--shadow-lg);animation:modalIn .3s ease}
@keyframes modalIn{from{opacity:0;transform:scale(.95) translateY(10px)}to{opacity:1;transform:scale(1) translateY(0)}}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-header h3{font-size:18px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px}
.modal-header h3 i{color:var(--primary)}
.modal-close{background:none;border:none;color:var(--text-muted);font-size:22px;cursor:pointer;padding:4px}
.modal-close:hover{color:var(--text)}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:6px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
code{color:var(--primary);background:var(--primary-bg);padding:2px 8px;border-radius:6px;font-size:12px}
</style>
@endpush
@section('content')
<div class="accounts-page">
    <div class="page-header">
        <h2><i class="fa-solid fa-users"></i> QUẢN LÝ TÀI KHOẢN</h2>
        <button class="btn-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Thêm Tài Khoản</button>
    </div>
    <table class="data-table">
        <thead><tr><th>ID</th><th>Họ Tên</th><th>Username</th><th>Email</th><th>Quyền</th><th>Trạng Thái</th><th>Hành Động</th></tr></thead>
        <tbody>
            @foreach($accounts as $acc)
            <tr>
                <td>{{ $acc->id }}</td>
                <td><strong>{{ $acc->name }}</strong></td>
                <td><code>{{ $acc->username }}</code></td>
                <td>{{ $acc->email ?? '-' }}</td>
                <td>@php $pc=$acc->Permission==='Admin'?'admin':($acc->Permission==='Kế Toán'?'ketoan':'nhanvien'); @endphp<span class="badge badge-{{ $pc }}">{{ $acc->Permission??'Nhân Viên' }}</span></td>
                <td><span class="badge {{ ($acc->TinhTrang??'')==='Đang Làm Việc'?'badge-active':'badge-inactive' }}">{{ $acc->TinhTrang??'Đang Làm Việc' }}</span></td>
                <td style="white-space:nowrap;display:flex;gap:6px">
                    <button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#d97706,#f59e0b);box-shadow:0 2px 8px rgba(217,119,6,.2)" onclick='openEditModal(@json($acc))'><i class="fa-solid fa-pen"></i></button>
                    @if($user->Permission === 'Admin' && $acc->id != auth()->id())<button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#2563eb,#3b82f6);box-shadow:0 2px 8px rgba(37,99,235,.2)" onclick="impersonateAccount({{ $acc->id }}, '{{ addslashes($acc->name) }}')"><i class="fa-solid fa-right-to-bracket"></i></button>@endif
                    @if($user->Permission === 'Admin' && $acc->id != auth()->id())<button class="btn-primary btn-sm" style="background:linear-gradient(135deg,#dc2626,#ef4444);box-shadow:0 2px 8px rgba(220,38,38,.2)" onclick="deleteAccount({{ $acc->id }})"><i class="fa-solid fa-trash"></i></button>@endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-overlay" id="addModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-user-plus"></i> Thêm Tài Khoản</h3><button class="modal-close" onclick="closeModal('addModal')">&times;</button></div>
<form method="POST" action="{{ route('accounts.store') }}">@csrf
<div class="form-row"><div class="form-group"><label>Họ Tên *</label><input type="text" name="name" class="form-input" required></div><div class="form-group"><label>Username *</label><input type="text" name="username" class="form-input" required></div></div>
<div class="form-row"><div class="form-group"><label>Mật Khẩu *</label><input type="password" name="password" class="form-input" required></div><div class="form-group"><label>Email</label><input type="email" name="email" class="form-input"></div></div>
<div class="form-group"><label>Quyền Hạn *</label><select name="Permission" class="form-select" required>@if($user->Permission==='Admin')<option value="Admin">Admin</option>@endif<option value="Kế Toán">Kế Toán</option><option value="Nhân Viên" selected>Nhân Viên</option></select></div>
<button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px"><i class="fa-solid fa-check"></i> Tạo Tài Khoản</button>
</form></div></div>
<div class="modal-overlay" id="editModal"><div class="modal-box">
<div class="modal-header"><h3><i class="fa-solid fa-user-pen"></i> Sửa Tài Khoản</h3><button class="modal-close" onclick="closeModal('editModal')">&times;</button></div>
<form id="editForm"><input type="hidden" id="editId">
<div class="form-row"><div class="form-group"><label>Họ Tên *</label><input type="text" id="editName" name="name" class="form-input" required></div><div class="form-group"><label>Email</label><input type="email" id="editEmail" name="email" class="form-input"></div></div>
<div class="form-group"><label>Mật Khẩu Mới (bỏ trống nếu không đổi)</label><input type="password" id="editPassword" name="password" class="form-input"></div>
<div class="form-row">
<div class="form-group"><label>Quyền Hạn *</label><select id="editPermission" name="Permission" class="form-select" required>@if($user->Permission==='Admin')<option value="Admin">Admin</option>@endif<option value="Kế Toán">Kế Toán</option><option value="Nhân Viên">Nhân Viên</option></select></div>
<div class="form-group"><label>Trạng Thái</label><select id="editTinhTrang" name="TinhTrang" class="form-select"><option value="Đang Làm Việc">Đang Làm Việc</option><option value="Đã Nghỉ Việc">Đã Nghỉ Việc</option></select></div>
</div>
<button type="button" class="btn-primary" onclick="saveEdit()" style="width:100%;justify-content:center;margin-top:8px;background:linear-gradient(135deg,#059669,#10b981);box-shadow:0 2px 8px rgba(5,150,105,.25)"><i class="fa-solid fa-save"></i> Lưu Thay Đổi</button>
</form></div></div>
@endsection
@push('scripts')
<script>
const csrfToken=document.querySelector('meta[name="csrf-token"]').content;
function openAddModal(){document.getElementById('addModal').classList.add('show')}
function closeModal(id){document.getElementById(id).classList.remove('show')}
function openEditModal(a){document.getElementById('editId').value=a.id;document.getElementById('editName').value=a.name||'';document.getElementById('editEmail').value=a.email||'';document.getElementById('editPermission').value=a.Permission||'Nhân Viên';document.getElementById('editTinhTrang').value=a.TinhTrang||'Đang Làm Việc';document.getElementById('editModal').classList.add('show')}
function saveEdit(){const id=document.getElementById('editId').value;const fd=new FormData(document.getElementById('editForm'));const d={};fd.forEach((v,k)=>{if(v)d[k]=v});fetch(`/accounts/${id}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify(d)}).then(r=>r.json()).then(r=>{if(r.success){alert(r.message);location.reload()}else alert(r.message||'Lỗi')})}
function deleteAccount(id){if(!confirm('Vô hiệu hóa tài khoản này?'))return;fetch(`/accounts/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrfToken}}).then(r=>r.json()).then(r=>{if(r.success){alert(r.message);location.reload()}else alert(r.message)})}
document.querySelectorAll('.modal-overlay').forEach(m=>{m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('show')})});
function impersonateAccount(id, name) {
    if (!confirm('Đăng nhập vào tài khoản: ' + name + '?')) return;
    fetch(`/accounts/${id}/impersonate`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrfToken}
    }).then(r => r.json()).then(r => {
        if (r.success) {
            alert(r.message);
            window.location.href = '/dashboard';
        } else {
            alert(r.message || 'Lỗi');
        }
    }).catch(() => alert('Lỗi kết nối'));
}
</script>
@endpush
