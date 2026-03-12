<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class API extends Model
{


	public function DonViGiaoHang()
	{
		return DB::table('donvigiaohang')->get();
	}
	public function LayToken(){
		$config = DB::table('api_configs')->where('config_key', 'token_ghtk')->first();
		return $config ? $config->config_value : '';
	}

	public function LayMaTrangThai($id){
		 $ArrMaTrangThai = array(
        "0"=>"1",//Chưa tiếp nhận
        "-1"=>"5",//Đã Hủy
        "1"=>"1",//Chưa tiếp nhận
        "2"=>"11",//Đã tiếp nhận
        "3"=>"7",//Đã lấy hàng/Đã nhập kho
        "4"=>"4",//Đã điều phối giao hàng/Đang giao hàng
        "5"=>"8",//Đã giao hàng/Chưa đối soát
        "6"=>"8",//Đã Đối Soát
        "7"=>"13",//Không lấy được hàng
        "8"=>"13",//Hoãn lấy hàng
        "9"=>"12",//Không giao được hàng
        "10"=>"2",//Delay giao hàng
        "11"=>"9",//Đã đối soát trả hàng
        "12"=>"13",//Đang lấy hàng
        "20"=>"9",//Đang trả hàng
        "21"=>"17",//Đã trả hàng
        "123"=>"7",//Shipper báo đã lấy hàng
        "127"=>"13",//Shipber báo không lấy được hàng
        "128"=>"13",//Shiper báo delay lấy hàng
        "45"=>"8",//Shiper báo đã giao hàng
        "49"=>"9",//Shiper báo không giao được hàng
        "410"=>"2",//Shiper báo delay giao hàng
        );
		 return $ArrMaTrangThai[$id];
	}

	public function DangDonGHTK($MaDH,$products,$SoDienThoai, $TenKH,$DiaChi,$Phuong,$Huyen,$Tinh,$TongTien,$HotLine,$date,$transport,$TongKhoiLuong){
		if($TongTien == 0)
			$GiaTriHangHoa = 2000000;
		else
			$GiaTriHangHoa = $TongTien;
	if($TongKhoiLuong >= 19 && $TongKhoiLuong <=20)
		$TongKhoiLuong = 21;
	if($TongKhoiLuong > 20)
	{
		$order = <<<HTTP_BODY
		{
		    "products": {$products},
		    "order": 
		    {
		        "id": "{$MaDH}",
		        "pick_name":"Thuỷ Sản",
		        "pick_address": "155 Dương Đình Hội, phường Phước Long B",
		        "pick_province":"Tp.HCM",
		        "pick_district":"Quận 9",
		        "pick_tel": "{$HotLine}",
				"tel": "{$SoDienThoai}",
				"name": "{$TenKH}",
				"address": "{$DiaChi}",
				"hamlet": "Khác",
				"ward" : "{$Phuong}",
				"province": "{$Tinh}",
				"district":"{$Huyen}",
				"is_freeship" : 1,
				"pick_date": "{$date}",
				"pick_money": "{$TongTien}",
				"deliver_option" : "none",
				"value": "{$GiaTriHangHoa}",
				"transport" : "{$transport}",
		        "note": "Cho xem hàng/ đồng kiểm",
		        "total_weight": {$TongKhoiLuong},
				"pick_option" : "post",
		        "3pl":1

		    }
		    
		}
		HTTP_BODY;
	}
	else
	{
		$order = <<<HTTP_BODY
		{
		    "products": {$products},
		    "order": {
		        "id": "{$MaDH}",
		        "pick_name":"Thuỷ Sản",
		        "pick_address": "155 Dương Đình Hội, phường Phước Long B",
		        "pick_province":"Tp.HCM",
		        "pick_district":"Quận 9",
		        "pick_tel": "{$HotLine}",
				"tel": "{$SoDienThoai}",
				"name": "{$TenKH}",
				"address": "{$DiaChi}",
				"hamlet": "Khác",
				"ward" : "{$Phuong}",
				"province": "{$Tinh}",
				"district":"{$Huyen}",
				"is_freeship" : 1,
				"pick_date": "{$date}",
				"pick_money": "{$TongTien}",
				"deliver_option" : "none",
				"value": "{$GiaTriHangHoa}",
				"transport" : "{$transport}",
		        "note": "Cho xem hàng/ đồng kiểm",
		        "pick_option" : "post",
		        "total_weight": {$TongKhoiLuong}
		    }
		}
		HTTP_BODY;
	}
	//echo $order."<br>";
	$token = $this->LayToken();
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
	curl_setopt_array($curl, array(
	    CURLOPT_URL => "https://services.giaohangtietkiem.vn/services/shipment/order/?ver=1.5HTTP/1.1",
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => $order,
	    CURLOPT_HTTPHEADER => array(
	        "Content-Type: application/json",
	        
	        "Token: ".$token,
	        

	        "Content-Length: ".strlen($order),
	    ),
	));
	$response = curl_exec($curl);
	$errNo = curl_errno($curl);
	$err = curl_error($curl);
	curl_close($curl);
	// echo $errNo."<br>";
	// echo $err;
return $response;
}

	public function DangDonGHTKKemCap4($MaDH,$products,$SoDienThoai, $TenKH,$DiaChi,$Phuong,$Huyen,$Tinh,$TongTien,$HotLine,$date,$transport,$TongKhoiLuong){
		if($TongTien == 0)
			$GiaTriHangHoa = 2000000;
		else
			$GiaTriHangHoa = $TongTien;
	if($TongKhoiLuong >= 19 && $TongKhoiLuong <=20)
		$TongKhoiLuong = 21;
	if($TongKhoiLuong > 20)
	{
		$order = <<<HTTP_BODY
		{
		    "products": {$products},
		    "order": 
		    {
		        "id": "{$MaDH}",
		        "pick_name":"Thuỷ Sản",
		        "pick_address": "Số 420 Liên Phường, phường Phước Long B",
		        "pick_province":"Tp.HCM",
		        "pick_district":"Quận 9",
		        "pick_tel": "{$HotLine}",
				"tel": "{$SoDienThoai}",
				"name": "{$TenKH}",
				"address": "{$DiaChi}",
				"hamlet": "{$DiaChi}",
				"ward" : "{$Phuong}",
				"province": "{$Tinh}",
				"district":"{$Huyen}",
				"is_freeship" : 1,
				"pick_date": "{$date}",
				"pick_money": "{$TongTien}",
				"deliver_option" : "none",
				"value": "{$GiaTriHangHoa}",
				"transport" : "{$transport}",
		        "note": "Cho xem hàng/ đồng kiểm",
		        "total_weight": {$TongKhoiLuong},
				"pick_option" : "post",
				"tags":[10],
		        "3pl":1

		    }
		    
		}
		HTTP_BODY;
	}
	else
	{
		$order = <<<HTTP_BODY
		{
		    "products": {$products},
		    "order": {
		        "id": "{$MaDH}",
		        "pick_name":"Thuỷ Sản",
		        "pick_address": "Số 420 Liên Phường, phường Phước Long B",
		        "pick_province":"Tp.HCM",
		        "pick_district":"Quận 9",
		        "pick_tel": "{$HotLine}",
				"tel": "{$SoDienThoai}",
				"name": "{$TenKH}",
				"address": "{$DiaChi}",
				"hamlet": "{$DiaChi}",
				"ward" : "{$Phuong}",
				"province": "{$Tinh}",
				"district":"{$Huyen}",
				"is_freeship" : 1,
				"pick_date": "{$date}",
				"pick_money": "{$TongTien}",
				"deliver_option" : "none",
				"value": "{$GiaTriHangHoa}",
				"transport" : "{$transport}",
		        "note": "Cho xem hàng/ đồng kiểm",
		        "pick_option" : "post",
		        "tags":[10],
		        "total_weight": {$TongKhoiLuong}
		    }
		}
		HTTP_BODY;
	}
	//echo $order."<br>";
	$token = $this->LayToken();
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
	curl_setopt_array($curl, array(
	    CURLOPT_URL => "https://services.giaohangtietkiem.vn/services/shipment/order/?ver=1.5HTTP/1.1",
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => $order,
	    CURLOPT_HTTPHEADER => array(
	        "Content-Type: application/json",
	        
	        "Token: ".$token,
	        "Content-Length: ".strlen($order),
	    ),
	));
	$response = curl_exec($curl);
	$errNo = curl_errno($curl);
	$err = curl_error($curl);
	curl_close($curl);
	// echo $errNo."<br>";
	// echo $err;
return $response;
}


public function LayDiaChiCap4($province,$district,$ward_street){
	$token = $this->LayToken();
	$data = array(
    "province" => "Hà nội",
    "district" => "Quận Ba Đình",
    "ward_street" => "Đội Cấn",
    "address" => "",
);
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://services.giaohangtietkiem.vn/services/address/getAddressLevel4?" . http_build_query($data),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => array(
    	"Token: ".$token,
        //"Token: 4B3956b7C7741FFfe2B4dFC8497bAA3E2a1eA0bc",
    ),
));

$response = curl_exec($curl);
curl_close($curl);

echo 'Response: ' . $response;
}
public function HuyDonHangGHTK($MaDH){
	$curl = curl_init();
	$token = $this->LayToken();
	curl_setopt_array($curl, array(
	    CURLOPT_URL => "https://services.giaohangtietkiem.vn/services/shipment/cancel/partner_id:{$MaDH}",
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_HTTPHEADER => array(
	        "Token: ".$token,
	        //"Token: 4B3956b7C7741FFfe2B4dFC8497bAA3E2a1eA0bc", //Token 2021
	        
	    ),
	));

	$response = curl_exec($curl);
	curl_close($curl);

	return $response;
}

public function CapNhatTrangThai(){


}

public function CheckDonGHTK($MaVanDonHang)
{
	$curl = curl_init();
	$token = $this->LayToken();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://services.giaohangtietkiem.vn/services/shipment/v2/'.$MaVanDonHang,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
	  	"Token: ".$token,
	    //'token: 38b4f568b596af4f65edbf0053d477ea9eab2793'
	  ),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	$ketqua = json_decode($response);
	if($ketqua->success == true)
	{
		$dulieu = $ketqua->order;
		$PhiShipThucTe = $dulieu->ship_money;
		$MaTT = $dulieu->status;
		$MaTrangthai = $this->LayMaTrangThai($MaTT);
		if($MaTrangthai == 8)
			$TienThuThucTe = $dulieu->pick_money;
		else
			$TienThuThucTe = 0;
		return $TienThuThucTe;
		//DB::table('donhang')->where('MaVanDonHang',$MaVanDonHang)->update(['MaTrangthai'=>$MaTrangthai,'PhiShipThucTe'=>$PhiShipThucTe,'TienThuThucTe'=>$TienThuThucTe]);
	}
}
	
public function LogViettel($MaDH,$Status_Name,$Status_Id,$Note){
	DB::table('viettel_log')->insert([
		'MaDH' => $MaDH,
        'Status_Name' => $Status_Name,
        'Note' => $Note,
        'Status_Id' => $Status_Id,

	]);
}

public function LayTrangThaiDonHang($MaDH){
	return DB::table('donhang')->where('MaDH',$MaDH)->first();
}





}