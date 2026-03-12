<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\NhanSu;
use App\Models\ChamCong;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $phongban = DB::table('phongban')->where('MaPB', $user->MaPB)->first();
        $chucvu = DB::table('chucvu')->where('MaCV', $user->MaCV)->first();
        $nhansu = DB::table('nhansu')->where('user_id', $user->id)->first();

        // Chấm công hôm nay
        $todayChamCong = ChamCong::where('user_id', $user->id)
            ->where('ngay', now()->toDateString())
            ->first();

        return view('main.profile.index', compact('user', 'phongban', 'chucvu', 'nhansu', 'todayChamCong'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:191',
            'GioiThieu' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Họ tên không được để trống.',
        ]);

        $user->name = $request->name;
        $user->GioiThieu = $request->GioiThieu;
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới tối thiểu 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Đổi mật khẩu thành công!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.max' => 'Ảnh không được vượt quá 5MB.',
        ]);

        $user = Auth::user();

        // Ensure directory exists
        $avatarDir = public_path('storage/avatars');
        if (!file_exists($avatarDir)) {
            mkdir($avatarDir, 0777, true);
        }

        // Delete old avatar if not default
        if ($user->avatar && $user->avatar !== 'default-avatar.jpg') {
            $oldPath = public_path('storage/avatars/' . $user->avatar);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Store new avatar directly to public/storage/avatars/
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($avatarDir, $filename);

        $user->avatar = $filename;
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Cập nhật ảnh đại diện thành công!');
    }

    public function submitHoSo(Request $request)
    {
        $user = Auth::user();

        // Check if user already has an HR record
        if (NhanSu::where('user_id', $user->id)->exists()) {
            return redirect()->route('profile.index')->with('error', 'Bạn đã có hồ sơ nhân sự!');
        }

        // Parse date fields dd/mm/yyyy -> Y-m-d
        $dateFields = ['NgaySinh', 'NgayCapCCCD', 'NgayKyHDTV', 'NgayHetHanHDTV', 'NgayKyHDXDTH', 'NgayHetHanHDXDTH', 'NgayKyHDKXD'];
        foreach ($dateFields as $field) {
            $val = $request->input($field);
            if ($val && preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $val, $m)) {
                $request->merge([$field => $m[3] . '-' . $m[2] . '-' . $m[1]]);
            } elseif (empty($val)) {
                $request->merge([$field => null]);
            }
        }

        $request->validate([
            'HoTen' => 'required|string|max:191',
        ], [
            'HoTen.required' => 'Họ tên không được để trống.',
        ]);

        $data = $request->only([
            'HoTen', 'NgaySinh', 'GioiTinh', 'SoCCCD', 'NgayCapCCCD', 'NoiCapCCCD',
            'SDT', 'Email', 'ThuongTru', 'DiaChiHienTai',
            'TrinhDoHocVan', 'TruongDaoTao', 'ChuyenNganh', 'NamTotNghiep',
            'LoaiHD', 'NgayKyHDTV', 'NgayHetHanHDTV', 'NgayKyHDXDTH', 'NgayHetHanHDXDTH', 'NgayKyHDKXD',
            'SoSoBHXH', 'MSTCaNhan', 'STKNganHang',
        ]);
        $data['user_id'] = $user->id;

        NhanSu::create($data);

        return redirect()->route('profile.index')->with('success', 'Gửi hồ sơ cá nhân thành công!');
    }

    /**
     * Trang chấm công riêng cho nhân viên
     */
    public function checkinPage()
    {
        $user = Auth::user();
        $todayChamCong = ChamCong::where('user_id', $user->id)
            ->where('ngay', now()->toDateString())
            ->first();
        $settings = DB::table('checkin_settings')->pluck('value', 'key');

        return view('main.checkin.index', compact('user', 'todayChamCong', 'settings'));
    }

    /**
     * Check-in chấm công (với bảo mật 3 lớp)
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // 0. Kiểm tra khung giờ check-in
        $settings = DB::table('checkin_settings')->pluck('value', 'key');
        $checkinMo = $settings['checkin_mo'] ?? '06:00';
        $checkinDong = $settings['checkin_dong'] ?? '22:00';
        $nowTime = now()->format('H:i');
        if ($nowTime < $checkinMo || $nowTime > $checkinDong) {
            return response()->json([
                'ok' => false,
                'message' => 'Ngoài khung giờ check-in cho phép (' . $checkinMo . ' - ' . $checkinDong . ').',
            ], 403);
        }

        // 1. Kiểm tra User-Agent: chặn mobile
        $ua = $request->header('User-Agent', '');
        $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'Opera Mini', 'IEMobile'];
        foreach ($mobileKeywords as $kw) {
            if (stripos($ua, $kw) !== false) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Chỉ được check-in trên máy tính. Vui lòng sử dụng trình duyệt trên PC.',
                ], 403);
            }
        }

        // 2. Kiểm tra IP whitelist
        $whitelist = DB::table('checkin_ip_whitelist')->where('ngay', $today)->first();
        if (!$whitelist) {
            return response()->json([
                'ok' => false,
                'message' => 'Chưa có IP chấm công hôm nay. Vui lòng liên hệ Nhân Sự/Admin.',
            ], 403);
        }
        $clientIp = $request->ip();
        if ($clientIp !== $whitelist->wan_ip) {
            return response()->json([
                'ok' => false,
                'message' => 'IP của bạn (' . $clientIp . ') không khớp với IP văn phòng. Vui lòng check-in tại nơi làm việc.',
            ], 403);
        }

        // 3. Kiểm tra device hash: 1 PC = 1 tài khoản/ngày
        $deviceHash = $request->input('device_hash', '');
        if (empty($deviceHash)) {
            return response()->json([
                'ok' => false,
                'message' => 'Không thể xác định thiết bị. Vui lòng cho phép trình duyệt truy cập thông tin thiết bị.',
            ], 403);
        }

        $existingDevice = DB::table('checkin_devices')
            ->where('device_hash', $deviceHash)
            ->where('ngay', $today)
            ->first();

        if ($existingDevice && $existingDevice->user_id != $user->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Máy tính này đã được sử dụng để check-in bởi tài khoản khác hôm nay.',
            ], 403);
        }

        // Kiểm tra đã check-in chưa
        $existing = ChamCong::where('user_id', $user->id)->where('ngay', $today)->first();
        if ($existing && $existing->gio_vao) {
            return response()->json([
                'ok' => false,
                'message' => 'Bạn đã check-in hôm nay lúc ' . substr($existing->gio_vao, 0, 5) . '.',
            ], 400);
        }

        // Tạo/cập nhật chấm công
        $record = ChamCong::updateOrCreate(
            ['user_id' => $user->id, 'ngay' => $today],
            ['trang_thai' => 'Đi Làm', 'gio_vao' => now()->format('H:i')]
        );

        // Lưu device
        DB::table('checkin_devices')->updateOrInsert(
            ['device_hash' => $deviceHash, 'ngay' => $today],
            [
                'user_id' => $user->id,
                'ip_address' => $clientIp,
                'user_agent' => $ua,
                'created_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Check-in thành công lúc ' . now()->format('H:i') . '!',
            'gio_vao' => now()->format('H:i'),
        ]);
    }

    /**
     * Check-out chấm công
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Kiểm tra đã check-in chưa
        $record = ChamCong::where('user_id', $user->id)->where('ngay', $today)->first();
        if (!$record || !$record->gio_vao) {
            return response()->json([
                'ok' => false,
                'message' => 'Bạn chưa check-in hôm nay.',
            ], 400);
        }

        if ($record->gio_ra) {
            return response()->json([
                'ok' => false,
                'message' => 'Bạn đã check-out hôm nay lúc ' . substr($record->gio_ra, 0, 5) . '.',
            ], 400);
        }

        // Kiểm tra device hash phải cùng máy
        $deviceHash = $request->input('device_hash', '');
        if ($deviceHash) {
            $device = DB::table('checkin_devices')
                ->where('device_hash', $deviceHash)
                ->where('ngay', $today)
                ->where('user_id', $user->id)
                ->first();
            if (!$device) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Bạn phải check-out trên cùng máy tính đã check-in.',
                ], 403);
            }
        }

        $record->gio_ra = now()->format('H:i');
        $record->save();

        return response()->json([
            'ok' => true,
            'message' => 'Check-out thành công lúc ' . now()->format('H:i') . '!',
            'gio_ra' => now()->format('H:i'),
        ]);
    }
}
