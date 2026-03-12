<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class API_DangDon extends Model
{
    /**
     * Lấy giá trị config từ bảng api_configs
     */
    public static function getConfig($key)
    {
        $row = DB::table('api_configs')->where('config_key', $key)->first();
        return $row ? $row->config_value : '';
    }

    /**
     * Lấy tất cả config API giao hàng
     */
    public static function getAllConfigs()
    {
        $rows = DB::table('api_configs')
            ->whereIn('config_key', ['token_ghtk', 'token_ghn', 'shopid_ghn', 'hotline'])
            ->pluck('config_value', 'config_key');

        return [
            'token_ghtk' => $rows['token_ghtk'] ?? '',
            'token_ghn'  => $rows['token_ghn'] ?? '',
            'shopid_ghn' => $rows['shopid_ghn'] ?? '',
            'hotline'    => $rows['hotline'] ?? '',
        ];
    }

    // =========================================================================
    // GHTK — GIAO HÀNG TIẾT KIỆM
    // =========================================================================

    /**
     * Đăng đơn lên GHTK
     *
     * @param array $orderData  ['MaDH', 'TongTien', 'transport', 'LoiAPI']
     * @param array $customer   ['TenKH', 'SoDienThoai', 'DiaChi', 'Xa', 'Huyen', 'Tinh']
     * @param string $productName  Tên sản phẩm chính
     * @param int $totalWeight  Tổng khối lượng (gram)
     * @return array ['success' => bool, 'message' => string, 'ma_van_don' => string, 'phi_ship' => int]
     */
    public function dangDonGHTK($orderData, $customer, $productName, $totalWeight)
    {
        $token   = self::getConfig('token_ghtk');
        $hotline = self::getConfig('hotline');

        if (empty($token)) {
            return ['success' => false, 'message' => 'Chưa cấu hình token GHTK', 'ma_van_don' => '', 'phi_ship' => 0];
        }

        $MaDH       = $orderData['MaDH'];
        $TongTien   = $orderData['TongTien'];
        $transport  = $orderData['transport'] ?? 'road';
        $LoiAPI     = $orderData['LoiAPI'] ?? 'Hợp Lệ';

        $TenKH      = !empty($customer['TenKH']) ? $customer['TenKH'] : 'API';
        $SoDienThoai = $customer['SoDienThoai'];
        $DiaChi     = $customer['DiaChi'];
        $Xa         = $customer['Xa'];
        $Huyen      = $customer['Huyen'];
        $Tinh       = $customer['Tinh'];

        $GiaTriHangHoa = ($TongTien == 0) ? 2000000 : $TongTien;

        // Quy đổi gram → kg
        $weightKg = round($totalWeight / 1000);
        if ($weightKg >= 19 && $weightKg <= 20) $weightKg = 21;

        // Chọn địa chỉ lấy hàng theo LoiAPI
        if ($LoiAPI === 'Hợp Lệ') {
            $pickAddress = '155 Dương Đình Hội, phường Phước Long B';
            $tags = '';
        } else {
            $pickAddress = 'Số 420 Liên Phường, phường Phước Long B';
            $tags = '"tags":[10],';
        }

        // Build product JSON
        $productsJson = json_encode([[
            'name'         => $productName,
            'weight'       => $weightKg,
            'length'       => 1,
            'width'        => 1,
            'height'       => 1,
            'product_code' => $productName,
            'quantity'     => 1,
        ]]);

        // Xây dựng payload — thêm "3pl":1 nếu weight > 20
        $extraFields = '';
        if ($weightKg > 20) {
            $extraFields = '"pick_option": "post", "3pl": 1';
        } else {
            $extraFields = '"pick_option": "post"';
        }

        $payload = json_encode([
            'products' => json_decode($productsJson, true),
            'order' => array_filter([
                'id'              => $MaDH,
                'pick_name'       => 'Thuỷ Sản',
                'pick_address'    => $pickAddress,
                'pick_province'   => 'Tp.HCM',
                'pick_district'   => 'Quận 9',
                'pick_tel'        => $hotline,
                'tel'             => $SoDienThoai,
                'name'            => $TenKH,
                'address'         => $DiaChi,
                'hamlet'          => ($LoiAPI === 'Hợp Lệ') ? 'Khác' : $DiaChi,
                'ward'            => $Xa,
                'province'        => $Tinh,
                'district'        => $Huyen,
                'is_freeship'     => 1,
                'pick_date'       => date('Y-m-d'),
                'pick_money'      => (string)$TongTien,
                'deliver_option'  => 'none',
                'value'           => (string)$GiaTriHangHoa,
                'transport'       => $transport,
                'note'            => 'Cho xem hàng/ đồng kiểm',
                'total_weight'    => $weightKg,
                'pick_option'     => 'post',
                'tags'            => ($LoiAPI !== 'Hợp Lệ') ? [10] : null,
                '3pl'             => ($weightKg > 20) ? 1 : null,
            ], function($v) { return $v !== null; }),
        ]);

        $response = $this->curlPost(
            'https://services.giaohangtietkiem.vn/services/shipment/order/?ver=1.5',
            $payload,
            ['Content-Type: application/json', 'Token: ' . $token]
        );

        if ($response['error']) {
            return ['success' => false, 'message' => 'cURL Error: ' . $response['error'], 'ma_van_don' => '', 'phi_ship' => 0];
        }

        $data = json_decode($response['body']);
        if (!is_object($data)) {
            return ['success' => false, 'message' => $MaDH . ': Không giải mã được response từ GHTK', 'ma_van_don' => '', 'phi_ship' => 0];
        }

        if (!empty($data->success) && $data->success === true) {
            $order = $data->order;
            return [
                'success'    => true,
                'message'    => $MaDH . ' đăng GHTK thành công',
                'ma_van_don' => $order->label,
                'phi_ship'   => $order->fee ?? 0,
            ];
        }

        // Xử lý lỗi ORDER_ID_EXIST
        if (isset($data->error) && isset($data->error->code) && $data->error->code === 'ORDER_ID_EXIST') {
            return [
                'success'    => true,
                'message'    => $MaDH . ' đã tồn tại trên GHTK, cập nhật mã vận đơn: ' . $data->error->ghtk_label,
                'ma_van_don' => $data->error->ghtk_label,
                'phi_ship'   => 0,
            ];
        }

        $errorMsg = $data->message ?? 'Lỗi không xác định từ GHTK';
        return ['success' => false, 'message' => $errorMsg, 'ma_van_don' => '', 'phi_ship' => 0];
    }

    // =========================================================================
    // GHN — GIAO HÀNG NHANH
    // =========================================================================

    /**
     * Lấy Service ID từ GHN
     */
    public function layServiceIdGHN($toDistrict)
    {
        $token  = self::getConfig('token_ghn');
        $shopId = self::getConfig('shopid_ghn');

        if (empty($token) || empty($shopId)) {
            return null;
        }

        $payload = json_encode([
            'shop_id'       => (int)$shopId,
            'from_district' => 1451,
            'to_district'   => (int)$toDistrict,
        ]);

        $response = $this->curlPost(
            'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services',
            $payload,
            ['Content-Type: application/json', 'token: ' . $token]
        );

        if ($response['error']) return null;

        $data = json_decode($response['body']);
        if (isset($data->data) && is_array($data->data) && count($data->data) > 0) {
            return $data->data[0]->service_id;
        }

        return null;
    }

    /**
     * Đăng đơn lên GHN
     *
     * @param array $orderData   ['MaDH', 'TongTien']
     * @param array $customer    ['TenKH', 'SoDienThoai', 'DiaChi', 'Xa', 'Huyen', 'Tinh', 'DistrictID', 'WardCode']
     * @param string $productName  Tên sản phẩm chính
     * @param int $totalWeight   Tổng cân nặng (gram)
     * @param int $serviceId     Service ID từ GHN
     * @return array ['success' => bool, 'message' => string, 'ma_van_don' => string, 'phi_ship' => int]
     */
    public function dangDonGHN($orderData, $customer, $productName, $totalWeight, $serviceId)
    {
        $token   = self::getConfig('token_ghn');
        $shopId  = self::getConfig('shopid_ghn');
        $hotline = self::getConfig('hotline');

        if (empty($token) || empty($shopId)) {
            return ['success' => false, 'message' => 'Chưa cấu hình token hoặc shop ID GHN', 'ma_van_don' => '', 'phi_ship' => 0];
        }

        $MaDH       = $orderData['MaDH'];
        $TongTien   = $orderData['TongTien'];
        $TenKH      = !empty($customer['TenKH']) ? $customer['TenKH'] : 'API';
        $DistrictID = (int)$customer['DistrictID'];
        $WardCode   = $customer['WardCode'];

        $DiaChiChiTiet = $customer['DiaChi'] . ', ' . $customer['Xa'] . ', ' . $customer['Huyen'] . ', ' . $customer['Tinh'];

        $items = [[
            'name'     => $productName,
            'quantity' => 1,
            'length'   => 10,
            'width'    => 10,
            'height'   => 10,
            'category' => ['level1' => $productName],
        ]];

        $payload = json_encode([
            'payment_type_id'   => 1,
            'note'              => 'CHO XEM HÀNG - KHÔNG THỬ HÀNG',
            'required_note'     => 'CHOXEMHANGKHONGTHU',
            'return_phone'      => $hotline,
            'return_address'    => '279 liên phương',
            'return_district_id'=> 1451,
            'return_ward_code'  => '20909',
            'client_order_code' => $MaDH,
            'to_name'           => $TenKH,
            'to_phone'          => $customer['SoDienThoai'],
            'to_address'        => $DiaChiChiTiet,
            'to_ward_code'      => $WardCode,
            'to_district_id'    => $DistrictID,
            'cod_amount'        => (int)$TongTien,
            'content'           => $productName,
            'weight'            => (int)$totalWeight,
            'length'            => 10,
            'width'             => 10,
            'height'            => 10,
            'insurance_value'   => (int)$TongTien,
            'service_id'        => (int)$serviceId,
            'pick_shift'        => [2],
            'items'             => $items,
        ]);

        $response = $this->curlPost(
            'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create',
            $payload,
            ['Content-Type: application/json', 'ShopId: ' . $shopId, 'token: ' . $token]
        );

        if ($response['error']) {
            return ['success' => false, 'message' => 'cURL Error: ' . $response['error'], 'ma_van_don' => '', 'phi_ship' => 0];
        }

        $data = json_decode($response['body'], true);
        if (!is_array($data)) {
            return ['success' => false, 'message' => $MaDH . ': Không giải mã được response từ GHN', 'ma_van_don' => '', 'phi_ship' => 0];
        }

        if (($data['code'] ?? 0) == 200) {
            return [
                'success'    => true,
                'message'    => 'Đăng API GHN đơn hàng ' . $MaDH . ' thành công',
                'ma_van_don' => $data['data']['order_code'] ?? '',
                'phi_ship'   => $data['data']['total_fee'] ?? 0,
            ];
        }

        return [
            'success'    => false,
            'message'    => $data['code_message_value'] ?? ($data['message'] ?? 'Lỗi không xác định từ GHN'),
            'ma_van_don' => '',
            'phi_ship'   => 0,
        ];
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    /**
     * cURL POST helper — trả ['body' => string, 'error' => string|null]
     */
    private function curlPost($url, $payload, $headers = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        ]);

        $body = curl_exec($curl);
        $error = curl_errno($curl) ? curl_error($curl) : null;
        curl_close($curl);

        return ['body' => $body, 'error' => $error];
    }
}
