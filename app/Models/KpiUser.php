<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiUser extends Model
{
    protected $table = 'kpi_users';

    protected $fillable = [
        'kpi_id', 'user_id', 'thang', 'nam', 'deadline_time', 'reported_at', 'bao_cao', 'hinh_anh',
        'trang_thai', 'danh_gia', 'ghi_chu', 'evaluated_by',
    ];

    public function kpi()
    {
        return $this->belongsTo(Kpi::class, 'kpi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
