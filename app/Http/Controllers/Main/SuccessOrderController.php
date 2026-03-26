<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuccessOrderController extends Controller
{
    public function index()
    {
        return view('main.success-orders.index');
    }

    public function fetchData(Request $request)
    {
        $ngay = $request->input('Ngay', now()->format('d/m/Y') . ' - ' . now()->format('d/m/Y'));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://thuysansg.com/webhook/xem-don-thanh-cong',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'Ngay' => $ngay,
            ],
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200 || !$response) {
            return response()->json(['orders' => [], 'error' => 'Không thể kết nối']);
        }

        $orders = json_decode($response, false) ?: [];

        // Check MaDH tồn tại trong DB donhang
        if (count($orders) > 0) {
            $maDHList = array_map(fn($o) => $o->MaDH ?? '', (array) $orders);
            $maDHList = array_filter($maDHList);
            $existingOrders = DB::table('donhang')->whereIn('MaDH', $maDHList)->select('MaDH', 'GiamGia', 'TongTien')->get()->keyBy('MaDH');

            // Lấy thông tin khách hàng
            $customers = DB::table('khachhang')
                ->whereIn('MaDH', $maDHList)
                ->select('MaDH', 'TenKH', 'SoDienThoai', 'DiaChi', 'Xa', 'Huyen', 'Tinh')
                ->get()
                ->keyBy('MaDH');

            // Lấy chi tiết sản phẩm
            $orderProducts = DB::table('chitietdonhang as ct')
                ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
                ->whereIn('ct.MaDH', $maDHList)
                ->select('ct.MaDH', 'sp.TenSP', 'ct.GiaBan', 'ct.SoLuong')
                ->get()
                ->groupBy('MaDH');

            foreach ($orders as $order) {
                $maDH = $order->MaDH ?? '';
                $order->existsInDb = $existingOrders->has($maDH);

                // Khách hàng
                $kh = $customers->get($maDH);
                $order->TenKH = $kh ? $kh->TenKH : '';
                $order->SoDienThoai = $kh ? $kh->SoDienThoai : '';
                $order->DiaChi = $kh ? $kh->DiaChi : '';
                $order->Xa = $kh ? $kh->Xa : '';
                $order->Huyen = $kh ? $kh->Huyen : '';
                $order->Tinh = $kh ? $kh->Tinh : '';

                // GiamGia và TongTien từ DB
                $dbOrder = $existingOrders->get($maDH);
                $order->GiamGia = $dbOrder ? (int)($dbOrder->GiamGia ?? 0) : 0;
                $order->dbTongTien = $dbOrder ? (int)($dbOrder->TongTien ?? 0) : 0;

                // Sản phẩm (trả về object có TenSP, GiaBan, SoLuong)
                $products = $orderProducts->get($maDH);
                $order->SanPham = $products ? $products->map(fn($p) => [
                    'TenSP' => $p->TenSP,
                    'GiaBan' => (int)$p->GiaBan,
                    'SoLuong' => (int)$p->SoLuong,
                ])->values()->toArray() : [];
            }
        }

        return response()->json([
            'orders' => $orders,
        ]);
    }
}
