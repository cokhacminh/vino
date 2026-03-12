<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanSu extends Model
{
    protected $table = 'nhansu';

    protected $fillable = [
        'user_id',
        // Thông tin cá nhân
        'HoTen', 'NgaySinh', 'GioiTinh', 'SoCCCD', 'NgayCapCCCD', 'NoiCapCCCD',
        'SDT', 'Email', 'ThuongTru', 'DiaChiHienTai',
        // Trình độ học vấn
        'TrinhDoHocVan', 'TruongDaoTao', 'ChuyenNganh', 'NamTotNghiep',
        // HĐLĐ
        'LoaiHD', 'NgayKyHDTV', 'NgayHetHanHDTV', 'NgayKyHDXDTH', 'NgayHetHanHDXDTH', 'NgayKyHDKXD',
        // Ngân hàng - BHXH - MST
        'SoSoBHXH', 'MSTCaNhan', 'STKNganHang',
    ];

    protected $casts = [
        'NgaySinh' => 'date',
        'NgayCapCCCD' => 'date',
        'NgayKyHDTV' => 'date',
        'NgayHetHanHDTV' => 'date',
        'NgayKyHDXDTH' => 'date',
        'NgayHetHanHDXDTH' => 'date',
        'NgayKyHDKXD' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
