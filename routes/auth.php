<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// use controller
use App\Http\Controllers\Auth\Authentication;
// use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    // Route::get('login', [AuthenticatedSessionController::class, 'create'])
    //     ->name('login');
    Route::post('/', [Authentication::class, 'submitLogin'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function(){
        $usersCount = User::count();
        return Inertia::render('Admin/Dashboard', [
            'usersCount' => $usersCount
        ]);
    })->name('dashboard');

    Route::get('/barang', function(){
        return redirect()->route('dashboard');
    })->name('barang');

    Route::get('/users', function(){
        $roleUsers = User::distinct()->pluck('role');
        $jkelUsers = User::distinct()->pluck('jkel');
        $dataUsers = User::select('id','username','nama','email','role','is_login','jkel')->get();
        return Inertia::render('Admin/Users', [
            'dataUsers' => $dataUsers,
            'roleUsers' => $roleUsers,
            'jkelUsers' => $jkelUsers,
        ]);
    })->name('users.page');

    Route::post('/users/tambah', [UserController::class, 'tambahUser'])->name('users.tambah');

    Route::post('/users/update', [UserController::class, 'updateUser'])->name('users.update');
    Route::post('/users/hapus', [UserController::class, 'hapusUser'])->name('users.hapus');

    Route::get('logout', [Authentication::class, 'logout']);
    Route::post('logout', [Authentication::class, 'logout'])
        ->name('logout');
});
