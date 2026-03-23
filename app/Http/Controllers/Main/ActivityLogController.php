<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = DB::table('activity_log')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($log) {
                $log->khach_hang = json_decode($log->khach_hang_data, true);
                $log->chi_tiet = json_decode($log->chi_tiet_data, true);
                return $log;
            });

        return view('main.activity-log.index', compact('logs'));
    }

    public function restore($id)
    {
        $log = DB::table('activity_log')->where('id', $id)->first();
        if (!$log) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy log']);
        }
        if ($log->restored) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng đã được khôi phục trước đó']);
        }

        // Kiểm tra MaDH đã tồn tại trong donhang chưa
        if (DB::table('donhang')->where('MaDH', $log->MaDH)->exists()) {
            return response()->json(['success' => false, 'message' => 'Mã đơn hàng ' . $log->MaDH . ' đã tồn tại, không thể khôi phục']);
        }

        DB::beginTransaction();
        try {
            // 1. Khôi phục đơn hàng
            DB::table('donhang')->insert([
                'MaDH' => $log->MaDH,
                'MaNV' => $log->MaNV,
                'TongTien' => $log->TongTien,
                'GiamGia' => $log->GiamGia,
                'Ngay' => $log->Ngay,
                'DonHang' => $log->DonHang,
            ]);

            // 2. Khôi phục khách hàng
            $kh = json_decode($log->khach_hang_data, true);
            if ($kh) {
                DB::table('khachhang')->insert([
                    'MaDH' => $log->MaDH,
                    'TenKH' => $kh['TenKH'] ?? '',
                    'SoDienThoai' => $kh['SoDienThoai'] ?? '',
                    'DiaChi' => $kh['DiaChi'] ?? '',
                    'Tinh' => $kh['Tinh'] ?? '',
                    'Huyen' => $kh['Huyen'] ?? '',
                    'Xa' => $kh['Xa'] ?? '',
                    'SoLanMua' => $kh['SoLanMua'] ?? 1,
                ]);
            }

            // 3. Khôi phục chi tiết đơn hàng + trừ tồn kho
            $chiTiet = json_decode($log->chi_tiet_data, true) ?: [];
            foreach ($chiTiet as $ct) {
                DB::table('chitietdonhang')->insert([
                    'MaDH' => $log->MaDH,
                    'MaSP' => $ct['MaSP'],
                    'SoLuong' => $ct['SoLuong'],
                    'GiaNhap' => $ct['GiaNhap'] ?? 0,
                    'GiaBan' => $ct['GiaBan'] ?? 0,
                    'NgayBan' => $ct['NgayBan'] ?? $log->Ngay,
                ]);

                // Trừ lại tồn kho
                DB::table('quanlysanpham')
                    ->where('MaSP', $ct['MaSP'])
                    ->decrement('SoLuong', $ct['SoLuong']);
            }

            // 4. Đánh dấu đã khôi phục
            DB::table('activity_log')->where('id', $id)->update([
                'restored' => true,
                'restored_at' => now(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Khôi phục đơn hàng thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
