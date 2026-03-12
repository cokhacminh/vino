<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonViTinh extends Model
{
    protected $table = 'donvitinh';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['DonViTinh'];
}
