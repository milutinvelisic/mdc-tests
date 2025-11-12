<?php

use App\Http\Controllers\Admin\DataImportController;
use App\Http\Controllers\Admin\ImportedDataController;
use App\Http\Controllers\Admin\ImportsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login', [AuthController::class, 'login'])->name('login.user');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/password_reset', [AuthController::class, 'passwordReset'])->name('password.request');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::middleware('permission:user-management')->group(function() {
                Route::resource('users', UsersController::class);
                Route::resource('permissions', PermissionController::class);
            });

            Route::middleware('permission:data-import')->group(function () {
                Route::get('data-import', [DataImportController::class, 'index'])->name('data-import.index');
                Route::post('data-import/import', [DataImportController::class, 'import'])->name('data-import.import');

                Route::get('/imported', [ImportedDataController::class, 'index'])->name('imported.index');
                Route::get('/imported/{importType}/{fileKey}', [ImportedDataController::class, 'show'])->name('imported.show');
                Route::get('/imported/{importType}/{fileKey}/export', [ImportedDataController::class, 'export'])->name('imported.export');
                Route::delete('/imported/{importType}/{fileKey}/row/{id}', [ImportedDataController::class, 'deleteRow'])->name('imported.deleteRow');
                Route::get('/imported/{importType}/{fileKey}/audits/{rowId}', [ImportedDataController::class, 'audits'])->name('imported.audits');

                Route::get('/imports', [ImportsController::class, 'index'])->name('imports.index');
                Route::get('/imports/{id}', [ImportsController::class, 'show'])->name('imports.show');
            });

        });
});
