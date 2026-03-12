<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'SoDienThoai', 'HinhAnh', 'MaCN', 'MaPB', 'MaCV',
        'TeamID', 'DiaChi', 'GioiThieu', 'TinhTrang',
        'NgayVaoLam', 'extension', 'SoData', 'Permission',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function phongBan()
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'MaPB', 'MaPB');
    }

    public function chucVu()
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'MaCV', 'MaCV');
    }
}
