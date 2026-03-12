@extends('main.layouts.app')
@section('title', 'API Giao Hàng')

@push('styles')
<style>
    .api-page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:14px; }
    .api-page-header h2 { margin:0; font-size:23px; color:#1e293b; }
    .api-cards { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
    @media(max-width:900px){ .api-cards { grid-template-columns:1fr; } }
    .api-card { background:white; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04); overflow:hidden; }
    .api-card-header { padding:18px 24px; display:flex; align-items:center; gap:14px; border-bottom:1px solid #f1f5f9; }
    .api-card-header .api-logo { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:700; color:white; flex-shrink:0; }
    .logo-ghtk { background:linear-gradient(135deg, #00b14f, #009e47); }
    .logo-ghn { background:linear-gradient(135deg, #f26522, #e05a1c); }
    .logo-hotline { background:linear-gradient(135deg, #6366f1, #4f46e5); }
    .api-card-header .api-info h3 { margin:0; font-size:17px; color:#1e293b; }
    .api-card-header .api-info p { margin:2px 0 0; font-size:12px; color:#94a3b8; }
    .api-card-body { padding:24px; }
    .api-form-group { margin-bottom:18px; }
    .api-form-group:last-child { margin-bottom:0; }
    .api-form-group label { display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:6px; }
    .api-form-group label .key-hint { font-weight:400; color:#94a3b8; font-size:11px; margin-left:4px; }
    .api-form-input { width:100%; padding:10px 14px; border:2px solid #e2e8f0; border-radius:10px; font-size:13px; font-family:'Courier New',monospace; transition:border-color 0.2s; box-sizing:border-box; background:#f8fafc; }
    .api-form-input:focus { outline:none; border-color:#6d28d9; background:white; }
    .api-card-footer { padding:16px 24px; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; gap:10px; }
    .btn-save-api { padding:9px 22px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; color:white; }
    .btn-save-ghtk { background:#00b14f; } .btn-save-ghtk:hover { background:#009e47; }
    .btn-save-ghn { background:#f26522; } .btn-save-ghn:hover { background:#e05a1c; }
    .btn-save-hotline { background:#6366f1; } .btn-save-hotline:hover { background:#4f46e5; }
    .alert-msg { padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px; font-weight:500; }
    .alert-success { background:#dcfce7; color:#15803d; }
    .alert-error { background:#fee2e2; color:#dc2626; }
    .toggle-vis { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; font-size:16px; padding:4px; }
    .toggle-vis:hover { color:#475569; }
    .input-wrap { position:relative; }
    .input-wrap .api-form-input { padding-right:40px; }
    .status-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:6px; }
    .status-active { background:#22c55e; }
    .status-inactive { background:#ef4444; }
    .status-label { font-size:12px; font-weight:500; }
    .hotline-card { grid-column: 1 / -1; }
</style>
@endpush

@section('content')
<div style="padding:10px;background:white;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04);">

    <div class="api-page-header">
        <h2>🚚 API Giao Hàng</h2>
        <span style="font-size:13px; color:#94a3b8;">Quản lý cấu hình API đối tác giao hàng</span>
    </div>


    <div class="api-cards">
        {{-- HOTLINE --}}
        <div class="api-card hotline-card">
            <div class="api-card-header">
                <div class="api-logo logo-hotline">📞</div>
                <div class="api-info">
                    <h3>Số Điện Thoại HotLine</h3>
                    <p>
                        <span class="status-dot {{ $configs['hotline'] ? 'status-active' : 'status-inactive' }}"></span>
                        <span class="status-label">{{ $configs['hotline'] ? 'Đã cấu hình: ' . $configs['hotline'] : 'Chưa cấu hình' }}</span>
                    </p>
                </div>
            </div>
            <form action="{{ route('system.apiGiaoHang.update') }}" method="POST">
                @csrf
                <input type="hidden" name="provider" value="hotline">
                <div class="api-card-body">
                    <div class="api-form-group">
                        <label>Số HotLine <span class="key-hint">(dùng cho pick_tel GHTK và return_phone GHN)</span></label>
                        <input type="text" name="hotline" class="api-form-input" value="{{ $configs['hotline'] }}" placeholder="Nhập số điện thoại HotLine..." style="font-family:inherit;">
                    </div>
                </div>
                <div class="api-card-footer">
                    <button type="submit" class="btn-save-api btn-save-hotline">💾 Lưu HotLine</button>
                </div>
            </form>
        </div>

        {{-- GHTK --}}
        <div class="api-card">
            <div class="api-card-header">
                <div class="api-logo logo-ghtk">GT</div>
                <div class="api-info">
                    <h3>Giao Hàng Tiết Kiệm (GHTK)</h3>
                    <p>
                        <span class="status-dot {{ $configs['token_ghtk'] ? 'status-active' : 'status-inactive' }}"></span>
                        <span class="status-label">{{ $configs['token_ghtk'] ? 'Đã cấu hình' : 'Chưa cấu hình' }}</span>
                    </p>
                </div>
            </div>
            <form action="{{ route('system.apiGiaoHang.update') }}" method="POST">
                @csrf
                <input type="hidden" name="provider" value="ghtk">
                <div class="api-card-body">
                    <div class="api-form-group">
                        <label>Token GHTK <span class="key-hint">(token_ghtk)</span></label>
                        <div class="input-wrap">
                            <input type="password" name="token_ghtk" class="api-form-input" value="{{ $configs['token_ghtk'] }}" placeholder="Nhập token API GHTK...">
                            <button type="button" class="toggle-vis" onclick="toggleVis(this)"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="api-card-footer">
                    <button type="submit" class="btn-save-api btn-save-ghtk">💾 Lưu GHTK</button>
                </div>
            </form>
        </div>

        {{-- GHN --}}
        <div class="api-card">
            <div class="api-card-header">
                <div class="api-logo logo-ghn">GH</div>
                <div class="api-info">
                    <h3>Giao Hàng Nhanh (GHN)</h3>
                    <p>
                        <span class="status-dot {{ $configs['token_ghn'] ? 'status-active' : 'status-inactive' }}"></span>
                        <span class="status-label">{{ $configs['token_ghn'] ? 'Đã cấu hình' : 'Chưa cấu hình' }}</span>
                    </p>
                </div>
            </div>
            <form action="{{ route('system.apiGiaoHang.update') }}" method="POST">
                @csrf
                <input type="hidden" name="provider" value="ghn">
                <div class="api-card-body">
                    <div class="api-form-group">
                        <label>Token GHN <span class="key-hint">(token_ghn)</span></label>
                        <div class="input-wrap">
                            <input type="password" name="token_ghn" class="api-form-input" value="{{ $configs['token_ghn'] }}" placeholder="Nhập token API GHN...">
                            <button type="button" class="toggle-vis" onclick="toggleVis(this)"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="api-form-group">
                        <label>Shop ID GHN <span class="key-hint">(shopid_ghn)</span></label>
                        <input type="text" name="shopid_ghn" class="api-form-input" value="{{ $configs['shopid_ghn'] }}" placeholder="Nhập Shop ID GHN...">
                    </div>
                </div>
                <div class="api-card-footer">
                    <button type="submit" class="btn-save-api btn-save-ghn">💾 Lưu GHN</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleVis(btn) {
    const input = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
