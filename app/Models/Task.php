<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'tieu_de', 'mo_ta', 'loai', 'do_uu_tien', 'trang_thai',
        'parent_id', 'created_by', 'MaPB',
        'ngay_bat_dau', 'ngay_ket_thuc', 'thu_tu', 'hinh_anh',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function taskUsers()
    {
        return $this->hasMany(TaskUser::class, 'task_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id')
            ->withPivot('vai_tro', 'tien_do', 'ghi_chu')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function labels()
    {
        return $this->belongsToMany(TaskLabel::class, 'task_label_pivot', 'task_id', 'label_id');
    }

    public function phongBan()
    {
        return $this->belongsTo(PhongBan::class, 'MaPB', 'MaPB');
    }

    // Tính tiến độ trung bình từ tất cả thành viên
    public function getTienDoTrungBinhAttribute()
    {
        $users = $this->taskUsers;
        if ($users->isEmpty()) return 0;
        return round($users->avg('tien_do'));
    }

    // Đếm subtask hoàn thành
    public function getSubtaskStatsAttribute()
    {
        $total = $this->subtasks()->count();
        $done = $this->subtasks()->where('trang_thai', 'hoan_thanh')->count();
        return ['total' => $total, 'done' => $done];
    }
}
