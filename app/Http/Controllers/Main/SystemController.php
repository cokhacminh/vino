<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    // ===========================
    // COMBO
    // ===========================

    public function combos()
    {
        $combos   = DB::table('combo')->orderBy('id')->get();
        $sanphams = DB::table('sanpham')->select('MaSP', 'TenSP')->orderBy('MaSP')->get();
        return view('main.system.combo', compact('combos', 'sanphams'));
    }

    public function storeCombo(Request $request)
    {
        if (!auth()->user()->can('Admin')) {
            return back()->with('error', 'Không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'TenCombo'   => 'required|string',
            'GiamGia'    => 'required|integer|min:0',
            'TinhTrang'  => 'required|in:Đang Hoạt Động,Tạm Dừng',
        ]);

        // Build YeuCau JSON from posted sp_ma[] & sp_qty[]
        $yeuCau = [];
        $spMas  = $request->input('sp_ma', []);
        $spQtys = $request->input('sp_qty', []);
        foreach ($spMas as $i => $ma) {
            if ($ma && isset($spQtys[$i]) && $spQtys[$i] > 0) {
                $yeuCau[$ma] = (string)$spQtys[$i];
            }
        }

        DB::table('combo')->insert([
            'TenCombo'  => $request->TenCombo,
            'YeuCau'    => json_encode($yeuCau, JSON_UNESCAPED_UNICODE),
            'GiamGia'   => $request->GiamGia,
            'TinhTrang' => $request->TinhTrang,
        ]);

        return back()->with('success', 'Thêm combo thành công.');
    }

    public function updateCombo(Request $request, $id)
    {
        if (!auth()->user()->can('Admin')) {
            return back()->with('error', 'Không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'TenCombo'  => 'required|string',
            'GiamGia'   => 'required|integer|min:0',
            'TinhTrang' => 'required|in:Đang Hoạt Động,Tạm Dừng',
        ]);

        $yeuCau = [];
        $spMas  = $request->input('sp_ma', []);
        $spQtys = $request->input('sp_qty', []);
        foreach ($spMas as $i => $ma) {
            if ($ma && isset($spQtys[$i]) && $spQtys[$i] > 0) {
                $yeuCau[$ma] = (string)$spQtys[$i];
            }
        }

        DB::table('combo')->where('id', $id)->update([
            'TenCombo'  => $request->TenCombo,
            'YeuCau'    => json_encode($yeuCau, JSON_UNESCAPED_UNICODE),
            'GiamGia'   => $request->GiamGia,
            'TinhTrang' => $request->TinhTrang,
        ]);

        return back()->with('success', 'Cập nhật combo thành công.');
    }

    public function destroyCombo($id)
    {
        if (!auth()->user()->can('Admin')) {
            return back()->with('error', 'Không có quyền thực hiện thao tác này.');
        }
        DB::table('combo')->where('id', $id)->delete();
        return back()->with('success', 'Xóa combo thành công.');
    }

    // ===========================
    // KHUYẾN MÃI
    // ===========================

    public function khuyenMai()
    {
        $khuyenmais = DB::table('khuyenmai')->orderBy('id')->get();
        $sanphams   = DB::table('sanpham')->select('MaSP', 'TenSP')->orderBy('MaSP')->get();
        return view('main.system.khuyen_mai', compact('khuyenmais', 'sanphams'));
    }

    public function storeKhuyenMai(Request $request)
    {
        if (!auth()->user()->can('Admin')) {
            return back()->with('error', 'Không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'TenChuongTrinh' => 'required|string',
            'TinhTrang'      => 'required|in:Đang Hoạt Động,Tạm Dừng',
        ]);

        // YeuCau = JSON object {MaSP: qty}
        $ycMas  = $request->input('yc_ma', []);
        $ycQtys = $request->input('yc_qty', []);
        $yeuCau = [];
        foreach ($ycMas as $i => $ma) {
            if ($ma && isset($ycQtys[$i]) && $ycQtys[$i] > 0) {
                $yeuCau[$ma] = (string)$ycQtys[$i];
            }
        }

        // QuaTang = JSON object {MaSP: qty}
        $qtMas  = $request->input('qt_ma', []);
        $qtQtys = $request->input('qt_qty', []);
        $quaTang = [];
        foreach ($qtMas as $i => $ma) {
            if ($ma && isset($qtQtys[$i]) && $qtQtys[$i] > 0) {
                $quaTang[$ma] = (string)$qtQtys[$i];
            }
        }

        DB::table('khuyenmai')->insert([
            'TenChuongTrinh' => $request->TenChuongTrinh,
            'YeuCau'         => json_encode($yeuCau, JSON_UNESCAPED_UNICODE),
            'YeuCau_SoLuong' => array_sum(array_map('intval', array_values($yeuCau))),
            'QuaTang'        => json_encode($quaTang, JSON_UNESCAPED_UNICODE),
            'TinhTrang'      => $request->TinhTrang,
        ]);

        return back()->with('success', 'Thêm khuyến mãi thành công.');
    }

    public function updateKhuyenMai(Request $request, $id)
    {
        if (!auth()->user()->can('Admin')) {
            return back()->with('error', 'Không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'TenChuongTrinh' => 'required|string',
            'TinhTrang'      => 'required|in:Đang Hoạt Động,Tạm Dừng',
        ]);

        // YeuCau = JSON object {MaSP: qty}
        $ycMas  = $request->input('yc_ma', []);
        $ycQtys = $request->input('yc_qty', []);
        $yeuCau = [];
        foreach ($ycMas as $i => $ma) {
            if ($ma && isset($ycQtys[$i]) && $ycQtys[$i] > 0) {
                $yeuCau[$ma] = (string)$ycQtys[$i];
            }
        }

        $qtMas  = $request->input('qt_ma', []);
        $qtQtys = $request->input('qt_qty', []);
        $quaTang = [];
        foreach ($qtMas as $i => $ma) {
            if ($ma && isset($qtQtys[$i]) && $qtQtys[$i] > 0) {
                $quaTang[$ma] = (string)$qtQtys[$i];
            }
        }

        DB::table('khuyenmai')->where('id', $id)->update([
            'TenChuongTrinh' => $request->TenChuongTrinh,
            'YeuCau'         => json_encode($yeuCau, JSON_UNESCAPED_UNICODE),
            'YeuCau_SoLuong' => array_sum(array_map('intval', array_values($yeuCau))),
            'QuaTang'        => json_encode($quaTang, JSON_UNESCAPED_UNICODE),
            'TinhTrang'      => $request->TinhTrang,
        ]);

        return back()->with('success', 'Cập nhật khuyến mãi thành công.');
    }

    public function destroyKhuyenMai($id)
    {
        if (!auth()->user()->can('Admin')) {
            return back()->with('error', 'Không có quyền thực hiện thao tác này.');
        }
        DB::table('khuyenmai')->where('id', $id)->delete();
        return back()->with('success', 'Xóa khuyến mãi thành công.');
    }

    // ===========================
    // API GIAO HÀNG
    // ===========================

    public function apiGiaoHang()
    {
        if (auth()->id() != 1) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $rows = DB::table('api_configs')
            ->whereIn('config_key', ['token_ghtk', 'token_ghn', 'shopid_ghn', 'hotline'])
            ->pluck('config_value', 'config_key');

        $configs = [
            'token_ghtk' => $rows['token_ghtk'] ?? '',
            'token_ghn'  => $rows['token_ghn'] ?? '',
            'shopid_ghn' => $rows['shopid_ghn'] ?? '',
            'hotline'    => $rows['hotline'] ?? '',
        ];

        return view('main.system.api_giao_hang', compact('configs'));
    }

    public function updateApiGiaoHang(Request $request)
    {
        if (auth()->id() != 1) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $provider = $request->input('provider');
        $keys = [];

        if ($provider === 'ghtk') {
            $keys = ['token_ghtk'];
        } elseif ($provider === 'ghn') {
            $keys = ['token_ghn', 'shopid_ghn'];
        } elseif ($provider === 'hotline') {
            $keys = ['hotline'];
        }

        foreach ($keys as $key) {
            DB::table('api_configs')->updateOrInsert(
                ['config_key' => $key],
                ['config_value' => $request->input($key, ''), 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Cập nhật cấu hình ' . strtoupper($provider) . ' thành công.');
    }
}
