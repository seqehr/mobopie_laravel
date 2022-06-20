<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return 'this is good';
});
Route::get('/admin/user/searchbyid/{id}', [\App\Http\Controllers\AdminController::class, 'SearchUserByid']);
Route::get('/admin/login', [\App\Http\Controllers\AuthController::class, 'GetAdminLogin']);
Route::post('/admin/login', [\App\Http\Controllers\AuthController::class, 'PostAdminLogin']);
Route::get('/admin/Dashboard', [\App\Http\Controllers\AdminController::class, 'Dashboard']);
Route::get('/admin/users', [\App\Http\Controllers\UserController::class, 'GetUserList']);
Route::get('/admin/getposts', [\App\Http\Controllers\UserController::class, 'PostByUsers']);
Route::get('/admin/edituser/{id}', [\App\Http\Controllers\UserController::class, 'GetEditUser']);
Route::get('/admin/disable/{id}', [\App\Http\Controllers\UserController::class, 'PostDisableUser']);
Route::post('/admin/edituser/{id}', [\App\Http\Controllers\UserController::class, 'PostEditUser']);
Route::get('/admin/logout', [\App\Http\Controllers\AuthController::class, 'AdminLogOut']);
