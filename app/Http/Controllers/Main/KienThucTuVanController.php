<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KienThucTuVanController extends Controller
{
    public function index(Request $request)
    {
        $phanLoai = $request->input('phan_loai', '');
        $search = $request->input('search', '');

        $query = DB::table('kien_thuc_tu_van');

        if ($phanLoai) {
            $query->where('PhanLoai', $phanLoai);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('CauHoiTinhHuong', 'like', "%{$search}%")
                  ->orWhere('CachTuVan', 'like', "%{$search}%");
            });
        }

        $items = $query->orderByDesc('id')->get();

        return view('main.kienthuc.index', compact('items', 'phanLoai', 'search'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->can('Admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền thêm nội dung.');
        }

        $request->validate([
            'PhanLoai' => 'required|in:Tình Huống,Tôm Giống,Thủy Sản,Vi Sinh,Vật Tư',
            'CauHoiTinhHuong' => 'required|string',
            'CachTuVan' => 'required|string',
        ]);

        DB::table('kien_thuc_tu_van')->insert([
            'PhanLoai' => $request->input('PhanLoai'),
            'CauHoiTinhHuong' => $request->input('CauHoiTinhHuong'),
            'CachTuVan' => $request->input('CachTuVan'),
        ]);

        return redirect()->route('kienThucTuVan.index')->with('success', 'Thêm kiến thức thành công!');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->can('Admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền sửa nội dung.');
        }

        $request->validate([
            'PhanLoai' => 'required|in:Tình Huống,Tôm Giống,Thủy Sản,Vi Sinh,Vật Tư',
            'CauHoiTinhHuong' => 'required|string',
            'CachTuVan' => 'required|string',
        ]);

        DB::table('kien_thuc_tu_van')->where('id', $id)->update([
            'PhanLoai' => $request->input('PhanLoai'),
            'CauHoiTinhHuong' => $request->input('CauHoiTinhHuong'),
            'CachTuVan' => $request->input('CachTuVan'),
        ]);

        return redirect()->route('kienThucTuVan.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user || !$user->can('Admin')) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa nội dung.');
        }

        DB::table('kien_thuc_tu_van')->where('id', $id)->delete();

        return redirect()->route('kienThucTuVan.index')->with('success', 'Đã xóa thành công!');
    }
}
