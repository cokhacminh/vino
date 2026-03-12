<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Auth;
use app\API;
class DonHang extends Model
{
    public function KiemTraQuyenHan(){
        $ThaoTac = Auth::user()->ThaoTac;
        $TachThaoTac = explode(",", $ThaoTac);
        $QuyenHan = array();
        foreach ($TachThaoTac as $value) {
            $QuyenHan[$value] = true;
        }
        return $QuyenHan;
    }
    public function DanhSachDonHangTheoMaDonVi($MaDV)
    {
        $today = date("Y-m-d");
        $today_start = $today." 00:00:00";
        $today_end = $today." 23:59:59";
        if( $MaDV == "all")
        {
        return DB::table('donhang')
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->orderBy('donhang.ThoiGian','desc')
        ->limit(100)
        ->get();
        }
        else
        {
        return DB::table('donhang')
        ->where('DonviGH',$MaDV)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->orderBy('donhang.ThoiGian','desc')
        ->limit(100)
        ->get();
        }
    }

     public function LocDanhSachDonHang($MaNV = 0, $TrangThai = 0,$DVGH ,$form_date = null, $to_date = null)
    {
        ini_set('memory_limit','256M');
        $MaCN = Auth::user()->MaCN;
        $ThaoTac = Auth::user()->ThaoTac;
        $TachThaoTac = explode(",", $ThaoTac);
        $QuyenHan = array();
        foreach ($TachThaoTac as $value) {
            $QuyenHan[$value] = true;
        }

        $thangnay = date("Y-m");
        if($form_date == null)
            $form_date = $thangnay."-01";
        if($to_date == null)
            $to_date = date("Y-m-d");
        # THỜI GIAN

            $where=" ThoiGian between '".$form_date." 00:00:00' and '".$to_date." 23:59:59'";
        if($MaNV == 0)
        {
            if(isset($QuyenHan['Quản Lý Bán Hàng'])&&!isset($QuyenHan['Quản Lý Giao Hàng']))
            {
                $where.="and MaNV in (select id from users where MaCN = '".$MaCN."')";
            }
            
            
        }
        elseif($MaNV !=0)
            $where.="and MaNV = '".$MaNV."'";

        # TRẠNG THÁI
        if( $TrangThai != 0)
            $where .=" and MaTrangthai = '".$TrangThai."'";
        else
            $where .=" and MaTrangthai != '5'";
        # DVGH
        if( $DVGH != 0)
            $where .=" and DonviGH = '".$DVGH."'";

        $data =  DB::table('donhang')
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangThai', '=', 'trangthaidonhang.MaTT')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
        ->orderBy('donhang.id','desc')->get();
        
        return $data;
    }
    
    public function LocDanhSachDonHangTheoMADV($MaDV = 0,$MaNV = 0, $TrangThai = 0, $form_date = null, $to_date = null, $key = '')
    {
        $thangnay = date("Y-m");
        if($form_date == null)
            $form_date = $thangnay."-01";
        if($to_date == null)
            $to_date = date("Y-m-d");

        $where = "";

        # MÃ NHÂN VIÊN
        if( $MaNV != 0)
            $where .= "MaNV = '".$MaNV."'";

        # ĐIỀU KIỆN - (HÀM LỌC DÙN CHUNG MODEL BÁN HÀNG - ĐIỀU KIỆN TRẢ VỀ DANH SÁCH TƯƠNG ỨNG VỚI MÀNG HÌNH ĐANG CHẠY)
        if($MaDV != 0 && $where == "")
            $where .= "DonviGH ='".$MaDV."'";
        elseif( $MaDV != 0 && $where != "")
            $where .= " and DonviGH ='".$MaDV."'";

        # TRẠNG THÁI
        if( $TrangThai != 0 && $where == "")
            $where .=" MaTrangthai = '".$TrangThai."'";
        elseif( $TrangThai != 0 && $where != "")
            $where.=" and MaTrangthai = '".$TrangThai."'";

        # KEY
        if($key !="" && $where == "")
            $where.=" MaDH like '%".$key."%' or TenKH like '%".$key."%' or SoDienThoai like '%".$key."%'";
        elseif($key !="" && $where != "")
            $where.=" and MaDH like '%".$key."%' or TenKH like '%".$key."%' or SoDienThoai like '%".$key."%'";

        # THỜI GIAN
        if($where =="")
            $where.=" ThoiGian between '".$form_date." 00:00:00' and '".$to_date." 23:59:59'";
        elseif($where !="")
            $where.=" and ThoiGian between '".$form_date." 00:00:00' and '".$to_date." 23:59:59'";

        return
        DB::table('donhang')
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangThai', '=', 'trangthaidonhang.MaTT')
        
        ->orderBy('donhang.id','desc')->get();
    }
     

     
     public function LayThongTinDonHang(){
        return DB::table('donhang')
        ->where('DonViGH','4')
        ->where('MaTrangThai','1')
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->orderBy('ThoiGian','desc')->limit(1)->first();
    }
        public function LayThongTinDonHang_MINH($NgayBatDau,$NgayKetThuc){
        $now = time();
        $delay = 180*1;
        $timecheck = $now-$delay;
        $where = "ThoiGian between '{$NgayBatDau} 00:00:00' and '{$NgayKetThuc} 23:59:59' and MaDH not in (select MaDH from ticket where TrangThai = 'Chưa Xử Lý') and Nhom in ( select MaDMSP from danhmucsanpham where MaNhom = 1)";
        return DB::table('donhang')
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->where('DonViGH','4')
        ->where('MaVanDonHang',null)
        ->where('Time_Temp','<',$timecheck)
        ->where('MaTrangThai','!=',5)

        ->whereRaw($where)
        ->select('donhang.*','users.name','donvigiaohang.TenDV','trangthaidonhang.TenTT')
        ->orderBy('ThoiGian','desc')->limit(1)->first();
    }
       public function LayThongTinDonHang_Update($NgayBatDau,$NgayKetThuc){
        $now = time();
        $delay = 3*3600*1;
        $timecheck = $now-$delay;
        $where = "ThoiGian between '{$NgayBatDau} 00:00:00' and '{$NgayKetThuc} 23:59:59'";
        return DB::table('donhang')
        ->where('MaVanDonHang','!=','')
        ->where('Time_Temp','<',$timecheck)
        ->whereNotIn('MaTrangThai',[5,8,10])
        ->whereRaw($where)
        ->orderBy('ThoiGian','desc')->limit(1)->first();
    }
       public function LayThongTinDonHang_JNT($NgayBatDau,$NgayKetThuc){
        $now = time();
        $delay = 3*3600*1;
        $timecheck = $now-$delay;
        $where = "ThoiGian between '{$NgayBatDau} 00:00:00' and '{$NgayKetThuc} 23:59:59'";
        return DB::table('donhang')
        ->where('MaVanDonHang','!=','')
        ->where('DonviGH','5')
        ->where('MaTrangthai','!=','5')
        ->where('Time_Temp','<',$timecheck)
        ->whereRaw($where)
        ->orderBy('ThoiGian','desc')->limit(1)->first();
    }
    public function LayThongTinDonHang_DB($a){
        //$a = "[".$a."]";
        $where = "MaDH IN (".$a.")";
        $now = time();
        $delay = 30*1;
        $timecheck = $now-$delay;
        return DB::table('donhang')
        ->where('DonViGH','4')
        ->where('MaVanDonHang',null)
        ->where('Time_Temp','<',$timecheck)
        ->where('MaTrangThai','!=',5)
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->orderBy('ThoiGian','desc')->limit(1)->first();
    }

    public function DanhSachNhanVienFilter(){
        return DB::table('users')->select('name','id')->where('MaPB','3')->orderBy('id','desc')->get();
    }
    public function SalesActive(){
        $QuyenHan = $this->KiemTraQuyenHan();
        if(isset($QuyenHan['Admin'])||isset($QuyenHan['Quản Lý Bán Hàng']))
            return DB::table('users')->select('name','id','MaCN')->where('TinhTrang','Đang Làm Việc')->orderBy('id','desc')->get();
        else
            return DB::table('users')->select('name','id','MaCN')->where('id',Auth::user()->id)->first();
    }
    public function DanhSachDonHangFilter()
    {
        return DB::table('donhang')->select('MaDH')->limit(100)->orderBy('MaDH','desc')
                ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
                ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
                ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
                ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
                ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
                ->get();
    }
    public function DanhSachDonHang($MaNV)
    {
        $thismonth = date("Y-m");
        $where = "Ngay like '{$thismonth}-%'";
        return DB::table('donhang')
        ->where('donhang.MaNV',$MaNV)
        ->whereRaw($where)
        ->where('MaTrangthai','!=','5')
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.DonviGH','donhang.TongTien','donhang.Ngay','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')
        ->get();
    }
        public function DanhSachDonHangPhanLoai($MaNV,$PhanLoai)
    {
        $thismonth = date("Y-m");
        
        switch ($PhanLoai) {
            case 'chua-gui-hang':
                $where = "ThoiGian between '{$thismonth}-01 00:00:00' and '{$thismonth}-31 23:59:59' and MaTrangthai in ('1','11','13','15')";
                break;
            case 'dang-giao-hang':
                $where = "ThoiGian between '{$thismonth}-01 00:00:00' and '{$thismonth}-31 23:59:59' and MaTrangthai in ('2','4','7','21','22','23')";
                break;
            case 'da-giao-hang':
                $where = "ThoiGian between '{$thismonth}-01 00:00:00' and '{$thismonth}-31 23:59:59' and MaTrangthai in ('8')";
                break;
            case 'giao-that-bai':
                $where = "ThoiGian between '{$thismonth}-01 00:00:00' and '{$thismonth}-31 23:59:59' and MaTrangthai in ('9','10','12','16','17','20')";
                break;
            case 'don-huy':
                $where = "ThoiGian between '{$thismonth}-01 00:00:00' and '{$thismonth}-31 23:59:59' and MaTrangthai in ('5')";
                break;
            case 'toan-bo':
                $where = "";
                break;
            default:
                $where = "ThoiGian between '{$thismonth}-01 00:00:00' and '{$thismonth}-31 23:59:59'";
                break;
        }
        if($where !="")
            return DB::table('donhang')
            ->where('donhang.MaNV',$MaNV)
            ->whereRaw($where)
            ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
            ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
            ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
            ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
            ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
            ->orderBy('donhang.ThoiGian','desc')
            ->get();
        else
            return DB::table('donhang')
            ->where('donhang.MaNV',$MaNV)
            ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
            ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
            ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
            ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
            ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
            ->orderBy('donhang.ThoiGian','desc')
            ->get();
    }
  
    public function ToanBoDonHang()
    {
        $today = date("Y-m");
        $where = "ThoiGian like '%{$today}%' and MaTrangthai !='5'";
        return DB::table('donhang')
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','khachhang.MaKH','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')
        ->limit(100)
        ->get();
    }
    public function ToanBoDonHangMinh()
    {
        $today = date("Y-m");
        $where = "ThoiGian like '%{$today}%' and MaTrangthai !='5'";
        return DB::table('donhang')
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','khachhang.MaKH','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')
        ->limit(100)
        ->get();
    }
    public function ToanBoDonHangPhanLoai($PhanLoai)
    {
        $today = date("Y-m");
        
        switch ($PhanLoai) {
            case 'chua-gui-hang':
                $where = "ThoiGian like '%{$today}%' and MaTrangthai in ('1','11','13','15')";
                break;
            case 'dang-giao-hang':
                $where = "ThoiGian like '%{$today}%' and MaTrangthai in ('2','4','7','21','22','23')";
                break;
            case 'da-giao-hang':
                $where = "ThoiGian like '%{$today}%' and MaTrangthai in ('8')";
                break;
            case 'giao-that-bai':
                $where = "ThoiGian like '%{$today}%'  and MaTrangthai in ('9','10','12','16','17','20')";
                break;
            case 'don-huy':
                $where = "ThoiGian like '%{$today}%'  and MaTrangthai in ('5')";
                break;
            case 'toan-bo':
                $where = "";
                break;
            default:
                $where = "ThoiGian like '%{$today}%' and MaTrangthai !='5'";
                break;
        }
        if($where !="")
            return DB::table('donhang')
            ->whereRaw($where)
            ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
            ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
            ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
            ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
            ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
            ->orderBy('donhang.ThoiGian','desc')
            ->get();
        else
            return DB::table('donhang')
            ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
            ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
            ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
            ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
            ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
            ->orderBy('donhang.ThoiGian','desc')
            ->get();
    }
    public function DanhSachDonHangTheoChiNhanh()
    {
        $MaCN = Auth::user()->MaCN;
        $today = date("Y-m");
        $where = "ThoiGian like '%{$today}%' and MaNV in ( select id from users where MaCN = '".$MaCN."' ) and MaTrangthai != '5'";
        return DB::table('donhang')
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')
        ->get();
    }
    public function DanhSachDonHangTheoChiNhanhPhanLoai($PhanLoai)
    {
        $MaCN = Auth::user()->MaCN;
        $today = date("Y-m");
        switch ($PhanLoai) {
            case 'chua-gui-hang':
                $where = "ThoiGian like '%{$today}%' and MaNV in ( select id from users where MaCN = '".$MaCN."' ) and MaTrangthai in ('1','11','13','15')";
                break;
            case 'dang-giao-hang':
                $where = "ThoiGian like '%{$today}%' and MaNV in ( select id from users where MaCN = '".$MaCN."' ) and MaTrangthai in ('2','4','7','21','22','23')";
                break;
            case 'da-giao-hang':
                $where = "ThoiGian like '%{$today}%' and MaNV in ( select id from users where MaCN = '".$MaCN."' ) and MaTrangthai in ('8')";
                break;
            case 'giao-that-bai':
                $where = "ThoiGian like '%{$today}%' and MaNV in ( select id from users where MaCN = '".$MaCN."' ) and MaTrangthai in ('9','10','12','16','17','20')";
                break;
            case 'don-huy':
                $where = "ThoiGian like '%{$today}%' and MaNV in ( select id from users where MaCN = '".$MaCN."' ) and MaTrangthai in ('5')";
                break;
            case 'toan-bo':
                $where = "MaNV in ( select id from users where MaCN = '".$MaCN."' )";
                break;
            default:
                $where = "ThoiGian like '%{$today}%' and MaNV in ( select id from users where MaCN = '".$MaCN."' )";
                break;
        }
        if($where != "")
            return DB::table('donhang')
            ->whereRaw($where)
            ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
            ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
            ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
            ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
            ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
            ->orderBy('donhang.ThoiGian','desc')
            ->get();
        else
            return DB::table('donhang')
            
            ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
            ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
            ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
            ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
            ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.MaNV','donhang.Ngay','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
            ->orderBy('donhang.ThoiGian','desc')
            ->get();
    }
    public function DanhSachDonHangAPI()
    {
        return DB::table('donhang')
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->select('donhang.*','users.name','donvigiaohang.TenDV','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')
        ->limit(0)
        ->get();
    }

     
    public function LayDonHangTheoMADH($MaDH)
    {
        return DB::table('donhang')
        ->where('donhang.MaDH',$MaDH)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->select('donhang.*','users.name','donvigiaohang.TenDV','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')->first();
    }
    
    public function LayDonHangTheoMADHAPI($MaDH)
    {
        return DB::table('donhang')
        ->where('donhang.MaDH',$MaDH)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->select('donhang.*','users.name','donvigiaohang.TenDV','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')->first();
    }



        public function ThemDonHang($MaDH, $MaNV, $TongTien,$GiamGia,$MaDV,$GhiChu)
    {
        $Ngay = date("Y-m-d");
        DB::table('donhang')->insert([
        'MaDH' => $MaDH,
        'MaNV' => $MaNV,
        'TongTien' => $TongTien,
        'GiamGia' =>$GiamGia,
        'MaTrangthai' => 1,
        'Ngay'=>$Ngay,
        'DonViGH' => $MaDV,
        'GhiChu'=>$GhiChu
        ]);
    }

        public function SuaDonHang($MaDH, $TongTien,$GiamGia, $MaDV,$GhiChu)
    {
        DB::table('donhang')
        ->where('donhang.MaDH',$MaDH)
        ->update([
        'TongTien' => $TongTien,
        'GiamGia' =>$GiamGia,
        'MaTrangthai' => 1,
        'DonViGH' => $MaDV,
        'GhiChu'=>$GhiChu
        ]);
    }

    public function SuaDonHangYCHT($MaDH, $MaKH, $TongTien, $PhiShip, $MaPage, $ChiTietDH,$SDT , $CocTruoc, $HinhAnhCoc,$MaTrangthai,$DonHang)
    {
         DB::table('donhang')
        ->where('MaDH','=',$MaDH)
        ->update([
        'MaDH' => $MaDH,
        'MaKH' => $MaKH,
        'TongTien' => $TongTien,
        'PhiShip' => $PhiShip,
        'ChiTietDH' => $ChiTietDH,
        'SDT' => $SDT,
        'CocTruoc' => $CocTruoc,
        'HinhAnhCoc' => $HinhAnhCoc,
        'MaTrangthai'=>$MaTrangthai,
        'DonHang'=>$DonHang,
        ]);
    }


     public function XoaDonHang($MaDH)
    {
         DB::table('donhang')
        ->where('MaDH','=',$MaDH)
        ->delete();
    }
     public function XoaKhachHang($MaKH)
    {
         DB::table('khachhang')
        ->where('MaKH','=',$MaKH)
        ->delete();
    }


    //KHÁCH HÀNG

    public function ThemKhachHang($MaDH,$TenKH,$SoDienThoai,$DiaChi,$Tinh,$Huyen,$Xa,$MaDiaChinh){
        DB::table('khachhang')->insert([
        'MaDH' => $MaDH,
        'TenKH' => $TenKH,
        'SoDienthoai' => $SoDienThoai,
        'DiaChi' => $DiaChi,
        'Tinh' => $Tinh,
        'Huyen' => $Huyen,
        'Xa'=>$Xa,
        'MaDiaChinh'=>$MaDiaChinh
        ]);
    }
    public function CapNhatKhachHang($TenKH,$SoDienThoai,$DiaChi,$Tinh,$Huyen,$Xa,$MaDiaChinh){
        DB::table('khachhang')
        ->where('SoDienThoai',$SoDienThoai)
        ->update([
        'TenKH' => $TenKH,
        'DiaChi' => $DiaChi,
        'Tinh' => $Tinh,
        'Huyen' => $Huyen,
        'Xa'=>$Xa,
        'MaDiaChinh'=>$MaDiaChinh
        ]);
        DB::table('khachhang')
        ->where('SoDienThoai',$SoDienThoai)
        ->increment('SoLanMua');
    }
    public function XoaChiTietDonHang($MaDH){
        DB::table('chitietdonhang')->where('donhang.MaDH',$MaDH)->delete();
    }
    public function LayChiNhanh($MaDH){
        return DB::table('donhang')->where('donhang.MaDH',$MaDH)->leftJoin('users','users.id','=','donhang.MaNV')->select('users.MaCN')->first()->MaCN;
    }
    public function SuaKhachHang($MaDH,$SoDienThoai,$TenKH,$DiaChi,$Tinh,$Huyen,$Xa,$MaDiaChinh){
        DB::table('khachhang')
        ->where('MaDH',$MaDH)
        ->update([
        'SoDienThoai' =>$SoDienThoai,
        'TenKH' => $TenKH,
        'DiaChi' => $DiaChi,
        'Tinh' => $Tinh,
        'Huyen' => $Huyen,
        'Xa'=>$Xa,
        'MaDiaChinh'=>$MaDiaChinh
        ]);
    }

    public function TimKhachHang($MaDH){
        return DB::table('khachhang')->where('MaDH',$MaDH)->first();
    }
    public function TimKhuVuc($MaDiaChinh){
        return DB::table('diachinh')->where('id',$MaDiaChinh)->select('KhuVuc')->first()->KhuVuc;
    }
    public function TimKhachHangTheoSDT($MaDH){
        return DB::table('khachhang')->where('MaKH',$MaDH)->first();
    }
    public function ChiTietDonHang($MaDH){
        return DB::table('chitietdonhang')
        ->where('chitietdonhang.MaDH',$MaDH)
        ->leftJoin('sanpham','chitietdonhang.MaSP','like','sanpham.MaSP')
        ->leftJoin('danhmucsanpham','sanpham.DanhMucSP','like','danhmucsanpham.MaDMSP')
        ->select('chitietdonhang.*','sanpham.DonViTinh','sanpham.TrongLuong','sanpham.TenSP','danhmucsanpham.TenDMSP','sanpham.DanhMucSP')
        ->get();
    }
    public function XemChiTietDonHang($MaDH){
        $DonHang = "";
        $DuLieu =  DB::table('chitietdonhang')
        ->where('chitietdonhang.MaDH',$MaDH)
        ->leftJoin('sanpham','chitietdonhang.MaSP','like','sanpham.MaSP')
        ->select('chitietdonhang.*','sanpham.DonViTinh','sanpham.TenSP','sanpham.DanhMucSP')
        ->get();
        foreach($DuLieu as $ChiTiet)
        {
            $Tong = $ChiTiet->SoLuong;
            $decimal = fmod($Tong,1);
            if($decimal != 0)
                $TongSL = $Tong;
            else
                $TongSL = floor($Tong);
            if($ChiTiet->QuaTang =="Yes")
                $DonHang .= '<div class="product_row"><div class="PRODUCT_ID" style="display:none">'.$ChiTiet->MaSP.'</div><span class="MASP"><i class="fas fa-gift"></i> '.$ChiTiet->TenSP.'</span><span> : </span><span class="SOLUONG">'.$TongSL.' '.$ChiTiet->DonViTinh.'</span></div>';
            else
                $DonHang .= '<div class="product_row"><div class="PRODUCT_ID" style="display:none">'.$ChiTiet->MaSP.'</div><span class="MASP">'.$ChiTiet->TenSP.'</span><span> : </span><span class="SOLUONG">'.$TongSL.' '.$ChiTiet->DonViTinh.'</span></div>';
        }
        return $DonHang;
    }
    public function ThemChiTietDonHang($MaKiemTra,$MaDH,$MaSP,$SoLuong,$NgayBan,$NhomSanPham)
    {
        DB::table('chitietdonhang')->insert([
            'MaKiemTra'=>$MaKiemTra,
            'MaDH'=>$MaDH,
            'MaSP'=>$MaSP,
            'SoLuong'=>$SoLuong,
            'NgayBan'=>$NgayBan,
            'NgayXuatKho'=>$NgayBan,
            'NhomSanPham'=>$NhomSanPham
        ]);
    }
    public function ThemQuaTang($MaKiemTra,$MaDH,$MaSP,$SoLuong,$NgayBan,$NhomSanPham)
    {
        DB::table('chitietdonhang')->insert([
            'MaKiemTra'=>$MaKiemTra,
            'MaDH'=>$MaDH,
            'MaSP'=>$MaSP,
            'SoLuong'=>$SoLuong,
            'NgayBan'=>$NgayBan,
            'NgayXuatKho'=>$NgayBan,
            'NhomSanPham'=>$NhomSanPham,
            'QuaTang' =>'Yes'
        ]);
    }     
    public function ThemTicketXacNhanCoc($MaDH,$HinhAnh,$TienCoc)
    {
        DB::table('donhang')->where('MaDH',$MaDH)->update([
            'HinhAnhCoc'=>$HinhAnh
        ]);
        DB::table('ticket')->where('MaDH',$MaDH)->update([
            'AnhCoc'=>$HinhAnh
        ]);
    }  
    public function ThemTicket($MaDH,$MaNV,$NoiDung,$TuyChon,$SoTien)
    {
        DB::table('ticket')->insert([
            'MaDH'=>$MaDH,
            'MaNV'=>$MaNV,
            'NoiDung'=>$NoiDung,
            'TuyChon'=>$TuyChon,
            'TrangThai'=>'Chờ Tiếp Nhận',
            'Xem'=>0,
            'SoTien'=>$SoTien
        ]);
    }
    public function ThemTicketSuaSanPham($MaDH,$MaNV,$NoiDung,$TuyChon,$Json,$SoTien)
    {
        DB::table('ticket')->insert([
            'MaDH'=>$MaDH,
            'MaNV'=>$MaNV,
            'NoiDung'=>$NoiDung,
            'TuyChon'=>$TuyChon,
            'TrangThai'=>'Chờ Tiếp Nhận',
            'Xem'=>0,
            'Json'=>$Json,
            'SoTien'=>$SoTien
        ]);
    }
    
    public function SuaTongTien($MaDH,$TongTien)
    {
        DB::table('donhang')->where('MaDH',$MaDH)->update([
            'TongTien'=>$TongTien
        ]);
    }
    public function XoaDonTicket($MaDH)
    {
        DB::table('ticket')->where('MaDH',$MaDH)->delete();
    }

    public function CapNhatDonHangYCHT($MaDH)
    {
        DB::table('donhang')->where('MaDH',$MaDH)->update([
            'YCHT'=>0
        ]);
    }
     public function DanhSachSanPham()
    {
        return DB::table('sanpham')->select('MaSP','ID','TenSP')->get();
    }
     public function DanhSachSanPhamDuocBan($MaCN)
    {
        $table = "CamBan_".$MaCN;
        return DB::table('sanpham')->select('MaSP','TenSP','ID')->where($table,'like','Được Bán')->get();
    }

     public function CHuyenIDSangMASP($ID)//
    {
        return DB::table('sanpham')->select('MaSP')->where('id',$ID)->first();
    }
      public function TinhTien($MaSP, $MaCN, $SoLuong)
    {
        $Gia = DB::table('quanlygia')->select('GiaThanh')->where('MaSP',$MaSP)->where('MaCN',$MaCN)->first();
        $GiaTien = $Gia->GiaThanh;
        $TongTien = (int)$GiaTien * $SoLuong;
        return $TongTien;
    }
     public function DanhSachTinh()
    {
        return DB::table('diachinh')->groupBy('Tinh')->select('Tinh')->orderBy('Tinh','asc')->get();
    }
     public function DanhSachKho()
    {
        return DB::table('kho')->get();
    }    
     public function AjaxTinh($MA_TINH)
    {
        return DB::table('huyen')->where('IdTinh',$MA_TINH)->orderBy('TenHuyen','asc')->get();
    }
    public function AjaxHuyen($MA_HUYEN)
    {
        return DB::table('xa')->where('IdHuyen',$MA_HUYEN)->orderBy('TenXa','asc')->get();
    }
    public function AjaxHuyenNew($MA_HUYEN)
    {
        return DB::table('xa')->where('IdHuyen',$MA_HUYEN)->orderBy('TenXa','asc')->get();
    }
    public function LayMaVanDonNhom($MaNhom){
        return DB::table('nhom')->select('MaVanDonNhom')->where('MaNhom',$MaNhom)->first();
    }
    public function LayMaVanDonDVGH($MaDV){
        return DB::table('donvigiaohang')->select('MaVanDonDVGH')->where('MaDV',$MaDV)->first();
    }
    public function CheckDate($CheckDate){
        return DB::table('mavandon')->where('date',$CheckDate)->first();
    }
    public function LayId(){
        return DB::table('mavandon')->max('id');
    }
    public function CapNhatID($checkdate){
        DB::table('mavandon')->insert([
        'date' => $checkdate,
        ]);
    }
     public function ThemID($checkdate){
        DB::table('mavandon')
        ->delete();
        DB::statement("ALTER TABLE mavandon AUTO_INCREMENT = 1;");
        DB::table('mavandon')->insert([
        'date' => $checkdate,
        ]);
    }

    
    public function LayTenTinh($id){
        return DB::table('tinh')->select('TenTinh')->where('id',$id)->first();
    }
    public function LayTenHuyen($id){
        return DB::table('huyen')->select('TenHuyen')->where('id',$id)->first();
    }
    public function DiaChinh($id){
        return DB::table('diachinh')->select('Xa')->where('id',$id)->first();
    }
    public function DiaChiKho($MaKho){
        return DB::table('kho')->select('DiaChi')->where('MaKho',$MaKho)->first()->DiaChi;
    }
    public function CheckHinhAnh($MaDH){
        return DB::table('donhang')->select('HinhAnhCoc')->where('donhang.MaDH',$MaDH)->first();
    }
      public function LayYCHT($MaDH){
        return DB::table('donhang')->select('YCHT')->where('donhang.MaDH',$MaDH)->first();
    }
    public function LayMaChiNhanhNhom($MaNhom){
        return DB::table('nhom')->select('MaCNN')->where('MaNhom',$MaNhom)->first();
    }


        public function DanhSachDonTrung()
    {       
        $today = date("Y-m-d");
        $time = time();
        $days = 7;
        $date_minus_time = $time - ( $days * 3600 * 26);
        $date_minus = gmdate("Y-m-d", $date_minus_time);
        $where = "ThoiGian between '{$date_minus} 00:00:00' and '{$today} 23:59:59' and khachhang.SoDienThoai IN ( SELECT khachhang.SoDienThoai FROM donhang LEFT JOIN khachhang ON `donhang`.MaDH = `khachhang`.`MaKH` where donhang.MaDH not like '%001%' and ThoiGian BETWEEN '{$date_minus} 00:00:00' and '{$today} 23:59:59' GROUP BY khachhang.SoDienThoai HAVING COUNT(khachhang.SoDienThoai) > 1 )";
         return DB::table('donhang')
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
        ->select('donhang.*','users.name','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT','khachhang.*')
        ->orderBy('khachhang.SoDienThoai','desc')
        ->get();
        
    }

    public function LaySoDienThoaiNhom($MaNhom){
        return DB::table('nhom')->select('SDT')->where('MaNhom',$MaNhom)->first();
    }
    public function CapNhatMaVanDonHang($MaDH,$MaVanDonHang,$PhiShipThucTe){
        DB::table('donhang')->where('donhang.MaDH',$MaDH)->update([
            'MaVanDonHang'=>$MaVanDonHang,
            'PhiShipThucTe'=>$PhiShipThucTe,
            'MaTrangthai'=>11,
        ]);
    }
    public function CapNhatDonGHTK($MaDH,$MaTrangthai){
        DB::table('donhang')->where('donhang.MaDH',$MaDH)->update([
            'MaTrangthai'=>$MaTrangthai,
        ]);
    }
    public function CapNhatDonDoiSoatGHTK($MaDH,$MaTrangthai,$PhiShipThucTe,$TienThuThucTe){
        DB::table('donhang')->where('donhang.MaDH',$MaDH)->update([
            'MaTrangthai'=>$MaTrangthai,
            'PhiShipThucTe'=>$PhiShipThucTe,
            'TienThuThucTe'=>$TienThuThucTe,
        ]);
    }
    public function CapNhatMaVanDonHangThatBai($MaDH){
        $time = time();
        DB::table('donhang')->where('donhang.MaDH',$MaDH)->update([
            'Time_Temp'=>$time
        ]);
    }
    public function CapNhatPTVC($MaDH){
        $time = time();
        DB::table('donhang')->where('donhang.MaDH',$MaDH)->update([
            
            'transport'=>'road'
        ]);
    }
    public function Minh_SetTimeTemp($MaDH){
        $time = time();
        DB::table('donhang')->where('donhang.MaDH',$MaDH)->update([
            'Time_Temp'=>$time,
        ]);
    }
    public function LayThongTinDonVi($MaDH){
        return DB::table('donhang')->select('DonviGH','MaVanDonHang')->where('donhang.MaDH',$MaDH)->first();
    }
    public function LayThongTinLogViettel($MaDH){
        return DB::table('viettel_log')
        ->where('MaDH','like',"%$MaDH%")
        ->leftJoin('trangthaidonhang', 'viettel_log.Status_Id', '=', 'trangthaidonhang.MaTT')
        ->orderBy('Status_Date','desc')->get();
    }
    public function LayThongTinLogGHTK($MaDH){
        return DB::table('ghtk_log')
        ->where('MaDH','like',"$MaDH")
        ->leftJoin('trangthaidonhang', 'ghtk_log.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        ->orderBy('ThoiGian','desc')->get();
    }
    public function LayTenTinhTheoID($id){
        return DB::table('tinh')->select('TenTinh')->where('id',$id)->first();
    }
    public function LayTenHuyenTheoID($id){
        return DB::table('huyen')->select('TenHuyen')->where('id',$id)->first();
    }
    public function LayTenXaTheoID($id){
        return DB::table('xa')->select('TenXa')->where('id',$id)->first();
    }
     public function KiemTraYCHT($MaDH,$TuyChon){
        $check = DB::table('ticket')
        ->where('donhang.MaDH',$MaDH)
        ->where('TuyChon',$TuyChon)
        ->first();
        if ($check) {
            return true;
        }
        else{
            return false;
        }
    }
    public function CapNhatTicket($MaDH,$TuyChon,$NoiDung,$TrangThai){
        DB::table('ticket')
        ->where('donhang.MaDH',$MaDH)
        ->where('TuyChon',$TuyChon)
        ->update([
            'NoiDUng'=>$NoiDung,
            'TrangThai'=>$TrangThai,
            'Xem'=>0,
        ]);
    }
        public function TinhPhiShip($MaNhom,$TongSanPham){
        $check = DB::table('cauhinhship')->where('NhomSP',$MaNhom)->first();
        if(!$check)
        {
            $SoLuongMienShip = 3;
            $PhiShip = 30000;
        }
        else
            $SoLuongMienShip = $check->SoLuong;
        if($TongSanPham >= $SoLuongMienShip)
            $PhiShip = 0;
        else
            $PhiShip = $check->SoTien;

        return $PhiShip;
    }
        public function TinhTongTienDonHang($DanhMucSP,$TongSanPham)
        {

            $b = DB::table('danhmucsanpham')->where('MaDMSP',$DanhMucSP)->first();
            $GiaBan = $b->GiaBan;
            $TongTien = $GiaBan * $TongSanPham;

        return $TongTien;
        }   
        public function LayNhomSP($ID){
            return DB::table('sanpham')->where('ID',$ID)->first();
        }
    public function HuyDonHang($MaDH){
        $APIModel = new API;
        $CheckDon = DB::table('donhang')->where('donhang.MaDH',$MaDH)->first();
        if($CheckDon->MaVanDonHang !="")
        {
            
            $HuyGHTK = $APIModel->HuyDonHangGHTK($MaDH);
        }
        $do = DB::table('donhang')
            ->where('donhang.MaDH',$MaDH)
            ->update([
                'MaTrangthai'=>5,
            ]);
        
        echo "<script>";
        if($do)
        {
            echo "alert('Đã Hủy Đơn Hàng {$MaDH}');";
        }
        echo "window.history.back();";
        echo "</script>";
    }
    public function HuyDon($MaDH){
         DB::table('donhang')
            ->where('donhang.MaDH',$MaDH)
            ->update([
                'MaTrangthai'=>5,
            ]);
    }
    //CHỨC NĂNG MỚI THÊM 9/10/2018 - 23h
    public function timkiemdonhang($key)
    {
        
        $QuyenHan = $this->KiemTraQuyenHan();
        if(isset($QuyenHan['Admin'])||isset($QuyenHan['Quản Lý Giao Hàng']))
            {
                $raw = 'donhang.MaDH like \'%'.$key.'%\' or khachhang.SoDienThoai like \'%'.$key.'%\' or donhang.MaVanDonHang like \'%'.$key.'%\'';
                return
                DB::table('donhang')
                ->whereRaw($raw)
                ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
                ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
                ->leftJoin('trangthaidonhang', 'donhang.MaTrangThai', '=', 'trangthaidonhang.MaTT')
                ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
                ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.Ngay','donhang.MaNV','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
                ->orderBy('donhang.id','desc')->get();

            }
        elseif(isset($QuyenHan['Quản Lý Bán Hàng']))
        {
            $MaCN = Auth::user()->MaCN;
            $raw = 'users.MaCN like \''.$MaCN.'\' and (donhang.MaDH like \'%'.$key.'%\' or khachhang.SoDienThoai like \'%'.$key.'%\' or donhang.MaVanDonHang like \'%'.$key.'%\')';
                return
                DB::table('donhang')
                
                ->whereRaw($raw)
                ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
                ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
                ->leftJoin('trangthaidonhang', 'donhang.MaTrangThai', '=', 'trangthaidonhang.MaTT')
                ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
                ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.Ngay','donhang.MaNV','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
                ->orderBy('donhang.id','desc')->get();
        }
        else
        {
            $raw = 'donhang.MaNV like \''.Auth::user()->id.'\' and ( donhang.MaDH like \'%'.$key.'%\'  or donhang.MaVanDonHang like \'%'.$key.'%\'  or khachhang.SoDienThoai like \'%'.$key.'%\' )';
            return
            DB::table('donhang')
            ->whereRaw($raw)
            ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
            ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
            ->leftJoin('trangthaidonhang', 'donhang.MaTrangThai', '=', 'trangthaidonhang.MaTT')
            ->leftJoin('khachhang', 'donhang.MaDH', '=', 'khachhang.MaDH')
            ->select('donhang.MaDH','donhang.MaVanDonHang','donhang.Ngay','donhang.MaNV','donhang.DonviGH','donhang.TongTien','donhang.TienThuThucTe','donhang.GhiChu','donhang.PhiShipThucTe','donhang.MaTrangthai','donhang.ThoiGian','donhang.CocTruoc','donhang.HinhAnhCoc','users.name','khachhang.TenKH','khachhang.SoDienThoai','khachhang.DiaChi','donvigiaohang.LogoDVGH','trangthaidonhang.TenTT')
            ->orderBy('donhang.id','desc')->get();

        }
            

    }

    public function GiaPhatSinhTheoMaSP($MaSP)
    {
        $a = DB::table('sanpham')->where('MaSP',$MaSP)->leftJoin('danhmucsanpham', 'sanpham.DanhMucSP', '=', 'danhmucsanpham.MaDMSP')->select('danhmucsanpham.GiaPhatSinh')->first();
        return $a->GiaPhatSinh;
    }
    public function DanhSachDanhMucSP()
    {
        return DB::table('danhmucsanpham')->get();
    } 


    public function DonTheoMaTrangThai($MaTrangthai)
    {
        $time = time();
        $from = $time - (3600 * 24 * 10);
        $fomdate = date("Y-m-d",$from);
        $today = date("Y-m-d");
        $where = "ThoiGian between '{$fomdate} 00:00:00' and '{$today} 23:59:59'";
        return DB::table('donhang')
        ->where('MaTrangthai',$MaTrangthai)
        ->whereRaw($where)
        ->leftJoin('users', 'donhang.MaNV', '=', 'users.id')
        ->leftJoin('donvigiaohang', 'donhang.DonviGH', '=', 'donvigiaohang.MaDV')
        ->leftJoin('trangthaidonhang', 'donhang.MaTrangthai', '=', 'trangthaidonhang.MaTT')
        
        ->select('donhang.*','users.name','donvigiaohang.TenDV','trangthaidonhang.TenTT')
        ->orderBy('donhang.ThoiGian','desc')
        ->get();
    }



    //CHƯA GUI
        public function TongDonChuaGuiHangThangNay($thang,$MaNV = null)
    {
        $where = "Ngay like '%{$thang}%'";
        if($MaNV == null)
            return DB::table('donhang')->whereIn('MaTrangthai',[1,11,13,15])->whereRaw($where)->count();
        else
            return DB::table('donhang')->whereIn('MaTrangthai',[1,11,13,15])->where('MaNV',$MaNV)->whereRaw($where)->count();
    }
    //DANG GIAO HANG
    public function TongDonDangGiaoHangThangNay($thang,$MaNV = null)
    {
        $where = "Ngay like '%{$thang}%'";
        if($MaNV == null)
            return DB::table('donhang')->whereIn('MaTrangthai',[2,4,7,21,22,23])->whereRaw($where)->count();
        else
            return DB::table('donhang')->whereIn('MaTrangthai',[2,4,7,21,22,23])->where('MaNV',$MaNV)->whereRaw($where)->count();
    }
    //GIAO THANH CONG
        public function TongDonGiaoThanhCongThangNay($thang,$MaNV = null)
    {
        $where = "Ngay like '%{$thang}%'";
        if($MaNV == null)
            return DB::table('donhang')->whereIn('MaTrangthai',[8])->whereRaw($where)->count();
        else
            return DB::table('donhang')->whereIn('MaTrangthai',[8])->where('MaNV',$MaNV)->whereRaw($where)->count();
    }

        //GIAO THAT BAI
        public function TongDonGiaoThatBaiThangNay($thang,$MaNV = null)
    {
        $where = "Ngay like '%{$thang}%'";
        if($MaNV == null)
            return DB::table('donhang')->whereIn('MaTrangthai',[9,10,12,16,17,20])->whereRaw($where)->count();
        else
            return DB::table('donhang')->whereIn('MaTrangthai',[9,10,12,16,17,20])->where('MaNV',$MaNV)->whereRaw($where)->count();
    }
            //ĐƠN HUỶ
        public function TongDonHuyThangNay($thang,$MaNV = null)
    {
        $where = "Ngay like '%{$thang}%'";
        if($MaNV == null)
            return DB::table('donhang')->whereIn('MaTrangthai',[5])->whereRaw($where)->count();
        else
            return DB::table('donhang')->whereIn('MaTrangthai',[5])->where('MaNV',$MaNV)->whereRaw($where)->count();
    }

        //CHƯA GUI
        public function TongDonChuaGuiHangThangNay_CN($thang)
    {
        $where = "Ngay like '%".$thang."%' and MaNV in (select id from users where MaCN = '".Auth::user()->MaCN."')";
            return DB::table('donhang')->whereIn('MaTrangthai',[1,11,13,15])->whereRaw($where)->count();
    }
    //DANG GIAO HANG
    public function TongDonDangGiaoHangThangNay_CN($thang)
    {
        $where = "Ngay like '%".$thang."%' and MaNV in (select id from users where MaCN = '".Auth::user()->MaCN."')";
            return DB::table('donhang')->whereIn('MaTrangthai',[2,4,7,21,22,23])->whereRaw($where)->count();
    }
    //GIAO THANH CONG
        public function TongDonGiaoThanhCongThangNay_CN($thang)
    {
        $where = "Ngay like '%".$thang."%' and MaNV in (select id from users where MaCN = '".Auth::user()->MaCN."')";
            return DB::table('donhang')->whereIn('MaTrangthai',[8])->whereRaw($where)->count();
    }

        //GIAO THAT BAI
        public function TongDonGiaoThatBaiThangNay_CN($thang)
    {
        $where = "Ngay like '%".$thang."%' and MaNV in (select id from users where MaCN = '".Auth::user()->MaCN."')";
            return DB::table('donhang')->whereIn('MaTrangthai',[9,10,12,16,17,20])->whereRaw($where)->count();
    }
            //ĐƠN HUỶ
        public function TongDonHuyThangNay_CN($thang)
    {
        $where = "Ngay like '%".$thang."%' and MaNV in (select id from users where MaCN = '".Auth::user()->MaCN."')";
            return DB::table('donhang')->whereIn('MaTrangthai',[5])->whereRaw($where)->count();
    }


    public function PhanLoaiDonHang($thang,$QuyenHan){
        if(isset($QuyenHan['Admin']))
        {
            $TongDonChuaGuiHangThangNay = $this->TongDonChuaGuiHangThangNay($thang);
            $TongDonDangGiaoHangThangNay = $this->TongDonDangGiaoHangThangNay($thang);
            $TongDonGiaoThanhCongThangNay = $this->TongDonGiaoThanhCongThangNay($thang);
            $TongDonGiaoThatBaiThangNay = $this->TongDonGiaoThatBaiThangNay($thang);
            $TongDonHuyThangNay = $this->TongDonHuyThangNay($thang);
        }
        elseif(isset($QuyenHan['Quản Lý Bán Hàng']))
        {
            $TongDonChuaGuiHangThangNay = $this->TongDonChuaGuiHangThangNay_CN($thang);
            $TongDonDangGiaoHangThangNay = $this->TongDonDangGiaoHangThangNay_CN($thang);
            $TongDonGiaoThanhCongThangNay = $this->TongDonGiaoThanhCongThangNay_CN($thang);
            $TongDonGiaoThatBaiThangNay = $this->TongDonGiaoThatBaiThangNay_CN($thang);
            $TongDonHuyThangNay = $this->TongDonHuyThangNay_CN($thang);
        }
        elseif(isset($QuyenHan['Bán Hàng']))
        {
            $MaNV = Auth::user()->id;
            $TongDonChuaGuiHangThangNay = $this->TongDonChuaGuiHangThangNay($thang,$MaNV);
            $TongDonDangGiaoHangThangNay = $this->TongDonDangGiaoHangThangNay($thang,$MaNV);
            $TongDonGiaoThanhCongThangNay = $this->TongDonGiaoThanhCongThangNay($thang,$MaNV);
            $TongDonGiaoThatBaiThangNay = $this->TongDonGiaoThatBaiThangNay($thang,$MaNV);
            $TongDonHuyThangNay = $this->TongDonHuyThangNay($thang,$MaNV);
        }
        else
        {
            
            $TongDonChuaTiepNhanThangNay = 0;
            $TongDonDangGiaoHangThangNay = 0;
            $TongDonGiaoThanhCongThangNay = 0;
            $TongDonGiaoThatBaiThangNay = 0;
            $TongDonHuyThangNay = 0;
        }
        return array("ChuaGuiHang"=>$TongDonChuaGuiHangThangNay,"DangGiao"=>$TongDonDangGiaoHangThangNay,"ThanhCong"=>$TongDonGiaoThanhCongThangNay,"ThatBai"=>$TongDonGiaoThatBaiThangNay,"Huy"=>$TongDonHuyThangNay);
    }
}