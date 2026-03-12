<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Chỉ Admin và Kế Toán mới xem được
        if (!in_array($user->Permission, ['Admin', 'Kế Toán'])) {
            return redirect()->route('dashboard')->with('error', 'Không có quyền truy cập');
        }

        $accounts = DB::table('users')->orderBy('name')->get();

        // Non-admin users cannot see Admin accounts
        if ($user->Permission !== 'Admin') {
            $accounts = $accounts->filter(fn($a) => $a->Permission !== 'Admin');
        }

        return view('main.accounts.index', compact('accounts', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->Permission, ['Admin', 'Kế Toán'])) {
            return back()->with('error', 'Không có quyền');
        }

        $request->validate([
            'name' => 'required|string|max:191',
            'username' => 'required|string|max:191|unique:users,username',
            'password' => 'required|string|min:4',
            'Permission' => 'required|string',
        ]);

        // Chỉ Admin mới được tạo tài khoản Admin
        $permission = $request->input('Permission');
        if ($permission === 'Admin' && $user->Permission !== 'Admin') {
            return back()->with('error', 'Chỉ Admin mới được tạo tài khoản Admin');
        }

        DB::table('users')->insert([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email', ''),
            'password' => Hash::make($request->input('password')),
            'Permission' => $permission,
            'TinhTrang' => 'Đang Làm Việc',
        ]);

        return redirect()->route('accounts.index')->with('success', 'Tạo tài khoản thành công');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->Permission, ['Admin', 'Kế Toán'])) {
            return response()->json(['success' => false, 'message' => 'Không có quyền']);
        }

        $target = DB::table('users')->where('id', $id)->first();
        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản']);
        }

        // Không cho phép Kế Toán sửa tài khoản Admin
        if ($target->Permission === 'Admin' && $user->Permission !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Không có quyền sửa tài khoản Admin']);
        }

        $data = [];
        if ($request->filled('name')) $data['name'] = $request->input('name');
        if ($request->filled('email')) $data['email'] = $request->input('email');
        if ($request->filled('password')) $data['password'] = Hash::make($request->input('password'));
        if ($request->filled('TinhTrang')) $data['TinhTrang'] = $request->input('TinhTrang');

        if ($request->filled('Permission')) {
            $newPerm = $request->input('Permission');
            if ($newPerm === 'Admin' && $user->Permission !== 'Admin') {
                return response()->json(['success' => false, 'message' => 'Chỉ Admin mới được gán quyền Admin']);
            }
            $data['Permission'] = $newPerm;
        }

        if (!empty($data)) {
            DB::table('users')->where('id', $id)->update($data);
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!in_array($user->Permission, ['Admin', 'Kế Toán'])) {
            return response()->json(['success' => false, 'message' => 'Không có quyền']);
        }

        $target = DB::table('users')->where('id', $id)->first();
        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy']);
        }

        if ($target->Permission === 'Admin' && $user->Permission !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa tài khoản Admin']);
        }

        if ($id == $user->id) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa chính mình']);
        }

        // Vô hiệu hóa thay vì xóa
        DB::table('users')->where('id', $id)->update(['TinhTrang' => 'Đã Nghỉ Việc']);

        return response()->json(['success' => true, 'message' => 'Đã vô hiệu hóa tài khoản']);
    }

    public function impersonate($id)
    {
        $user = Auth::user();
        if ($user->Permission !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Chỉ Admin mới được sử dụng tính năng này']);
        }

        $target = DB::table('users')->where('id', $id)->first();
        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản']);
        }

        if ($id == $user->id) {
            return response()->json(['success' => false, 'message' => 'Bạn đang dùng tài khoản này']);
        }

        // Đăng xuất admin, đăng nhập tài khoản target
        Auth::logout();
        Auth::loginUsingId($id);
        request()->session()->regenerate();

        return response()->json(['success' => true, 'message' => 'Đã chuyển sang tài khoản ' . $target->name]);
    }
}
