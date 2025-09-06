<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ActivityLogController;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['csrf' => csrf_token()]);
});

Route::get('/docs-api', function () {
    $file = storage_path('api-docs/api-docs.json');
    if (!File::exists($file)) {
        abort(404);
    }
    return response()->file($file, [
        'Content-Type' => 'application/json'
    ]);
})->name('l5-swagger.default.docs');

Route::post('/login', [AuthController::class, 'login'])
    ->name('web.login');

Route::post('/register', [AuthController::class, 'register'])
    ->name('web.register');

Route::middleware(['auth:sanctum', 'activity'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('web.logout');

    Route::prefix('users')->group(function() {
        Route::middleware('role:1,2')->group(function () {
            Route::get('/', [UserController::class, 'getUsers'])
                ->name('web.get_users');
    
            Route::get('/{userId}', [UserController::class, 'getUserById'])
                ->name('web.get_user_by_id');
    
            Route::put('/update/{userId}', [UserController::class, 'updateUser'])
                ->name('web.update_user');
            
            Route::delete('/delete/{userId}', [UserController::class, 'deleteUser'])
                ->name('web.delete_user');
        });
    });

    Route::prefix('tasks')->group(function() {
        Route::middleware('role:1,2,3')->group(function () {
            Route::get('/', [TaskController::class, 'getTasks'])
                ->name('web.get_tasks');
    
            Route::get('/{taskId}', [TaskController::class, 'getTaskById'])
                ->name('web.get_task_by_id');
                
            Route::put('/update/{taskId}', [TaskController::class, 'updateTask'])
                ->name('web.update_task');
                
            Route::put('/update-task-status/{taskId}', [TaskController::class, 'updateTaskStatus'])
                ->name('web.update_task_status');
        });
        Route::middleware('role:1,2')->group(function () { 
            Route::post('/create', [TaskController::class, 'createTask'])
                ->name('web.create_task');

            Route::delete('/delete/{taskId}', [TaskController::class, 'deleteTask'])
                ->name('web.delete_task');

            Route::put('/assign-users-task/{taskId}', [TaskController::class, 'assignUsersToTask'])
                ->name('web.assign_users_task');
        });
    });

    Route::prefix('projects')->group(function() {
        Route::middleware('role:1,2')->group(function () {
            Route::get('/{projectId?}', [ProjectController::class, 'getProjects'])
                ->name('web.get_projects');
    
            Route::post('/create', [ProjectController::class, 'createProject'])
                ->name('web.create_project');
    
            Route::put('/update/{projectId}', [ProjectController::class, 'updateProject'])
                ->name('web.update_project');
    
            Route::put('/assign-users-project/{projectId}', [ProjectController::class, 'assignUsersToProject'])
                ->name('web.assign_users_project');
    
            Route::delete('/delete/{projectId}', [ProjectController::class, 'deleteProject'])
                ->name('web.delete_project');    
        });
    });

    Route::prefix('activity-logs')->group(function() {
        Route::middleware('role:1')->group(function () {
            Route::get('/', [ActivityLogController::class, 'getLogs'])
                ->name('web.get_logs');
        });
    });    
});