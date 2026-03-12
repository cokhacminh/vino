<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    /**
     * Webhook nhận callback từ Giao Hàng Nhanh (GHN)
     * DonviGH = 3
     */
    public function ghn(Request $request)
    {
        $data = $request->json()->all();

        // Validate required fields
        $requiredFields = ['ClientOrderCode', 'Status', 'TotalFee', 'CODAmount'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return response()->json(['success' => false, 'message' => "Thiếu trường: $field"], 400);
            }
        }

        $MaDH         = $data['ClientOrderCode'];
        $TrangThai    = $data['Status'];
        $PhiShipThucTe = $data['TotalFee'];
        $TienThuThucTe = $data['CODAmount'];
        $NoiDung      = $data['Description'] ?? '';
        $Reason       = $data['Reason'] ?? '';

        // Mapping trạng thái GHN sang mã nội bộ
        $ghnStatusMap = [
            'ready_to_pick'            => 11,  // Mới tạo đơn hàng
            'picking'                  => 15,  // Nhân viên đang lấy hàng
            'cancel'                   => 5,   // Hủy đơn hàng
            'money_collect_picking'    => 15,  // Đang thu tiền người gửi
            'picked'                   => 7,   // Nhân viên đã lấy hàng
            'storing'                  => 22,  // Hàng đang nằm ở kho
            'transporting'             => 4,   // Đang luân chuyển hàng
            'sorting'                  => 7,   // Đang phân loại hàng hóa
            'delivering'               => 4,   // Nhân viên đang giao cho người nhận
            'money_collect_delivering' => 4,   // Nhân viên đang thu tiền người nhận
            'delivered'                => 8,   // Giao hàng thành công
            'delivery_fail'            => 12,  // Giao hàng thất bại
            'waiting_to_return'        => 16,  // Đang đợi trả hàng
            'return'                   => 16,  // Trả hàng
            'return_transporting'      => 16,  // Đang luân chuyển hàng trả
            'return_sorting'           => 16,  // Đang phân loại hàng trả
            'returning'                => 16,  // Nhân viên đang đi trả hàng
            'return_fail'              => 18,  // Trả hàng thất bại
            'returned'                 => 17,  // Trả hàng thành công
            'exception'                => 12,  // Đơn hàng ngoại lệ
            'damage'                   => 21,  // Hàng bị hư hỏng
            'lost'                     => 21,  // Hàng bị mất
        ];

        $MaTrangThai = $ghnStatusMap[$TrangThai] ?? null;

        if (!$MaTrangThai) {
            return response()->json(['success' => false, 'message' => 'Trạng thái không hợp lệ: ' . $TrangThai], 400);
        }

        // Kiểm tra đơn hàng: chưa hủy (!=5) và thuộc GHN (DonviGH=3)
        $donHang = DB::table('donhang')
            ->where('MaDH', $MaDH)
            ->where('MaTrangthai', '!=', '5')
            ->where('DonviGH', '3')
            ->first();

        if (!$donHang) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        // Ghi log — nếu giao thất bại hoặc trả thất bại thì lưu Reason, còn lại lưu Description
        $logContent = in_array($MaTrangThai, [12, 18]) ? $Reason : $NoiDung;

        DB::table('ghn_log')->insert([
            'MaDH'          => $MaDH,
            'NoiDung'       => $logContent,
            'MaTrangThai'   => $MaTrangThai,
            'TenTrangThai'  => $TrangThai,
            'PhiShipThucTe' => $PhiShipThucTe,
            'TienThuThucTe' => $TienThuThucTe,
            'json'          => '',
        ]);

        // Cập nhật trạng thái đơn hàng
        DB::table('donhang')->where('MaDH', $MaDH)->update([
            'MaTrangthai'   => $MaTrangThai,
            'PhiShipThucTe' => $PhiShipThucTe,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Webhook nhận callback từ Giao Hàng Tiết Kiệm (GHTK)
     * DonviGH = 4
     */
    public function ghtk(Request $request)
    {
        $data = $request->all();

        // Validate required fields
        $requiredFields = ['partner_id', 'status_id', 'label_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return response()->json(['success' => false, 'message' => "Thiếu trường: $field"], 400);
            }
        }

        // Mapping trạng thái GHTK sang mã nội bộ
        $statusMap = [
            '0'   => '1',   // Chưa tiếp nhận
            '-1'  => '5',   // Đã Hủy
            '1'   => '1',   // Chưa tiếp nhận
            '2'   => '11',  // Đã tiếp nhận
            '3'   => '7',   // Đã lấy hàng / Đã nhập kho
            '4'   => '4',   // Đang giao hàng
            '5'   => '8',   // Đã giao hàng / Chưa đối soát
            '6'   => '8',   // Đã Đối Soát
            '7'   => '13',  // Không lấy được hàng
            '8'   => '13',  // Hoãn lấy hàng
            '9'   => '12',  // Không giao được hàng
            '10'  => '2',   // Delay giao hàng
            '11'  => '9',   // Đã đối soát trả hàng
            '12'  => '13',  // Đang lấy hàng
            '20'  => '9',   // Đang trả hàng
            '21'  => '17',  // Đã trả hàng
            '123' => '7',   // Shipper báo đã lấy hàng
            '127' => '13',  // Shipper báo không lấy được hàng
            '128' => '13',  // Shipper báo delay lấy hàng
            '45'  => '8',   // Shipper báo đã giao hàng
            '49'  => '9',   // Shipper báo không giao được hàng
            '410' => '2',   // Shipper báo delay giao hàng
        ];

        $MaDH       = $data['partner_id'];
        $statusId   = (string) $data['status_id'];
        $NoiDung    = $data['reason'] ?? '';

        // Kiểm tra status_id có hợp lệ không
        if (!isset($statusMap[$statusId])) {
            return response()->json(['success' => false, 'message' => 'Trạng thái không hợp lệ'], 400);
        }

        $MaTrangthai = $statusMap[$statusId];

        // Phí ship
        $PhiShipThucTe = $data['fee'] ?? $data['ship_money'] ?? 0;

        // Tiền thu thực tế (chỉ lấy khi đã đối soát — status_id = 8)
        $TienThuThucTe = ($statusId == '8' && isset($data['pick_money'])) ? $data['pick_money'] : 0;

        // Kiểm tra đơn hàng: chưa hủy (!=5) và thuộc GHTK (DonviGH=4)
        $donHang = DB::table('donhang')
            ->where('MaDH', $MaDH)
            ->where('MaTrangthai', '!=', '5')
            ->where('DonviGH', '4')
            ->first();

        if (!$donHang) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        // Tính phụ phí trả hàng: Nếu trả hàng (status 9,11) và không phải nội tỉnh thì +50%
        if (in_array($statusId, ['9', '11'])) {
            $isNoiTinh = (!empty($donHang->Tinh) && $donHang->Tinh == 2);
            if (!$isNoiTinh) {
                $PhiShipThucTe = $PhiShipThucTe * 1.5;
            }
        }

        // Ghi log
        DB::table('ghtk_log')->insert([
            'MaDH'        => $MaDH,
            'MaTrangthai' => $MaTrangthai,
            'NoiDung'     => $NoiDung,
        ]);

        // Cập nhật đơn hàng
        $updateData = [
            'PhiShipThucTe' => $PhiShipThucTe,
            'MaTrangthai'   => $MaTrangthai,
        ];

        if ($TienThuThucTe != 0) {
            $updateData['TienThuThucTe'] = $TienThuThucTe;
        }

        DB::table('donhang')->where('MaDH', $MaDH)->update($updateData);

        return response()->json(['success' => true]);
    }
}
