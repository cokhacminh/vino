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

        // Đơn hàng tháng
        $ordersMonth = DB::table('donhang')
            ->whereBetween('Ngay', [$dateFrom, $dateTo])
            ->count();

        $revenueMonth = DB::table('donhang')
            ->whereBetween('Ngay', [$dateFrom, $dateTo])
            ->sum('TongTien');

        // Tổng tiền hàng (SoLuong * GiaNhap từ chitietdonhang)
        $tongTienHang = DB::table('chitietdonhang')
            ->whereBetween('NgayBan', [$dateFrom, $dateTo])
            ->select(DB::raw('SUM(SoLuong * GiaNhap) as total'))
            ->value('total') ?? 0;

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

        // Đơn giao thành công hôm nay (webhook)
        $successOrders = collect();
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://thuysansg.com/webhook/don-thanh-cong',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response, false) ?: [];
            if (count($data) > 0) {
                $maDHList = array_filter(array_map(fn($o) => $o->MaDH ?? '', (array) $data));
                $existingMaDH = DB::table('donhang')->whereIn('MaDH', $maDHList)->pluck('MaDH')->toArray();
                foreach ($data as $o) {
                    $o->daChuyenDon = in_array($o->MaDH ?? '', $existingMaDH);
                }
                $successOrders = collect($data);
            }
        } catch (\Exception $e) {}

        // Thống kê đơn hàng theo ngày
        $dailyOrders = DB::table('donhang')
            ->whereBetween('Ngay', [$dateFrom, $dateTo])
            ->leftJoin(DB::raw('(SELECT MaDH, SUM(SoLuong * GiaNhap) as TienHang FROM chitietdonhang GROUP BY MaDH) as ct'), 'ct.MaDH', '=', 'donhang.MaDH')
            ->select(
                'donhang.Ngay',
                DB::raw('COUNT(*) as SoDon'),
                DB::raw('SUM(donhang.TongTien) as TongDoanhThu'),
                DB::raw('SUM(COALESCE(donhang.PhiShip, 0)) as TongPhiShip'),
                DB::raw('SUM(COALESCE(ct.TienHang, 0)) as TongTienHang')
            )
            ->groupBy('donhang.Ngay')
            ->orderByDesc('donhang.Ngay')
            ->get();

        // Tổng sản phẩm bán ra
        $topProducts = DB::table('chitietdonhang as ct')
            ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
            ->whereBetween('ct.NgayBan', [$dateFrom, $dateTo])
            ->select(
                'sp.TenSP',
                DB::raw('CAST(SUM(ct.SoLuong) AS UNSIGNED) as TongSL')
            )
            ->groupBy('sp.TenSP')
            ->orderByDesc('TongSL')
            ->get();

        return view('main.dashboard', compact(
            'month', 'user', 'ordersMonth', 'revenueMonth', 'tongTienHang',
            'topSellers', 'successOrders', 'dailyOrders', 'topProducts'
        ));
    }
}
