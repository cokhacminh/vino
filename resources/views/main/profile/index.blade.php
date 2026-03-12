@extends('main.layouts.app')

@section('title', 'Thông Tin Cá Nhân')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .profile-container {
        width: 100%;
    }

    /* Avatar Section */
    .profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .profile-banner {
        height: 140px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 40%, #2563eb 100%);
        position: relative;
    }
    .profile-banner::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        background: linear-gradient(transparent, rgba(0,0,0,0.1));
    }
    .profile-info-section {
        display: flex;
        align-items: flex-end;
        gap: 24px;
        padding: 0 30px 24px;
        margin-top: -50px;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
    }
    .avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }
    .avatar-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        background: #f1f5f9;
        cursor: pointer;
        transition: filter 0.2s;
    }
    .avatar-img:hover {
        filter: brightness(0.85);
    }
    .avatar-overlay {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #6d28d9, #4f46e5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.4);
        border: 2px solid white;
        transition: transform 0.2s;
    }
    .avatar-overlay:hover { transform: scale(1.1); }

    .profile-name-area {
        flex: 1;
        min-width: 200px;
    }
    .profile-name {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 4px;
    }
    .profile-role {
        font-size: 14px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .role-badge.dept {
        background: #ede9fe;
        color: #6d28d9;
    }
    .role-badge.pos {
        background: #dbeafe;
        color: #2563eb;
    }
    .role-badge.status-active {
        background: #dcfce7;
        color: #16a34a;
    }
    .role-badge.status-inactive {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Grid Layout */
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media (max-width: 768px) {
        .profile-grid { grid-template-columns: 1fr; }
    }

    .card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .card-header i {
        color: #6d28d9;
        font-size: 18px;
    }
    .card-body {
        padding: 20px;
    }

    /* Info rows */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-label i { font-size: 14px; color: #94a3b8; width: 18px; text-align: center; }
    .info-value {
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
        text-align: right;
    }

    /* Form */
    .form-group { margin-bottom: 14px; }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }
    .form-input {
        width: 100%;
        padding: 9px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .form-input:focus {
        outline: none;
        border-color: #6d28d9;
        box-shadow: 0 0 0 3px rgba(109,40,217,0.1);
    }
    textarea.form-input { resize: vertical; min-height: 80px; }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 24px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3);
        transition: all 0.2s;
        width: 100%;
        justify-content: center;
    }
    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(109,40,217,0.4);
    }

    /* Alert */
    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 16px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* NhanSu Info */
    .ns-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 16px;
    }
    .ns-info-item {
        padding: 6px 0;
    }
    .ns-info-label {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
    }
    .ns-info-value {
        font-size: 13px;
        color: #1e293b;
        font-weight: 500;
    }

    /* Full width card */
    .card-full { grid-column: 1 / -1; }

    /* Modal */
    .modal-overlay {
        display: none; position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
        z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white; border-radius: 16px;
        width: 95%; max-width: 800px; max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        animation: modalIn 0.25s ease;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 24px; border-bottom: 1px solid #f1f5f9;
        position: sticky; top: 0; background: white; z-index: 2;
    }
    .modal-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1e293b; }
    .modal-close {
        width: 32px; height: 32px; border: none; background: #f1f5f9;
        border-radius: 8px; cursor: pointer; font-size: 18px; color: #64748b;
        display: flex; align-items: center; justify-content: center;
    }
    .modal-close:hover { background: #e2e8f0; }
    .modal-body { padding: 5px 24px; }
    .modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 16px 24px; border-top: 1px solid #f1f5f9;
        position: sticky; bottom: 0; background: white;
    }

    /* Tabs */
    .ns-tabs {
        display: flex; gap: 0; border-bottom: 2px solid #e2e8f0;
        margin-bottom: 20px; flex-wrap: wrap;
    }
    .ns-tab {
        padding: 10px 18px; cursor: pointer; font-size: 13px;
        font-weight: 600; color: #64748b; border-bottom: 2px solid transparent;
        margin-bottom: -2px; transition: all 0.2s; white-space: nowrap;
    }
    .ns-tab:hover { color: #6d28d9; }
    .ns-tab.active { color: #6d28d9; border-bottom-color: #6d28d9; }
    .ns-tab-content { display: none; }
    .ns-tab-content.active { display: block; }

    /* Form rows */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-row.three-col { grid-template-columns: 1fr 1fr 1fr; }
    .form-group .required { color: #dc2626; }
    select.form-input { appearance: auto; cursor: pointer; }

    .section-title {
        font-size: 14px; font-weight: 700; color: #6d28d9;
        margin-bottom: 14px; padding-bottom: 8px;
        border-bottom: 1px solid #ede9fe;
        display: flex; align-items: center; gap: 8px;
    }

    .btn-modal-cancel {
        padding: 10px 20px; background: #f1f5f9; color: #475569;
        border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-save {
        padding: 10px 24px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white; border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3);
    }
    .btn-modal-save:hover { opacity: 0.9; }

    .btn-submit-hoso {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 24px; margin-top: 12px;
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
        color: white; border: none; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        box-shadow: 0 2px 8px rgba(109,40,217,0.3); transition: all 0.2s;
    }
    .btn-submit-hoso:hover {
        transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109,40,217,0.4);
    }

    /* Check-in Card */
    .checkin-card {
        background: white; border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        overflow: hidden; margin-bottom: 20px;
    }
    .checkin-body {
        display: flex; align-items: center; gap: 24px;
        padding: 20px 24px; flex-wrap: wrap;
    }
    .checkin-status {
        display: flex; align-items: center; gap: 12px; flex: 1; min-width: 200px;
    }
    .checkin-icon {
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0;
    }
    .checkin-icon.waiting { background: #fef3c7; color: #d97706; }
    .checkin-icon.checked-in { background: #dcfce7; color: #16a34a; }
    .checkin-icon.checked-out { background: #dbeafe; color: #2563eb; }
    .checkin-info h4 { margin: 0 0 2px; font-size: 15px; font-weight: 700; color: #1e293b; }
    .checkin-info p { margin: 0; font-size: 13px; color: #64748b; }
    .checkin-time {
        display: flex; gap: 16px; align-items: center;
    }
    .checkin-time-item {
        text-align: center; padding: 8px 16px;
        background: #f8fafc; border-radius: 10px;
    }
    .checkin-time-item .t-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
    .checkin-time-item .t-value { font-size: 18px; font-weight: 800; color: #1e293b; }
    .btn-checkin, .btn-checkout {
        padding: 12px 28px; border: none; border-radius: 12px;
        font-size: 14px; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.2s; white-space: nowrap;
    }
    .btn-checkin {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: white; box-shadow: 0 3px 12px rgba(22,163,74,0.3);
    }
    .btn-checkin:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(22,163,74,0.4); }
    .btn-checkout {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white; box-shadow: 0 3px 12px rgba(220,38,38,0.3);
    }
    .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,38,38,0.4); }
    .btn-checkin:disabled, .btn-checkout:disabled {
        opacity: 0.5; cursor: not-allowed; transform: none !important;
        box-shadow: none !important;
    }
</style>
@endpush

@section('content')
<div class="profile-container">


    <!-- Profile Header Card -->
    <div class="profile-card">
        <div class="profile-banner"></div>
        <div class="profile-info-section">
            <div class="avatar-wrapper">
                <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;"
                           onchange="document.getElementById('avatarForm').submit();">
                </form>
                @php
                    $avatarUrl = $user->avatar
                        ? (file_exists(public_path('storage/avatars/' . $user->avatar))
                            ? asset('storage/avatars/' . $user->avatar)
                            : (file_exists(public_path('images/' . $user->avatar))
                                ? asset('images/' . $user->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=120&background=6d28d9&color=fff&bold=true'))
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=120&background=6d28d9&color=fff&bold=true';
                @endphp
                <img src="{{ $avatarUrl }}" alt="Avatar" class="avatar-img"
                     onclick="document.getElementById('avatarInput').click();">
                <div class="avatar-overlay" onclick="document.getElementById('avatarInput').click();">
                    <i class="fa-solid fa-camera"></i>
                </div>
            </div>
            <div class="profile-name-area">
                <h2 class="profile-name">{{ $user->name }}</h2>
                <div class="profile-role">
                    <span class="role-badge dept">
                        <i class="fa-solid fa-building"></i>
                        {{ $phongban->TenPB ?? 'Chưa có phòng ban' }}
                    </span>
                    <span class="role-badge pos">
                        <i class="fa-solid fa-briefcase"></i>
                        {{ $chucvu->TenCV ?? 'Chưa có chức vụ' }}
                    </span>
                    <span class="role-badge {{ $user->TinhTrang == 'Active' ? 'status-active' : 'status-inactive' }}">
                        <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                        {{ $user->TinhTrang == 'Active' ? 'Đang làm việc' : 'Đã nghỉ việc' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Check-in / Check-out Card -->
    <div class="checkin-card">
        <div class="checkin-body">
            <div class="checkin-status">
                @if($todayChamCong && $todayChamCong->gio_ra)
                    <div class="checkin-icon checked-out"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="checkin-info">
                        <h4>Đã hoàn tất hôm nay</h4>
                        <p>Bạn đã check-in và check-out</p>
                    </div>
                @elseif($todayChamCong && $todayChamCong->gio_vao)
                    <div class="checkin-icon checked-in"><i class="fa-solid fa-clock"></i></div>
                    <div class="checkin-info">
                        <h4>Đang làm việc</h4>
                        <p>Bạn đã check-in hôm nay</p>
                    </div>
                @else
                    <div class="checkin-icon waiting"><i class="fa-solid fa-right-to-bracket"></i></div>
                    <div class="checkin-info">
                        <h4>Chưa check-in</h4>
                        <p>Hãy check-in để bắt đầu ngày làm việc</p>
                    </div>
                @endif
            </div>
            <div class="checkin-time">
                <div class="checkin-time-item">
                    <div class="t-label">Giờ vào</div>
                    <div class="t-value" id="ciTimeIn">{{ $todayChamCong && $todayChamCong->gio_vao ? substr($todayChamCong->gio_vao, 0, 5) : '--:--' }}</div>
                </div>
                <div class="checkin-time-item">
                    <div class="t-label">Giờ ra</div>
                    <div class="t-value" id="ciTimeOut">{{ $todayChamCong && $todayChamCong->gio_ra ? substr($todayChamCong->gio_ra, 0, 5) : '--:--' }}</div>
                </div>
            </div>
            <div>
                @if(!$todayChamCong || !$todayChamCong->gio_vao)
                    <button type="button" class="btn-checkin" id="btnCheckin" onclick="doCheckin()">
                        <i class="fa-solid fa-right-to-bracket"></i> Check-in
                    </button>
                @elseif(!$todayChamCong->gio_ra)
                    <button type="button" class="btn-checkout" id="btnCheckout" onclick="doCheckout()">
                        <i class="fa-solid fa-right-from-bracket"></i> Check-out
                    </button>
                @else
                    <button type="button" class="btn-checkin" disabled>
                        <i class="fa-solid fa-check"></i> Hoàn tất
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="profile-grid">
        <!-- Thông tin tài khoản -->
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-user-circle"></i>
                <h3>Thông Tin Tài Khoản</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-user"></i> Tên hiển thị</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-at"></i> Tên đăng nhập</span>
                    <span class="info-value">{{ $user->username }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-building"></i> Phòng ban</span>
                    <span class="info-value">{{ $phongban->TenPB ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-briefcase"></i> Chức vụ</span>
                    <span class="info-value">{{ $chucvu->TenCV ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-circle-info"></i> Trạng thái</span>
                    <span class="info-value">{{ $user->TinhTrang == 'Active' ? 'Đang làm việc' : 'Đã nghỉ việc' }}</span>
                </div>
            </div>
        </div>

        <!-- Thông tin nhân sự -->
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-id-card"></i>
                <h3>Thông Tin Nhân Sự</h3>
            </div>
            <div class="card-body">
                @if($nhansu)
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-venus-mars"></i> Giới tính</span>
                    <span class="info-value">{{ $nhansu->GioiTinh ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-cake-candles"></i> Ngày sinh</span>
                    <span class="info-value">{{ $nhansu->NgaySinh ? \Carbon\Carbon::parse($nhansu->NgaySinh)->format('d/m/Y') : '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-id-card"></i> Số CCCD</span>
                    <span class="info-value">{{ $nhansu->SoCCCD ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-phone"></i> SĐT</span>
                    <span class="info-value">{{ $nhansu->SDT ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-envelope"></i> Email</span>
                    <span class="info-value">{{ $nhansu->Email ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa-solid fa-file-contract"></i> Loại HĐ</span>
                    <span class="info-value">{{ $nhansu->LoaiHD ?? '—' }}</span>
                </div>
                @else
                <div style="text-align:center; padding:20px; color:#94a3b8;">
                    <i class="fa-solid fa-circle-info" style="font-size:24px; margin-bottom:8px;"></i>
                    <p style="margin:0; font-size:14px;">Chưa có hồ sơ nhân sự liên kết</p>
                    <button type="button" class="btn-submit-hoso" onclick="openModal('modalSubmitHoSo')">
                        <i class="fa-solid fa-paper-plane"></i> Gửi Hồ Sơ Cá Nhân
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Cập nhật thông tin -->
        <div class="card" style="height:350px">
            <div class="card-header">
                <i class="fa-solid fa-pen-to-square"></i>
                <h3>Cập Nhật Thông Tin</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Tên hiển thị</label>
                        <input type="text" name="name" class="form-input" value="{{ $user->name }}" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Giới thiệu bản thân</label>
                        <textarea name="GioiThieu" class="form-input" placeholder="Viết vài dòng giới thiệu về bạn...">{{ $user->GioiThieu }}</textarea>
                    </div>
                    <button type="submit" class="btn-save" style="margin-top:30px;">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                    </button>
                </form>
            </div>
        </div>

        <!-- Đổi mật khẩu -->
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-lock"></i>
                <h3>Đổi Mật Khẩu</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="form-input" placeholder="Nhập mật khẩu hiện tại..." required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-input" placeholder="Nhập mật khẩu mới..." required>
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" class="form-input" placeholder="Nhập lại mật khẩu mới..." required>
                    </div>
                    <button type="submit" class="btn-save" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); box-shadow: 0 2px 8px rgba(220,38,38,0.3);">
                        <i class="fa-solid fa-key"></i> Đổi Mật Khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(!$nhansu)
<!-- ===================== MODAL: GỬI HỒ SƠ CÁ NHÂN ===================== -->
<div class="modal-overlay" id="modalSubmitHoSo">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-paper-plane"></i> Gửi Hồ Sơ Cá Nhân</h3>
            <button class="modal-close" onclick="closeModal('modalSubmitHoSo')">✕</button>
        </div>
        <form action="{{ route('profile.ho-so') }}" method="POST">
            @csrf
            <div class="modal-body">
                <!-- Tabs -->
                <div class="ns-tabs" id="hosoTabs">
                    <div class="ns-tab active" onclick="switchTab('hosoTabs', 'hosoTab', 0)"><i class="fa-solid fa-user"></i> Cá Nhân</div>
                    <div class="ns-tab" onclick="switchTab('hosoTabs', 'hosoTab', 1)"><i class="fa-solid fa-graduation-cap"></i> Học Vấn</div>
                    <div class="ns-tab" onclick="switchTab('hosoTabs', 'hosoTab', 2)"><i class="fa-solid fa-building-columns"></i> NH - BHXH - MST</div>
                </div>

                <!-- Tab 1: Thông tin cá nhân -->
                <div class="ns-tab-content active" id="hosoTab0">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ tên <span class="required">*</span></label>
                            <input type="text" name="HoTen" class="form-input" value="{{ $user->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="text" name="NgaySinh" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="form-row three-col">
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="GioiTinh" class="form-input">
                                <option value="">-- Chọn --</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Số CCCD</label>
                            <input type="text" name="SoCCCD" class="form-input" placeholder="Nhập số CCCD...">
                        </div>
                        <div class="form-group">
                            <label>Ngày cấp CCCD</label>
                            <input type="text" name="NgayCapCCCD" class="form-input datepicker" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nơi cấp CCCD</label>
                        <input type="text" name="NoiCapCCCD" class="form-input" placeholder="Nhập nơi cấp...">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="SDT" class="form-input" placeholder="Nhập SĐT...">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="Email" class="form-input" placeholder="Nhập email...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Thường trú</label>
                        <textarea name="ThuongTru" class="form-input" placeholder="Nhập địa chỉ thường trú..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ hiện tại</label>
                        <textarea name="DiaChiHienTai" class="form-input" placeholder="Nhập địa chỉ hiện tại..."></textarea>
                    </div>
                </div>

                <!-- Tab 2: Trình độ học vấn -->
                <div class="ns-tab-content" id="hosoTab1">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Trình độ học vấn</label>
                            <select name="TrinhDoHocVan" class="form-input">
                                <option value="">-- Chọn --</option>
                                <option value="THPT">THPT</option>
                                <option value="Trung cấp">Trung cấp</option>
                                <option value="Cao đẳng">Cao đẳng</option>
                                <option value="Đại học">Đại học</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Năm tốt nghiệp</label>
                            <input type="text" name="NamTotNghiep" class="form-input" placeholder="VD: 2020">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Trường đào tạo</label>
                        <input type="text" name="TruongDaoTao" class="form-input" placeholder="Nhập tên trường...">
                    </div>
                    <div class="form-group">
                        <label>Chuyên ngành</label>
                        <input type="text" name="ChuyenNganh" class="form-input" placeholder="Nhập chuyên ngành...">
                    </div>
                </div>

                <!-- Tab 3: Ngân hàng - BHXH - MST -->
                <div class="ns-tab-content" id="hosoTab2">
                    <div class="form-group">
                        <label>Số sổ BHXH</label>
                        <input type="text" name="SoSoBHXH" class="form-input" placeholder="Nhập số sổ BHXH...">
                    </div>
                    <div class="form-group">
                        <label>MST cá nhân</label>
                        <input type="text" name="MSTCaNhan" class="form-input" placeholder="Nhập mã số thuế cá nhân...">
                    </div>
                    <div class="form-group">
                        <label>STK Ngân hàng</label>
                        <input type="text" name="STKNganHang" class="form-input" placeholder="Nhập số tài khoản ngân hàng...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalSubmitHoSo')">Hủy</button>
                <button type="submit" class="btn-modal-save"><i class="fa-solid fa-paper-plane"></i> Gửi Hồ Sơ</button>
            </div>
        </form>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
<script>
    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('show'); });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show'));
    });

    function switchTab(tabsId, tabPrefix, index) {
        var tabsEl = document.getElementById(tabsId);
        tabsEl.querySelectorAll('.ns-tab').forEach(function(t, i) { t.classList.toggle('active', i === index); });
        var i = 0;
        while (document.getElementById(tabPrefix + i)) {
            document.getElementById(tabPrefix + i).classList.toggle('active', i === index);
            i++;
        }
    }

    // Flatpickr init
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.datepicker', { dateFormat: 'd/m/Y', allowInput: true, locale: 'vn' });
        }
    });

    // ========== Hardware Fingerprint ==========
    function getHardwareFingerprint() {
        var parts = [];
        // Screen
        parts.push(screen.width + 'x' + screen.height);
        parts.push(window.devicePixelRatio || 1);
        // CPU & RAM
        parts.push(navigator.hardwareConcurrency || 'unknown');
        parts.push(navigator.deviceMemory || 'unknown');
        // Timezone
        parts.push(Intl.DateTimeFormat().resolvedOptions().timeZone);
        // Platform
        parts.push(navigator.platform || 'unknown');
        // WebGL GPU
        try {
            var canvas = document.createElement('canvas');
            var gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            if (gl) {
                var ext = gl.getExtension('WEBGL_debug_renderer_info');
                if (ext) {
                    parts.push(gl.getParameter(ext.UNMASKED_VENDOR_WEBGL));
                    parts.push(gl.getParameter(ext.UNMASKED_RENDERER_WEBGL));
                }
            }
        } catch(e) {}

        var str = parts.join('|');
        // Simple hash
        return simpleHash(str);
    }

    function simpleHash(str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            var chr = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + chr;
            hash |= 0;
        }
        // Convert to hex string
        return (hash >>> 0).toString(16).padStart(8, '0') + '-' + str.length.toString(16);
    }

    // ========== Check-in ==========
    function doCheckin() {
        var btn = document.getElementById('btnCheckin');
        if (btn) btn.disabled = true;

        var deviceHash = getHardwareFingerprint();

        fetch('{{ route("profile.checkin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ device_hash: deviceHash })
        })
        .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
        .then(function(result) {
            if (result.data.ok) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Check-in Thành Công!', text: result.data.message, timer: 2000, showConfirmButton: false }).then(function() {
                        location.reload();
                    });
                } else {
                    alert(result.data.message);
                    location.reload();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Không thể Check-in', text: result.data.message });
                } else {
                    alert(result.data.message);
                }
                if (btn) btn.disabled = false;
            }
        })
        .catch(function(err) {
            alert('Lỗi kết nối: ' + err.message);
            if (btn) btn.disabled = false;
        });
    }

    // ========== Check-out ==========
    function doCheckout() {
        var btn = document.getElementById('btnCheckout');
        if (btn) btn.disabled = true;

        var deviceHash = getHardwareFingerprint();

        fetch('{{ route("profile.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ device_hash: deviceHash })
        })
        .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
        .then(function(result) {
            if (result.data.ok) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Check-out Thành Công!', text: result.data.message, timer: 2000, showConfirmButton: false }).then(function() {
                        location.reload();
                    });
                } else {
                    alert(result.data.message);
                    location.reload();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Không thể Check-out', text: result.data.message });
                } else {
                    alert(result.data.message);
                }
                if (btn) btn.disabled = false;
            }
        })
        .catch(function(err) {
            alert('Lỗi kết nối: ' + err.message);
            if (btn) btn.disabled = false;
        });
    }
</script>
@endsection
