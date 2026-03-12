<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\KpiUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    private function checkPermission()
    {
        $user = Auth::user();
        if (!$user) abort(403);
        if ($user->hasRole('Admin') || $user->hasRole('Nhân Sự') || $user->can('Admin') || $user->can('Nhân Sự')) {
            return;
        }
        abort(403, 'Bạn không có quyền truy cập.');
    }

    private function isAdmin()
    {
        $user = Auth::user();
        return $user && ($user->hasRole('Admin') || $user->can('Admin'));
    }

    public function index(Request $request)
    {
        $this->checkPermission();

        $phongbanList = DB::table('phongban')->where('TrangThai', 1)->orderBy('TenPB')->get();
        $chucvuList = DB::table('chucvu')->where('TrangThai', 1)->orderBy('TenCV')->get();
        $activeUsers = User::where('TinhTrang', 'Active')->orderBy('name')->get(['id', 'name', 'MaCV', 'MaPB']);

        $filterCV = $request->get('chucvu', '');

        $query = Kpi::query()->withCount('kpiUsers');

        if ($filterCV) {
            $query->where('MaCV', $filterCV);
        }

        $kpis = $query->with('targetUser')->orderBy('created_at', 'desc')->get();

        $cvMap = DB::table('chucvu')->pluck('TenCV', 'MaCV');

        // Deadline kpi_users: approaching deadline or past due ≤10 days
        $today = Carbon::today();
        $cutoff = $today->copy()->subDays(10);
        $deadlineKpiUsers = KpiUser::where('deadline_time', '>=', $cutoff->toDateString())
            ->with(['kpi', 'user'])
            ->orderByRaw("CASE WHEN deadline_time >= ? THEN 0 ELSE 1 END", [$today->toDateString()])
            ->orderBy('deadline_time', 'asc')
            ->get();

        return view('main.kpi.index', compact(
            'kpis', 'chucvuList', 'phongbanList', 'cvMap', 'activeUsers',
            'filterCV', 'deadlineKpiUsers'
        ));
    }

    public function store(Request $request)
    {
        $this->checkPermission();
        if (!$this->isAdmin()) {
            abort(403, 'Chỉ Admin có quyền thêm KPI.');
        }

        $request->validate([
            'loai_ap_dung' => 'required|in:Chức Vụ,Cá Nhân',
            'tan_suat' => 'required|in:Cố Định,Hàng Tháng',
            'MaCV' => 'required_if:loai_ap_dung,Chức Vụ|nullable|integer',
            'target_user_id' => 'required_if:loai_ap_dung,Cá Nhân|nullable|integer',
            'deadline' => 'required_if:tan_suat,Cố Định|nullable|date',
            'nam' => 'required_if:tan_suat,Hàng Tháng|nullable|integer|min:2020',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'nullable|string',
        ]);

        // Parse data based on frequency
        if ($request->tan_suat === 'Cố Định') {
            $deadline = $request->deadline;
            $deadlineDate = Carbon::parse($deadline);
            $thang = $deadlineDate->month;
            $nam = $deadlineDate->year;
        } else {
            $deadline = null;
            $nam = (int) $request->nam;
            $thang = null;
        }

        // Chống tạo trùng
        $duplicate = Kpi::where('tieu_de', $request->tieu_de)
            ->where('nam', $nam)
            ->where('created_by', Auth::id())
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($duplicate) {
            return redirect()->route('kpi.index')->with('success', 'KPI đã được tạo!');
        }

        $kpi = Kpi::create([
            'MaCV' => $request->loai_ap_dung === 'Chức Vụ' ? $request->MaCV : null,
            'thang' => $thang,
            'nam' => $nam,
            'deadline' => $deadline,
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'created_by' => Auth::id(),
            'loai_ap_dung' => $request->loai_ap_dung,
            'target_user_id' => $request->loai_ap_dung === 'Cá Nhân' ? $request->target_user_id : null,
            'tan_suat' => $request->tan_suat,
        ]);

        $this->syncKpiUsers($kpi);

        return redirect()->route('kpi.index')->with('success', 'Thêm KPI thành công!');
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission();
        if (!$this->isAdmin()) {
            abort(403, 'Chỉ Admin có quyền sửa KPI.');
        }

        $kpi = Kpi::findOrFail($id);

        $request->validate([
            'loai_ap_dung' => 'required|in:Chức Vụ,Cá Nhân',
            'tan_suat' => 'required|in:Cố Định,Hàng Tháng',
            'MaCV' => 'required_if:loai_ap_dung,Chức Vụ|nullable|integer',
            'target_user_id' => 'required_if:loai_ap_dung,Cá Nhân|nullable|integer',
            'deadline' => 'required_if:tan_suat,Cố Định|nullable|date',
            'nam' => 'required_if:tan_suat,Hàng Tháng|nullable|integer|min:2020',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'nullable|string',
        ]);

        // Parse data based on frequency
        if ($request->tan_suat === 'Cố Định') {
            $deadline = $request->deadline;
            $deadlineDate = Carbon::parse($deadline);
            $thang = $deadlineDate->month;
            $nam = $deadlineDate->year;
        } else {
            $deadline = null;
            $nam = (int) $request->nam;
            $thang = null;
        }

        $kpi->update([
            'MaCV' => $request->loai_ap_dung === 'Chức Vụ' ? $request->MaCV : null,
            'thang' => $thang,
            'nam' => $nam,
            'deadline' => $deadline,
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'loai_ap_dung' => $request->loai_ap_dung,
            'target_user_id' => $request->loai_ap_dung === 'Cá Nhân' ? $request->target_user_id : null,
            'tan_suat' => $request->tan_suat,
        ]);

        // Sync users: add new, remove old, update deadline
        $this->syncKpiUsers($kpi);

        return redirect()->route('kpi.index')->with('success', 'Cập nhật KPI thành công!');
    }

    public function destroy($id)
    {
        $this->checkPermission();
        if (!$this->isAdmin()) {
            abort(403, 'Chỉ Admin có quyền xóa KPI.');
        }

        $kpi = Kpi::findOrFail($id);
        $kpi->delete();

        return redirect()->route('kpi.index')->with('success', 'Xóa KPI thành công!');
    }

    public function detail($id)
    {
        $this->checkPermission();

        $kpi = Kpi::findOrFail($id);

        $query = KpiUser::where('kpi_id', $id);

        if ($kpi->tan_suat === 'Hàng Tháng') {
            // Filter by month for recurring KPIs
            $thang = (int) request()->get('thang', now()->month);
            $nam = (int) request()->get('nam', $kpi->nam);

            $query->where('thang', $thang)->where('nam', $nam);
        }
        // For "Cố Định", show all users (no month filter)

        $kpiUsers = $query->with('user')
            ->get()
            ->map(function ($ku) {
                return [
                    'id' => $ku->id,
                    'user_id' => $ku->user_id,
                    'user_name' => $ku->user ? $ku->user->name : '—',
                    'bao_cao' => $ku->bao_cao,
                    'hinh_anh' => $ku->hinh_anh,
                    'trang_thai' => $ku->trang_thai,
                    'danh_gia' => $ku->danh_gia,
                    'ghi_chu' => $ku->ghi_chu,
                    'deadline_time' => $ku->deadline_time,
                    'updated_at' => $ku->updated_at ? $ku->updated_at->format('d/m/Y H:i') : null,
                ];
            });

        return response()->json([
            'kpi' => $kpi,
            'users' => $kpiUsers,
        ]);
    }

    public function evaluate(Request $request, $id)
    {
        $this->checkPermission();

        $kpiUser = KpiUser::findOrFail($id);

        $request->validate([
            'trang_thai' => 'required|in:Hợp Lệ,Báo Cáo Lại',
            'ghi_chu' => 'nullable|string',
            'danh_gia' => 'nullable|in:Không Đạt,Đạt KPI,Vượt KPI',
        ]);

        $data = [
            'trang_thai' => $request->trang_thai,
            'evaluated_by' => Auth::id(),
        ];

        // Only update ghi_chu if explicitly provided
        if ($request->has('ghi_chu')) {
            $data['ghi_chu'] = $request->ghi_chu;
        }

        if ($request->trang_thai === 'Hợp Lệ' && $request->danh_gia) {
            $data['danh_gia'] = $request->danh_gia;
        }

        $kpiUser->update($data);

        return response()->json(['success' => true, 'message' => 'Đánh giá thành công!']);
    }

    /**
     * Sync KPI users when creating or updating a KPI.
     * - Determines target user IDs based on loai_ap_dung
     * - Removes users no longer in scope
     * - Adds new users
     * - Updates deadline_time on all rows
     */
    private function syncKpiUsers(Kpi $kpi)
    {
        // Determine target user IDs
        $targetUserIds = $this->getTargetUserIds($kpi);

        if ($kpi->tan_suat === 'Cố Định') {
            // Fixed: one set of rows, deadline from KPI
            $deadlineTime = $kpi->deadline;

            // Remove users not in target list
            KpiUser::where('kpi_id', $kpi->id)
                ->whereNotIn('user_id', $targetUserIds)
                ->delete();

            // Get existing user IDs
            $existingUserIds = KpiUser::where('kpi_id', $kpi->id)->pluck('user_id');

            // Add new users
            $newUserIds = $targetUserIds->diff($existingUserIds);
            foreach ($newUserIds as $userId) {
                KpiUser::create([
                    'kpi_id' => $kpi->id,
                    'user_id' => $userId,
                    'thang' => $kpi->thang,
                    'nam' => $kpi->nam,
                    'deadline_time' => $deadlineTime,
                    'trang_thai' => 'Chưa Báo Cáo',
                ]);
            }

            // Update deadline_time on existing rows
            KpiUser::where('kpi_id', $kpi->id)
                ->update(['deadline_time' => $deadlineTime]);

        } else {
            // Hàng Tháng: create for current month
            $thang = now()->month;
            $nam = $kpi->nam;
            $deadlineTime = Carbon::create($nam, $thang)->endOfMonth()->toDateString();

            $this->syncKpiUsersForMonth($kpi, $thang, $nam);
        }
    }

    /**
     * Sync KPI users for a specific month (used by Hàng Tháng KPIs)
     */
    private function syncKpiUsersForMonth(Kpi $kpi, int $thang, int $nam)
    {
        $targetUserIds = $this->getTargetUserIds($kpi);
        $deadlineTime = Carbon::create($nam, $thang)->endOfMonth()->toDateString();

        // Remove users not in target for this month
        KpiUser::where('kpi_id', $kpi->id)
            ->where('thang', $thang)
            ->where('nam', $nam)
            ->whereNotIn('user_id', $targetUserIds)
            ->delete();

        // Get existing for this month
        $existingUserIds = KpiUser::where('kpi_id', $kpi->id)
            ->where('thang', $thang)
            ->where('nam', $nam)
            ->pluck('user_id');

        // Add new
        $newUserIds = $targetUserIds->diff($existingUserIds);
        foreach ($newUserIds as $userId) {
            KpiUser::create([
                'kpi_id' => $kpi->id,
                'user_id' => $userId,
                'thang' => $thang,
                'nam' => $nam,
                'deadline_time' => $deadlineTime,
                'trang_thai' => 'Chưa Báo Cáo',
            ]);
        }
    }

    /**
     * Get target user IDs based on KPI's loai_ap_dung
     */
    private function getTargetUserIds(Kpi $kpi)
    {
        if ($kpi->loai_ap_dung === 'Cá Nhân') {
            return $kpi->target_user_id
                ? collect([$kpi->target_user_id])
                : collect();
        }

        // Chức Vụ: all active users with matching MaCV
        return User::where('TinhTrang', 'Active')
            ->where('MaCV', $kpi->MaCV)
            ->pluck('id');
    }
}
