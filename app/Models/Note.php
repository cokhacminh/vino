<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';

    protected $fillable = [
        'user_id', 'tieu_de', 'noi_dung', 'mau_sac', 'ghim',
    ];

    protected $casts = [
        'ghim' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'note_id');
    }

    public function activeReminder()
    {
        return $this->hasOne(Reminder::class, 'note_id')
            ->where('trang_thai', '!=', 'hoan_thanh')
            ->orderBy('thoi_gian');
    }
}
