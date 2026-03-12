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
        $nhapList = DB::table('nhapkho as ct')
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
        $tongNhap = DB::table('nhapkho as ct')
            ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
            ->select('sp.TenSP', DB::raw('CAST(SUM(ct.SoLuong) AS UNSIGNED) as TongSL'))

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
        ]);

        $ngay = Carbon::now()->format('Y-m-d');
        if ($request->Ngay) {
            $p = explode('/', $request->Ngay);
            if (count($p) == 3) $ngay = "{$p[2]}-{$p[1]}-{$p[0]}";
        }

        DB::table('nhapkho')->insert([
            'Ngay' => $ngay,
            'MaSP' => $request->MaSP,
            'SoLuong' => $request->SoLuong,
            'GiaNhap' => $request->GiaNhap ?? 0,
        ]);

        // Cập nhật giá nhập mới vào bảng sản phẩm
        if ($request->GiaNhap > 0) {
            DB::table('sanpham')->where('MaSP', $request->MaSP)->update(['GiaNhap' => $request->GiaNhap]);
        }

        // Nhập kho: cộng tồn kho
        $currentStock = DB::table('quanlysanpham')->where('MaSP', $request->MaSP)->first();
        if ($currentStock) {
            $newQty = $currentStock->SoLuong + $request->SoLuong;
            DB::table('quanlysanpham')->where('MaSP', $request->MaSP)->update(['SoLuong' => $newQty]);
        } else {
            $sp = DB::table('sanpham')->where('MaSP', $request->MaSP)->first();
            DB::table('quanlysanpham')->insert([
                'MaSP' => $request->MaSP,
                'TenSP' => $sp ? $sp->TenSP : $request->MaSP,
                'SoLuong' => $request->SoLuong,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã nhập kho thành công!']);
        }
        return back()->with('success', "Đã nhập kho {$request->SoLuong} sản phẩm {$request->MaSP}!");
    }

    public function deleteNhap($id)
    {
        $record = DB::table('nhapkho')->where('id', $id)->first();
        if ($record) {
            // Hoàn lại tồn kho: xóa nhập kho = trừ tồn
            $stock = DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->first();
            if ($stock) {
                $newQty = max(0, $stock->SoLuong - $record->SoLuong);
                DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->update(['SoLuong' => $newQty]);
            }
            DB::table('nhapkho')->where('id', $id)->delete();
        }
        return response()->json(['success' => true, 'message' => 'Đã xóa phiếu nhập kho']);
    }

    public function updateNhap(Request $request, $id)
    {
        $record = DB::table('nhapkho')->where('id', $id)->first();
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
        DB::table('nhapkho')->where('id', $id)->update(['SoLuong' => $newSL]);

        // Cập nhật tồn kho
        // Nhập kho: tồn kho += chênh lệch
        $stock = DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->first();
        if ($stock) {
            $newStockQty = max(0, $stock->SoLuong + $diff);
            DB::table('quanlysanpham')->where('MaSP', $record->MaSP)->update(['SoLuong' => $newStockQty]);
        }

        return response()->json(['success' => true, 'message' => "Đã sửa số lượng: {$oldSL} → {$newSL}"]);
    }

    public function history(Request $request)
    {
        return $this->index($request->merge(['tab' => 'history']));
    }

    public function resetData(Request $request)
    {
        // Chỉ Admin mới được reset
        if (auth()->user()->Permission !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện']);
        }

        $mode = $request->input('mode'); // ngay | thang
        $type = $request->input('type'); // xuat | nhap | all
        $value = $request->input('value'); // dd/mm/yyyy hoặc mm/yyyy

        if (!$mode || !$type || !$value) {
            return response()->json(['success' => false, 'message' => 'Thiếu thông tin']);
        }

        // Xác định khoảng thời gian
        if ($mode === 'ngay') {
            $parts = explode('/', $value);
            if (count($parts) !== 3) return response()->json(['success' => false, 'message' => 'Ngày không hợp lệ']);
            $dateFrom = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
            $dateTo = $dateFrom;
        } else {
            // Theo tháng: mm/yyyy
            $parts = explode('/', $value);
            if (count($parts) !== 2) return response()->json(['success' => false, 'message' => 'Tháng không hợp lệ']);
            $month = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $year = $parts[1];
            $dateFrom = "{$year}-{$month}-01";
            $dateTo = date('Y-m-t', strtotime($dateFrom));
        }

        DB::beginTransaction();
        try {
            $messages = [];

            // Reset Xuất Kho (hoặc bước 1 của Reset Toàn Bộ)
            if ($type === 'xuat' || $type === 'all') {
                // Lấy chi tiết đơn hàng trong khoảng thời gian
                $chiTiet = DB::table('chitietdonhang')
                    ->whereBetween('NgayBan', [$dateFrom, $dateTo])
                    ->get();

                // Bù lại tồn kho
                foreach ($chiTiet as $ct) {
                    DB::table('quanlysanpham')
                        ->where('MaSP', $ct->MaSP)
                        ->increment('SoLuong', $ct->SoLuong);
                }

                // Lấy danh sách MaDH cần xóa
                $maDHs = DB::table('donhang')
                    ->whereBetween('Ngay', [$dateFrom, $dateTo])
                    ->pluck('MaDH')
                    ->toArray();

                // Xóa chi tiết đơn hàng
                DB::table('chitietdonhang')
                    ->whereBetween('NgayBan', [$dateFrom, $dateTo])
                    ->delete();

                // Xóa đơn hàng
                $countXuat = DB::table('donhang')
                    ->whereBetween('Ngay', [$dateFrom, $dateTo])
                    ->delete();

                $messages[] = "Đã xóa {$countXuat} đơn hàng xuất kho";
            }

            // Reset Nhập Kho (hoặc bước 2 của Reset Toàn Bộ)
            if ($type === 'nhap' || $type === 'all') {
                // Lấy nhập kho trong khoảng thời gian
                $nhapList = DB::table('nhapkho')
                    ->whereBetween('Ngay', [$dateFrom, $dateTo])
                    ->get();

                // Trừ lại tồn kho
                foreach ($nhapList as $nk) {
                    DB::table('quanlysanpham')
                        ->where('MaSP', $nk->MaSP)
                        ->decrement('SoLuong', $nk->SoLuong);
                }

                // Xóa nhập kho
                $countNhap = DB::table('nhapkho')
                    ->whereBetween('Ngay', [$dateFrom, $dateTo])
                    ->delete();

                $messages[] = "Đã xóa {$countNhap} phiếu nhập kho";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => implode('. ', $messages),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ]);
        }
    }
}
