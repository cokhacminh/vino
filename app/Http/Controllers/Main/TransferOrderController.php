<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransferOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Danh sách tồn kho với giá bán
        $tonKho = DB::table('quanlysanpham as q')
            ->leftJoin('sanpham as s', 's.MaSP', '=', 'q.MaSP')
            ->select('q.MaSP', 'q.TenSP', DB::raw('CAST(q.SoLuong AS UNSIGNED) as SoLuong'), 's.GiaBan_SG')
            ->orderBy('q.TenSP')
            ->get();

        // Dữ liệu tồn kho cho JS
        $tonKhoJs = $tonKho->map(function($p) {
            return [
                'MaSP' => $p->MaSP,
                'TenSP' => $p->TenSP,
                'SoLuong' => (int) $p->SoLuong,
                'GiaBan' => (int) ($p->GiaBan_SG ?? 0),
            ];
        })->values();

        // Lấy cài đặt thuật toán từ DB, tự động khởi tạo nếu chưa có
        $row = DB::table('algorithm_settings')->where('key', 'transfer_order_algo')->first();
        if (!$row) {
            $defaultSettings = [
                'markup' => 20,
                'tiers' => [
                    ['from' => 0,       'to' => 1000000,   'maxQty' => 999, 'minSP' => 1, 'priority' => 'random'],
                    ['from' => 1000000,  'to' => 4000000,   'maxQty' => 4,   'minSP' => 2, 'priority' => 'high'],
                    ['from' => 4000000,  'to' => 8000000,   'maxQty' => 5,   'minSP' => 3, 'priority' => 'high'],
                    ['from' => 8000000,  'to' => 999999999, 'maxQty' => 10,  'minSP' => 3, 'priority' => 'high'],
                ],
            ];
            DB::table('algorithm_settings')->insert([
                'key' => 'transfer_order_algo',
                'value' => json_encode($defaultSettings),
            ]);
            $algoSettings = $defaultSettings;
        } else {
            $algoSettings = json_decode($row->value, true);
        }

        return view('main.transfer-orders.index', compact('tonKho', 'tonKhoJs', 'user', 'algoSettings'));
    }

    public function getData(Request $request)
    {
        $dateStr = $request->input('date', now()->format('d/m/Y'));
        $parts = explode('/', $dateStr);
        $dbDate = count($parts) == 3 ? "{$parts[2]}-{$parts[1]}-{$parts[0]}" : now()->format('Y-m-d');

        // Gửi curl check-import
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thuysansg.com/webhook/check-import',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('Ngay' => $dbDate),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $orders = json_decode($response, false) ?: [];

        // Kiểm tra MaDH đã tồn tại trong DB donhang chưa
        if (count($orders) > 0) {
            $maDHList = array_map(function($o) { return $o->MaDH ?? ''; }, (array) $orders);
            $maDHList = array_filter($maDHList);
            $existingMaDH = DB::table('donhang')->whereIn('MaDH', $maDHList)->pluck('MaDH')->toArray();

            foreach ($orders as $order) {
                $order->existsInDb = in_array($order->MaDH ?? '', $existingMaDH);
            }
        }

        return response()->json([
            'orders' => $orders,
            'date' => $dateStr,
        ]);
    }

    public function saveSettings(Request $request)
    {
        $settings = $request->input('settings');

        DB::table('algorithm_settings')->updateOrInsert(
            ['key' => 'transfer_order_algo'],
            ['value' => json_encode($settings)]
        );

        return response()->json(['success' => true, 'message' => 'Đã lưu cài đặt']);
    }

    public function transferOrder(Request $request)
    {
        $maDH = $request->input('MaDH');
        $ngay = $request->input('Ngay');
        $tongTien = (int) $request->input('TongTien');
        $items = $request->input('items', []);

        // Kiểm tra đơn đã tồn tại chưa
        if (DB::table('donhang')->where('MaDH', $maDH)->exists()) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng đã tồn tại']);
        }

        DB::beginTransaction();
        try {
            // Tính tổng giá trị sản phẩm đã chọn
            $tongGiaTriSP = 0;
            foreach ($items as $item) {
                $tongGiaTriSP += ($item['GiaBan'] ?? 0) * ($item['SoLuong'] ?? 1);
            }
            $giamGia = $tongGiaTriSP - $tongTien;
            if ($giamGia < 0) $giamGia = 0;

            // Lấy MaNV ngẫu nhiên (không phải Admin)
            $nonAdminUsers = DB::table('users')
                ->where('permission', '!=', 'Admin')
                ->pluck('id')
                ->toArray();
            $maNV = count($nonAdminUsers) > 0 ? $nonAdminUsers[array_rand($nonAdminUsers)] : 1;

            // Insert đơn hàng
            DB::table('donhang')->insert([
                'MaDH' => $maDH,
                'MaNV' => $maNV,
                'TongTien' => $tongTien,
                'GiamGia' => $giamGia,
                'Ngay' => $ngay,
                'DonHang' => '',
            ]);

            // Xóa chi tiết đơn hàng cũ nếu có, rồi insert mới + trừ tồn kho
            DB::table('chitietdonhang')->where('MaDH', $maDH)->delete();
            foreach ($items as $item) {
                if (empty($item['MaSP'])) continue;
                $sp = DB::table('sanpham')->where('MaSP', $item['MaSP'])->first();
                DB::table('chitietdonhang')->insert([
                    'MaDH' => $maDH,
                    'MaSP' => $item['MaSP'],
                    'SoLuong' => $item['SoLuong'] ?? 1,
                    'GiaNhap' => $sp->GiaNhap ?? 0,
                    'GiaBan' => $item['GiaBan'] ?? ($sp->GiaBan_SG ?? 0),
                    'NgayBan' => $ngay,
                ]);

                // Xuất kho: trừ số lượng tồn kho
                DB::table('quanlysanpham')
                    ->where('MaSP', $item['MaSP'])
                    ->decrement('SoLuong', $item['SoLuong'] ?? 1);
            }

            // Chỉ gửi curl và thêm khách hàng nếu chưa có dữ liệu
            $kh = null;
            if (!DB::table('khachhang')->where('MaDH', $maDH)->exists()) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://thuysansg.com/webhook/check-kh',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => ['MaDH' => $maDH],
                ]);
                $khResponse = curl_exec($curl);
                curl_close($curl);

                $khData = json_decode($khResponse, true);
                if (!empty($khData) && is_array($khData)) {
                    $kh = $khData[0] ?? $khData;
                    DB::table('khachhang')->insert([
                        'MaDH' => $maDH,
                        'TenKH' => $kh['TenKH'] ?? '',
                        'SoDienThoai' => $kh['SoDienThoai'] ?? '',
                        'DiaChi' => $kh['DiaChi'] ?? '',
                        'Tinh' => $kh['Tinh'] ?? '',
                        'Huyen' => $kh['Huyen'] ?? '',
                        'Xa' => $kh['Xa'] ?? '',
                        'SoLanMua' => $kh['SoLanMua'] ?? 1,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Chuyển đơn {$maDH} thành công",
                'khachHang' => $kh ?? null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Xóa dữ liệu nếu đã insert ngoài transaction
            DB::table('chitietdonhang')->where('MaDH', $maDH)->delete();
            DB::table('khachhang')->where('MaDH', $maDH)->delete();
            DB::table('donhang')->where('MaDH', $maDH)->delete();

            return response()->json([
                'success' => false,
                'message' => "Lỗi: " . $e->getMessage(),
            ]);
        }
    }
}
