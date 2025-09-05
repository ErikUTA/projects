<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'status_id',
        'priority_id',
        'due_date'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }
}
