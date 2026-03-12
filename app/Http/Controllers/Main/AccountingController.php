<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountingController extends Controller
{
    public function invoices()
    {
        abort_unless(auth()->user()->can('Admin') || auth()->user()->can('Kế Toán'), 403);
        return view('main.accounting.invoices');
    }

    public function expenses(Request $request)
    {
        abort_unless(auth()->user()->can('Admin') || auth()->user()->can('Kế Toán'), 403);

        // Month filter — default current month
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $dateFrom = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
        $dateTo   = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');
        $tinhTrangFilter = $request->input('tinh_trang', 'all');

        $query = DB::table('hoadonchi as h')
            ->leftJoin('users as nc', 'nc.id', '=', 'h.NguoiChi')
            ->leftJoin('users as nd', 'nd.id', '=', DB::raw('CAST(h.NguoiDuyet AS UNSIGNED)'))
            ->leftJoin('users as nh', 'nh.id', '=', DB::raw('CAST(h.NguoiHuy AS UNSIGNED)'))
            ->select(
                'h.*',
                'nc.name as TenNguoiChi',
                'nd.name as TenNguoiDuyet',
                'nh.name as TenNguoiHuy'
            )
            ->whereBetween('h.Ngay', [$dateFrom, $dateTo]);

        if ($tinhTrangFilter && $tinhTrangFilter !== 'all') {
            $query->where('h.TinhTrang', $tinhTrangFilter);
        }

        $expenses = $query->orderByDesc('h.Ngay')->orderByDesc('h.id')->get();

        // Summary
        $validExpenses = $expenses->where('TinhTrang', 'Có Hiệu Lực');
        $totalSoTien  = $validExpenses->sum('SoTien');
        $totalHoaDon  = $validExpenses->count();

        // Count by status for tabs
        $countBase = DB::table('hoadonchi')->whereBetween('Ngay', [$dateFrom, $dateTo]);
        $countAll       = (clone $countBase)->count();
        $countCoHieuLuc = (clone $countBase)->where('TinhTrang', 'Có Hiệu Lực')->count();
        $countDaHuy     = (clone $countBase)->where('TinhTrang', 'Đã Huỷ')->count();

        return view('main.accounting.expenses', compact(
            'expenses', 'month', 'tinhTrangFilter',
            'totalSoTien', 'totalHoaDon',
            'countAll', 'countCoHieuLuc', 'countDaHuy'
        ));
    }

    public function storeExpense(Request $request)
    {
        $user = auth()->user();
        if (!$user || (!$user->can('Admin') && !$user->can('Kế Toán'))) {
            return redirect()->back()->with('error', 'Bạn không có quyền thêm phiếu chi.');
        }

        $ngay = null;
        if ($request->input('Ngay')) {
            try {
                $ngay = Carbon::createFromFormat('d/m/Y', $request->input('Ngay'))->format('Y-m-d');
            } catch (\Exception $e) {
                $ngay = $request->input('Ngay');
            }
        }

        DB::table('hoadonchi')->insert([
            'NguoiChi'  => $user->id,
            'Ngay'      => $ngay ?? Carbon::now()->format('Y-m-d'),
            'SoTien'    => $request->input('SoTien', 0),
            'NoiDung'   => $request->input('NoiDung', ''),
            'LoaiPhieu' => 'Phiếu Chi',
            'HinhAnh'   => '',
            'TinhTrang' => 'Có Hiệu Lực',
        ]);

        return redirect()->back()->with('success', 'Đã thêm phiếu chi.');
    }

    public function cancelExpense(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || (!$user->can('Admin') && !$user->can('Kế Toán'))) {
            return redirect()->back()->with('error', 'Bạn không có quyền huỷ phiếu chi.');
        }

        DB::table('hoadonchi')->where('id', $id)->update([
            'TinhTrang'   => 'Đã Huỷ',
            'NguoiHuy'    => $user->id,
            'NguyenNhan'  => $request->input('NguyenNhan', ''),
            'ThoiGianHuy' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Đã huỷ phiếu chi.');
    }

    public function destroyExpense($id)
    {
        $user = auth()->user();
        if (!$user || !$user->can('Admin')) {
            return redirect()->back()->with('error', 'Chỉ Admin mới có quyền xóa vĩnh viễn.');
        }

        DB::table('hoadonchi')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Đã xóa phiếu chi.');
    }

    public function seedInvoices(Request $request)
    {
        abort_unless(auth()->user()->can('Admin') || auth()->user()->can('Kế Toán'), 403);

        // Month filter — default current month
        $month    = $request->input('month', Carbon::now()->format('Y-m'));
        $dateFrom = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
        $dateTo   = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');

        $query = DB::table('hoadonbangiong as h')
            ->leftJoin('donhang_giong as dg', DB::raw('dg.MaDH COLLATE utf8_general_ci'), '=', DB::raw('h.MaDH COLLATE utf8_general_ci'))
            ->leftJoin('khachhang as kh', DB::raw('kh.MaDH COLLATE utf8_general_ci'), '=', DB::raw('h.MaDH COLLATE utf8_general_ci'))
            ->leftJoin('users as nv', 'nv.id', '=', DB::raw('CAST(dg.MaNV AS UNSIGNED)'))
            ->select(
                'h.id',
                'h.MaDH',
                'h.NgayThanhToan',
                'h.SoLuongNhan',
                'h.ThucNhan',
                'h.ChuyenTraTrai',
                'h.DoanhSo',
                'h.GhiChu',
                'kh.TenKH',
                'kh.SoDienThoai',
                'kh.Tinh',
                'kh.Huyen',
                'kh.Xa',
                'kh.DiaChi',
                'nv.name as TenNV',
                'dg.NgayGiao',
                'dg.SoLuong',
                'dg.TongTien'
            );

        // Filter by month
        $query->where(function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('h.NgayThanhToan', [$dateFrom, $dateTo])
              ->orWhere(function($q2) use ($dateFrom, $dateTo) {
                  $q2->whereNull('h.NgayThanhToan')
                     ->whereBetween('dg.NgayGiao', [$dateFrom, $dateTo]);
              });
        });

        $invoices = $query->orderByDesc('h.NgayThanhToan')->orderByDesc('h.id')->get();

        // Summary stats
        $totalCount    = $invoices->count();
        $totalThucNhan = $invoices->sum('ThucNhan');
        $totalChuyenTra = $invoices->sum('ChuyenTraTrai');
        $totalDoanhSo  = $invoices->sum('DoanhSo');

        return view('main.accounting.seed_invoices', compact(
            'invoices', 'month',
            'totalCount', 'totalThucNhan', 'totalChuyenTra', 'totalDoanhSo'
        ));
    }

    public function destroySeedInvoice($id)
    {
        $user = auth()->user();
        if (!$user || !$user->can('Admin')) {
            return redirect()->back()->with('error', 'Chỉ Admin mới có quyền xóa.');
        }

        DB::table('hoadonbangiong')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Đã xóa hóa đơn.');
    }
}
