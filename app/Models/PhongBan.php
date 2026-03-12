<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhongBan extends Model
{
    protected $table = 'phongban';
    protected $primaryKey = 'MaPB';

    protected $fillable = ['TenPB', 'TrangThai'];

    protected $casts = [
        'TrangThai' => 'integer',
    ];

    /**
     * Một phòng ban có nhiều chức vụ
     */
    public function chucVus()
    {
        return $this->hasMany(ChucVu::class, 'MaPB', 'MaPB');
    }

    /**
     * Đếm số chức vụ thuộc phòng ban
     */
    public function getSoChucVuAttribute()
    {
        return $this->chucVus()->count();
    }
}
