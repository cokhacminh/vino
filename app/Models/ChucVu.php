<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    protected $table = 'chucvu';
    protected $primaryKey = 'MaCV';

    protected $fillable = ['TenCV', 'MaPB', 'TrangThai'];

    protected $casts = [
        'TrangThai' => 'integer',
    ];

    /**
     * Chức vụ thuộc về một phòng ban
     */
    public function phongBan()
    {
        return $this->belongsTo(PhongBan::class, 'MaPB', 'MaPB');
    }
}
