<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MigratePermissionsToSpatie extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ================================================
        // 1. Tạo Permissions từ bảng quyenhan
        // ================================================
        $quyenHans = DB::table('quyenhan')->get();
        foreach ($quyenHans as $qh) {
            Permission::firstOrCreate(['name' => $qh->TenQuyenHan, 'guard_name' => 'web']);
        }

        // Thêm permission "Admin" (không có trong bảng quyenhan nhưng dùng trong ThaoTac)
        Permission::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        echo "✅ Created " . Permission::count() . " permissions\n";

        // ================================================
        // 2. Tạo Roles từ bảng chucvu
        // ================================================
        $chucVus = DB::table('chucvu')->get();
        foreach ($chucVus as $cv) {
            $role = Role::firstOrCreate(['name' => $cv->TenCV, 'guard_name' => 'web']);

            // Gán permissions cho role
            if ($cv->TenCV === 'Admin') {
                // Admin = toàn quyền
                $role->syncPermissions(Permission::all());
            } elseif (!empty($cv->QuyenHan)) {
                $perms = array_map('trim', explode(',', $cv->QuyenHan));
                // Tạo permission nếu chưa có
                foreach ($perms as $perm) {
                    Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
                }
                $role->syncPermissions($perms);
            }

            echo "✅ Role '{$cv->TenCV}' → " . $role->permissions->count() . " permissions\n";
        }

        // ================================================
        // 3. Gán Roles + Permissions cho Users
        // ================================================
        $users = User::all();
        foreach ($users as $user) {
            // Gán role dựa trên MaCV
            $chucVu = DB::table('chucvu')->where('MaCV', $user->MaCV)->first();
            if ($chucVu) {
                $role = Role::where('name', $chucVu->TenCV)->first();
                if ($role) {
                    $user->syncRoles([$role]);
                }
            }

            // Gán permissions bổ sung từ ThaoTac (ngoài quyền của role)
            if (!empty($user->ThaoTac)) {
                $userPerms = array_unique(array_map('trim', explode(',', $user->ThaoTac)));
                foreach ($userPerms as $perm) {
                    Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
                }
                // Gán direct permissions (ngoài role)
                $user->syncPermissions($userPerms);
            }

            echo "✅ User '{$user->name}' → Role: " . ($chucVu->TenCV ?? 'N/A') . " + " . $user->permissions->count() . " direct permissions\n";
        }

        echo "\n🎉 Migration completed!\n";
        echo "Total Permissions: " . Permission::count() . "\n";
        echo "Total Roles: " . Role::count() . "\n";
        echo "Total Users: " . User::count() . "\n";
    }
}
