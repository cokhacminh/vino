<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->TinhTrang !== 'Đang Làm Việc') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'username' => 'Tài khoản đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.',
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ])->withInput($request->only('username', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function quickAccessPage()
    {
        $users = \Illuminate\Support\Facades\DB::table('users')
            ->where('TinhTrang', 'Đang Làm Việc')
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'Permission', 'TinhTrang']);

        // Map Permission to permissions array for view compatibility
        foreach ($users as $u) {
            $u->permissions = $u->Permission ? [$u->Permission] : [];
        }

        return view('auth.quick-access', compact('users'));
    }

    public function quickAccessLogin(Request $request)
    {
        $userId = $request->input('user_id');
        $user = \App\Models\User::find($userId);

        if (!$user || $user->TinhTrang !== 'Đang Làm Việc') {
            return back()->with('error', 'Tài khoản không hợp lệ.');
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }
}
