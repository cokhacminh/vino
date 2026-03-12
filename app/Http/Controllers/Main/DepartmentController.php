<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\PhongBan;
use App\Models\ChucVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DepartmentController extends Controller
{
    private function checkPermission()
    {
        $user = Auth::user();
        if (!$user) abort(403, 'Bạn không có quyền truy cập trang này.');
        if ($user->hasRole('Admin') || $user->can('Admin')) {
            return;
        }
        abort(403, 'Bạn không có quyền truy cập trang này.');
    }

    /**
     * Trang chính - Phòng Ban & Chức Vụ
     */
    public function index()
    {
        $this->checkPermission();

        $phongBans = PhongBan::withCount('chucVus')->orderBy('MaPB')->get();
        $chucVus = ChucVu::with('phongBan')->orderBy('MaCV')->get();

        // Danh sách quyền hạn từ Spatie
        $danhSachQuyen = Permission::orderBy('name')->pluck('name')->toArray();

        // Gắn permissions từ Spatie role cho mỗi chức vụ
        $chucVus->each(function ($cv) {
            $role = Role::where('name', $cv->TenCV)->first();
            $cv->spatiePermissions = $role ? $role->permissions->pluck('name')->toArray() : [];
        });

        return view('main.departments.index', compact('phongBans', 'chucVus', 'danhSachQuyen'));
    }

    // ==========================================
    // PHÒNG BAN CRUD
    // ==========================================

    public function storePhongBan(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'TenPB' => 'required|string|max:200|unique:phongban,TenPB',
        ], [
            'TenPB.required' => 'Tên phòng ban không được để trống.',
            'TenPB.max' => 'Tên phòng ban không được quá 200 ký tự.',
            'TenPB.unique' => 'Tên phòng ban đã tồn tại.',
        ]);

        PhongBan::create([
            'TenPB' => $request->TenPB,
            'TrangThai' => 1,
        ]);

        return redirect()->route('departments.index')->with('success', 'Thêm phòng ban thành công!');
    }

    public function updatePhongBan(Request $request, $id)
    {
        $this->checkPermission();

        $request->validate([
            'TenPB' => 'required|string|max:200|unique:phongban,TenPB,' . $id . ',MaPB',
        ], [
            'TenPB.required' => 'Tên phòng ban không được để trống.',
            'TenPB.max' => 'Tên phòng ban không được quá 200 ký tự.',
            'TenPB.unique' => 'Tên phòng ban đã tồn tại.',
        ]);

        $phongBan = PhongBan::findOrFail($id);
        $phongBan->update([
            'TenPB' => $request->TenPB,
            'TrangThai' => $request->has('TrangThai') ? $request->TrangThai : $phongBan->TrangThai,
        ]);

        return redirect()->route('departments.index')->with('success', 'Cập nhật phòng ban thành công!');
    }

    public function destroyPhongBan($id)
    {
        $this->checkPermission();

        $phongBan = PhongBan::findOrFail($id);

        if ($phongBan->chucVus()->count() > 0) {
            return redirect()->route('departments.index')->with('error', 'Không thể xóa phòng ban có chức vụ liên kết! Vui lòng xóa các chức vụ trước.');
        }

        $phongBan->delete();
        return redirect()->route('departments.index')->with('success', 'Xóa phòng ban thành công!');
    }

    // ==========================================
    // CHỨC VỤ CRUD (+ Spatie Role sync)
    // ==========================================

    public function storeChucVu(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'TenCV' => 'required|string|max:50',
            'MaPB' => 'required|exists:phongban,MaPB',
        ], [
            'TenCV.required' => 'Tên chức vụ không được để trống.',
            'TenCV.max' => 'Tên chức vụ không được quá 50 ký tự.',
            'MaPB.required' => 'Vui lòng chọn phòng ban.',
            'MaPB.exists' => 'Phòng ban không hợp lệ.',
        ]);

        // Lưu vào bảng chucvu
        $chucVu = ChucVu::create([
            'TenCV' => $request->TenCV,
            'MaPB' => $request->MaPB,
            'TrangThai' => 1,
        ]);

        // Tạo Spatie Role và gán permissions
        $role = Role::firstOrCreate(['name' => $request->TenCV, 'guard_name' => 'web']);
        if ($request->has('QuyenHan')) {
            $role->syncPermissions($request->QuyenHan);
        }

        return redirect()->route('departments.index')->with('success', 'Thêm chức vụ thành công!');
    }

    public function updateChucVu(Request $request, $id)
    {
        $this->checkPermission();

        $request->validate([
            'TenCV' => 'required|string|max:50',
            'MaPB' => 'required|exists:phongban,MaPB',
        ], [
            'TenCV.required' => 'Tên chức vụ không được để trống.',
            'TenCV.max' => 'Tên chức vụ không được quá 50 ký tự.',
            'MaPB.required' => 'Vui lòng chọn phòng ban.',
            'MaPB.exists' => 'Phòng ban không hợp lệ.',
        ]);

        $chucVu = ChucVu::findOrFail($id);
        $oldName = $chucVu->TenCV;

        $chucVu->update([
            'TenCV' => $request->TenCV,
            'MaPB' => $request->MaPB,
            'TrangThai' => $request->has('TrangThai') ? $request->TrangThai : $chucVu->TrangThai,
        ]);

        // Cập nhật Spatie Role
        $role = Role::where('name', $oldName)->first();
        if ($role) {
            // Đổi tên role nếu đổi tên chức vụ
            if ($oldName !== $request->TenCV) {
                $role->name = $request->TenCV;
                $role->save();
            }
            // Sync permissions
            if ($request->has('QuyenHan')) {
                $role->syncPermissions($request->QuyenHan);
            } else {
                $role->syncPermissions([]);
            }
        }

        return redirect()->route('departments.index')->with('success', 'Cập nhật chức vụ thành công!');
    }

    public function destroyChucVu($id)
    {
        $this->checkPermission();

        $chucVu = ChucVu::findOrFail($id);

        // Xóa Spatie Role tương ứng
        $role = Role::where('name', $chucVu->TenCV)->first();
        if ($role) {
            $role->delete();
        }

        $chucVu->delete();
        return redirect()->route('departments.index')->with('success', 'Xóa chức vụ thành công!');
    }

    // Legacy resource methods
    public function create() { return redirect()->route('departments.index'); }
    public function store(Request $request) { return $this->storePhongBan($request); }
    public function edit($id) { return redirect()->route('departments.index'); }
    public function update(Request $request, $id) { return $this->updatePhongBan($request, $id); }
    public function destroy($id) { return $this->destroyPhongBan($id); }
}
