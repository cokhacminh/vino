<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $dateFrom = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
        $dateTo = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');

        // Stats
        $totalUsers = DB::table('users')->where('TinhTrang', 'Đang Làm Việc')->count();
        $totalCustomers = DB::table('khachhang')->count();
        $totalProducts = DB::table('sanpham')->count();

        // Đơn hàng tháng (dùng cột Ngay)
        $ordersMonth = DB::table('donhang')
            ->whereBetween('Ngay', [$dateFrom, $dateTo])
            ->count();

        $revenueMonth = DB::table('donhang')
            ->whereBetween('Ngay', [$dateFrom, $dateTo])
            ->sum('TongTien');

        // Chi phí
        $expensesMonth = DB::table('hoadonchi')
            ->whereBetween('Ngay', [$dateFrom, $dateTo])
            ->where('TinhTrang', 'Có Hiệu Lực')
            ->sum('SoTien');

        // Top 10 Best Seller
        $topSellers = DB::table('donhang as dh')
            ->leftJoin('users as nv', 'nv.id', '=', 'dh.MaNV')
            ->whereBetween('dh.Ngay', [$dateFrom, $dateTo])
            ->select(
                'nv.id as MaNV',
                'nv.name as TenNV',
                DB::raw('COUNT(DISTINCT dh.MaDH) as SoDonHang'),
                DB::raw('SUM(dh.TongTien) as TongDoanhThu')
            )
            ->groupBy('nv.id', 'nv.name')
            ->orderByDesc('TongDoanhThu')
            ->limit(10)
            ->get();

        // Tồn kho cảnh báo (sản phẩm tồn kho thấp)
        $lowStock = DB::table('quanlysanpham as q')
            ->leftJoin('sanpham as s', 's.MaSP', '=', 'q.MaSP')
            ->where('q.SoLuong', '<=', 10)
            ->where('q.SoLuong', '>', 0)
            ->select('q.MaSP', 'q.TenSP', 'q.SoLuong', 's.DonViTinh')
            ->orderBy('q.SoLuong')
            ->limit(10)
            ->get();

        return view('main.dashboard', compact(
            'month', 'user', 'totalUsers', 'totalCustomers', 'totalProducts',
            'ordersMonth', 'revenueMonth', 'expensesMonth',
            'topSellers', 'lowStock'
        ));
    }
}
