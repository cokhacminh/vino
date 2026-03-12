<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChamCong extends Model
{
    protected $table = 'chamcong';

    protected $fillable = ['user_id', 'ngay', 'trang_thai', 'gio_vao', 'gio_ra', 'ghi_chu'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
