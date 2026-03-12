@extends('main.layouts.app')

@section('title', 'Cài Đặt Máy Nhánh')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .ph-page {
        padding: 10px; background: white; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
    }
    .ph-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    }
    .ph-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
    .ph-header-btn {
        padding: 9px 18px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white;
        font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s;
    }
    .ph-header-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(139,92,246,0.4); }

    /* Alert */
    .ph-alert {
        padding: 12px 16px; border-radius: 10px; margin-bottom: 16px;
        font-size: 14px; font-weight: 500;
    }
    .ph-alert-success { background: #dcfce7; color: #15803d; }
    .ph-alert-error { background: #fee2e2; color: #dc2626; }

    /* Table */
    .ph-table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
    .ph-table { width: 100%; border-collapse: collapse; }
    .ph-table thead th {
        padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 700;
        text-transform: uppercase; color: white; background: black;
        border-bottom: 2px solid #e2e8f0; white-space: nowrap;
    }
    .ph-table tbody td {
        padding: 14px 16px; border-bottom: 1px solid #f1f5f9;
        font-size: 15px; color: #334155; vertical-align: middle; text-align: center;
    }
    .ph-table tbody tr { transition: background 0.15s; }
    .ph-table tbody tr:nth-child(even) { background: #f1f5f9; }
    .ph-table tbody tr:hover { background: #e8edf4; }

    .ph-ext {
        display: inline-block; padding: 4px 14px; border-radius: 8px;
        background: #8b5cf6; color: white; font-weight: 700; font-size: 16px;
        font-family: monospace;
    }
    .ph-pass {
        font-family: monospace; font-size: 14px; background: #f1f5f9;
        padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px;
    }
    .ph-pass-toggle {
        cursor: pointer; color: #64748b; font-size: 13px; border: none; background: none;
    }
    .ph-pass-toggle:hover { color: #3b82f6; }
    .ph-staff { font-weight: 600; color: #1e293b; }
    .ph-staff-empty { color: #94a3b8; font-style: italic; }
    .ph-dauso { font-family: monospace; font-size: 14px; color: #475569; }

    /* Action buttons */
    .ph-actions { display: flex; gap: 6px; justify-content: center; }
    .ph-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 36px; height: 32px; border-radius: 6px; border: none;
        cursor: pointer; font-size: 16px; transition: all 0.15s;
    }
    .ph-btn:hover { transform: scale(1.1); }
    .ph-btn-edit { background: #3b82f6; color: white; }
    .ph-btn-delete { background: #ef4444; color: white; }

    /* Modal */
    .ph-modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;
    }
    .ph-modal-overlay.show { display: flex; }
    .ph-modal {
        background: white; border-radius: 16px; width: 500px; max-width: 95vw;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .ph-modal-header {
        padding: 18px 24px; border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .ph-modal-header h3 { margin: 0; font-size: 18px; color: #1e293b; }
    .ph-modal-close {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        background: #f1f5f9; cursor: pointer; font-size: 18px; color: #64748b;
    }
    .ph-modal-close:hover { background: #e2e8f0; }
    .ph-modal-body { padding: 24px; }
    .ph-modal-footer {
        padding: 16px 24px; border-top: 1px solid #e2e8f0;
        display: flex; justify-content: flex-end; gap: 10px;
    }
    .ph-form-group { margin-bottom: 16px; }
    .ph-form-group label {
        display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;
    }
    .ph-form-group .required { color: #dc2626; }
    .ph-form-input {
        width: 100%; padding: 9px 14px; border: 2px solid #e2e8f0; border-radius: 10px;
        font-size: 14px; transition: border-color 0.2s; box-sizing: border-box;
    }
    .ph-form-input:focus { outline: none; border-color: #8b5cf6; }
    .ph-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .ph-btn-submit {
        padding: 9px 20px; border-radius: 10px; border: none; font-size: 14px;
        font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .ph-btn-primary { background: #8b5cf6; color: white; }
    .ph-btn-primary:hover { background: #7c3aed; }
    .ph-btn-secondary { background: #f1f5f9; color: #475569; }
    .ph-btn-secondary:hover { background: #e2e8f0; }
</style>
@endpush

@section('content')
<div class="ph-page">
    <div class="ph-header">
        <h2><i class="fa-solid fa-phone-volume" style="color:#8b5cf6;"></i> Cài Đặt Máy Nhánh</h2>
        @can('Admin')
        <button class="ph-header-btn" onclick="openPhModal()"><i class="fa-solid fa-plus"></i> Thêm Máy Nhánh</button>
        @endcan
    </div>

    @if(session('success'))
        <div class="ph-alert ph-alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="ph-alert ph-alert-error">❌ {{ session('error') }}</div>
    @endif

    <div class="ph-table-wrap">
        <table class="ph-table">
            <thead>
                <tr>
                    <th style="width:60px;">STT</th>
                    <th>Số Máy Nhánh</th>
                    <th>Mật Khẩu</th>
                    <th>Nhân Viên</th>
                    <th>Đầu Số</th>
                    @can('Admin')
                    <th style="width:120px;">Thao Tác</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($phones as $index => $phone)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="ph-ext">{{ $phone->extension }}</span></td>
                    <td>
                        <span class="ph-pass">
                            <span class="ph-pass-text" data-pass="{{ $phone->password }}">••••••••</span>
                            <button class="ph-pass-toggle" onclick="togglePass(this)" title="Hiện/Ẩn"><i class="fa-solid fa-eye"></i></button>
                        </span>
                    </td>
                    <td>
                        @if($phone->userName)
                            <span class="ph-staff">{{ $phone->userName }}</span>
                        @else
                            <span class="ph-staff-empty">Chưa gán</span>
                        @endif
                    </td>
                    <td><span class="ph-dauso">{{ $phone->DauSo ?: '—' }}</span></td>
                    @can('Admin')
                    <td>
                        <div class="ph-actions">
                            <button class="ph-btn ph-btn-edit" title="Sửa" onclick='openPhEdit(@json($phone))'><i class="fa-solid fa-pen"></i></button>
                            <form action="{{ route('customers.destroyPhone', $phone->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa máy nhánh {{ $phone->extension }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ph-btn ph-btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">
                        Chưa có máy nhánh nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== CÀI ĐẶT CUỘC GỌI ===== --}}
<div class="ph-page" style="margin-top: 20px;">
    <div class="ph-header">
        <h2><i class="fa-solid fa-gear" style="color:#8b5cf6;"></i> Cài Đặt Cuộc Gọi</h2>
        @can('Admin')
        <button class="ph-header-btn" onclick="openCsModal()"><i class="fa-solid fa-plus"></i> Thêm Cài Đặt</button>
        @endcan
    </div>

    <div class="ph-table-wrap">
        <table class="ph-table">
            <thead>
                <tr>
                    <th style="width:60px;">STT</th>
                    <th>Sockets</th>
                    <th>URI</th>
                    <th>Thời Gian Gọi</th>
                    @can('Admin')
                    <th style="width:120px;">Thao Tác</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($callSettings as $index => $cs)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-family:monospace; font-size:13px; text-align:left;">{{ $cs->sockets }}</td>
                    <td style="font-family:monospace; font-size:13px; text-align:left;">{{ $cs->uri }}</td>
                    <td><span style="display:inline-block;padding:4px 14px;border-radius:8px;background:#f0fdf4;color:#16a34a;font-weight:700;font-size:14px;">{{ $cs->call_duration ?? 5 }} phút</span></td>
                    @can('Admin')
                    <td>
                        <div class="ph-actions">
                            <button class="ph-btn ph-btn-edit" title="Sửa" onclick='openCsEdit(@json($cs))'><i class="fa-solid fa-pen"></i></button>
                            <form action="{{ route('customers.destroyCallSetting', $cs->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa cài đặt này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ph-btn ph-btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:#94a3b8;">
                        Chưa có cài đặt cuộc gọi nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== CALL SETTING MODAL ===== --}}
@can('Admin')
<div class="ph-modal-overlay" id="csModal">
    <div class="ph-modal">
        <div class="ph-modal-header">
            <h3 id="csModalTitle">Thêm Cài Đặt Cuộc Gọi</h3>
            <button class="ph-modal-close" onclick="closeCsModal()">✕</button>
        </div>
        <form id="csForm" method="POST" action="{{ route('customers.storeCallSetting') }}">
            @csrf
            <input type="hidden" name="_method" id="csMethod" value="POST">
            <div class="ph-modal-body">
                <div class="ph-form-group">
                    <label>Sockets <span class="required">*</span></label>
                    <input type="text" name="sockets" id="csSockets" class="ph-form-input" required placeholder="wss://example.com:8089/ws">
                </div>
                <div class="ph-form-group">
                    <label>URI <span class="required">*</span></label>
                    <input type="text" name="uri" id="csUri" class="ph-form-input" required placeholder="sip:example@domain.com">
                </div>
                <div class="ph-form-group">
                    <label>Thời Gian Gọi (phút) <span class="required">*</span></label>
                    <input type="number" name="call_duration" id="csDuration" class="ph-form-input" required min="1" max="60" value="5" placeholder="VD: 5">
                    <small style="color:#64748b;font-size:12px;margin-top:4px;display:block;">Cuộc gọi sẽ tự ngắt khi quá thời gian này</small>
                </div>
            </div>
            <div class="ph-modal-footer">
                <button type="button" class="ph-btn-submit ph-btn-secondary" onclick="closeCsModal()">Hủy</button>
                <button type="submit" class="ph-btn-submit ph-btn-primary" id="csSubmitBtn">Thêm</button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ===== ADD/EDIT MODAL ===== --}}
@can('Admin')
<div class="ph-modal-overlay" id="phModal">
    <div class="ph-modal">
        <div class="ph-modal-header">
            <h3 id="phModalTitle">Thêm Máy Nhánh</h3>
            <button class="ph-modal-close" onclick="closePhModal()">✕</button>
        </div>
        <form id="phForm" method="POST" action="{{ route('customers.storePhone') }}">
            @csrf
            <input type="hidden" name="_method" id="phMethod" value="POST">
            <div class="ph-modal-body">
                <div class="ph-form-row">
                    <div class="ph-form-group">
                        <label>Số Máy Nhánh <span class="required">*</span></label>
                        <input type="text" name="extension" id="phExtension" class="ph-form-input" required placeholder="VD: 100">
                    </div>
                    <div class="ph-form-group">
                        <label>Mật Khẩu <span class="required">*</span></label>
                        <input type="text" name="password" id="phPassword" class="ph-form-input" required placeholder="Mật khẩu máy nhánh">
                    </div>
                </div>
                <div class="ph-form-row">
                    <div class="ph-form-group">
                        <label>Nhân Viên</label>
                        <select name="MaNV" id="phMaNV" class="ph-form-input">
                            <option value="">— Chưa gán —</option>
                            @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ph-form-group">
                        <label>Đầu Số</label>
                        <input type="text" name="DauSo" id="phDauSo" class="ph-form-input" placeholder="VD: 0592133584">
                    </div>
                </div>
            </div>
            <div class="ph-modal-footer">
                <button type="button" class="ph-btn-submit ph-btn-secondary" onclick="closePhModal()">Hủy</button>
                <button type="submit" class="ph-btn-submit ph-btn-primary" id="phSubmitBtn">Thêm</button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
    function openPhModal() {
        document.getElementById('phModalTitle').textContent = 'Thêm Máy Nhánh';
        document.getElementById('phForm').action = '{{ route("customers.storePhone") }}';
        document.getElementById('phMethod').value = 'POST';
        document.getElementById('phExtension').value = '';
        document.getElementById('phPassword').value = '';
        document.getElementById('phMaNV').value = '';
        document.getElementById('phDauSo').value = '';
        document.getElementById('phSubmitBtn').textContent = 'Thêm';
        document.getElementById('phModal').classList.add('show');
    }

    function openPhEdit(p) {
        document.getElementById('phModalTitle').textContent = 'Sửa Máy Nhánh ' + p.extension;
        document.getElementById('phForm').action = '/customers/phones/' + p.id;
        document.getElementById('phMethod').value = 'PUT';
        document.getElementById('phExtension').value = p.extension || '';
        document.getElementById('phPassword').value = p.password || '';
        document.getElementById('phMaNV').value = p.MaNV || '';
        document.getElementById('phDauSo').value = p.DauSo || '';
        document.getElementById('phSubmitBtn').textContent = 'Lưu Thay Đổi';
        document.getElementById('phModal').classList.add('show');
    }

    function closePhModal() {
        document.getElementById('phModal').classList.remove('show');
    }

    function togglePass(btn) {
        const textEl = btn.parentElement.querySelector('.ph-pass-text');
        const icon = btn.querySelector('i');
        if (textEl.textContent === '••••••••') {
            textEl.textContent = textEl.dataset.pass;
            icon.className = 'fa-solid fa-eye-slash';
        } else {
            textEl.textContent = '••••••••';
            icon.className = 'fa-solid fa-eye';
        }
    }

    // Close on backdrop click
    document.getElementById('phModal')?.addEventListener('click', function(e) {
        if (e.target === this) closePhModal();
    });

    // ===== CALL SETTING MODAL =====
    function openCsModal() {
        document.getElementById('csModalTitle').textContent = 'Thêm Cài Đặt Cuộc Gọi';
        document.getElementById('csForm').action = '{{ route("customers.storeCallSetting") }}';
        document.getElementById('csMethod').value = 'POST';
        document.getElementById('csSockets').value = '';
        document.getElementById('csUri').value = '';
        document.getElementById('csDuration').value = '5';
        document.getElementById('csSubmitBtn').textContent = 'Thêm';
        document.getElementById('csModal').classList.add('show');
    }

    function openCsEdit(cs) {
        document.getElementById('csModalTitle').textContent = 'Sửa Cài Đặt Cuộc Gọi';
        document.getElementById('csForm').action = '/customers/call-settings/' + cs.id;
        document.getElementById('csMethod').value = 'PUT';
        document.getElementById('csSockets').value = cs.sockets || '';
        document.getElementById('csUri').value = cs.uri || '';
        document.getElementById('csDuration').value = cs.call_duration || 5;
        document.getElementById('csSubmitBtn').textContent = 'Lưu Thay Đổi';
        document.getElementById('csModal').classList.add('show');
    }

    function closeCsModal() {
        document.getElementById('csModal').classList.remove('show');
    }

    document.getElementById('csModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCsModal();
    });
</script>
@endpush
