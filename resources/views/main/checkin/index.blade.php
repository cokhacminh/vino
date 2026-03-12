@extends('main.layouts.app')

@section('title', 'Chấm Công')

@push('styles')
<style>
    .ci-page { max-width: 600px; margin: 0 auto; }

    /* Clock */
    .ci-clock-card {
        background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 40%, #2563eb 100%);
        border-radius: 20px; padding: 40px; text-align: center; color: white;
        margin-bottom: 24px; position: relative; overflow: hidden;
    }
    .ci-clock-card::after {
        content: ''; position: absolute; top: -50%; right: -30%;
        width: 300px; height: 300px; border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .ci-date { font-size: 14px; font-weight: 500; opacity: 0.8; margin-bottom: 4px; }
    .ci-time { font-size: 56px; font-weight: 800; letter-spacing: 2px; margin-bottom: 8px; }
    .ci-greeting { font-size: 16px; font-weight: 600; opacity: 0.9; }

    /* Status Card */
    .ci-status-card {
        background: white; border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        overflow: hidden; margin-bottom: 24px;
    }
    .ci-status-body { padding: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
    .ci-status-icon {
        width: 64px; height: 64px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; flex-shrink: 0;
    }
    .ci-status-icon.waiting { background: #fef3c7; color: #d97706; }
    .ci-status-icon.checked-in { background: #dcfce7; color: #16a34a; }
    .ci-status-icon.done { background: #dbeafe; color: #2563eb; }
    .ci-status-text h3 { margin: 0 0 4px; font-size: 18px; font-weight: 700; color: #1e293b; }
    .ci-status-text p { margin: 0; font-size: 14px; color: #64748b; }

    /* Time display */
    .ci-times { display: flex; gap: 20px; margin-bottom: 24px; }
    .ci-time-box {
        flex: 1; background: white; border-radius: 16px; padding: 20px;
        text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
    }
    .ci-time-box .label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .ci-time-box .value { font-size: 28px; font-weight: 800; color: #1e293b; }
    .ci-time-box.in .value { color: #16a34a; }
    .ci-time-box.out .value { color: #dc2626; }

    /* Work hours info */
    .ci-info {
        background: white; border-radius: 16px; padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 16px rgba(0,0,0,0.04);
        margin-bottom: 24px;
    }
    .ci-info-title { font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 8px; }
    .ci-info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
    .ci-info-row .k { color: #94a3b8; }
    .ci-info-row .v { font-weight: 600; color: #1e293b; }

    /* Button */
    .ci-action { text-align: center; }
    .btn-ci {
        padding: 16px 48px; border: none; border-radius: 14px;
        font-size: 16px; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 10px;
        transition: all 0.25s; letter-spacing: 0.5px;
    }
    .btn-ci.checkin {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: white; box-shadow: 0 4px 16px rgba(22,163,74,0.35);
    }
    .btn-ci.checkin:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(22,163,74,0.45); }
    .btn-ci.checkout {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white; box-shadow: 0 4px 16px rgba(220,38,38,0.35);
    }
    .btn-ci.checkout:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(220,38,38,0.45); }
    .btn-ci.done {
        background: #e2e8f0; color: #94a3b8; cursor: default;
    }
    .btn-ci:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; box-shadow: none !important; }
</style>
@endpush

@section('content')
<div class="ci-page">
    <!-- Clock -->
    <div class="ci-clock-card">
        <div class="ci-date" id="ciDate"></div>
        <div class="ci-time" id="ciClock">--:--:--</div>
        <div class="ci-greeting">Xin chào, {{ $user->name }}!</div>
    </div>

    <!-- Status -->
    <div class="ci-status-card">
        <div class="ci-status-body">
            @if($todayChamCong && $todayChamCong->gio_ra)
                <div class="ci-status-icon done"><i class="fa-solid fa-circle-check"></i></div>
                <div class="ci-status-text">
                    <h3>Đã hoàn tất hôm nay</h3>
                    <p>Bạn đã check-in và check-out thành công</p>
                </div>
            @elseif($todayChamCong && $todayChamCong->gio_vao)
                <div class="ci-status-icon checked-in"><i class="fa-solid fa-clock"></i></div>
                <div class="ci-status-text">
                    <h3>Đang làm việc</h3>
                    <p>Nhớ check-out khi tan làm nhé!</p>
                </div>
            @else
                <div class="ci-status-icon waiting"><i class="fa-solid fa-right-to-bracket"></i></div>
                <div class="ci-status-text">
                    <h3>Chưa check-in</h3>
                    <p>Hãy check-in để bắt đầu ngày làm việc</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Times -->
    <div class="ci-times">
        <div class="ci-time-box in">
            <div class="label">Giờ vào</div>
            <div class="value">{{ $todayChamCong && $todayChamCong->gio_vao ? substr($todayChamCong->gio_vao, 0, 5) : '--:--' }}</div>
        </div>
        <div class="ci-time-box out">
            <div class="label">Giờ ra</div>
            <div class="value">{{ $todayChamCong && $todayChamCong->gio_ra ? substr($todayChamCong->gio_ra, 0, 5) : '--:--' }}</div>
        </div>
    </div>

    <!-- Work hours info -->
    <div class="ci-info">
        <div class="ci-info-title"><i class="fa-solid fa-clock" style="margin-right:4px;"></i> Giờ làm việc</div>
        <div class="ci-info-row"><span class="k">Giờ bắt đầu</span><span class="v">{{ $settings['gio_bat_dau'] ?? '08:00' }}</span></div>
        <div class="ci-info-row"><span class="k">Giờ kết thúc</span><span class="v">{{ $settings['gio_ket_thuc'] ?? '17:30' }}</span></div>
        <div class="ci-info-row"><span class="k">Trễ sau</span><span class="v">{{ $settings['gio_tre_han'] ?? '08:30' }}</span></div>
    </div>

    <!-- Action Button -->
    <div class="ci-action">
        @if(!$todayChamCong || !$todayChamCong->gio_vao)
            <button type="button" class="btn-ci checkin" id="btnCheckin" onclick="doCheckin()">
                <i class="fa-solid fa-right-to-bracket"></i> CHECK-IN
            </button>
        @elseif(!$todayChamCong->gio_ra)
            <button type="button" class="btn-ci checkout" id="btnCheckout" onclick="doCheckout()">
                <i class="fa-solid fa-right-from-bracket"></i> CHECK-OUT
            </button>
        @else
            <button type="button" class="btn-ci done" disabled>
                <i class="fa-solid fa-check"></i> ĐÃ HOÀN TẤT
            </button>
        @endif
    </div>
</div>
@stop

@push('scripts')
<script>
    // Live clock
    function updateClock() {
        var now = new Date();
        var h = String(now.getHours()).padStart(2, '0');
        var m = String(now.getMinutes()).padStart(2, '0');
        var s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('ciClock').textContent = h + ':' + m + ':' + s;

        var days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
        var d = now.getDate();
        var mo = now.getMonth() + 1;
        var y = now.getFullYear();
        document.getElementById('ciDate').textContent = days[now.getDay()] + ', ' + d + '/' + mo + '/' + y;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Hardware fingerprint
    function getHardwareFingerprint() {
        var parts = [];
        parts.push(screen.width + 'x' + screen.height);
        parts.push(window.devicePixelRatio || 1);
        parts.push(navigator.hardwareConcurrency || 'unknown');
        parts.push(navigator.deviceMemory || 'unknown');
        parts.push(Intl.DateTimeFormat().resolvedOptions().timeZone);
        parts.push(navigator.platform || 'unknown');
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
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = ((hash << 5) - hash) + str.charCodeAt(i);
            hash |= 0;
        }
        return (hash >>> 0).toString(16).padStart(8, '0') + '-' + str.length.toString(16);
    }

    function doCheckin() {
        var btn = document.getElementById('btnCheckin');
        if (btn) btn.disabled = true;
        fetch('{{ route("profile.checkin") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ device_hash: getHardwareFingerprint() })
        })
        .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
        .then(function(result) {
            if (result.data.ok) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon:'success', title:'Check-in Thành Công!', text:result.data.message, timer:2000, showConfirmButton:false }).then(function() { location.reload(); });
                } else { alert(result.data.message); location.reload(); }
            } else {
                if (typeof Swal !== 'undefined') { Swal.fire({ icon:'error', title:'Không thể Check-in', text:result.data.message }); }
                else { alert(result.data.message); }
                if (btn) btn.disabled = false;
            }
        }).catch(function(err) { alert('Lỗi: ' + err.message); if (btn) btn.disabled = false; });
    }

    function doCheckout() {
        var btn = document.getElementById('btnCheckout');
        if (btn) btn.disabled = true;
        fetch('{{ route("profile.checkout") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ device_hash: getHardwareFingerprint() })
        })
        .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
        .then(function(result) {
            if (result.data.ok) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon:'success', title:'Check-out Thành Công!', text:result.data.message, timer:2000, showConfirmButton:false }).then(function() { location.reload(); });
                } else { alert(result.data.message); location.reload(); }
            } else {
                if (typeof Swal !== 'undefined') { Swal.fire({ icon:'error', title:'Không thể Check-out', text:result.data.message }); }
                else { alert(result.data.message); }
                if (btn) btn.disabled = false;
            }
        }).catch(function(err) { alert('Lỗi: ' + err.message); if (btn) btn.disabled = false; });
    }
</script>
@endpush
