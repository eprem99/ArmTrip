<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('index');
    Route::get('/dashboard', function () {
        return view('admin');
    })->name('dashboard');
    Route::get('/settings', function () {
        return redirect()->route('admin.settings.organization');
    })->name('settings');
    Route::get('/settings/organization', function () {
        return view('admin');
    })->name('settings.organization');
    Route::get('/settings/api/organization', [SettingsController::class, 'organization']);
    Route::post('/settings/api/organization', [SettingsController::class, 'saveOrganization']);
});
