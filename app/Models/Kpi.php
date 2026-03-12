<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $table = 'kpis';

    protected $fillable = [
        'MaCV', 'thang', 'nam', 'deadline', 'tieu_de', 'noi_dung', 'created_by',
        'loai_ap_dung', 'target_user_id', 'tan_suat',
    ];

    public function kpiUsers()
    {
        return $this->hasMany(KpiUser::class, 'kpi_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
