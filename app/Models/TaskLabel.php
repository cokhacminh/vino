<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLabel extends Model
{
    protected $table = 'task_labels';

    protected $fillable = [
        'ten', 'mau_sac',
    ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_label_pivot', 'label_id', 'task_id');
    }
}
