<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Project;
use App\Models\Task;

class User extends Authenticatable
{
    use HasApiTokens;
    
    protected $table = 'users';

    protected $fillable = [
        'name',
        'last_name',
        'maternal_surname',
        'email',
        'password',
        'role_id',
        'active'
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')->withTimestamps();
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')->withTimestamps();
    }
}
