<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'products');
        $search = $request->input('search', '');

        $query = DB::table('sanpham as sp')
            ->leftJoin('danhmucsanpham as dm', 'dm.MaDMSP', '=', 'sp.DanhMucSP')
            ->select('sp.*', 'dm.TenDMSP');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sp.MaSP', 'like', "%$search%")
                  ->orWhere('sp.TenSP', 'like', "%$search%");
            });
        }

        $products = $query->orderBy('sp.MaSP')->paginate(50);

        $categories = DB::table('danhmucsanpham')->orderBy('TenDMSP')->get();
        $groups = DB::table('nhomsanpham')->orderBy('TenNhom')->get();

        return view('main.products.index', compact('products', 'categories', 'groups', 'search', 'tab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'MaSP' => 'required|string|unique:sanpham,MaSP',
            'TenSP' => 'required|string',
        ]);

        DB::table('sanpham')->insert([
            'MaSP' => $request->MaSP,
            'TenSP' => $request->TenSP,
            'MoTa' => $request->MoTa ?? '',
            'DonViTinh' => $request->DonViTinh ?? 'Kg',
            'TrongLuong' => $request->TrongLuong ?? 0,
            'GiaNhap' => $request->GiaNhap ?? 0,
            'GiaBan_SG' => $request->GiaBan ?? 0,
            'DanhMucSP' => $request->DanhMucSP ?? null,
            'NhomSP' => $request->NhomSP ?? null,
        ]);

        // Thêm vào quản lý kho
        DB::table('quanlysanpham')->insert([
            'MaSP' => $request->MaSP,
            'TenSP' => $request->TenSP,
            'SoLuong' => 0,
        ]);

        return back()->with('success', 'Thêm sản phẩm thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TenSP' => 'required|string',
        ]);

        $sp = DB::table('sanpham')->where('ID', $id)->first();
        if (!$sp) return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);

        DB::table('sanpham')->where('ID', $id)->update([
            'TenSP' => $request->TenSP,
            'MoTa' => $request->MoTa ?? $sp->MoTa,
            'DonViTinh' => $request->DonViTinh ?? $sp->DonViTinh,
            'TrongLuong' => $request->TrongLuong ?? $sp->TrongLuong,
            'GiaNhap' => $request->GiaNhap ?? $sp->GiaNhap,
            'GiaBan_SG' => $request->GiaBan ?? $sp->GiaBan_SG,
            'DanhMucSP' => $request->DanhMucSP ?? $sp->DanhMucSP,
            'NhomSP' => $request->NhomSP ?? $sp->NhomSP,
        ]);

        // Cập nhật tên trong quanlysanpham
        DB::table('quanlysanpham')->where('MaSP', $sp->MaSP)->update(['TenSP' => $request->TenSP]);

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['TenDMSP' => 'required|string']);

        DB::table('danhmucsanpham')->insert([
            'TenDMSP' => $request->TenDMSP,
            'NhomSanPham' => $request->NhomSanPham ?? 0,
            'MaNganh' => $request->MaNganh ?? '',
            'GhiChu' => $request->GhiChu ?? '',
        ]);

        return back()->with('success', 'Thêm danh mục thành công!');
    }

    public function updateCategory(Request $request, $id)
    {
        DB::table('danhmucsanpham')->where('MaDMSP', $id)->update([
            'TenDMSP' => $request->TenDMSP,
            'NhomSanPham' => $request->NhomSanPham ?? 0,
            'GhiChu' => $request->GhiChu ?? '',
        ]);

        return response()->json(['success' => true, 'message' => 'Cập nhật danh mục thành công!']);
    }

    public function deleteCategory($id)
    {
        DB::table('danhmucsanpham')->where('MaDMSP', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa danh mục.']);
    }
}
