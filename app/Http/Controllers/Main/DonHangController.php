<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\DonHang;
use App\BanHang;
use App\SanPham;
use App\QuyenHan;
use App\Kho;
use Illuminate\Support\Facades\Auth;
use App\API;
// use App\API_JT;
use App\API_GHN;
// use App\API_Ninjavan;
use \NumberFormatter;
use Response;
use File;
use DB;
use Jenssegers\Agent\Agent;
class DonHangController extends Controller
{
    public function ReturnInit($View_Path,$DanhSachDonHang = array()){
        $MaCN = Auth::user()->MaCN;
        $DonHangModel = new DonHang();
        $BanHangModel = new BanHang();
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
        $DanhSachNhanVien = $DonHangModel->SalesActive();
        $DanhSachTrangThai = $BanHangModel->TrangThai();
        $DanhSachSanPham = DB::table('sanpham')->select('MaSP','ID','TenSP')->get();
        
        $DanhSachTinh = $DonHangModel->DanhSachTinh();
        $DonViGiaoHang = $BanHangModel->DonViGiaoHangActive();
        $dulieudonhang = $this->XuLyDuLieuDonHang($DanhSachDonHang);

        $DanhSachKho = $DonHangModel->DanhSachKho();
        $PhanLoaiDonHang = $DonHangModel->PhanLoaiDonHang(date("y-m"),$QuyenHan);
        $SoCombo = DB::table('combo')->where('TinhTrang','Đang Hoạt Động')->count();
        $SoKM = DB::table('khuyenmai')->where('TinhTrang','Đang Hoạt Động')->count();

        return view($View_Path,[
            'danhsachdonhang'=> $dulieudonhang,
            'danhsachnhanvien' => $DanhSachNhanVien,
            'trangthai' => $DanhSachTrangThai,
            'sanpham'=>$DanhSachSanPham,
            'DonViGiaoHang'=>$DonViGiaoHang,
            'tinh' =>$DanhSachTinh,
            'DanhSachKho'=>$DanhSachKho,
            'PhanLoaiDonHang'=>$PhanLoaiDonHang,
            'SoCombo'=>$SoCombo,
            'SoKM'=>$SoKM

        ]);
    }
    public function ThaoTacDanhSachDonYCHTTheoNV($TenTT, $MaDH,$XuLy){
        $color;
        if($XuLy == "Đã Xử Lý")
        {
            $color = "green";
        }
        else
        {
            $color = "#ff710d";
        }


        $url = \URL::to('/');
        return '
            <p style="background-color: '.$color.'; border-radius: 4px;font-weight: bold;color: #fff;padding: 0 2px; text-align: center">'.$XuLy.'</p>

        ';
    }




    public function DanhSachDonHang(){
        $DonHangModel = new DonHang();
        $agent = new Agent();
        if ($agent->isMobile()) {
            $View_Path = 'don-hang.danh-sach-don-hang-mobile';
        }
        else
        $View_Path = 'don-hang.danh-sach-don-hang';
        $DanhSachDonHang = array();
        return $this->ReturnInit($View_Path,$DanhSachDonHang);
    }
    public function AjaxDanhSachDonHang(){
        $MaNV = Auth::user()->id;
        $DonHangModel = new DonHang();
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
        if(isset($QuyenHan['Admin'])||isset($QuyenHan['Quản Lý Giao Hàng']))
        $DanhSachDonHang = $DonHangModel->ToanBoDonHang();
        else if(isset($QuyenHan['Quản Lý Bán Hàng']))
            $DanhSachDonHang = $DonHangModel->DanhSachDonHangTheoChiNhanh();
        else
            $DanhSachDonHang = $DonHangModel->DanhSachDonHang($MaNV);
        $DuLieu = $this->XuLyDuLieuDonHang($DanhSachDonHang,"ajax");
        echo $DuLieu;
    }

    public function DanhSachDonHangCH(){
        $MaNV = Auth::user()->id;
        $DonHangModel = new DonHang();
        $DanhSachDonHang = $DonHangModel->DanhSachDonHang($MaNV);
        $View_Path = 'don-hang.danh-sach-don-hang';
        return $this->ReturnInit($View_Path,$DanhSachDonHang);
    }

    public function DonHangMobile(){
        $agent = new Agent();
        if ($agent->isMobile()) {
            echo "found";
        }
        else
            echo "not found";
    }

    public function DanhSachDonHangPhanLoai($PhanLoai){
        $MaNV = Auth::user()->id;
        $DonHangModel = new DonHang();
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
        if(isset($QuyenHan['Admin']))
        $DanhSachDonHang = $DonHangModel->ToanBoDonHangPhanLoai($PhanLoai);
        else if(isset($QuyenHan['Quản Lý Bán Hàng']))
            $DanhSachDonHang = $DonHangModel->DanhSachDonHangTheoChiNhanhPhanLoai($PhanLoai);
        else
            $DanhSachDonHang = $DonHangModel->DanhSachDonHangPhanLoai($MaNV,$PhanLoai);
        $View_Path = 'don-hang.danh-sach-don-hang';
        return $this->ReturnInit($View_Path,$DanhSachDonHang);
    }
    public function XuatExcel(request $request){
            $fileType = \PHPExcel_IOFactory::identify(resource_path('excel-template/DanhSachDonHang.xlsx')); // đọc loại file template
            $objReader = \PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load(resource_path('excel-template/DanhSachDonHang.xlsx')); //load dữ liệu từ file excel luu vao bien $objPHPExcel
            $ThoiGian = $request->thoigian;
            $TachThoiGian = explode(" - ",$ThoiGian);
            $NgayBatDau = date_format( new \DateTime(str_replace("/","-", $TachThoiGian[0])),'Y-m-d');
            $NgayKetThuc = date_format( new \DateTime(str_replace("/","-", $TachThoiGian[1])),'Y-m-d');

            $i = 0;
    		$TrangThaiXuat = $request->TrangThai;
            $DonviGH = $request->dvgh;
            $NhanVien = $request->chonnhanvien;
            //$objPHPExcel->createSheet();
	        $setCell = $objPHPExcel->setActiveSheetIndex(0);
	           $raw = "";
	            // $Cell = $objPHPExcel->getActiveSheet()->setTitle($TenDanhMucSanPham->TenDMSP);
            if($TrangThaiXuat != "0")
                $raw .= "MaTrangthai = '".$TrangThaiXuat."'";
            if($DonviGH != "0")
                $raw .= "DonviGH = '".$DonviGH."'";
            if($NhanVien != "0")
                $raw .= "MaNV = '".$NhanVien."'";
            if($raw != "")
                $DonHang = DB::table('donhang')->where('ThoiGian','>=', $NgayBatDau.' '.'00:00:00' )->where('ThoiGian','<=', $NgayKetThuc.' '.'23:59:59')->whereRaw($raw)->OrderBy('ThoiGian','asc')->get(); 
            else
                $DonHang = DB::table('donhang')->where('ThoiGian','>=', $NgayBatDau.' '.'00:00:00' )->where('ThoiGian','<=', $NgayKetThuc.' '.'23:59:59')->OrderBy('ThoiGian','asc')->get(); 
           $this->XuatDonHangRaFileExCel($setCell,  $DonHang);
            $i++;
	        


            // CHỨC NĂNG TỰ SAO LƯU FILE EXPORT VÀO PUBLIC
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); //Ham tao moi file excel
         
            if (!is_dir(public_path('excel'))) {
                mkdir(public_path('excel'));
            }
         
            if (!is_dir(public_path('excel/import'))) {
                mkdir(public_path('excel/import'));
            }
            //-----------------------------------------------------------
         
            $path = 'excel/import/' . time() . 'danh-sach-don-hang.xlsx'; //dat ten cho file excel
         
            $objWriter->save(public_path($path)); //luu file excel vao thu muc
         
            return redirect($path); //tra file excel ve cho nguoi dung
    }
    public function XuatDonHangRaFileExCel($setCell, $DONHANG){
            $index = 1;
 
            $row = 2;  //danh dau dong bat dau them data, su dung trong vong lap foreach
            
            foreach ($DONHANG as $key => $item) {
                $TEN_NV = "Không tìm thấy nhân viên";
                $TEN_NV = DB::table('users')->where('id',$item->MaNV)->first();
                if ($TEN_NV) {
                   $TEN_NV = $TEN_NV->name;
                }
                $DB_KH = DB::table('khachhang')->where('MaDH',$item->MaDH)->first();
                if($DB_KH)                
                {
                    $TenKH = $DB_KH->TenKH;
                    $SoDienthoaiKH = $DB_KH->SoDienThoai;
                    $DiaChiKH = $DB_KH->DiaChi;
                    $huyen = $DB_KH->Huyen;
                    $tinh = $DB_KH->Tinh;
                    $xa = $DB_KH->Xa;
                }
                else
                {
                    $TenKH = "";
                    $SoDienthoaiKH = "";
                    $DiaChiKH = "";
                    $huyen = "";
                    $tinh = "";
                    $xa = "";
                }
                
                $GhiChu = $item->GhiChu;
                $TRANG_THAI = DB::table('trangthaidonhang')->where('MaTT',$item->MaTrangthai)->first();
                $SanPham = "";
                $laydulieu = DB::table('chitietdonhang')->where('MaDH',$item->MaDH)->get();

                foreach($laydulieu as $chitiet)
                {
                    $SP = DB::table('sanpham')->where('MaSP',$chitiet->MaSP)->first();
                    if($SP)
                        $SanPham .= $chitiet->SoLuong." ".$SP->DonViTinh." ".$SP->TenSP." \n ";
                    else
                        $SanPham .= $chitiet->SoLuong." ".$chitiet->MaSP." \n ";
                }

                $setCell
                    ->setCellValue('A' . $row, $index)
                    ->setCellValue('B' . $row, $item->MaDH)
                    ->setCellValue('C' . $row, $TEN_NV)
                    ->setCellValue('D' . $row, $TenKH)
                    ->setCellValue('E' . $row, $SoDienthoaiKH)
                    ->setCellValue('F' . $row, $DiaChiKH)
                    ->setCellValue('G' . $row, $xa)
                    ->setCellValue('H' . $row, $huyen)
                    ->setCellValue('I' . $row, $tinh)
                    ->setCellValue('J' . $row, $SanPham)
                    ->setCellValue('K' . $row, $GhiChu)
                    ->setCellValue('L' . $row, $item->TongTien)
                    ->setCellValue('M' . $row, $TRANG_THAI->TenTT)
                    ->setCellValue('N' . $row, $item->ThoiGian) ;
                    
                    
         
                $index++;
         
                $row++;
            }
    }

    public function Filter(Request $request){
        $ThoiGian = $request->ThoiGian;
        $TachThoiGian = explode(" - ",$ThoiGian);
        $from_date = date_format( new \DateTime(str_replace("/","-",$TachThoiGian[0])),'Y-m-d');
        $to_date = date_format( new \DateTime(str_replace("/","-", $TachThoiGian[1])),'Y-m-d');
        $manv = 0;
        if(isset($request->filter_manv)) $manv = $request->filter_manv;
        $trangthai = 0;
        if(isset($request->filter_trangthai)) $trangthai = $request->filter_trangthai;
        $DVGH = 0;
        if(isset($request->DVGH)) $DVGH = $request->DVGH;
        $DonHangModel = new DonHang();
        $DanhSachDonHang = $DonHangModel->LocDanhSachDonHang($manv,$trangthai,$DVGH,$from_date,$to_date);
         $DuLieu = $this->XuLyDuLieuDonHang($DanhSachDonHang,"ajax");
         echo $DuLieu;
    }


    public function ThemDonHang(Request $request){
        $DonHangModel = new DonHang();
        $KhoModel = new Kho();
        $MaNV = Auth::user()->id;
        $checkdate =  $DonHangModel->CheckDate(date("Y-m-d"));
        if (!$checkdate) 
            {
                $DonHangModel->ThemID(date("Y-m-d"));
            }
        $id = $DonHangModel->LayId();
        $DonHangModel->CapNhatID(date("Y-m-d"));
        $MaDH = "DH".date("dmy")."S".$id;
        $MaSP= $request->input('MaSP');
        $SoLuong= $request->input('SoLuong');
        $TongTien = $request->TongTien;
        $TongTien = str_replace(".","",$TongTien);
        $TongTien = str_replace(",","",$TongTien);
        if(isset($request->GiamGia))
            $GiamGia = $request->GiamGia;
        else
            $GiamGia = 0;
        $DonviGH = $request->dvgh;
        $GhiChu = $request->GhiChu;
        if($SoLuong < 0)$SoLuong = abs($SoLuong);
        $count = count($MaSP);
        $data = array_merge($MaSP,$SoLuong);

        $noidungloi = "";
        $error = false;
        $NgayBan = date("Y-m-d");

        for ($i=0; $i < $count ; $i++) 
        {
            $MaSP = $data[$i];
            $SoLuongSP = $data[$i+$count];
            $CheckIDSP = DB::table('sanpham')->where('MaSP',$MaSP)->first();
            $NhomSP = $CheckIDSP->DanhMucSP;
            $MaKiemTra = $MaDH.".".$MaSP;
            $DonHangModel->ThemChiTietDonHang($MaKiemTra,$MaDH,$MaSP,$SoLuongSP,$NgayBan,$NhomSP);
        }
        if(isset($request->MaSP_Tang))
        {
            $MaSP_Tang= $request->input('MaSP_Tang');
            $SoLuong_Tang= $request->input('SoLuong_Tang');
            $SoQuaTang = count($MaSP_Tang);
            $data_tang = array_merge($MaSP_Tang,$SoLuong_Tang);
            for ($i=0; $i < $SoQuaTang ; $i++) 
            {
                $MaSP = $data_tang[$i];
                $SoLuongSP = $data_tang[$i+$SoQuaTang];
                $CheckIDSP = DB::table('sanpham')->Where('MaSP',$MaSP)->first();
                $NhomSP = $CheckIDSP->DanhMucSP;
                $MaKiemTra = $MaDH.".".$MaSP;
                $DonHangModel->ThemQuaTang($MaKiemTra,$MaDH,$MaSP,$SoLuongSP,$NgayBan,$NhomSP);

            }
        }

                $TenTinh = $request->Tinh;
                $TenHuyen = $request->Huyen;
                $MaDiaChinh = $request->Xa;
                $TenXa = $DonHangModel->DiaChinh($MaDiaChinh)->Xa;
                $DiaChiKH = $request->DiaChi;
                

            $TenKH = $request->TenKH;
            $SoDienThoai = str_replace(" ","",$request->SoDienThoai);
            if($error ==false)
            {
                $DonHangModel->ThemKhachHang($MaDH,$TenKH,$SoDienThoai,$DiaChiKH,$TenTinh,$TenHuyen,$TenXa,$MaDiaChinh);

                $DonHangModel->ThemDonHang(
                $MaDH,
                $MaNV,
                $TongTien,
                $GiamGia,
                $DonviGH,
                $GhiChu
                );
                $noidungloi = $MaDH;
                $MaCN = Auth::user()->MaCN;
                if($MaCN =="SG")
                {
                    $CheckData = DB::table('data_sg')->where('SoDienThoai',$SoDienThoai)->first();
                    if(!$CheckData)

                        DB::table('data_sg')->insert([
                            "TenKH"=>$TenKH,
                            "SoDienThoai"=>$SoDienThoai,
                            "MaNV"=>$MaNV,
                            "MaNhomKH"=>7,
                            "Tinh"=>$TenTinh
                            ]);

                }
                else
                {
                    $CheckData = DB::table('data_nt')->where('SoDienThoai',$SoDienThoai)->first();
                    if(!$CheckData)
                        DB::table('data_nt')->insert([
                            "TenKH"=>$TenKH,
                            "SoDienThoai"=>$SoDienThoai,
                            "MaNV"=>$MaNV,
                            "MaNhomKH"=>7,
                            "Tinh"=>$TenTinh
                            ]);
                    
                }
            }
            $result = array("loi"=>$error,"noidung"=>$noidungloi);
            echo json_encode($result);   
    }
    public function SuaDonHang(Request $request){
        $DonHangModel = new DonHang();
        $KhoModel = new Kho();
        $MaDH = $request->input('MaDH');
        $MaSP= $request->input('MaSP');
        $SoLuong= $request->input('SoLuong');
        
        
        $TongTien = $request->TongTien;
        $TongTien = str_replace(".","",$TongTien);
        $TongTien = str_replace(",","",$TongTien);
        $DonviGH = $request->dvgh;
        $GhiChu = $request->GhiChu;
        if(isset($request->GiamGia))
            $GiamGia = $request->GiamGia;
        else
            $GiamGia = 0;
        if($SoLuong < 0)$SoLuong = abs($SoLuong);
        $count = count($MaSP);
        $data = array_merge($MaSP,$SoLuong);
        $noidungloi = "";
        $error = false;
        $NgayBan = DB::table('donhang')->where('MaDH',$MaDH)->select('Ngay')->first()->Ngay;
        DB::table('chitietdonhang')->where('MaDH',$MaDH)->delete();

            for ($i=0; $i < $count ; $i++) 
            {
                $MaSP = $data[$i];
                $SoLuongSP = $data[$i+$count];
                $CheckIDSP = DB::table('sanpham')->Where('MaSP',$MaSP)->first();
                $NhomSP = $CheckIDSP->DanhMucSP;
                $MaKiemTra = $MaDH.".".$MaSP;
                $DonHangModel->ThemChiTietDonHang($MaKiemTra,$MaDH,$MaSP,$SoLuongSP,$NgayBan,$NhomSP);

            }
        if(isset($request->MaSP_Tang))
        {
            $MaSP_Tang= $request->input('MaSP_Tang');
            $SoLuong_Tang= $request->input('SoLuong_Tang');
            $SoQuaTang = count($MaSP_Tang);
            $data_tang = array_merge($MaSP_Tang,$SoLuong_Tang);
            for ($i=0; $i < $SoQuaTang ; $i++) 
            {
                $MaSP = $data_tang[$i];
                $SoLuongSP = $data_tang[$i+$SoQuaTang];
                $CheckIDSP = DB::table('sanpham')->Where('MaSP',$MaSP)->first();
                $NhomSP = $CheckIDSP->DanhMucSP;
                $MaKiemTra = $MaDH.".".$MaSP;
                $DonHangModel->ThemQuaTang($MaKiemTra,$MaDH,$MaSP,$SoLuongSP,$NgayBan,$NhomSP);

            }
        }
            
                $TenTinh = $request->Tinh;
                $TenHuyen = $request->Huyen;
                $MaDiaChinh = $request->Xa;
                $TenXa = $DonHangModel->DiaChinh($MaDiaChinh)->Xa;
                $DiaChiKH = $request->DiaChi;
                
            $TenKH = $request->TenKH;
            $SoDienThoai = $request->SoDienThoai;
            if($error ==false)
            {
                $DonHangModel->SuaKhachHang($MaDH,$SoDienThoai,$TenKH,$DiaChiKH,$TenTinh,$TenHuyen,$TenXa,$MaDiaChinh);
                $DonHangModel->SuaDonHang(
                $MaDH,
                $TongTien,
                $GiamGia,
                $DonviGH,
                $GhiChu
                );
                $noidungloi = $MaDH;
            }

            $result = array("loi"=>$error,"noidung"=>$noidungloi);
            echo json_encode($result);
    }
    public function DangDonAPI($MaDH){
        $DonHangModel = new DonHang();
        $ThongTinDH = $DonHangModel->LayDonHangTheoMADHAPI($MaDH);
        if ($ThongTinDH) {
        $DonviGH = $ThongTinDH->DonviGH;
        switch ($DonviGH) {
            case '3':
                 $this->DangAPIGHN($ThongTinDH);
                break;
            case '4':
                 $this->DangAPIGHTK($ThongTinDH);
                break;
            case '5':
                 $this->DangAPIJT($ThongTinDH);
                break;
            default:
                echo "Mã đơn hàng ".$MaDH." bị lỗi ";
                break;
        }
        
            
    }
}


    public function DangAPIGHTK($ThongTinDH){
            $MaVanDonHang = "";
            $DonHangModel = new DonHang();
            $MaDH = $ThongTinDH->MaDH;
            $respone="";
            $products = '';
            $APIModel = new API();
            $TongKhoiLuong = 0;
                $HotLine = $this->LayHotLine();
                $ChiTietDonHang = $DonHangModel->ChiTietDonHang($MaDH);
                foreach ($ChiTietDonHang as $ChiTiet) {
                    $KhoiLuong = $ChiTiet->TrongLuong * $ChiTiet->SoLuong;
                    $TongKhoiLuong += $KhoiLuong;
                    if($ChiTiet->DanhMucSP != "4")
                        $SanPham = $ChiTiet->TenDMSP;
                    else
                        $SanPham = $ChiTiet->TenSP;
                }
                
            $TongKhoiLuong = round($TongKhoiLuong / 1000);
                $date = date("Y-m-d");
                $KhachHang = $DonHangModel->TimKhachHang($MaDH);
                $TenKH = $KhachHang->TenKH;
                if($TenKH =="")
                    $TenKH = "API";
                $TenTinh = $KhachHang->Tinh;
                $TenHuyen = $KhachHang->Huyen;
                $TenXa = $KhachHang->Xa;

                //
                $products.="{\"name\": \"{$SanPham}\",\"weight\": {$TongKhoiLuong},\"length\": 1,\"width\": 1,\"height\": 1,\"product_code\": \"{$SanPham}\",\"quantity\": 1"."},";
                $products = rtrim($products,",");
                $products = "[".$products."]";
                //
                $TongTien = $ThongTinDH->TongTien;
                //
                if($ThongTinDH->LoiAPI =="Hợp Lệ")
                $respone = $APIModel->DangDonGHTK(
                    $ThongTinDH->MaDH,
                    $products,
                    $KhachHang->SoDienThoai,
                    $TenKH,
                    $KhachHang->DiaChi,
                    $TenXa,
                    $TenHuyen,
                    $TenTinh,
                    $TongTien,
                    $HotLine,
                    $date,
                    $ThongTinDH->transport,
                    $TongKhoiLuong,
                );
                else
                    $respone = $APIModel->DangDonGHTKKemCap4(
                    $ThongTinDH->MaDH,
                    $products,
                    $KhachHang->SoDienThoai,
                    $TenKH,
                    $KhachHang->DiaChi,
                    $TenXa,
                    $TenHuyen,
                    $TenTinh,
                    $TongTien,
                    $HotLine,
                    $date,
                    $ThongTinDH->transport,
                    $TongKhoiLuong,
                );

            if (isset($respone)) 
            {
                
                $data = json_decode($respone);
                
                if(!is_object($data))
                {
                    $DonHangModel->Minh_SetTimeTemp($ThongTinDH->MaDH);
                    echo $ThongTinDH->MaDH." : Không CURL được";
                    var_dump($respone);
                    //header("refresh: 3;");
                }
                else{
                    if ($data->success==true) 
                    {
                        $order = $data->order;
                        $MaVanDonHang = $order->label;
                        $MaDH= $order->partner_id;
                        $PhiShipThucTe= $order->fee;
                        $DonHangModel->CapNhatMaVanDonHang($MaDH,$MaVanDonHang,$PhiShipThucTe);
                        $ketqua = true;
                        $ketqua_noidung = $ThongTinDH->MaDH." đăng thành công"."";
                       
                    }
                    else
                    {

                        $tinnhan = $data->message;
                        if(isset($data->error))
                        {
                            $error = $data->error;
                            $code_error = $error->code;
                            if($code_error =="ORDER_ID_EXIST")
                            {
                                $maghtk = $error->ghtk_label;
                                $madh = $error->partner_id;
                                $PhiShipThucTe = 0;
                                $DonHangModel->CapNhatMaVanDonHang($madh,$maghtk,$PhiShipThucTe);
                                $ketqua = true;
                                $ketqua_noidung = $ThongTinDH->MaDH." đã cập nhật mã GHTK : ".$maghtk."";
                            
                            }
                        }
                        
                        else
                        {
                           
                                DB::table('donhang')->where('MaDH',$MaDH)->update(['transport'=>'road','LoiAPI'=>'Lỗi']);
                            //$DonHangModel->CapNhatMaVanDonHangThatBai($ThongTinDH->MaDH);
                            $ketqua = false;
                            $ketqua_noidung = $tinnhan;
                            
                        }
                        
                    }
                }
                
            }
            $result = array('KetQua'=>$ketqua,"NoiDung"=>$ketqua_noidung,"MaVanDonHang"=>$MaVanDonHang);
            echo json_encode($result);                    
    }

     public function DangAPIGHN($ThongTinDH){
            $DonHangModel = new DonHang();
            $APIModel = new API_GHN(); 
            $respone="";
            $MaDH = $ThongTinDH->MaDH;
            $ChiNhanh = $DonHangModel->LayChiNhanh($MaDH);
            $HotLine = $this->LayHotLine($ChiNhanh);
            //$HotLine = $this->LaySDTNhanVien($ThongTinDH->MaNV);
            //$ChiTietDH = DB::table('chitietdonhang')->where('MaDH',$MaDH)->get();
            $ChiTietDH = $DonHangModel->ChiTietDonHang($MaDH);
            $result = array();
            $ketqua = "";
            $items = array();
            $ketqua_noidung = "";
            $MaVanDonHang = "";
            $CanNang = 0;
            foreach ($ChiTietDH as $ChiTiet) 
            {
                $MaSP = $ChiTiet->MaSP;
                $SoLuong = $ChiTiet->SoLuong;
                $NhomSanPham = $ChiTiet->NhomSanPham;
                if($NhomSanPham != 4)
                    $TenSanPham = DB::table('danhmucsanpham')->where('MaDMSP',$NhomSanPham)->first()->TenDMSP;
                else
                    $TenSanPham = $ChiTiet->TenSP;
                $CheckSP = DB::table('sanpham')->where('MaSP',$MaSP)->first();
                $CanNang += $CheckSP->TrongLuong * $SoLuong;
                
            }
            $items[] = array(
                     "name" =>$TenSanPham,
                     "quantity" => 1,
                     "length" => 10,
                     "width" => 10,
                     "height" => 10,
                     "category" => array("level1" =>$TenSanPham)
                );
                $KhachHang = $DonHangModel->TimKhachHang($MaDH);
                $SoDienThoai = $KhachHang->SoDienThoai;
                $TenKH = $KhachHang->TenKH;
                if($TenKH =="")
                    $TenKH = "API";
                $MaDiaChinh = $KhachHang->MaDiaChinh;
                $CheckDiaChinh = DB::table('diachinh')->where('id',$MaDiaChinh)->first();
                $TenTinh = $KhachHang->Tinh;
                $TenHuyen = $KhachHang->Huyen;
                $TenXa = $KhachHang->Xa;
                $DiaChiChiTiet = $KhachHang->DiaChi.", ".$TenXa.", ".$TenHuyen.", ".$TenTinh;

                $DistrictID = $CheckDiaChinh->DistrictID;
                $WardCode = $CheckDiaChinh->WardCode;
                $service_id = $CheckDiaChinh->service_id;
                if($service_id !=="")
                $service_id = $APIModel->LayServiceID($DistrictID,$ChiNhanh);
                $respone = $APIModel->DangDonGHN(
                    $MaDH,
                    $HotLine,
                    $SoDienThoai,
                    $TenKH,
                    $DiaChiChiTiet,
                    $DistrictID,
                    $WardCode,
                    $ThongTinDH->TongTien,
                    $service_id,
                    $items,
                    $TenSanPham,
                    $CanNang,
                    $ChiNhanh
                );

                $data = json_decode($respone,true);
                //print_r($data);
                 if($data['code'] ==200)
                 {
                    $DuLieu = $data['data'];
                    $MaVanDonHang = $DuLieu['order_code'];
                    $PhiShipThucTe = $DuLieu['total_fee'];
                    $UpdateDB = DB::table('donhang')->where('MaDH',$ThongTinDH->MaDH)->update(['PhiShipThucTe'=>$PhiShipThucTe,'MaVanDonHang'=>$MaVanDonHang,'DonviGH'=>3]);
                    $ketqua_noidung = "ĐĂNG API GHN ĐƠN HÀNG ".$ThongTinDH->MaDH." THÀNH CÔNG <BR>";
                    $ketqua = true;
                 }
                 else
                 {

                    $ketqua = false;
                    
                    $ketqua_noidung = $data['code_message_value'];
                 }
                $result = array('KetQua'=>$ketqua,"NoiDung"=>$ketqua_noidung,"MaVanDonHang"=>$MaVanDonHang);
                echo json_encode($result);
    }
    public function DangAPINinjavan($ThongTinDH){
            
            $DonHangModel = new DonHang();
            $MaDH = $ThongTinDH->MaDH;
            $respone="";
            $items = array();
            $HotLine = $this->LayHotLine();
            $APIModel = new API_Ninjavan(); 
            $result = array();
            $ketqua = "";
            $ketqua_noidung = "";
            $MaVanDonHang = "";
           
            $KhachHang = $DonHangModel->TimKhachHang($MaDH);
            $SoDienThoai = $KhachHang->SoDienThoai;
            $TenKH = $KhachHang->TenKH;
            $DiaChiChiTiet = $KhachHang->DiaChiChiTiet;
            if($TenKH =="")
                $TenKH = "API";
            $TenTinh = $KhachHang->Tinh;
            $TenHuyen = $KhachHang->Huyen;
            $TenXa = $KhachHang->Xa;
            $DBTinh = DB::table('tinh')->where('TenTinh',$TenTinh)->first();
            $IdTinh = $DBTinh->id;
            $MaTinh = $DBTinh->Prov_id;
            $DBHuyen = DB::table('huyen')->where('TenHuyen',$TenHuyen)->where('IdTinh',$IdTinh)->first();
            $DistrictID = $DBHuyen->DistrictID;
            $IdHuyen = $DBHuyen->id;
            $DBXa = DB::table('xa')->where('TenXa',$TenXa)->where('IdHuyen',$IdHuyen)->first();
            $NgayGui = date("Y-m-d");
            $Token_Time = DB::table('cauhinh')->first()->token_ninjavan_time;
            $timenow = time();

            if($timenow - $Token_Time > 3600)
                $Token = $APIModel->TaoToken();
            else
                $Token = DB::table('cauhinh')->first()->token_ninjavan;
            
            // $Token = "OdHwLv09XficiTjWQzJIau3gOCmvy9ka";
            $respone = $APIModel->DangAPI($MaDH,$HotLine,$ThongTinDH->TongTien,$TenKH,$SoDienThoai,$KhachHang->DiaChi,$TenHuyen,$TenTinh,$NgayGui,$Token);
            $dulieu = json_decode($respone);
            if(array_key_exists("error",$dulieu))
            {
                $error = $dulieu->error;
                if(array_key_exists("details",$error))
                {
                    $details = json_encode($error->details);
                }
                else
                    $details = json_encode($error);
                $KetQua = false;
                $NoiDung = $details;
            }
            if(array_key_exists('tracking_number',$dulieu))
            {
                $KetQua = true;
                $NoiDung = "Đăng đơn hàng ".$MaDH." thành công";
                $MaVanDonHang = $dulieu->tracking_number;
                DB::table('donhang')->where('MaDH',$MaDH)->update(['MaVanDonHang'=>$MaVanDonHang]);
            }
            $result = array('KetQua'=>$KetQua,"NoiDung"=>$NoiDung,"MaVanDonHang"=>$MaVanDonHang);
            echo json_encode($result);

                //  $data = json_decode($respone,true);
                //  if($data['code'] ==200)
                //  {
                //     $DuLieu = $data['data'];
                //     $MaVanDonHang = $DuLieu['order_code'];
                //     $PhiShipThucTe = $DuLieu['total_fee'];
                //     $UpdateDB = DB::table('donhang')->where('MaDH',$ThongTinDH->MaDH)->update(['PhiShipThucTe'=>$PhiShipThucTe,'MaVanDonHang'=>$MaVanDonHang,'DonviGH'=>3]);
                //     $ketqua_noidung = "ĐĂNG API GHN ĐƠN HÀNG ".$ThongTinDH->MaDH." THÀNH CÔNG <BR>";
                //     $ketqua = true;
                //  }
                //  else
                //  {

                //     $ketqua = false;
                //     $ketqua_noidung = "<font color='red'><b>MÃ ĐƠN <font color='black'>".$ThongTinDH->MaDH."</font> CÓ LỖI XẢY RA : ".$data['code_message_value']."</b></font>";
                //  }
                // $result = array('KetQua'=>$ketqua,"NoiDung"=>$ketqua_noidung,"MaVanDonHang"=>$MaVanDonHang);
                // echo json_encode($result);
    }

    public function DangTatCaDonAPI(Request $request){
         $APIModel = new API();
        $DonHangModel = new DonHang();
        $time = time();
        $homqua = $time - (24*60*60);
        $Ngay = date("Y-m-d",$homqua);
        if(isset($request->ngaybatdaudangapi))
            $NgayBatDau = date_format( new \DateTime(str_replace("/","-", $request->ngaybatdaudangapi)),'Y-m-d');
        else
            $NgayBatDau = $Ngay;
        if(isset($request->ngayketthucdangapi))
            $NgayKetThuc = date_format( new \DateTime(str_replace("/","-", $request->ngayketthucdangapi)),'Y-m-d');
        else
            $NgayKetThuc = $Ngay;
        $where = "ThoiGian between '{$NgayBatDau} 00:00:00' and '{$NgayKetThuc} 23:59:59' and MaDH not in (select MaDH from ticket where TrangThaiXuLy = 'Chưa Xử Lý')";
        $TongDonChuaDang = DB::table('donhang')->WhereNull('MaVanDonHang')->where('MaDH','not like','%-001%')->where('MaTrangthai','!=','5')->whereRaw($where)->count('id');
        if( $TongDonChuaDang > 0 )
        {
            $DanhSachDonChuaDang = DB::table('donhang')->WhereNull('MaVanDonHang')->where('MaDH','not like','%-001%')->where('MaTrangthai','!=','5')->whereRaw($where)->get();
            $KetQua = "";
            foreach ($DanhSachDonChuaDang as $ds) {
                if($KetQua =="")
                    $KetQua = $ds->MaDH;
                else
                    $KetQua .=" ".$ds->MaDH;
            }
        }
        else
            $KetQua = 0;
        echo $KetQua;
    }

  

    public function HuyDonHang(Request $request){
        $MaDH = $request->MaDH;
        $CheckMaDon = strpos($MaDH,"-001");
        if($CheckMaDon ==true)
        {
            echo "Không thể huỷ đơn giao hàng 1 phần";
            die();
        }
        $CheckMaDon = strpos($MaDH,"-PR");
        if($CheckMaDon ==true)
        {
            echo "Không thể huỷ đơn giao hàng 1 phần";
            die();
        }
            $ThongTinDH = DB::Table('donhang')->where('MaDH',$MaDH)->select('DonviGH','MaVanDonHang')->first();
            $DonviGH = $ThongTinDH->DonviGH;
            $MaVanDonHang = $ThongTinDH->MaVanDonHang;
            if($MaVanDonHang =="")
            {
                DB::table('donhang')->where('MaDH',$MaDH)->update(['MaTrangthai'=>5]);
                echo "Đã Huỷ Đơn Hàng ".$MaDH;
            }
            else
            {
                switch ($DonviGH) {
                    case 3:
                        $this->HuyDonGHN($ThongTinDH->MaVanDonHang);
                        break;
                    case 4:
                        $this->HuyDonGHTK($MaDH);
                        break;
                    case 5:
                        $this->HuyDonJNT($MaDH);
                        break;
                    case 6:
                        $this->HuyDonNinjavan($ThongTinDH->MaVanDonHang);
                        break;        
                    
                    default:
                        DB::table('donhang')->where('MaDH',$MaDH)->update(['MaTrangthai'=>5]);
                        echo "Đã Huỷ Đơn Hàng ".$MaDH;
                        break;
                }
            }
       
    }

    public function MHuyDonHang($MaDH){

        $CheckMaDon = strpos($MaDH,"-001");
        if($CheckMaDon ==true)
        {
            echo "Không thể huỷ đơn giao hàng 1 phần";
            die();
        }
        $CheckMaDon = strpos($MaDH,"-PR");
        if($CheckMaDon ==true)
        {
            echo "Không thể huỷ đơn giao hàng 1 phần";
            die();
        }
            $ThongTinDH = DB::Table('donhang')->where('MaDH',$MaDH)->select('DonviGH','MaVanDonHang')->first();
            $DonviGH = $ThongTinDH->DonviGH;
            $MaVanDonHang = $ThongTinDH->MaVanDonHang;
            if($MaVanDonHang =="")
            {
                DB::table('donhang')->where('MaDH',$MaDH)->update(['MaTrangthai'=>5]);
                echo "Đã Huỷ Đơn Hàng ".$MaDH;
            }
            else
            {
                switch ($DonviGH) {
                    case 3:
                        $this->HuyDonGHN($ThongTinDH->MaVanDonHang);
                        break;
                    case 4:
                        $this->HuyDonGHTK($MaDH);
                        break;
                    case 5:
                        $this->HuyDonJNT($MaDH);
                        break;
                    case 6:
                        $this->HuyDonNinjavan($ThongTinDH->MaVanDonHang);
                        break;        
                    
                    default:
                        DB::table('donhang')->where('MaDH',$MaDH)->update(['MaTrangthai'=>5]);
                        echo "Đã Huỷ Đơn Hàng ".$MaDH;
                        break;
                }
            }
       
    }

    public function HuyDonGHN($MaVanDonHang){
        $APIModel = new API_GHN();
        $HuyDon = $APIModel->HuyDon($MaVanDonHang);
        DB::table('donhang')->where('MaVanDonHang',$MaVanDonHang)->update(['MaTrangthai'=>5]);
        $data = json_decode($HuyDon,true);
         if($data['code'] ==200)
         {
            echo "HUỶ ĐƠN HÀNG ".$MaVanDonHang." THÀNH CÔNG ";
         }
         else
         {
            echo "CHỈ HUỶ ĐƠN ĐƯỢC TRÊN HỆ THỐNG , CHƯA HUỶ ĐƯỢC TRÊN GIAOHANGNHANH \nVUI LÒNG LÊN WEBSITE GIAOHANGNHANH ĐỂ HUỶ ĐƠN";
         }
    }
    public function HuyDonGHTK($MaDH){
        $APIModel = new API();
        $HuyDon = $APIModel->HuyDonHangGHTK($MaDH);
        $data = json_decode($HuyDon,true);
         if($data['success'] ==true)
         {
            echo "HUỶ ĐƠN HÀNG ".$MaDH." THÀNH CÔNG ";
            DB::table('donhang')->where('MaDH',$MaDH)->update(['MaTrangthai'=>5]);
         }
         else
         {
            DB::table('donhang')->where('MaDH',$MaDH)->update(['MaTrangthai'=>5]);
            echo "CHƯA HUỶ ĐƯỢC TRÊN GIAOHANGTIETKIEM \nVUI LÒNG LÊN WEBSITE GIAOHANGTIETKIEM ĐỂ HUỶ ĐƠN";
         }
    }
    public function HuyDonJNT($MaDH){
        $APIModel = new API_JT();
        $HuyDon = $APIModel->HuyDon($MaDH);
        DB::table('donhang')->where('MaDH',$MaDH)->update(['MaTrangthai'=>5]);
        $data = json_decode($HuyDon,true);
        $respon = $data['responseitems'];
        $ketqua = $respon[0];
        if($ketqua['success']=="true")
        {
            echo "HUỶ ĐƠN HÀNG ".$MaDH." THÀNH CÔNG ";
        } 
        else
        {
            echo "CHỈ HUỶ ĐƠN ĐƯỢC TRÊN HỆ THỐNG , CHƯA HUỶ ĐƯỢC TRÊN J&T\nVUI LÒNG LÊN WEBSITE JNT EXPRESS ĐỂ HUỶ ĐƠN";
         }
    }
    public function HuyDonNinjavan($MaVanDonHang){
        DB::table('donhang')->where('MaVanDonHang',$MaVanDonHang)->update(['MaTrangthai'=>5]);
        $APIModel = new API_Ninjavan();
        $Token_Time = DB::table('cauhinh')->first()->token_ninjavan_time;
        $timenow = time();

        if($timenow - $Token_Time > 3600)
            $Token = $APIModel->TaoToken();
        else
            $Token = DB::table('cauhinh')->first()->token_ninjavan;
        $APIModel = new API_Ninjavan();
        $HuyDon = $APIModel->HuyDon($MaVanDonHang,$Token);
         if($HuyDon == true)
         {
            echo "HUỶ ĐƠN HÀNG ".$MaVanDonHang." THÀNH CÔNG ";
         }
         else
         {
            echo "CHỈ HUỶ ĐƠN ĐƯỢC TRÊN HỆ THỐNG , CHƯA HUỶ ĐƯỢC TRÊN NINJAVAN \nVUI LÒNG LÊN WEBSITE NINJAVAN ĐỂ HUỶ ĐƠN";
         }
    }  
    public function GuiTicket( request $request){
        $DonHangModel = new DonHang();
        $YeuCau = $request->YeuCau;
        $MaDH = $request->MaDH;

        if($YeuCau =="Xác nhận chuyển khoản")
        {
            $file = $request->file('FileCoc');
            if( $file != null)
            {   
                $TienCoc = $request->TienCoc;
                $NoiDung = "Xác nhận khách chuyển khoản ".$TienCoc;
                $temp = $file->getClientOriginalName();
                $HinhAnh = rand(0,999999999).$temp;
                $file->move('../public/images/tiencoc',$HinhAnh);
                $DonHangModel->ThemTicket($MaDH,Auth::user()->id,$NoiDung,$YeuCau,str_replace(",","",$TienCoc));
                $DonHangModel->ThemTicketXacNhanCoc($MaDH,$HinhAnh,str_replace(",","",$TienCoc));
                 return Redirect::back()->with(['message_success'=>"Đã gửi yêu cầu hổ trợ thành công!",'message_code'=>5]);
            }
            else
                return Redirect::back()->with(['message_success'=>"Gửi YCHT Thất Bại Do Thiếu Ảnh Xác Minh!",'message_code'=>0]);
        }
        elseif($YeuCau =="Sửa tổng tiền")
        {
            $TongTien = $request->TongTien;
            $TongTienMoi = $request->TongTienMoi;
            $NoiDung = "Sửa tổng tiền từ ".$TongTien." thành ".$TongTienMoi;
            $DonHangModel->ThemTicket($MaDH,Auth::user()->id,$NoiDung,$YeuCau,str_replace(",","",$TongTienMoi));
            return Redirect::back()->with(['message_success'=>"Đã gửi yêu cầu hổ trợ thành công!",'message_code'=>5]);
        }
        elseif($YeuCau =="Đổi DVGH")
        {
            $DVGH = $request->DVGH;
            $DVGHCu = $request->DVGHCu;
            $TenDVGHMoi = DB::table('donvigiaohang')->where('MaDV',$DVGH)->select('TenDV')->first()->TenDV;
            $NoiDung = "Đổi DVGH từ ".$DVGHCu." qua ".$TenDVGHMoi;
            $DonHangModel->ThemTicket($MaDH,Auth::user()->id,$NoiDung,$YeuCau,$DVGH);
            return Redirect::back()->with(['message_success'=>"Đã gửi yêu cầu hổ trợ thành công!",'message_code'=>5]);
        }
        elseif($YeuCau =="Hủy Đơn Hàng")
        {
            $TongTien = $request->TongTien;
            $LyDoHuy = $request->LyDoHuy;
            $NoiDung = "Huỷ đơn hàng ".$MaDH." vì : ".$LyDoHuy;
            $DonHangModel->ThemTicket($MaDH,Auth::user()->id,$NoiDung,$YeuCau,0);
            return Redirect::back()->with(['message_success'=>"Đã gửi yêu cầu hổ trợ thành công!",'message_code'=>5]);
        }
        elseif($YeuCau =="Khác")
        {
            $TongTien = $request->TongTien;
            $LyDoHuy = $request->LyDoHuy;
            $NoiDung = $request->NoiDung;
            $DonHangModel->ThemTicket($MaDH,Auth::user()->id,$NoiDung,$YeuCau,0);
            return Redirect::back()->with(['message_success'=>"Đã gửi yêu cầu hổ trợ thành công!",'message_code'=>5]);
        }
        elseif($YeuCau =="Sửa sản phẩm")
        {
            $arraysp = array();
            $DonHang = "";
            $MaSP= $request->input('MaSP');
            $SoLuong= $request->input('SoLuong');
            $data = array_merge($MaSP,$SoLuong);
            $count = count($MaSP);
            for ($i=0; $i < $count ; $i++) 
            {
                $MaSP = $data[$i];
                $SoLuongSP = $data[$i+$count];
                $CheckIDSP = DB::table('sanpham')->Where('MaSP',$MaSP)->first();
                $DVT = $CheckIDSP->DonViTinh;
                $DonHang .= '<div class="product_row"><div class="PRODUCT_ID" style="display:none">'.$MaSP.'</div><span class="MASP">'.$CheckIDSP->TenSP.'</span><span> : </span><span class="SOLUONG">'.$SoLuongSP.' '.$DVT.'</span></div>';
                $arraysp[$MaSP] = $SoLuongSP;
            }
            $arr = array("chitiet"=>$arraysp);
            $SoTien = str_replace(",","",$request->TongTien);
            $SoTien = str_replace(".","",$SoTien);
            $DonHangModel->ThemTicketSuaSanPham($MaDH,Auth::user()->id,$DonHang,$YeuCau,json_encode($arr),$SoTien);
            return Redirect::back()->with(['message_success'=>"Đã gửi yêu cầu hổ trợ thành công ".$SoTien."!",'message_code'=>5]);
        }

        
    }

    public function XoaDonHang($MaDH)
    {
        $DonHangModel = new DonHang();
        $DonHang = DB::table('donhang')
        ->where('MaDH',$MaDH)->leftJoin('khachhang', 'donhang.MaKH', '=', 'khachhang.MaKH')->first();

        $this->CreateLog(Auth::user()->name." đã xóa đơn hàng", 'ban-hang', $MaDH." - Chi tiết đơn hàng: ".$DonHang->ChiTietDH." - Chi tiết khách hàng: ".$DonHang->TenKH." + SĐT: ".$DonHang->SoDienthoaiKH." + Địa chỉ: ".$DonHang->DiaChiKH);

        $DonHangModel->XoaDonHang($MaDH);
        $DonHangModel->XoaKhachHang($MaDH);
         return redirect('ban-hang/danh-sach-don-hang')->with(['message_success'=>"Đã xóa đơn hàng ".$MaDH." thành công",'message_code'=>5]);
    }

    public function AjaxInfoEdit($MADH)
    {
        $DonHangModel = new DonHang();
        $DonHang = $DonHangModel->LayDonHangTheoMADH($MADH);
        $dulieu = array();
        $QuaKhuyenMai = array();
        $SoQuaTang = 0;
        $KhuyenMai = "Yes";
        $TenNV = DB::table('users')->where('id',$DonHang->MaNV)->select('name')->first()->name;
            $KhachHang =  DB::table('khachhang')->where('MaDH',$DonHang->MaDH)->first();
            
            $ChiTietDH = DB::table('chitietdonhang')->where('MaDH',$MADH)->get();
            $_ChiTietDH = array();
            foreach($ChiTietDH as $chitiet){
                if($chitiet->QuaTang =="Yes")
                {
                    $SanPham = DB::table('sanpham')->where('MaSP',$chitiet->MaSP)->first();
                    $QuaKhuyenMai[] = array("MaSP"=>$chitiet->MaSP,"SoLuong"=>$chitiet->SoLuong,"TenSP"=>$SanPham->TenSP);
                    $KhuyenMai = "No";
                    $SoQuaTang++;
                }
                else
                    $_ChiTietDH[$chitiet->MaSP] = $chitiet->SoLuong;
            }
            if($DonHang->GiamGia != 0)
                $KhuyenMai = "No";
            $dulieu = array(
                "TenKH"=>$KhachHang->TenKH,
                "Tinh"=>$KhachHang->Tinh,
                "Huyen"=>$KhachHang->Huyen,
                "Xa"=>$KhachHang->Xa,
                "MaDiaChinh"=>$KhachHang->MaDiaChinh,
                "DiaChi"=>$KhachHang->DiaChi,
                "SoDienThoai"=>$KhachHang->SoDienThoai,
                "DonviGH"=>$DonHang->DonviGH,
                "TongTien"=>$DonHang->TongTien,
                "ChiTietDH"=>$_ChiTietDH,
                "MaNV"=>$DonHang->MaNV,
                "TenNV"=>$TenNV,
                "ThoiGian"=>$DonHang->ThoiGian,
                "GhiChu"=>$DonHang->GhiChu,
                "KhuyenMai"=>$KhuyenMai,
                "SoQuaTang"=>$SoQuaTang,
                "GiamGia"=>$DonHang->GiamGia,
                "QuaKhuyenMai"=>$QuaKhuyenMai
            );
        
        
       return json_encode($dulieu);
    }

    public function AjaxPrice(Request $request)
    {
            $KhoModel = new Kho();
            $DonHangModel = new DonHang();
            $QuyenHanModel = new QuyenHan();
            $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
            $PRODUCT_ARR = $request->data_product;
            $HTML_PRODUCT_CONFIG = '';
            $TOTAL_MONEY = 0;
            $TOTAL_PRODUCT = 0;
            $ARR_CHECK = array();
            $MaKho = $request->kho;
            $error = false;
            $Loi = "";
            foreach($PRODUCT_ARR as $item)
            {   

                $PRODUCT_QUANTITY = explode("|",$item);
                if($PRODUCT_QUANTITY[1] > 0 && $PRODUCT_QUANTITY[0] != '0')
                {
                    $CheckIDSP = DB::table('sanpham')->where('id',$PRODUCT_QUANTITY[0])->orWhere('MaSP',$PRODUCT_QUANTITY[0])->leftJoin('danhmucsanpham', 'sanpham.DanhMucSP', '=', 'danhmucsanpham.MaDMSP')->first();
                    $NhomSP = $CheckIDSP->DanhMucSP;
                    $PRODUCT_ID = $CheckIDSP->TenSP;
                    $TOTAL_PRODUCT += $PRODUCT_QUANTITY[1];
                    $PRODUCT_PRICE = str_replace(",","",$PRODUCT_QUANTITY[2]);
                    $PRODUCT_MONEY = $PRODUCT_PRICE*$PRODUCT_QUANTITY[1];
                    $TOTAL_MONEY += $PRODUCT_MONEY;
                    $HTML_PRODUCT_CONFIG .= '<div class=\'row bill-product\'>';
                    $HTML_PRODUCT_CONFIG .= '<div class=\'col-md-6\'><label>'.$PRODUCT_ID.'</label></div><div class=\'col-md-2\'><label>'.number_format($PRODUCT_PRICE).'</label></div><div class=\'col-md-1\'><label>'.$PRODUCT_QUANTITY[1].'</label></div><div class=\'col-md-3\' style="text-align:right"><label>'.number_format($PRODUCT_MONEY).' Đ</label></div></div>';

                }
            }


            if(isset($QuyenHan['Admin']))
                $HTML_TOTAL_MONEY = '<div class="row bill-product" style="color: red;align-items:center"><div class="col-md-7"><label style="font-size:16px">Tổng Cộng</label></div><div class="col-md-5" style="text-align:right"><input type="text" name="TongTien" class="form-control input_sotien tongtiendonhang" value="'.number_format($TOTAL_MONEY).'"></div></div>';
            else
                $HTML_TOTAL_MONEY = '<div class="row bill-product" style="color: red"><div class="col-md-7"><label>Tổng Cộng</label></div><div class="col-md-5" style="text-align:right"><input type="hidden" name="TongTien" class="tongtiendonhang" value="'.number_format($TOTAL_MONEY).'"><label>'.number_format(($TOTAL_MONEY)).' Đ</label></div></div>';
            $result = array("loi"=>$error,"noidung"=>$HTML_PRODUCT_CONFIG.$HTML_TOTAL_MONEY,"noidungloi"=>$Loi);
            echo json_encode($result);
        
    }
    public function AjaxPriceMobile(Request $request)
    {
            $KhoModel = new Kho();
            $DonHangModel = new DonHang();
            $QuyenHanModel = new QuyenHan();
            $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
            $PRODUCT_ARR = $request->data_product;
            $HTML_PRODUCT_CONFIG = '';
            $TOTAL_MONEY = 0;
            $TOTAL_PRODUCT = 0;
            $ARR_CHECK = array();
            $MaKho = $request->kho;
            $error = false;
            $Loi = "";
            foreach($PRODUCT_ARR as $item)
            {   

                $PRODUCT_QUANTITY = explode("|",$item);
                if($PRODUCT_QUANTITY[1] > 0 && $PRODUCT_QUANTITY[0] != '0')
                {
                    $CheckIDSP = DB::table('sanpham')->where('id',$PRODUCT_QUANTITY[0])->orWhere('MaSP',$PRODUCT_QUANTITY[0])->leftJoin('danhmucsanpham', 'sanpham.DanhMucSP', '=', 'danhmucsanpham.MaDMSP')->first();
                    $NhomSP = $CheckIDSP->DanhMucSP;
                    $PRODUCT_ID = $CheckIDSP->TenSP;
                    $TOTAL_PRODUCT += $PRODUCT_QUANTITY[1];
                    $PRODUCT_PRICE = str_replace(",","",$PRODUCT_QUANTITY[2]);
                    $PRODUCT_MONEY = $PRODUCT_PRICE*$PRODUCT_QUANTITY[1];
                    $TOTAL_MONEY += $PRODUCT_MONEY;
                    $HTML_PRODUCT_CONFIG .= '<div class=\'bill-product\'>';
                    $HTML_PRODUCT_CONFIG .= '<div class=\'col-sp\'><label>'.$PRODUCT_ID.'</label></div><div class=\'col-sl\' style=\'text-align:center\'><label>'.$PRODUCT_QUANTITY[1].'</label></div><div class=\'col-tt\' style="text-align:right"><label>'.number_format($PRODUCT_MONEY).' Đ</label></div></div>';

                }
            }


            if(isset($QuyenHan['Admin']))
                $HTML_TOTAL_MONEY = '<div class="bill-product" style="color: red;align-items:center"><div class="col-md-7"><label style="font-size:16px">Tổng Cộng</label></div><div class="col-md-5" style="text-align:right"><input type="text" name="TongTien" class="form-control input_sotien tongtiendonhang" value="'.number_format($TOTAL_MONEY).'"></div></div>';
            else
                $HTML_TOTAL_MONEY = '<div class="bill-product" style="color: red"><div class="col-md-7"><label>Tổng Cộng</label></div><div class="col-md-5" style="text-align:right"><input type="hidden" name="TongTien" class="tongtiendonhang" value="'.number_format($TOTAL_MONEY).'"><label>'.number_format(($TOTAL_MONEY)).' Đ</label></div></div>';
            $result = array("loi"=>$error,"noidung"=>$HTML_PRODUCT_CONFIG.$HTML_TOTAL_MONEY,"noidungloi"=>$Loi);
            echo json_encode($result);
        
    }
    public function AjaxPriceProcess(Request $request)
    {
        if($request->ajax())
        {
            $DonHangModel = new DonHang();
            $MA_CHI_NHANH = Auth::user()->MaCN;
            $MA_NHOM = Auth::user()->MaNhom;
            if($MA_NHOM){
                $CHECK_MA_CHI_NHANH = $DonHangModel->LayMaChiNhanhNhom($MA_NHOM);
                if ($CHECK_MA_CHI_NHANH) {
                    $MA_CHI_NHANH = $CHECK_MA_CHI_NHANH->MaCNN;
                }
            }
            $PRODUCT_ARR = $request->data_product;
            $HTML_PRODUCT_CONFIG = '';
            $TOTAL_MONEY = 0;
            $TOTAL_PRODUCT = 0;
            $ARR_CHECK = array();
            foreach($PRODUCT_ARR as $item)
            {   
                $PRODUCT_QUANTITY = explode("|",$item);
                if($PRODUCT_QUANTITY[1] > 0 && $PRODUCT_QUANTITY[0] != '0')
                {
                    $NhomSP = $DonHangModel->LayNhomSP($PRODUCT_QUANTITY[0])->DanhMucSP;
                    $ARR_CHECK[$NhomSP] = true;
                    $TOTAL_PRODUCT += $PRODUCT_QUANTITY[1];
                    $PRODUCT_ID = $DonHangModel->CHuyenIDSangMASP($PRODUCT_QUANTITY[0])->MaSP;
                    $PRODUCT_PRICE = $DonHangModel->LayGiaTheoMaSPvaMaCN($PRODUCT_ID,$MA_CHI_NHANH)->GiaThanh;
                    $PRODUCT_MONEY = $PRODUCT_PRICE*$PRODUCT_QUANTITY[1];
                    $HTML_PRODUCT_CONFIG .= '<div class=\'row bill-product\'><div class=\'col\'><label>'.$PRODUCT_ID.'</label></div><div class=\'col\'><label>'.$PRODUCT_QUANTITY[1].'</label></div><div class=\'col\'><label>'.number_format($PRODUCT_MONEY).' Đ</label></div></div>';
                    $TOTAL_MONEY += $PRODUCT_MONEY;
                }
            }

            
            
            $HTML_TOTAL_MONEY .= '<div class="row bill-product" style="color: red"><div class="col"><label>Tổng Cộng</label></div><div class="col"></div><div class="col"><label>'.number_format(($TOTAL_MONEY)).' Đ</label></div></div>';
            $HTML_TOTAL_MONEY .= '<div class="row bill-product" style=""><div class="col"><label>Thay đổi giá</label></div><div class="col"></div><div class="col"><input type="number" name="TongTien" class="form-control" value='.($TOTAL_MONEY).'></div></div>';
            echo $HTML_PRODUCT_CONFIG.$HTML_TOTAL_MONEY;
        }
    }


    public function XemLogDonHang($MaDH){
            $DonHangModel = new DonHang();
            $log = "";
            $DonviGH = $DonHangModel->LayThongTinDonVi($MaDH);

            $log = "";

            echo "<div class=\"row\"><div class=\"col\">";

            if ($DonviGH) {
                $Donvi = $DonviGH->DonviGH;
                $MaVanDonHang = $DonviGH->MaVanDonHang;
                if ($Donvi=="4") {
                   $GHTKLog = $DonHangModel->LayThongTinLogGHTK($MaDH);
                    if ($GHTKLog) {
                        foreach ($GHTKLog as $item) {
                            $tachthoigian = explode(" ", $item->ThoiGian);
                            $tachngay = explode("-", $tachthoigian[0]);
                            $log_day = $tachngay[2]."/".$tachngay[1]."/".$tachngay[0];
                            if($item->NoiDung != "")
                        $log .= "<div style=\"margin-bottom:2px\">Đơn hàng <b>".$item->MaDH."</b> cập nhật trạng thái <b>".$item->TenTT."</b> lúc <b>".$tachthoigian[1]."</b> ngày <b>".$log_day."</b><br>".nl2br($item->NoiDung).".</div><hr><br/>";
                            else
                        $log .= "<div style=\"margin-bottom:2px\">Đơn hàng <b>".$item->MaDH."</b> cập nhật trạng thái <b>".$item->TenTT."</b> lúc <b>".$tachthoigian[1]."</b> ngày <b> ".$log_day."</b></div><hr><br/>";
                        }
                        if($log !="")
                        echo $log;
                    else
                        echo "Chưa có dữ liệu trả về từ đơn vị giao hàng";

                    }
                    else
                    { 
                        echo "Chưa hỗ trợ xem log";
                    }
                }
                if ($Donvi=="5") {
                   $APIModel = new API_JT();
                   $J_TLog = $APIModel->LayLichTrinhDonHang($MaDH,$MaVanDonHang);
                   echo $J_TLog;
                }
                if ($Donvi=="3") {
                    $APIModel = new API_GHN();
                    $APIModel->LayLichTrinhDonHang($MaVanDonHang);
                   
                }
                if($Donvi ==6){
                    $API = new API_Ninjavan();
                    $Log = DB::table('ninjavan_log')->where('MaDH',$MaDH)->get();
                    if($Log)
                    {
                        $dulieulog = "";
                        foreach($Log as $dulieu)
                        {
                            $thoigian = date("H:i:s d/m/Y",strtotime($dulieu->ThoiGian));
                            $TrangThai = $dulieu->TrangThai;
                            $MaTrangthai = $API->LayMaTrangThai($TrangThai);
                            switch ($MaTrangthai) {
                                case 1:
                                    break;
                                case 8:
                                    $TenTrangThai = $API->LayTenTrangThai($MaTrangthai);
                                    $dulieulog.='<div style = "font-size:15px">'.$thoigian.' : '.$MaDH.' trạng thái '.$TenTrangThai.' . Tiền COD : '.$dulieu->TienThuThucTe.'</div>';
                                    break;
                                default:
                                    $TenTrangThai = $API->LayTenTrangThai($MaTrangthai);
                                    $dulieulog.='<div style = "font-size:15px">'.$thoigian.' : '.$MaDH.' trạng thái '.$TenTrangThai.' . '.$dulieu->NoiDung.'</div>';
                                    break;
                            }
                            
                            
                        }
                        echo $dulieulog;
                    }
                    else
                        echo "Chưa có dữ liệu trả về từ Ninjavan";
                }
            }
            echo "</div></div>";
            
    }

    public function XemYCHT($MaDH){
            $log = "";
            
            echo "<div class=\"row\"><div class=\"col\">";
            $DuLieu = DB::table('ticket')->where('MaDH',$MaDH)->get();
            foreach ($DuLieu as $item) {
                $MaNV = $item->MaNV;
                $TenNV = DB::table('users')->where('id',$MaNV)->select('name')->first()->name;
                $log .= "<div style=\"margin-bottom:2px;text-align:center;font-size:16px\">".date("H:i - d/m",strtotime($item->ThoiGian))." : ".$TenNV." gửi YCHT <b>".$item->TuyChon."</b> <br> ".$item->NoiDung." . <br>Kết Quả : ".$item->TrangThai."</div><hr><br/>";
            }
            echo $log;
            
            echo "</div></div>";
            
    }

        //CHỨC NĂNG MỚI THÊM 9/10/2018 - 23h
    public function TimKiemDonHang(Request $request)
    {
        $key_search = $request->key_search;
        $DonHangModel = new DonHang();
        $View_Path = 'don-hang.danh-sach-don-hang';
        $DanhSachDonHang = $DonHangModel->timkiemdonhang($key_search);
        return $this->ReturnInit($View_Path,$DanhSachDonHang);
    }
        public function PostTimKiemDonHang(Request $request)
    {
        $DonHangModel = new DonHang();
        $key_search = $request->key_search;
        $DonHang = $DonHangModel->timkiemdonhang($key_search);
        $DuLieu = $this->XuLyDuLieuDonHang($DonHang,"ajax");
        echo $DuLieu;

    }
    public function XuLyDuLieuDonHang($DanhSachDonHang,$ajax = null){
        $MaCN = Auth::user()->MaCN;
        $DonHangModel = new DonHang();
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();

        $url = \URL::to('/');
        if(count($DanhSachDonHang) > 0){
            
        foreach ($DanhSachDonHang as $item) {

            # MADH
            $TenNV = "không tìm thấy";
            $TenNV = DB::table('users')->select('name')->where('id', $item->MaNV)->first();
            if ($TenNV) {
                $TenNV = $TenNV->name;
            }

                $MaVanDonHang = $item->MaVanDonHang;

            $_MaDH = $item->ThoiGian."<br><span style='font-weight: bold;color:green;font-size:14px'>".$item->MaDH."</span></br><span style='font-weight: bold'>NV: ".$TenNV."</span><br><span style='color:blue'>".$MaVanDonHang."</span>";
            # KHACH HANG
            $KhachHang = DB::table('khachhang')->where('MaDH',$item->MaDH)->first();

            if($KhachHang)
            {
                if(isset($QuyenHan['Admin']))
                    $_KhachHang ="<b>Tên:</b>".$KhachHang->TenKH."<br><b>Số ĐT:</b>".$KhachHang->SoDienThoai."<br><b>Địa chỉ:</b>".$KhachHang->DiaChi.",".$KhachHang->Xa.",".$KhachHang->Huyen.",".$KhachHang->Tinh."<br>";
                else
                {
                    $phoneNumber = $this->maskPhoneNumber($KhachHang->SoDienThoai);
                    $_KhachHang ="<b>Tên:</b>".$KhachHang->TenKH."<br><b>Số ĐT:</b>".$phoneNumber."<br><b>Địa chỉ:</b>".$KhachHang->DiaChi.",".$KhachHang->Xa.",".$KhachHang->Huyen.",".$KhachHang->Tinh."<br>";
                }
            }
            else
                $_KhachHang ="<b>Tên:</b><br><b>Số ĐT:</b><br><b>Địa chỉ:</b><br>";
            if($item->GhiChu != "")
                    $_KhachHang.='<div class="ghichudonhang">Ghi Chú : '.$item->GhiChu.'</div>';
            # SAN PHAM
            $ChiTietDonHang = $DonHangModel->XemChiTietDonHang($item->MaDH);
            $_SANPHAM = '<div class="chitietdh">'.$ChiTietDonHang.'</div>';

            
            # THOIGIAN
            $_ThoiGian = $item->ThoiGian;
            if($item->CocTruoc > 0)
                $CocTruoc = "<label><b>Chuyển khoản : ".number_format($item->CocTruoc)."</b></label> <a class=\"xem-anh-coc\" data-id=\"".$item->MaDH."\" data-toggle=\"modal\" data-href=\"".$url."/don-hang/xem-anh-coc/".$item->MaDH."\" data-target=\"#XemAnhCoc\" title=\"Xem Ảnh Cọc\">[ẢNH_CỌC]</a><br>";
                else
                $CocTruoc = "";
            if($item->TienThuThucTe !="")
                $_TongTien ="
                <label><b>Tổng:</b> ".number_format($item->TongTien)." Đ</label><br>
                ".$CocTruoc."
                
                <hr style='margin-bottom:5px;margin-top:0px'>
                <label><b>Đã Thu:</b> ".number_format($item->TienThuThucTe)." Đ</label><br>
                <label><b>Cước Ship:</b> ".number_format($item->PhiShipThucTe)." Đ</label>
                ";
            elseif($item->TienThuThucTe =="")
            $_TongTien ="
                <label><b>Tổng:</b> ".number_format($item->TongTien)." Đ</label><br>
                ".$CocTruoc."
                
                <label><b>Cước Ship:</b> ".number_format($item->PhiShipThucTe)." Đ</label>
                ";
            # TONG TIEN
  
            // # TRANG BAN HANG
            if($item->DonviGH != 7)
                $_TrangBanHang = '<img src="'.$url.'/images/'.$item->LogoDVGH.'" width="100%" height="80px" />';
            else
            {
                $ChanhXeID = DB::table('donhang')->where('MaDH',$item->MaDH)->select('ChanhXeID')->first()->ChanhXeID;
                if($ChanhXeID =="")
                    $_TrangBanHang = '<b>GỬI CHÀNH XE <br><span style="color:blue">Chưa chọn chành</span></b><br>Bấm nút <button data-toggle="modal" data-target="#ChonChanhXe" type="button" class="btn btn-danger chon-chanh-xe" data-name='.$item->MaDH.' data-id='.$item->MaDH.' data-href="'.$url.'/don-hang/chon-chanh-xe/'.$item->MaDH.'" title="Chọn Chành Xe"><i class="fa fa-list-alt"></i></button> để chọn chành xe';
                else
                {
                    $TenChanhXe = DB::table('chanhxe')->where('ChanhXeID',$ChanhXeID)->select('TenChanhXe')->first()->TenChanhXe;
                    $_TrangBanHang = '<b>GỬI CHÀNH XE <br><span style="color:blue">'.$TenChanhXe.'</span></b>';
                }
            }

            
            # GHI CHU
 
    
            # THAO TAC
            $ButtonThaoTac = $this->ButtonThaoTac($item->MaTrangthai,$item->TenTT,$item->MaDH,$item->Ngay,$item->DonviGH,$item->MaVanDonHang);
            $_ThaoTac = "";
            $_ThaoTac = $ButtonThaoTac['TrangThai'];
            $_ThaoTac.= "<div class=\"button_donhang\">";
            $_ThaoTac.= $ButtonThaoTac['DangDonAPI'];
            $_ThaoTac.= $ButtonThaoTac['Edit'];
            $_ThaoTac.= $ButtonThaoTac['HuyDon'];
            $_ThaoTac.= $ButtonThaoTac['Logs'];
            $_ThaoTac.= $ButtonThaoTac['HoaDon'];
            $_ThaoTac.= $ButtonThaoTac['HoaDon'];
            $_ThaoTac.= $ButtonThaoTac['Copy'];
            $_ThaoTac .= "</div>";
            //PUSH DATA TO ARRAY
            $array_json[] = array(
                "MaDH"=>$_MaDH,
                "KhachHang"=>$_KhachHang,
                "SanPham"=>$_SANPHAM,
                "TongTien"=>$_TongTien,
                "TrangBanHang"=>$_TrangBanHang,
                "ThaoTac"=>$_ThaoTac
            );
        }
        }
        else
        {
            $array_json[] = array(
                "MaDH"=>"",
                "KhachHang"=>"",
                "SanPham"=>"",
                "TongTien"=>"",
                "TrangBanHang"=>"",
                "ThaoTac"=>""
            );
        }
        if(isset($ajax))
        {
            //echo "3333<br>";
            echo json_encode(array("responseData"=>$array_json));    
        }
        
        else
        return json_encode($array_json);
        
    }
        // THAO TÁC DANH SÁCH ĐƠN HÀNG
     public function ButtonThaoTac($MaTrangthai,$TenTT, $MaDH,$ThoiGian,$DonviGH,$MaVanDonHang){
       
        $HomNay = date("Y-m-d");
         $url = \URL::to('/');
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
        
        $button = array(
            "TrangThai"=>"<button type=\"button\" class=\"btn btn-success\">".$TenTT."</button><br>",
           
            
            "YCHT"=>'<button type="button" class="btn btn-default ycht" data-id='.$MaDH.' data-toggle="modal" data-target="#YCHTModal" title="Gửi Yêu Cầu Hỗ Trợ"><i class="fa fa-podcast"></i></button>',
            "Logs"=>'<button data-toggle="modal" data-target="#ViewLog" type="button" class="btn btn-default xem-log-don-hang XemLog" data-name='.$MaDH.' data-id='.$MaDH.' data-href="'.$url.'/don-hang/xem-log-don-hang/'.$MaDH.'" title="Xem Chi Tiết"><i class="fa fa-list-alt"></i></button>',
            "HuyYCHT"=>'<button type="button" class="btn btn-warning huy-xu-ly-ycht HuyXuLyYCHT" data-name='.$MaDH.' data-id='.$MaDH.' data-href="'.$url.'/xu-ly-don-hang/yeu-cau-ho-tro/huy-xu-ly/'.$MaDH.'" title="Hủy Yêu Cầu Hỗ Trợ"><i class="fa fa-exclamation-triangle"></i></button>',
            "XuLyYCHT"=>'<button type="button" class="btn btn-default xu-ly-ycht" data-id='.$MaDH.' data-toggle="modal" data-href="'.$url.'/xu-ly-don-hang/yeu-cau-ho-tro/xu-ly/'.$MaDH.'" data-target="#XuLyYCHT"><i class="fas fa-edit"></i></button>',
            "XemYCHT"=>'<button type="button" class="btn btn-warning xemycht" data-id='.$MaDH.' data-toggle="modal" data-href="'.$url.'/don-hang/xem-yeu-cau-ho-tro/'.$MaDH.'" data-target="#XemYCHT" title="Xem Danh Sách YCHT"><i class="fas fa-journal-whills"></i></button>'
        );
        $button['HuyDon'] = "";
        $button['Edit'] = "";
        $button['DangDonAPI'] = "";
        $button['GiaoMotPhan'] = "";
        if($DonviGH != 7)
            $button['Logs'] = '<button data-toggle="modal" data-target="#ViewLog" type="button" class="btn btn-default xem-log-don-hang XemLog" data-name='.$MaDH.' data-id='.$MaDH.' data-href="'.$url.'/don-hang/xem-log-don-hang/'.$MaDH.'" title="Xem Chi Tiết"><i class="fa fa-list-alt"></i></button>';
        else
            $button['Logs'] = '<button data-toggle="modal" data-target="#ChonChanhXe" type="button" class="btn btn-danger chon-chanh-xe" data-name='.$MaDH.' data-id='.$MaDH.' data-href="'.$url.'/don-hang/chon-chanh-xe/'.$MaDH.'" title="Chọn Chành Xe"><i class="fa fa-list-alt"></i></button>';
        if($MaVanDonHang == "" && $DonviGH != 7)
        {
            if(isset($QuyenHan['Admin'])||isset($QuyenHan['Quản Lý Bán Hàng']))
           $button['DangDonAPI'] = '<button type="button" class="btn btn-default dang-api" data-name='.$MaDH.' data-id='.$MaDH.' data-href="'.$url.'/don-hang/danh-sach-don-hang/dang-api/'.$MaDH.'"><i class="fas fa-arrow-up"></i></button>';
        }
        
        if(isset($QuyenHan['Quản Lý Bán Hàng']))
        {
            if($ThoiGian == $HomNay)
            {
                $button['Edit'] = '<button type="button" class="btn btn-default edit-product-bill" data-id='.$MaDH.' data-toggle="modal" data-target="#TaoDonHang" title="Sửa Đơn"><i class="fas fa-edit"></i></button>';
                $button['HuyDon'] = '<button type="button" class="btn btn-default" data-name='.$MaDH.' data-id='.$MaDH.' title="Hủy Đơn" onclick="HuyDonHang(\''.$MaDH.'\')"><i class=" fa fa-exclamation-triangle"></i></button>';        
            }
            
        }
                
        elseif(isset($QuyenHan['Admin']))
            {
                $button['Edit'] = '<button type="button" class="btn btn-default edit-product-bill" data-id='.$MaDH.' data-toggle="modal" data-target="#TaoDonHang" title="Sửa Đơn"><i class="fas fa-edit"></i></button>';
                $button['HuyDon'] = '<button type="button" class="btn btn-default" data-name='.$MaDH.' data-id='.$MaDH.' title="Hủy Đơn" onclick="HuyDonHang(\''.$MaDH.'\')"><i class=" fa fa-exclamation-triangle"></i></button>';

            }
        $button['Copy'] = '<button  class="btn btn-danger copy-noi-dung-don-hang" data-id='.$MaDH.' data-toggle="modal" data-target="#CopyNoiDungDonHang" title="COPY TIN NHẮN"><i class="far fa-paper-plane"></i></button>';
        if(isset($QuyenHan['Admin']))
            $button['GiaoMotPhan'] = '<button  class="btn btn-danger don-giao-mot-phan" data-id='.$MaDH.' data-toggle="modal" data-target="#DonGiaoMotPhan" title="GIAO MỘT PHẦN"><i class="fas fa-chalkboard-teacher"></i></button>';
        $button['HoaDon'] = '<button  class="btn btn-danger xem-hoa-don" data-id='.$MaDH.' data-href="'.$url.'/don-hang/in-don-hang/'.$MaDH.'" data-toggle="modal" data-target="#XemHoaDon" title="HOÁ ĐƠN"><i class="fas fa-image"></i></button>';
        return $button;              
    }
    //CHỨC NĂNG MỚI THÊM 9/10/2018 - 23h
    public function TimKiemDonHangTheoDonVi(Request $request,$MaDV){
        $key_search = $request->key_search;
        $DonHangModel = new DonHang();
        return $this->DSDonHangTheoDonVi($MaDV,$DonHangModel->timkiemdonhangtheodonvi($MaDV,$key_search));
    }
        //DANH SÁCH ĐƠN TRÙNG
    public function DonTrungLap(){
$DonHangModel = new DonHang();
        $View_Path = 'don-hang.danh-sach-don-hang';
        $DanhSachDonHang = $DonHangModel->DanhSachDonTrung();
        return $this->ReturnInit($View_Path,$DanhSachDonHang);
    }

    public function UpDateTrangThaiGHTK(Request $request){
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
        "13"=>"12",//Không giao được hàng
        "10"=>"2",//Delay giao hàng
        "11"=>"9",//Đã đối soát trả hàng
        "12"=>"7",//Đang lấy hàng
        "20"=>"9",//Đang trả hàng
        "21"=>"17",//Đã trả hàng
        "123"=>"7",//Shipper báo đã lấy hàng
        "127"=>"13",//Shipber báo không lấy được hàng
        "128"=>"13",//Shiper báo delay lấy hàng
        "45"=>"8",//Shiper báo đã giao hàng
        "49"=>"9",//Shiper báo không giao được hàng
        "410"=>"2",//Shiper báo delay giao hàng
        );
        $APIModel = new API();
        $DonHangModel = new DonHang();
        $time = time();
        $homqua = $time - (24*60*60);
        $Ngay = date("Y-m-d",$homqua);
        if(isset($request->ngaybatdaudangapi))
            $NgayBatDau = date_format( new \DateTime(str_replace("/","-", $request->ngaybatdaudangapi)),'Y-m-d');
        else
            $NgayBatDau = $Ngay;
        if(isset($request->ngayketthucdangapi))
            $NgayKetThuc = date_format( new \DateTime(str_replace("/","-", $request->ngayketthucdangapi)),'Y-m-d');
        else
            $NgayKetThuc = $Ngay;
        $ThongTinDH = $DonHangModel->LayThongTinDonHang_Update($NgayBatDau,$NgayKetThuc);
        if($ThongTinDH)
        {
            $respone = $APIModel->CheckDonGHTK($ThongTinDH->MaVanDonHang);
        if (isset($respone)) 
            {
                
                $data = json_decode($respone);

                if(!is_object($data))
                {
                    //$DonHangModel->Minh_SetTimeTemp($ThongTinDH->MaDH);
                    echo $ThongTinDH->MaVanDonHang." : Không CURL được<br>";
                    var_dump($respone);

                }
                else
                {
                    if ($data->success==true) 
                    {
                        $TongTien = $ThongTinDH->TongTien;

                        $order = $data->order;
                        $MaDH= $order->partner_id;
                        $PhiShipThucTe= $order->ship_money;
                        $status_id = $order->status;
                        $TienThuThucTe = $order->pick_money;
                        $MaTrangthai = $ArrMaTrangThai[$status_id];
                        $TenTrangThai = DB::table('trangthaidonhang')->where('MaTT',$MaTrangthai)->first()->TenTT;
                            // echo "<font color='red'>".$ThongTinDH->MaDH." tong tien : ".$TongTien." - TienCOD : ".$TienThuThucTe."</font><br>";

                        if($status_id == 6)
                        {
                            $DonHangModel->CapNhatDonDoiSoatGHTK($MaDH,$MaTrangthai,$PhiShipThucTe,$TienThuThucTe);
                            echo $MaDH." <b><font color='green'>Đã Giao Thành Công</font></b> .  Kết quả :  đã thu ".number_format($TienThuThucTe)." , phí ship : ".number_format($PhiShipThucTe)."<br>";
                        }
                        elseif($status_id == 9 or $status_id ==11)
                        {
                            if(isset($ThongTinDH->Tinh) && $ThongTinDH->Tinh !="")
                            $MaTinh = $ThongTinDH->Tinh;
                            else $MaTinh = 1;
                            if($MaTinh == 2)
                            $PhiShipThucTe= $order->ship_money;
                            else
                            $PhiShipThucTe= $order->ship_money + $order->ship_money*0.5;
                            $DonHangModel->CapNhatDonDoiSoatGHTK($MaDH,$MaTrangthai,$PhiShipThucTe,0);
                             echo $MaDH." <b><font color='blue'>Giao Hàng Thất Bại</font></b> . Phí Ship : ".number_format($PhiShipThucTe)."<br>";
                        }
                        else
                        {
                            $DonHangModel->CapNhatDonGHTK($MaDH,$MaTrangthai);
                            $TenKH = $order->customer_fullname;
                            $SDT = $order->customer_tel;
                            $DiaChi = $order->address;
                            DB::table('donhang')->where('MaDH',$MaDH)
                            ->update([
                                'TenKH' => $TenKH,
                                'SDT' => $SDT,
                                'DiaChi' => $DiaChi
                            ]);

                             echo $MaDH." Cập Nhật Trạng Thái :  <b>".$TenTrangThai."</b><br>";
                        }

                        $DonHangModel->Minh_SetTimeTemp($ThongTinDH->MaDH);
                        
                       
                    }
                    elseif($data->success==false)
                    {
                        $DonHangModel->Minh_SetTimeTemp($ThongTinDH->MaDH);
                        echo $ThongTinDH->MaDH." BỊ XÓA KHỎI GHTK<BR>";
                    }
                    else
                    {
                        echo $ThongTinDH->MaDH." LỖI BẤT THƯỜNG<BR>";
                        print_r($respone);
                    }
                }
            
            }
            else echo $ThongTinDH->MaDH." LỖI CURL";
        }
        else 
            echo "Đã cập nhật trạng thái của toàn bộ đơn hàng <br>";     
    }

    public function XemTheoMaTrangThai($MaTrangthai){
        $MaNV = Auth::user()->id;
        $DonHangModel = new DonHang();
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
        if(isset($QuyenHan['Admin'])||isset($QuyenHan['Chăm Đơn']))
        $DanhSachDonHang = $DonHangModel->DonTheoMaTrangThai($MaTrangthai);
        
        $View_Path = 'don-hang.danh-sach-don-hang';
        return $this->ReturnInit($View_Path,$DanhSachDonHang);
    }

 	public function ThemMaXaPhuongThiTran(Request $request){
 		$MaDH = $request->MaDH;
 		$MaVung = $request->MaVung;
 		$checkdon = DB::table('donhang')->where('MaDH',$MaDH)->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaKH')->first();
 		if($checkdon)
 		{
 			$Tinh = $checkdon->Tinh;
	 		$Huyen = $checkdon->Huyen;
	 		$Xa = $checkdon->Xa;
	 		$MaTinh = DB::table('tinh')->where('TenTinh',$Tinh)->first()->id;
	 		$MaHuyen = DB::table('huyen')->where('TenHuyen',$Huyen)->where('IdTinh',$MaTinh)->first()->id;
	 		$DBXa = DB::table('xa')->where('TenXa',$Xa)->where('IdHuyen',$MaHuyen)->first();
	 		if($DBXa)
	 		{
	 			if($DBXa->Area_id =="")
	 			{
	 				DB::table('xa')->where('id',$DBXa->id)->update(['Area_id'=>$MaVung]);
	 				echo "Đã cập nhật mã vùng cho ".$DBXa->TenXa;
	 			}
	 			else
	 				echo $DBXa->TenXa." đã có mã vùng là ".$DBXa->Area_id;
	 			
	 		}
 		}
 	}

    public function UpDateTrangThaiJNT(Request $request){
        $ketqua = "";
        $APIModel = new API_JT();
        $DonHangModel = new DonHang();
        $time = time();
        $homqua = $time - (24*60*60);
        $Ngay = date("Y-m-d",$homqua);
        if(isset($request->ngaybatdaudangapi))
            $NgayBatDau = date_format( new \DateTime(str_replace("/","-", $request->ngaybatdaudangapi)),'Y-m-d');
        else
            $NgayBatDau = $Ngay;
        if(isset($request->ngayketthucdangapi))
            $NgayKetThuc = date_format( new \DateTime(str_replace("/","-", $request->ngayketthucdangapi)),'Y-m-d');
        else
            $NgayKetThuc = $Ngay;
        $ThongTinDH = $DonHangModel->LayThongTinDonHang_JNT($NgayBatDau,$NgayKetThuc);
        if($ThongTinDH)
        {
            $respone = $APIModel->LayTrangThaiDonHang($ThongTinDH->MaVanDonHang);
        if (isset($respone)) 
            {
                
              $kq = json_decode($respone);
              $data = $kq->responseitems;
              $dl = $data[0];

              if($dl->success =="true")
                {
                  $tracesList = $dl->tracesList;
                  $traces = $tracesList[0];
                  $lichtrinh = $traces->details;
                  $thamso =  end($lichtrinh);
                  $Ma = $thamso->scantype;
                  $MaTrangthai = $APIModel->LayMaTrangThaiTheoTen($Ma);
                  $checktrangthai = DB::table('trangthaidonhang')->where('MaTT',$MaTrangthai)->first();
                  $TenTrangThai = $checktrangthai->TenTT;
                  echo "Đơn hàng ".$ThongTinDH->MaDH." đã cập nhật trạng thái ".$TenTrangThai." . Mã dữ liệu : ".$Ma."<br>";
                  DB::table('donhang')->where('MaDH',$ThongTinDH->MaDH)->update(['MaTrangthai'=>$MaTrangthai]);

                }
              else
                {
                 echo "Không lấy được dữ liệu . Mã lỗi ".$dl->reason;
                }

            
            }
            else echo $ThongTinDH->MaDH." LỖI CURL";
            $DonHangModel->Minh_SetTimeTemp($ThongTinDH->MaDH);
        }
        else 
                echo "Đã cập nhật trạng thái của toàn bộ đơn hàng <br>";
    }
    public function XemAnhCoc($MaDH){
        $url = \URL::to('/');
        $DL = DB::table('donhang')->where("MaDH",$MaDH)->select('HinhAnhCoc')->first();
        echo '<img src="'.$url.'/images/tiencoc/'.$DL->HinhAnhCoc.'" width="100%"/>';
    }

    public function LayHotLine(){
        $HotLine = "0933160079";
        return $HotLine;
    }
    public function DoiDVGH(Request $request){
      $MaDV = $request->MaDV;
      $MaDH = $request->MaDH;
      $update = DB::table('donhang')->where('MaDH',$MaDH)->update(['DonviGH'=>$MaDV]);
      if($update)
      {
        $CheckDV = DB::table('donvigiaohang')->where('MaDV',$MaDV)->first();
        echo $CheckDV->TenDV;
      }
    }
    public function TimKho($MaKho){
        $DuLieu = DB::table('kho')->where('MaKho',$MaKho)->leftJoin('diachinh','kho.MaDiaChinh','like','diachinh.id')->first();
        $KetQua = array("DiaChi"=>$DuLieu->DiaChi,"Tinh"=>$DuLieu->Tinh,"Huyen"=>$DuLieu->Huyen,"Xa"=>$DuLieu->Xa);
        echo json_encode($KetQua);
    }
    public function KiemTraPhiShip(Request $request){
        $APIModel = new API_GHN();
        $MaDiaChinh = $request->MaDiaChinh;
        $data_product = $request->data_product;
        $CheckDiaChinh = DB::table('diachinh')->where('id',$MaDiaChinh)->select('DistrictID','service_id','WardCode')->first();
        $CanNang = 0;
        foreach($data_product as $chitiet)
        {
            $tach = explode("|",$chitiet);
            $IDSP = $tach[0];
            $SoLuong = $tach[1];
            $LaySP = DB::table('sanpham')->where('id',$IDSP)->first();
            $CanNang+= $LaySP->TrongLuong * $SoLuong;
        }
        if($CanNang < 1500)
            $CanNang = 500;
        elseif($CanNang >1500 and $CanNang <= 2000)
            $CanNang = 1000;
        elseif($CanNang > 2000 and $CanNang <= 3000)
            $CanNang = 2000;
        
        $PhiShip = $APIModel->TinhPhiShip($CheckDiaChinh->DistrictID,$CheckDiaChinh->service_id,$CheckDiaChinh->WardCode,$CanNang);
        echo $PhiShip;

    }
    public function UpdateGHTK(){
        $APIModel = new API();
        $DSDH = DB::table('donhang')->where('Ngay','like','%2023-12%')->whereNotIn('MaTrangthai',['5','8','9','10','12'])->get();
        foreach($DSDH as $DH)
        {
            //$CapNhat = $APIModel->CheckDonGHTK($DH->MaVanDonHang);
            echo $DH->MaDH." : ".$DH->MaVanDonHang."<br>";
            
        }
    }
    public function CheckDonGHTK(){
        $APIModel = new API();
        $DSDH = DB::table('donhang')->where('Ngay','like','%2023-02%')->where('DonviGH',4)->where('TienThuThucTe','!=','TongTien')->where('MaTrangthai',8)->get();
        foreach($DSDH as $DH)
        {
            //$CapNhat = $APIModel->CheckDonGHTK($DH->MaVanDonHang);
            echo $DH->MaDH." : ".$DH->MaVanDonHang."<br>";
            
        }
    }
    public function ChonChanhXe($MaDH){
        $MaDiaChinh = DB::table('donhang')->where('donhang.MaDH',$MaDH)->leftJoin('khachhang','donhang.MaDH','like','khachhang.MaDH')->select('khachhang.MaDiaChinh')->first()->MaDiaChinh;
        $ChanhXeDaChon = DB::table('donhang')->where('MaDH',$MaDH)->select('ChanhXeID')->first()->ChanhXeID;
        $DiaChinh = DB::table('diachinh')->where('id',$MaDiaChinh)->first();
        $ProvinceID = $DiaChinh->ProvinceID;
        $DistrictID = $DiaChinh->DistrictID;
        $ListChanhXe = DB::table('chanhxe')->where('DistrictID','like','%'.$DistrictID.'%')->orwhere('ProvinceID','like','%'.$ProvinceID.'%')->get();
        if($ListChanhXe and count($ListChanhXe)>0)
        {
            echo '<table id="danhsachchanhxe" border="1" style="width: 100%;font-size:12px">
                            <thead style="background: black;color: white;">
                                <tr>
                                  <th>Tên Chành</th>
                                  <th>Thông tin</th>
                                  <th>Tuyến Tỉnh</th>
                                  <th>Tuyến Huyện</th>
                                  <th>TT</th>
                                </tr>
                            </thead>
                            <tbody>';
            foreach($ListChanhXe as $ChanhXe)
            {
                $TuyenTinh = "";
                $TuyenHuyen = "";
                $JSonListTinh = $ChanhXe->ProvinceID;
                $JSonListHuyen = $ChanhXe->DistrictID;
                $ListTinh = json_decode($JSonListTinh);
                $ListHuyen = json_decode($JSonListHuyen);
                foreach($ListTinh as $Tinh)
                {
                    $LayTinh = DB::table('diachinh')->where('ProvinceID',$Tinh)->select('Tinh')->first();   
                    if($TuyenTinh =="")
                        $TuyenTinh = $LayTinh->Tinh;
                    else
                        $TuyenTinh.=" , ".$LayTinh->Tinh;
                }
                foreach($ListHuyen as $Huyen)
                {
                    $LayHuyen = DB::table('diachinh')->where('DistrictID',$Huyen)->select('Huyen')->first();    
                    if($TuyenHuyen =="")
                        $TuyenHuyen=$LayHuyen->Huyen;
                    else
                        $TuyenHuyen.=" , ".$LayHuyen->Huyen;
                }
                if($ChanhXeDaChon != "")
                {
                    if($ChanhXe->ChanhXeID == $ChanhXeDaChon)
                        $ThaoTac = '<button class="btn btn-warning luuchanhxe" data-ChanhXeID="'.$ChanhXe->ChanhXeID.'" data-MaDH="'.$MaDH.'">ĐÃ CHỌN</button>';
                    else
                        $ThaoTac = '<button class="btn btn-success luuchanhxe" data-ChanhXeID="'.$ChanhXe->ChanhXeID.'" data-MaDH="'.$MaDH.'">ĐỔI</button>';
                }
                else
                    $ThaoTac = '<button class="btn btn-success luuchanhxe" data-ChanhXeID="'.$ChanhXe->ChanhXeID.'" data-MaDH="'.$MaDH.'">LƯU</button>';
                
                echo '<tr>
                    <td>'.$ChanhXe->TenChanhXe.'</td>
                    <td>'.$ChanhXe->SoDienThoai.'<br>'.$ChanhXe->DiaChi.'</td>
                    <td>'.$TuyenTinh.'</td>
                    <td>'.$TuyenHuyen.'</td>
                    <td>'.$ThaoTac.'</td>
                    </tr>
                ';
            }
            echo '</tbody></table>';
        }
        else
            echo "Không tìm thấy chành xe phù hợp";
    }
    public function LuuChanhXe(Request $request){
        $MaDH = $request->MaDH;
        $ChanhXeID = $request->ChanhXeID;
        $Luu = DB::table('donhang')->where('MaDH',$MaDH)->update(['ChanhXeID'=>$ChanhXeID]);
        if($Luu)
            echo "true";
        else
            echo "false";
    }

    public function KiemTraVaSuaDon(){
        $DSDon = DB::table('donhang')->where('Ngay','like','%2023-03%')->where('MaTrangthai','!=','5')->select('MaDH','Ngay')->get();
        foreach($DSDon as $Don)
        {
            $Check = DB::table('chitietdonhang')->where('MaDH',$Don->MaDH)->where('NgayBan','!=',$Don->Ngay)->first();
            if($Check)
            {
                echo $Don->MaDH."<br>";
                 DB::table('chitietdonhang')->where('MaDH',$Don->MaDH)->update(['NgayBan'=>$Don->Ngay]);
            }
        }
    }
    public function TimSoLuongKhuyenMai(Request $request){
        $MaSPCheck = $request->MaSP;
        $CauHinh = DB::table('cauhinh')->select('QuaKhuyenMai')->first()->QuaKhuyenMai;
        $QuaTang = json_decode($CauHinh);
        foreach($QuaTang as $MaSP => $SoLuong)
        {
            if($MaSPCheck == $MaSP)
                echo $SoLuong;

        }
    }

    public function XemTongTienDonHang(Request $request){
        $MaDH = $request->MaDH;
        $TongTien =  DB::table('donhang')->where('MaDH',$MaDH)->select('TongTien')->first()->TongTien;
        return number_format($TongTien);
    }

    public function XemChiTietDonHang(Request $request){
        $MaDH = $request->MaDH;
        $DonHangModel = new DonHang();
        return $DonHangModel->XemChiTietDonHang($MaDH);
    }
    public function XemSanPhamCuaDonHang(Request $request){
        $MaDH = $request->MaDH;
        $ChiTiet = DB::table('chitietdonhang')->where('MaDH',$MaDH)->get();
        $data = array();
        foreach($ChiTiet as $SanPham)
        {
            $TenSP = DB::table('sanpham')->where('MaSP',$SanPham->MaSP)->select('TenSP')->first()->TenSP;
            $data[] = array("MaSP"=>$SanPham->MaSP,"TenSP"=>$TenSP);
        }
        echo json_encode($data);
    }
    public function XemDVGH(Request $request){
        $MaDH = $request->MaDH;
        $DVGH = DB::table('donhang')->where('MaDH',$MaDH)->leftJoin('donvigiaohang','donhang.DonviGH','like','donvigiaohang.MaDV')->select('donvigiaohang.TenDV')->first()->TenDV;
        return $DVGH;
    }
    public function TaoDonGiaoMotPhan(Request $request){
        $MaDH = $request->MaDH;
        $MaSP = $request->MaSP;
        $SoLuong = $request->SoLuong;
        $data = array_merge($MaSP,$SoLuong);
        $count = count($MaSP);
        $ListSP = array();
        $ListSP_Cu = array();
        $ListSP_Hoan = array();
        $DonHangMoi = "";
        $DonHangHoan = "";
        $MaDonHoan = $MaDH."H";
        for ($i=0; $i < $count ; $i++) 
        {
            $MaSP = $data[$i];
            $SoLuong = $data[$i+$count];
            $ListSP[$MaSP] = $SoLuong;
        }
        $TongTien = $request->TongTien;
        $ThongTinDH = DB::table('donhang')->Where('MaDH',$MaDH)->first();
        if($ThongTinDH)
        {
            $ChiTietDH_Cu = DB::table('chitietdonhang')->where('MaDH',$MaDH)->get();

            foreach($ChiTietDH_Cu as $ChiTiet)
            {
                $SL = 
                $ListSP_Cu[$ChiTiet->MaSP] = $ChiTiet->SoLuong;
                //echo $ChiTiet->SoLuong;
            }
            foreach($ListSP_Cu as $MaSP=>$SoLuong)
            {
                $MaKiemTra = "";
                if(array_key_exists($MaSP,$ListSP))
                {
                    $SoLuongMoi = $ListSP[$MaSP];
                    $TTSP = DB::table('sanpham')->where('MaSP',$MaSP)->first();
                    if($SoLuongMoi != $SoLuong)
                    {
                        $SoLuongConLai = $SoLuong - $SoLuongMoi;
                        $MaKiemTra = $MaDH.".".$MaSP;
                        DB::table('chitietdonhang')->where('MaKiemTra',$MaKiemTra)->update(['SoLuong'=>$SoLuongMoi]);
                        $DonHangMoi .= '<div class="product_row"><div class="PRODUCT_ID" style="display:none">'.$MaSP.'</div><span class="MASP">'.$TTSP->TenSP.'</span><span> : </span><span class="SOLUONG">'.$SoLuongMoi.' '.$TTSP->DonViTinh.'</span></div>';
                        $ListSP_Hoan[$MaSP] = $SoLuongConLai;    
                    }
                    else
                        $DonHangMoi .= '<div class="product_row"><div class="PRODUCT_ID" style="display:none">'.$MaSP.'</div><span class="MASP">'.$TTSP->TenSP.'</span><span> : </span><span class="SOLUONG">'.$SoLuong.' '.$TTSP->DonViTinh.'</span></div>';
                }
                else
                {
                    $ListSP_Hoan[$MaSP] = $SoLuong;
                    DB::table('chitietdonhang')->where('MaKiemTra',$MaKiemTra)->delete();


                }
            }
            foreach($ListSP_Hoan as $MaSP=>$SoLuong)
            {
                $TTSP = DB::table('sanpham')->where('MaSP',$MaSP)->first();
                $TTDHCu = DB::table('chitietdonhang')->where('MaDH',$MaDH)->first();
                $NgayBan = $TTDHCu->NgayBan;
                $NgayXuatKho = $TTDHCu->NgayXuatKho;
                $MaKiemTra = $MaDonHoan.".".$MaSP;
                DB::table('chitietdonhang')->insert([
                    'MaKiemTra'=>$MaKiemTra,
                    'MaDH'=>$MaDonHoan,
                    'MaSP'=>$MaSP,
                    'SoLuong'=>$SoLuong,
                    'NhomSanPham'=>$TTSP->DanhMucSP,
                    'NgayBan'=>$NgayBan,
                    'NgayXuatKho'=>$NgayXuatKho
                    ]);
                $DonHangHoan .= '<div class="product_row"><div class="PRODUCT_ID" style="display:none">'.$MaSP.'</div><span class="MASP">'.$TTSP->TenSP.'</span><span> : </span><span class="SOLUONG">'.$SoLuong.' '.$TTSP->DonViTinh.'</span></div>';
            }
            DB::table('donhang')->where('MaDH',$MaDH)->update(['DonHang'=>$DonHangMoi,'TongTien'=>$TongTien]);
            DB::table('donhang')->insert([
                'MaDH'=>$MaDonHoan,
                'MaNV'=>$ThongTinDH->MaNV,
                'TongTien'=>0,
                'MaTrangthai'=>12,
                'Ngay'=>$ThongTinDH->Ngay,
                'ThoiGian'=>$ThongTinDH->ThoiGian,
                'DonviGH'=>$ThongTinDH->DonviGH,
                'DonHang'=>$DonHangHoan
            ]);

            

        }
        return Redirect::back()->with(['message_success'=>"Đã tạo đơn giao hàng 1 phần!",'message_code'=>5]);
    }
    public function DanhSachCombo(){
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
        $Combo = DB::table('combo')->where('TinhTrang','Đang Hoạt Động')->get();

        foreach($Combo as $a)
        {
            echo '<i class="fas fa-hand-point-right" style="font-size: 19px;"></i><button class="btn btn-danger checkcombo" data-id="'.$a->id.'">'.$a->TenCombo.'</button>';
        }
    }
    public function DanhSachKhuyenMai(){
        $QuyenHanModel = new QuyenHan();
        $QuyenHan = $QuyenHanModel->QuyenHanNhanVien();
        if(isset($QuyenHan['Admin']))
            $KhuyenMai = DB::table('khuyenmai')->where('TinhTrang','Đang Hoạt Động')->get();
        else
        {
            $MaCN = Auth::user()->MaCN;
            $KhuyenMai = DB::table('khuyenmai')->where($MaCN,'Có')->where('TinhTrang','Đang Hoạt Động')->get();
        }
        foreach($KhuyenMai as $a)
        {
            echo '<button class="btn btn-danger checkkhuyenmai" data-id="'.$a->id.'">'.$a->TenChuongTrinh.'</button>';
        }
    }
    public function CheckCombo(Request $request){
        $id = $request->id;
        $data_product = $request->data_product;
        $Arr_SanPham = array();
        foreach($data_product as $SanPham)
        {
            $TachSanPham = explode("|",$SanPham);
            $Arr_SanPham[$TachSanPham[0]] = $TachSanPham[1];
        }
        $DuLieu = DB::table('combo')->where('id',$id)->first();
        $YeuCau = json_decode($DuLieu->YeuCau);
        foreach($YeuCau as $MaSP=>$SoLuong)
        {
            if(array_key_exists($MaSP,$Arr_SanPham))
            {
                if($SoLuong <= $Arr_SanPham[$MaSP])
                    {
                        $datyeucau = "true";
                        
                    }
                else
                {
                    $datyeucau = "false";
                    break;
                    
                }
            }
            else
            {
                $datyeucau = "false";
                break;
            }
        }
        if($datyeucau =="true")
            $GiamGia = $DuLieu->GiamGia;
        else
            $GiamGia = 0;
        $data = array("KetQua"=>$datyeucau,"GiamGia"=>$GiamGia,"TextGiamGia"=>number_format($GiamGia));
        echo json_encode($data);

    }
    public function CheckKhuyenMai(Request $request){
        $id = $request->id;
        $data_product = $request->data_product;
        $DuLieu = DB::table('khuyenmai')->where('id',$id)->first();
        $YeuCau = json_decode($DuLieu->YeuCau);
        $SoLuongCan = $DuLieu->YeuCau_SoLuong;
        $QuaTang = json_decode($DuLieu->QuaTang);
        $ListQuaTang = array();
        foreach($QuaTang as $MaSP=>$SoLuong)
        {
            $SP = DB::table('sanpham')->where('MaSP',$MaSP)->select('DonViTinh','TenSP')->first();
            $ListQuaTang[] = array("MaSP"=>$MaSP,"TenSP"=>$SP->TenSP,"SoLuong"=>$SoLuong,'DonViTinh'=>$SP->DonViTinh);
        }
        $TongSoLuong = 0;
        foreach($data_product as $SanPham)
        {
            $TachSanPham = explode("|",$SanPham);
            $MaSP = $TachSanPham[0];
            $SoLuong = $TachSanPham[1];
            if(in_array($MaSP, $YeuCau))
                $TongSoLuong += $SoLuong;
        }
        if($TongSoLuong >= $SoLuongCan)
        {
            $KetQua = "true";
            $SoQuaTang = floor($TongSoLuong / $SoLuongCan);
        }
        else
        {
            $KetQua = "false";
            $SoQuaTang = 0;
        }
        $result = array("KetQua"=>$KetQua,"SoQuaTang"=>$SoQuaTang,"QuaTang"=>$ListQuaTang);
        echo json_encode($result);

    }
    public function LaySDTNhanVien($MaNV){
        $a = DB::table('users')->where('id',$MaNV)->select('SoDienThoai','MaCN')->first();
        if($a->SoDienThoai !="")
            return $a->SoDienThoai;
        else
        {
            if($a->MaCN =="SG")
                return "0933160079";
            else
                return "0941102093";
        }
    }
    private function maskPhoneNumber($phoneNumber) {
    if (is_string($phoneNumber) && strlen($phoneNumber) >= 7) {
        $maskedPart = str_repeat('*', 3);
        $phoneNumber = substr_replace($phoneNumber, $maskedPart, 4, 3);
    }
    return $phoneNumber;
}
}