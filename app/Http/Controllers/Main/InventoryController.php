<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $products = DB::table('sanpham')->orderBy('TenSP')->get(['MaSP', 'TenSP', 'DonViTinh', 'GiaNhap']);
        return view('main.inventory.index', compact('products'));
    }

    public function data(Request $request)
    {
        $defFrom = '01/' . now()->format('m/Y');
        $defTo = now()->format('d/m/Y');

        $xuatFrom = $request->input('xuat_from', $defFrom);
        $xuatTo = $request->input('xuat_to', $defTo);
        $nhapFrom = $request->input('nhap_from', $defFrom);
        $nhapTo = $request->input('nhap_to', $defTo);

        // Parse helper
        $parseDMY = function($d) {
            $p = explode('/', $d);
            return count($p) == 3 ? "{$p[2]}-{$p[1]}-{$p[0]}" : null;
        };

        $dbXuatFrom = $parseDMY($xuatFrom);
        $dbXuatTo = $parseDMY($xuatTo);
        $dbNhapFrom = $parseDMY($nhapFrom);
        $dbNhapTo = $parseDMY($nhapTo);

        // DANH SÁCH XUẤT KHO
        $xuatList = DB::table('chitietdonhang as ct')
            ->leftJoin('donhang as dh', 'dh.MaDH', '=', 'ct.MaDH')
            ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
            ->select('ct.id', 'dh.Ngay', 'ct.MaDH', 'sp.TenSP', 'ct.SoLuong', 'ct.GiaNhap', 'ct.GiaBan')
            ->when($dbXuatFrom, fn($q) => $q->where('dh.Ngay', '>=', $dbXuatFrom))
            ->when($dbXuatTo, fn($q) => $q->where('dh.Ngay', '<=', $dbXuatTo))
            ->orderByDesc('dh.Ngay')->get();

        // DANH SÁCH NHẬP KHO
        $nhapList = DB::table('chitietphieunhapxuat as ct')
            ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
            ->select('ct.*', 'sp.TenSP')
            ->when($dbNhapFrom, fn($q) => $q->where('ct.Ngay', '>=', $dbNhapFrom))
            ->when($dbNhapTo, fn($q) => $q->where('ct.Ngay', '<=', $dbNhapTo))
            ->orderByDesc('ct.Ngay')->get();

        // TỔNG XUẤT KHO
        $tongXuat = DB::table('chitietdonhang as ct')
            ->leftJoin('donhang as dh', 'dh.MaDH', '=', 'ct.MaDH')
            ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
            ->select('sp.TenSP', DB::raw('CAST(SUM(ct.SoLuong) AS UNSIGNED) as TongSL'))
            ->when($dbXuatFrom, fn($q) => $q->where('dh.Ngay', '>=', $dbXuatFrom))
            ->when($dbXuatTo, fn($q) => $q->where('dh.Ngay', '<=', $dbXuatTo))
            ->groupBy('sp.TenSP')->orderBy('sp.TenSP')->get();

        // TỔNG NHẬP KHO
        $tongNhap = DB::table('chitietphieunhapxuat as ct')
            ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
            ->select('sp.TenSP', DB::raw('CAST(SUM(ct.SoLuong) AS UNSIGNED) as TongSL'))
            ->where('ct.LoaiPhieu', 'Nhập Kho')
            ->when($dbNhapFrom, fn($q) => $q->where('ct.Ngay', '>=', $dbNhapFrom))
            ->when($dbNhapTo, fn($q) => $q->where('ct.Ngay', '<=', $dbNhapTo))
            ->groupBy('sp.TenSP')->orderBy('sp.TenSP')->get();

        // TỒN KHO
        $tonKho = DB::table('quanlysanpham as q')
            ->leftJoin('sanpham as s', 's.MaSP', '=', 'q.MaSP')
            ->select('q.MaSP', 'q.TenSP', DB::raw('CAST(q.SoLuong AS UNSIGNED) as SoLuong'), 's.GiaNhap')
            ->orderBy('q.TenSP')->get();

        $totalValue = $tonKho->sum(fn($i) => ($i->SoLuong ?? 0) * ($i->GiaNhap ?? 0));

        return response()->json([
            'xuatFrom' => $xuatFrom, 'xuatTo' => $xuatTo,
            'nhapFrom' => $nhapFrom, 'nhapTo' => $nhapTo,
            'xuatList' => $xuatList,
            'nhapList' => $nhapList,
            'tongXuat' => $tongXuat,
            'tongNhap' => $tongNhap,
            'tonKho' => $tonKho,
            'totalValue' => $totalValue,
        ]);
    }

    public function storeImportExport(Request $request)
    {
        $request->validate([
            'MaSP' => 'required|string',
            'SoLuong' => 'required|numeric|min:0.1',
            'LoaiPhieu' => 'required|in:Nhập Kho,Xuất Kho',
        ]);

        $ngay = Carbon::now()->format('Y-m-d');
        if ($request->Ngay) {
            $p = explode('/', $request->Ngay);
            if (count($p) == 3) $ngay = "{$p[2]}-{$p[1]}-{$p[0]}";
        }

        DB::table('chitietphieunhapxuat')->insert([
            'Ngay' => $ngay,
            'MaSP' => $request->MaSP,
            'SoLuong' => $request->SoLuong,
            'GiaNhap' => $request->GiaNhap ?? 0,
            'LoaiPhieu' => $request->LoaiPhieu,
        ]);

        // Cập nhật giá nhập mới vào bảng sản phẩm
        if ($request->LoaiPhieu === 'Nhập Kho' && $request->GiaNhap > 0) {
            DB::table('sanpham')->where('MaSP', $request->MaSP)->update(['GiaNhap' => $request->GiaNhap]);
        }

        $currentStock = DB::table('quanlysanpham')->where('MaSP', $request->MaSP)->first();
        if ($currentStock) {
            $newQty = $request->LoaiPhieu === 'Nhập Kho'
                ? $currentStock->SoLuong + $request->SoLuong
                : max(0, $currentStock->SoLuong - $request->SoLuong);
            DB::table('quanlysanpham')->where('MaSP', $request->MaSP)->update(['SoLuong' => $newQty]);
        } else {
            $sp = DB::table('sanpham')->where('MaSP', $request->MaSP)->first();
            DB::table('quanlysanpham')->insert([
                'MaSP' => $request->MaSP,
                'TenSP' => $sp ? $sp->TenSP : $request->MaSP,
                'SoLuong' => $request->LoaiPhieu === 'Nhập Kho' ? $request->SoLuong : 0,
            ]);
        }

        $typeName = $request->LoaiPhieu === 'Nhập Kho' ? 'nhập' : 'xuất';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Đã {$typeName} kho thành công!"]);
        }
        return back()->with('success', "Đã {$typeName} kho {$request->SoLuong} sản phẩm {$request->MaSP}!");
    }

    public function deleteNhap($id)
    {
        $record = DB::table('chitietphieunhapxuat')->where('id', $id)->first();
        if ($record) {
            // Hoàn lại tồn kho
            $stock = DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->first();
            if ($stock) {
                $newQty = $record->LoaiPhieu === 'Nhập Kho'
                    ? max(0, $stock->SoLuong - $record->SoLuong)
                    : $stock->SoLuong + $record->SoLuong;
                DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->update(['SoLuong' => $newQty]);
            }
            DB::table('chitietphieunhapxuat')->where('id', $id)->delete();
        }
        return response()->json(['success' => true, 'message' => 'Đã xóa phiếu nhập/xuất']);
    }

    public function updateNhap(Request $request, $id)
    {
        $record = DB::table('chitietphieunhapxuat')->where('id', $id)->first();
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy phiếu']);
        }

        $newSL = (float) $request->input('SoLuong', 0);
        if ($newSL <= 0) {
            return response()->json(['success' => false, 'message' => 'Số lượng phải lớn hơn 0']);
        }

        $oldSL = (float) $record->SoLuong;
        $diff = $newSL - $oldSL;

        // Cập nhật số lượng trong phiếu
        DB::table('chitietphieunhapxuat')->where('id', $id)->update(['SoLuong' => $newSL]);

        // Cập nhật tồn kho
        $stock = DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->first();
        if ($stock) {
            if ($record->LoaiPhieu === 'Nhập Kho') {
                // Nhập thêm: tồn kho += chênh lệch
                $newStockQty = max(0, $stock->SoLuong + $diff);
            } else {
                // Xuất thêm: tồn kho -= chênh lệch
                $newStockQty = max(0, $stock->SoLuong - $diff);
            }
            DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->update(['SoLuong' => $newStockQty]);
        }

        return response()->json(['success' => true, 'message' => "Đã sửa số lượng: {$oldSL} → {$newSL}"]);
    }

    public function history(Request $request)
    {
        return $this->index($request->merge(['tab' => 'history']));
    }
}
