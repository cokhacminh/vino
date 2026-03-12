<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class API_GHN extends Model
{

private function LayToken($ChiNhanh)
{
    $token = "18fe527e-77f8-11f0-a089-5ac01be04810";
    return $token;

}
private function LayShopId($ChiNhanh,$CanNang = 0)
{        $ShopId = "4407579";

    return $ShopId;

}
///GIAO HÀNG NHANH
	public function LayServiceID($to_district,$ChiNhanh){

$url = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services";
$token = "18fe527e-77f8-11f0-a089-5ac01be04810";
$ShopId = "5929719";
$curl = curl_init($url);
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYHOST=> false,
  CURLOPT_SSL_VERIFYPEER=>false,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"shop_id\" : {$ShopId},\"from_district\" : 1451,\"to_district\": {$to_district}}",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json",
    "token: {$token}"
  ),
));
$resp = curl_exec($curl);
curl_close($curl);
$DuLieu = json_decode($resp);
$data = $DuLieu->data;
$data1 = $data[0];
return $data1->service_id;
}

public function DangDonGHN($MaDH,$HotLine,$SoDienThoai, $TenKH,$DiaChi,$DistrictID,$WardCode,$TongTien,$ServiceID,$items,$TenSanPham,$CanNang,$ChiNhanh){

    $token = "18fe527e-77f8-11f0-a089-5ac01be04810";
    $ShopId = "5929719";
    $url = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create";
    $CanNang = $CanNang;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
       "Content-Type: application/json",
       "ShopId: {$ShopId}",
       "token: {$token}"
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $mahoa = json_encode($items);

     //"note": "Khách từ chối nhận hàng xin gọi lại hotline {$HotLine} cho shop",
    $data = <<<DATA
    {
        "payment_type_id": 1,
        "note": "CHO XEM HÀNG - KHÔNG THỬ HÀNG",
        "required_note": "CHOXEMHANGKHONGTHU",
        "return_phone": "{$HotLine}",
        "return_address": "279 liên phương",
        "return_district_id": 1451,
        "return_ward_code": "20909",
        "client_order_code": "{$MaDH}",
        "to_name": "{$TenKH}",
        "to_phone": "{$SoDienThoai}",
        "to_address": "{$DiaChi}",
        "to_ward_code": "{$WardCode}",
        "to_district_id": {$DistrictID},
        "cod_amount": {$TongTien},
        "content": "{$TenSanPham}",
        "weight": {$CanNang},
        "length": 10,
        "width": 10,
        "height": 10,
        "insurance_value": {$TongTien},
        "service_id": {$ServiceID},
        "pick_shift":[2],
        "items": 
             {$mahoa}

    }
    DATA;
    //echo $data;
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;
}


public function HuyDon($MaGHN){
    $a = DB::table('donhang')->where('MaVanDonHang',$MaGHN)->select('MaNV')->first();
    if($a)
    {
        $b = DB::table('users')->where('id',$a->MaNV)->select('MaCN')->first();
        $token = "18fe527e-77f8-11f0-a089-5ac01be04810";
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://online-gateway.ghn.vn/shiip/public-api/v2/switch-status/cancel",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\"order_codes\":[\"{$MaGHN}\"]}",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json",
            "token: {$token}"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } 
        else {
            return $response;
        }
    }


}

public function TinhPhiShip($to_district,$service_id,$to_ward_code,$weight){

$curl = curl_init();

$dulieu = array(
    "from_district_id"=> 1451,
    "service_id"=> (int)$service_id,
    "service_type_id"=> null,
    "to_district_id"=> (int)$to_district,
    "to_ward_code"=> $to_ward_code,
    "height"=> 10,
    "length"=> 10,
    "weight"=> $weight,
    "width"=> 10,
    "insurance_fee"=> 0,
    "coupon"=> null
);
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode($dulieu),
  CURLOPT_HTTPHEADER => array(
    'token: 89c0d602-332b-11ec-ab03-e680cb72ac98',
    'ShopId: 2126255',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
$dulieu = json_decode($response);
$data = $dulieu->data;
echo number_format($data->total);

}


public function LayLichTrinhDonHang($MaGHN){
    $MaCN = DB::table('donhang')->leftJoin('users','donhang.MaNV','like','users.id')->where('MaVanDonHang',$MaGHN)->select('users.MaCN')->first()->MaCN;
    $token = "18fe527e-77f8-11f0-a089-5ac01be04810";
    $url = "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail";
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\"order_code\": \"{$MaGHN}\"}",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        "token: {$token}"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $dulieu = json_decode($response);
    $code =  $dulieu->code;
    
    if($code == 200)
    {
        $data = $dulieu->data;
        // print_r($data->log);
        // echo "<br>";
        foreach($data->log as $log)
        {
            if(isset($log->driver_name))
                $TaiXe = "( Tài xế : ".$log->driver_name." )";
            else
                $TaiXe = "";
            $TrangThai = $log->status;
            $TenTrangThai = $this->LayTenTrangThai($TrangThai);
            $MaTrangthai = $this->LayMaTrangThai($TrangThai);
            DB::table('donhang')->where('MaVanDonHang',$MaGHN)->update(['MaTrangthai'=>$MaTrangthai]);

            
            $updated_date = $log->updated_date;
            $ThoiGian = date("H:i:s d/m/Y", strtotime($updated_date));
            if(isset($log->reason))
                $LyDo = "Lý do : ".$log->reason;
            else
                $LyDo = "";
            echo "<p>".$ThoiGian." : Trạng thái ".$TenTrangThai." ".$LyDo.$TaiXe."</p>";
        }
    }
    elseif($code == 400)
        echo "Chưa có dữ liệu đơn hàng";
    else
    {
        echo $MaGHN."<br>";
        echo $response;
    }
}


public function LayDanhSachTinh(){
    $url = "https://online-gateway.ghn.vn/shiip/public-api/master-data/province";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
       "Content-Type: application/json",
       "ShopId: 2126255",
       "Token: 89c0d602-332b-11ec-ab03-e680cb72ac98",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);



    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;
}
public function LayDanhSachHuyen($ProvinceID){
    $url = "https://online-gateway.ghn.vn/shiip/public-api/master-data/district";
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\r\n\t\"province_id\":{$ProvinceID}\r\n}",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        "postman-token: 6469d349-564c-5a25-a3a3-bef5884a6551",
        "token: 89c0d602-332b-11ec-ab03-e680cb72ac98"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
}

public function LayDanhSachXa($DistrictID){
    $url = "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id";
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\r\n\t\"district_id\":{$DistrictID}\r\n}",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        "postman-token: 6469d349-564c-5a25-a3a3-bef5884a6551",
        "token: 89c0d602-332b-11ec-ab03-e680cb72ac98"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return $response;
}

public function LayMaTrangThai($MaTT){
    $ArrMaTrangThai = array(
    "ready_to_pick"=>   11 ,  //Mới tạo đơn hàng
    "picking"=>  15  ,  //Nhân viên đang lấy hàng
    "cancel"=> 5   ,  //Hủy đơn hàng
    "money_collect_picking"=> 15   ,  //Đang thu tiền người gửi
    "picked"=> 7   ,  //Nhân viên đã lấy hàng
    "storing"=> 22   ,  //Hàng đang nằm ở kho
    "transporting"=>  4  ,  //Đang luân chuyển hàng
    "sorting"=>   7 ,  //Đang phân loại hàng hóa
    "delivering"=> 4   ,  //Nhân viên đang giao cho người nhận
    "money_collect_delivering"=> 4   ,  //Nhân viên đang thu tiền người nhận
    "delivered"=>  8  ,  //Nhân viên đã giao hàng thành công
    "delivery_fail"=>  12  ,  //Nhân viên giao hàng thất bại
    "waiting_to_return"=> 16   ,  //Đang đợi trả hàng về cho người gửi
    "return"=>   16 ,  //Trả hàng
    "return_transporting"=>  16  ,  //Đang luân chuyển hàng trả
    "return_sorting"=>  16  ,  //Đang phân loại hàng trả
    "returning"=>  16  ,  //Nhân viên đang đi trả hàng
    "return_fail"=>  18  ,  //Nhân viên trả hàng thất bại
    "returned"=>   17 ,  //Nhân viên trả hàng thành công
    "exception"=>  12  ,  //Đơn hàng ngoại lệ không nằm trong quy trình
    "damage"=> 21   ,  //Hàng bị hư hỏng
    "lost"=>  21    //Hàng bị mất
    );
    return $ArrMaTrangThai[$MaTT];
}

public function LayTenTrangThai($MaTT){
    $ArrMaTrangThai = array(
    "ready_to_pick"=>   11 ,  //Mới tạo đơn hàng
    "picking"=>  15  ,  //Nhân viên đang lấy hàng
    "cancel"=> 5   ,  //Hủy đơn hàng
    "money_collect_picking"=> 15   ,  //Đang thu tiền người gửi
    "picked"=> 7   ,  //Nhân viên đã lấy hàng
    "storing"=> 22   ,  //Hàng đang nằm ở kho
    "transporting"=>  4  ,  //Đang luân chuyển hàng
    "sorting"=>   7 ,  //Đang phân loại hàng hóa
    "delivering"=> 4   ,  //Nhân viên đang giao cho người nhận
    "money_collect_delivering"=> 4   ,  //Nhân viên đang thu tiền người nhận
    "delivered"=>  8  ,  //Nhân viên đã giao hàng thành công
    "delivery_fail"=>  12  ,  //Nhân viên giao hàng thất bại
    "waiting_to_return"=> 16   ,  //Đang đợi trả hàng về cho người gửi
    "return"=>   16 ,  //Trả hàng
    "return_transporting"=>  16  ,  //Đang luân chuyển hàng trả
    "return_sorting"=>  16  ,  //Đang phân loại hàng trả
    "returning"=>  16  ,  //Nhân viên đang đi trả hàng
    "return_fail"=>  18  ,  //Nhân viên trả hàng thất bại
    "returned"=>   17 ,  //Nhân viên trả hàng thành công
    "exception"=>  12  ,  //Đơn hàng ngoại lệ không nằm trong quy trình
    "damage"=> 21   ,  //Hàng bị hư hỏng
    "lost"=>  21    //Hàng bị mất
    );
    $MaTrangthai = $ArrMaTrangThai[$MaTT];
    return DB::table('trangthaidonhang')->where('MaTT',$MaTrangthai)->first()->TenTT;
}















}