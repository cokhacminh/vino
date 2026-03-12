<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\NhanSu;
use App\Models\User;
use App\Models\ChamCong;
use App\Models\KpiUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NhanSuController extends Controller
{
    private $dateFields = [
        'NgaySinh', 'NgayCapCCCD',
        'NgayKyHDTV', 'NgayHetHanHDTV',
        'NgayKyHDXDTH', 'NgayHetHanHDXDTH',
        'NgayKyHDKXD',
    ];

    private function checkPermission()
    {
        $user = Auth::user();
        if (!$user) abort(403, 'Bạn không có quyền truy cập trang này.');
        if ($user->hasRole('Admin') || $user->hasRole('Nhân Sự') || $user->can('Admin') || $user->can('Nhân Sự')) {
            return;
        }
        abort(403, 'Bạn không có quyền truy cập trang này.');
    }

    /**
     * Convert dd/mm/yyyy date fields to Y-m-d for database storage
     */
    private function parseDates(Request $request)
    {
        foreach ($this->dateFields as $field) {
            $val = $request->input($field);
            if ($val && preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $val, $m)) {
                $request->merge([$field => $m[3] . '-' . $m[2] . '-' . $m[1]]);
            } elseif (empty($val)) {
                $request->merge([$field => null]);
            }
        }
    }

    public function index()
    {
        $this->checkPermission();

        $nhansuList = NhanSu::with('user')->orderBy('HoTen', 'asc')->get();
        $users = User::orderBy('name', 'asc')->get();
        $phongbanList = DB::table('phongban')->orderBy('TenPB', 'asc')->get();

        return view('main.nhansu.index', compact('nhansuList', 'users', 'phongbanList'));
    }

    public function store(Request $request)
    {
        $this->checkPermission();
        $this->parseDates($request);

        $request->validate([
            'HoTen' => 'required|string|max:191',
            'user_id' => 'nullable|exists:users,id|unique:nhansu,user_id',
        ], [
            'HoTen.required' => 'Họ tên không được để trống.',
            'user_id.unique' => 'Tài khoản này đã được liên kết với nhân sự khác.',
        ]);

        NhanSu::create($request->only([
            'user_id',
            'HoTen', 'NgaySinh', 'GioiTinh', 'SoCCCD', 'NgayCapCCCD', 'NoiCapCCCD',
            'SDT', 'Email', 'ThuongTru', 'DiaChiHienTai',
            'TrinhDoHocVan', 'TruongDaoTao', 'ChuyenNganh', 'NamTotNghiep',
            'LoaiHD', 'NgayKyHDTV', 'NgayHetHanHDTV', 'NgayKyHDXDTH', 'NgayHetHanHDXDTH', 'NgayKyHDKXD',
            'SoSoBHXH', 'MSTCaNhan', 'STKNganHang',
        ]));

        return redirect()->route('nhansu.index')->with('success', 'Thêm nhân sự thành công!');
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission();
        $this->parseDates($request);

        $nhansu = NhanSu::findOrFail($id);

        $request->validate([
            'HoTen' => 'required|string|max:191',
            'user_id' => 'nullable|exists:users,id|unique:nhansu,user_id,' . $id,
        ], [
            'HoTen.required' => 'Họ tên không được để trống.',
            'user_id.unique' => 'Tài khoản này đã được liên kết với nhân sự khác.',
        ]);

        $nhansu->update($request->only([
            'user_id',
            'HoTen', 'NgaySinh', 'GioiTinh', 'SoCCCD', 'NgayCapCCCD', 'NoiCapCCCD',
            'SDT', 'Email', 'ThuongTru', 'DiaChiHienTai',
            'TrinhDoHocVan', 'TruongDaoTao', 'ChuyenNganh', 'NamTotNghiep',
            'LoaiHD', 'NgayKyHDTV', 'NgayHetHanHDTV', 'NgayKyHDXDTH', 'NgayHetHanHDXDTH', 'NgayKyHDKXD',
            'SoSoBHXH', 'MSTCaNhan', 'STKNganHang',
        ]));

        return redirect()->route('nhansu.index')->with('success', 'Cập nhật nhân sự thành công!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || (!$user->hasRole('Admin') && !$user->can('Admin'))) {
            abort(403, 'Chỉ Admin mới có quyền xóa nhân sự.');
        }

        $nhansu = NhanSu::findOrFail($id);
        $nhansu->delete();

        return redirect()->route('nhansu.index')->with('success', 'Xóa nhân sự thành công!');
    }

    public function kpiStats(Request $request)
    {
        $this->checkPermission();

        $thang = (int) $request->get('thang', now()->month);
        $nam = (int) $request->get('nam', now()->year);
        $search = $request->get('search', '');

        // Get all active users with phòng ban + chức vụ (loại trừ tài khoản Admin)
        $usersQuery = User::where('TinhTrang', 'Active')
            ->whereDoesntHave('permissions', function ($q) {
                $q->where('name', 'Admin');
            })
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            })
            ->leftJoin('phongban', 'users.MaPB', '=', 'phongban.MaPB')
            ->leftJoin('chucvu', 'users.MaCV', '=', 'chucvu.MaCV')
            ->select('users.id', 'users.name', 'phongban.TenPB', 'chucvu.TenCV')
            ->orderBy('phongban.TenPB')
            ->orderBy('users.name');

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('phongban.TenPB', 'like', "%{$search}%")
                  ->orWhere('chucvu.TenCV', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->get();

        // Get all kpi_users for this month/year
        $allKpiUsers = KpiUser::where('thang', $thang)
            ->where('nam', $nam)
            ->get()
            ->groupBy('user_id');

        // Build stats per user
        $userStats = [];
        foreach ($users as $user) {
            $kpis = $allKpiUsers->get($user->id, collect());
            $total = $kpis->count();

            $hoanThanh = $kpis->filter(function ($ku) {
                return $ku->trang_thai === 'Hợp Lệ'
                    && $ku->reported_at && $ku->deadline_time
                    && Carbon::parse($ku->reported_at)->startOfDay()->lte(Carbon::parse($ku->deadline_time));
            })->count();

            $treDeadline = $kpis->filter(function ($ku) {
                return $ku->trang_thai === 'Hợp Lệ'
                    && $ku->reported_at && $ku->deadline_time
                    && Carbon::parse($ku->reported_at)->startOfDay()->gt(Carbon::parse($ku->deadline_time));
            })->count();

            $dat = $kpis->where('danh_gia', 'Đạt KPI')->count();
            $khongDat = $kpis->where('danh_gia', 'Không Đạt')->count();
            $vuot = $kpis->where('danh_gia', 'Vượt KPI')->count();

            $userStats[] = [
                'user' => $user,
                'total' => $total,
                'hoan_thanh' => $hoanThanh,
                'tre_deadline' => $treDeadline,
                'dat' => $dat,
                'khong_dat' => $khongDat,
                'vuot' => $vuot,
            ];
        }

        return view('main.nhansu.kpi_stats', compact('userStats', 'thang', 'nam', 'search'));
    }

    public function chamCong(Request $request)
    {
        $this->checkPermission();

        $thang = (int) $request->get('thang', now()->month);
        $nam = (int) $request->get('nam', now()->year);

        // Days in month
        $daysInMonth = Carbon::createFromDate($nam, $thang, 1)->daysInMonth;

        // Active non-Admin users
        $users = User::where('TinhTrang', 'Active')
            ->leftJoin('phongban', 'users.MaPB', '=', 'phongban.MaPB')
            ->leftJoin('chucvu', 'users.MaCV', '=', 'chucvu.MaCV')
            ->select('users.id', 'users.name', 'phongban.TenPB', 'chucvu.TenCV')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            })
            ->whereDoesntHave('permissions', function ($q) {
                $q->where('name', 'Admin');
            })
            ->orderBy('phongban.TenPB')
            ->orderBy('users.name')
            ->get();

        // Attendance records for this month
        $startDate = Carbon::createFromDate($nam, $thang, 1)->toDateString();
        $endDate = Carbon::createFromDate($nam, $thang, $daysInMonth)->toDateString();

        $records = ChamCong::whereBetween('ngay', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($r) {
                return $r->user_id . '-' . $r->ngay;
            })
            ->map(fn($g) => $g->first());

        // Lấy IP whitelist hôm nay
        $todayIp = DB::table('checkin_ip_whitelist')->where('ngay', now()->toDateString())->first();

        // Lấy thiết lập chấm công
        $settings = DB::table('checkin_settings')->pluck('value', 'key');
        $isAdmin = Auth::user()->hasRole('Admin') || Auth::user()->can('Admin');

        return view('main.nhansu.cham_cong', compact('users', 'records', 'thang', 'nam', 'daysInMonth', 'todayIp', 'settings', 'isAdmin'));
    }

    public function chamCongToggle(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'user_id' => 'required|integer',
            'ngay' => 'required|date',
            'trang_thai' => 'nullable|string',
            'gio_vao' => 'nullable|date_format:H:i',
            'gio_ra' => 'nullable|date_format:H:i',
            'ghi_chu' => 'nullable|string|max:500',
        ]);

        $userId = $request->input('user_id');
        $ngay = $request->input('ngay');
        $trangThai = $request->input('trang_thai');

        if (empty($trangThai)) {
            ChamCong::where('user_id', $userId)->where('ngay', $ngay)->delete();
            return response()->json(['ok' => true, 'trang_thai' => null]);
        }

        $data = ['trang_thai' => $trangThai];
        if ($request->has('gio_vao')) $data['gio_vao'] = $request->input('gio_vao') ?: null;
        if ($request->has('gio_ra')) $data['gio_ra'] = $request->input('gio_ra') ?: null;
        if ($request->has('ghi_chu')) $data['ghi_chu'] = $request->input('ghi_chu') ?: null;

        $record = ChamCong::updateOrCreate(
            ['user_id' => $userId, 'ngay' => $ngay],
            $data
        );

        return response()->json([
            'ok' => true,
            'trang_thai' => $record->trang_thai,
            'gio_vao' => $record->gio_vao ? substr($record->gio_vao, 0, 5) : null,
            'gio_ra' => $record->gio_ra ? substr($record->gio_ra, 0, 5) : null,
            'ghi_chu' => $record->ghi_chu,
        ]);
    }

    /**
     * Cập nhật IP whitelist chấm công (Admin / Nhân Sự)
     */
    public function updateCheckinIp(Request $request)
    {
        $this->checkPermission();

        $wanIp = $request->ip();
        $lanIp = $request->input('lan_ip', '');
        $ngay = now()->toDateString();

        DB::table('checkin_ip_whitelist')->updateOrInsert(
            ['ngay' => $ngay],
            [
                'wan_ip' => $wanIp,
                'lan_ip' => $lanIp,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
            'wan_ip' => $wanIp,
            'lan_ip' => $lanIp,
            'message' => 'Cập nhật IP chấm công thành công!',
        ]);
    }

    /**
     * Lưu thiết lập thời gian chấm công (chỉ Admin)
     */
    public function saveCheckinSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->hasRole('Admin') && !$user->can('Admin'))) {
            return response()->json(['ok' => false, 'message' => 'Chỉ Admin mới có quyền thiết lập.'], 403);
        }

        $keys = ['gio_bat_dau', 'gio_ket_thuc', 'gio_tre_han', 'gio_som_han', 'checkin_mo', 'checkin_dong'];
        foreach ($keys as $key) {
            $val = $request->input($key);
            if ($val !== null) {
                DB::table('checkin_settings')->where('key', $key)->update([
                    'value' => $val,
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json(['ok' => true, 'message' => 'Lưu thiết lập thành công!']);
    }

    /**
     * API lấy thiết lập chấm công (cho trang checkin)
     */
    public function getCheckinSettings()
    {
        $settings = DB::table('checkin_settings')->pluck('value', 'key');
        return response()->json($settings);
    }
}
