<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucSP extends Model
{
    protected $table = 'danhmucsp';
    protected $primaryKey = 'MaDM';
    public $timestamps = false;

    protected $fillable = ['TenDM'];

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'DanhMucSP', 'MaDM');
    }
}
