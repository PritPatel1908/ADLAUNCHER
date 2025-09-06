<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceLayoutController;
use App\Http\Controllers\DeviceScreenController;
use App\Http\Controllers\ShowColumnController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScheduleMediaController;
use App\Http\Controllers\RolePermissionController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Route (placeholder)
Route::get('/', function () {
    return view('dashboard.index');
})->name('dashboard')->middleware('auth');

Route::resource('location', LocationController::class)->middleware('auth');
Route::get('/locations/data', [LocationController::class, 'getData'])->name('locations.data')->middleware('auth');

// Company Routes
Route::resource('company', CompanyController::class)->middleware('auth');
Route::get('/companies/data', [CompanyController::class, 'getData'])->name('companies.data')->middleware('auth');

// Area Routes
Route::resource('area', AreaController::class)->middleware('auth');
Route::get('/areas/data', [AreaController::class, 'getData'])->name('areas.data')->middleware('auth');

// User Routes
Route::resource('user', UserController::class)->middleware('auth');
Route::get('/users/data', [UserController::class, 'getData'])->name('users.data')->middleware('auth');

// Device Routes
Route::resource('device', DeviceController::class)->middleware('auth');
Route::get('/devices/data', [DeviceController::class, 'getData'])->name('devices.data')->middleware('auth');

// Device Layout Routes
Route::resource('device-layout', DeviceLayoutController::class)->middleware('auth');
Route::get('/device-layouts', [DeviceLayoutController::class, 'index'])->name('device-layouts.index')->middleware('auth');
Route::get('/device/{device}/layouts', [DeviceLayoutController::class, 'getDeviceLayouts'])->name('device.layouts')->middleware('auth');
Route::get('/device-layout-stats', [DeviceLayoutController::class, 'getLayoutStats'])->name('device-layout.stats')->middleware('auth');

// Device Screen Routes
Route::resource('device-screen', DeviceScreenController::class)->middleware('auth');
Route::get('/device/{device}/screens', [DeviceScreenController::class, 'getDeviceScreens'])->name('device.screens')->middleware('auth');

// Schedule Routes
Route::resource('schedule', ScheduleController::class)->middleware('auth');
Route::get('/schedules/data', [ScheduleController::class, 'getData'])->name('schedules.data')->middleware('auth');
// Schedule Media Routes
Route::resource('schedule-media', ScheduleMediaController::class)->middleware('auth');
Route::get('/schedule/{schedule}/medias', [ScheduleMediaController::class, 'getScheduleMedias'])->name('schedule.medias')->middleware('auth');

// Column Visibility Routes
Route::get('/columns', [ShowColumnController::class, 'getColumns'])->name('columns.get')->middleware('auth');
Route::post('/columns', [ShowColumnController::class, 'updateColumn'])->name('columns.update')->middleware('auth');

// Role & Permission Routes
Route::get('/role-permission', [RolePermissionController::class, 'index'])->name('role-permission.index')->middleware('auth');
Route::get('/roles/data', [RolePermissionController::class, 'getRolesData'])->name('roles.data')->middleware('auth');
Route::resource('roles', RolePermissionController::class)->middleware('auth');
Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'getPermissions'])->name('roles.permissions')->middleware('auth');
Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'storePermissions'])->name('roles.permissions.store')->middleware('auth');
Route::get('/role-permission/stats', [RolePermissionController::class, 'getStats'])->name('role-permission.stats')->middleware('auth');
