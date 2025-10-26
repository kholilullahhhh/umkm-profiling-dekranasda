<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Admin\UserMenuController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\UmkmController;
use App\Http\Controllers\Admin\JenisUsahaController;
use App\Http\Controllers\Admin\PembinaanController;
use App\Http\Controllers\Admin\ProfilingController;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController as Auths;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/{any}', [App\Http\Controllers\PagesController::class, 'index'])->where('any', '.*');


// Auth::routes();

// Route::resource('photos', PhotoController::class)->except(['create', 'store', 'update', 'destroy']);
// Route::resource('photos', PhotoController::class)->only(['index', 'show']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::domain('')->group(function () { // development
    // Route::domain('permohonan.bpfkmakassar.go.id')->group(function () { // production

    // Auth::routes();
    Route::get('/auth/login', [Auths::class, 'index'])->name('admin.login');
    Route::post('/auth/login', [Auths::class, 'login'])->name('login');

    Route::get('/logout', [Auths::class, 'logout'])->middleware('auth');


    // ADMIN_ROUTES
    Route::group(['prefix' => 'admin', 'middleware' => ['web']], function () {

        Route::get('/', [DashboardController::class, 'index'])->name('admin');


        # APPS 

        // MENU TODO

        Route::group(['prefix' => '/todo'], function () {
            Route::get('/', [TodoController::class, 'index'])->name('todo.index');
            Route::get('/data', [TodoController::class, 'data'])->name('todo.data');
            Route::get('/create', [TodoController::class, 'create'])->name('todo.create');
            Route::post('/store', [TodoController::class, 'store'])->name('todo.store');
            Route::get('/{id}/edit', [TodoController::class, 'edit'])->name('todo.edit');
            Route::put('/{id}', [TodoController::class, 'update'])->name('todo.update');
            Route::delete('/{id}', [TodoController::class, 'destroy'])->name('todo.delete');
        });


        // MENU DATA UMKM
        Route::group(['prefix' => '/umkm'], function () {
            Route::get('/', [UmkmController::class, 'index'])->name('umkm.index');
            Route::get('/data', [UmkmController::class, 'data'])->name('umkm.data');
            Route::get('/create', [UmkmController::class, 'create'])->name(name: 'umkm.create');
            Route::post('/store', [UmkmController::class, 'store'])->name('umkm.store');
            Route::get('/{id}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
            Route::put('/{id}', [UmkmController::class, 'update'])->name('umkm.update');
            Route::delete('/{id}', [UmkmController::class, 'destroy'])->name('umkm.delete');
        });

        // MENU JENIS USAHA
        Route::group(['prefix' => '/jenisusaha'], function () {
            Route::get('/', [JenisusahaController::class, 'index'])->name('jenisusaha.index');
            Route::get('/data', [JenisusahaController::class, 'data'])->name('jenisusaha.data');
            Route::get('/create', [JenisusahaController::class, 'create'])->name('jenisusaha.create');
            Route::post('/store', [JenisusahaController::class, 'store'])->name('jenisusaha.store');
            Route::get('/{id}/edit', [JenisusahaController::class, 'edit'])->name('jenisusaha.edit');
            Route::put('/{id}', [JenisusahaController::class, 'update'])->name('jenisusaha.update');
            Route::delete('/{id}', [JenisusahaController::class, 'destroy'])->name('jenisusaha.delete');
        });

        // MENU DATA PEMBINAAN
        Route::group(['prefix' => '/pembinaan'], function () {
            Route::get('/', [PembinaanController::class, 'index'])->name('pembinaan.index');
            Route::get('/data', [PembinaanController::class, 'data'])->name('pembinaan.data');
            Route::get('/create', [PembinaanController::class, 'create'])->name('pembinaan.create');
            Route::post('/store', [PembinaanController::class, 'store'])->name('pembinaan.store');
            Route::get('/{id}/edit', [PembinaanController::class, 'edit'])->name('pembinaan.edit');
            Route::put('/{id}', [PembinaanController::class, 'update'])->name('pembinaan.update');
            Route::delete('/{id}', [PembinaanController::class, 'destroy'])->name('pembinaan.delete');
        });

        // MENU DATA PROFILING
        Route::group(['prefix' => '/profiling'], function () {
            Route::get('/', [ProfilingController::class, 'index'])->name('profiling.index');
            Route::get('/data', [ProfilingController::class, 'data'])->name('profiling.data');
            Route::get('/create', [ProfilingController::class, 'create'])->name('profiling.create');
            Route::post('/store', [ProfilingController::class, 'store'])->name('profiling.store');
            Route::get('/{id}/edit', [ProfilingController::class, 'edit'])->name('profiling.edit');
            Route::put('/{id}', [ProfilingController::class, 'update'])->name('profiling.update');
            Route::delete('/{id}', [ProfilingController::class, 'destroy'])->name('profiling.delete');
        });







        # MENU MASTER DATA


        # USER SETTING
        Route::group(['prefix' => '/roles'], function () {
            Route::get('/', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/data', [RoleController::class, 'data'])->name('roles.data');
            Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.delete');
        });

        Route::group(['prefix' => '/menus'], function () {
            Route::get('/', [MenuController::class, 'index'])->name('menus.index');
            Route::get('/data', [MenuController::class, 'data'])->name('menus.data');
            Route::get('/create', [MenuController::class, 'create'])->name('menus.create');
            Route::post('/store', [MenuController::class, 'store'])->name('menus.store');
            Route::get('/{id}/edit', [MenuController::class, 'edit'])->name('menus.edit');
            Route::put('/{id}', [MenuController::class, 'update'])->name('menus.update');
            Route::delete('/{id}', [MenuController::class, 'destroy'])->name('menus.delete');
        });

        Route::group(['prefix' => '/user-menus'], function () {
            Route::get('/', [UserMenuController::class, 'index'])->name('user-menus.index');
            Route::get('/data', [UserMenuController::class, 'data'])->name('user-menus.data');
            Route::post('/store', [UserMenuController::class, 'store'])->name('user-menus.store');
            Route::get('/{id}/edit', [UserMenuController::class, 'edit'])->name('user-menus.edit');
            Route::get('/{id}/show', [UserMenuController::class, 'show'])->name('user-menus.show');
            Route::delete('/{id}', [UserMenuController::class, 'destroy'])->name('user-menus.delete');
        });
        Route::get('user-menus/create/{id}', [UserMenuController::class, 'create'])->name('user-menus.create');


        Route::group(['prefix' => '/users'], function () {
            Route::get('/', [UsersController::class, 'index'])->name('users.index');
            Route::get('/data', [UsersController::class, 'data'])->name('users.data');
            Route::get('/create', [UsersController::class, 'create'])->name('users.create');
            Route::post('/store', [UsersController::class, 'store'])->name('users.store');
            Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('users.edit');
            Route::put('/{id}', [UsersController::class, 'update'])->name('users.update');
            Route::delete('/{id}', [UsersController::class, 'destroy'])->name('users.delete');
        });

        Route::group(['prefix' => '/settings'], function () {
            Route::get('/', [SettingController::class, 'index'])->name('settings.index');
            Route::get('/data', [SettingController::class, 'data'])->name('settings.data');
            Route::get('/create', [SettingController::class, 'create'])->name('settings.create');
            Route::post('/store', [SettingController::class, 'store'])->name('settings.store');
            Route::get('/{id}/edit', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('/{id}', [SettingController::class, 'update'])->name('settings.update');
            Route::delete('/{id}', [SettingController::class, 'destroy'])->name('settings.delete');
        });
    });
});
