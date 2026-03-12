<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = 'reminders';

    protected $fillable = [
        'user_id', 'note_id', 'tieu_de', 'thoi_gian', 'lap_lai', 'trang_thai',
    ];

    protected $casts = [
        'thoi_gian' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function note()
    {
        return $this->belongsTo(Note::class, 'note_id');
    }
}
