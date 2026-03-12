<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    // 4 quyền cố định không thể sửa/xóa
    const FIXED_PERMISSIONS = ['Admin', 'Sale', 'Sale Manager', 'Kế Toán', 'Nhân Sự'];

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
     * Trang quản lý quyền hạn
     */
    public function index()
    {
        $this->checkPermission();

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('id')
            ->get();

        // Đếm số roles đang dùng mỗi permission
        $permissions->each(function ($perm) {
            $perm->roles_count = $perm->roles()->count();
            $perm->users_count = $perm->users()->count();
            $perm->is_fixed = in_array($perm->name, self::FIXED_PERMISSIONS);
        });

        // Lấy tất cả roles để hiển thị bảng ma trận
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('main.departments.permissions', compact('permissions', 'roles'));
    }

    /**
     * Thêm quyền hạn mới
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
        ], [
            'name.required' => 'Tên quyền hạn không được để trống.',
            'name.max' => 'Tên quyền hạn tối đa 100 ký tự.',
            'name.unique' => 'Quyền hạn này đã tồn tại.',
        ]);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return redirect()->route('permissions.index')->with('success', 'Thêm quyền hạn thành công!');
    }

    /**
     * Cập nhật quyền hạn
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission();

        $permission = Permission::findOrFail($id);

        // Không cho sửa 4 quyền cố định
        if (in_array($permission->name, self::FIXED_PERMISSIONS)) {
            return redirect()->route('permissions.index')->with('error', 'Không thể sửa quyền hạn cố định!');
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name,' . $id,
        ], [
            'name.required' => 'Tên quyền hạn không được để trống.',
            'name.unique' => 'Quyền hạn này đã tồn tại.',
        ]);

        $permission->update(['name' => $request->name]);

        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('success', 'Cập nhật quyền hạn thành công!');
    }

    /**
     * Xóa quyền hạn
     */
    public function destroy($id)
    {
        $this->checkPermission();

        $permission = Permission::findOrFail($id);

        // Không cho xóa 4 quyền cố định
        if (in_array($permission->name, self::FIXED_PERMISSIONS)) {
            return redirect()->route('permissions.index')->with('error', 'Không thể xóa quyền hạn cố định!');
        }

        $permission->delete();

        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permissions.index')->with('success', 'Xóa quyền hạn thành công!');
    }
}
