<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'sanpham';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'MaSP', 'TenSP', 'AnhSP', 'MoTa', 'LieuDung', 'DonViTinh',
        'QuyCach', 'TrongLuong', 'GiaNhap', 'GiaBan', 'DanhMucSP',
    ];
}
