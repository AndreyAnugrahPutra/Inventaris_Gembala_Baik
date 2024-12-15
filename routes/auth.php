<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// use controller
use App\Http\Controllers\Auth\Authentication;
use App\Models\Role;
// use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::post('/', [Authentication::class, 'submitLogin'])->name('login.submit');
});

Route::middleware('auth')->group(function () {

    Route::get('/barang', function(){
        return redirect()->route('dashboard');
    })->name('barang');

    Route::post('/users/tambah', [UserController::class, 'tambahUser'])->name('users.tambah');

    Route::post('/users/update', [UserController::class, 'updateUser'])->name('users.update');
    Route::post('/users/hapus', [UserController::class, 'hapusUser'])->name('users.hapus');

    Route::get('logout', [Authentication::class, 'logout']);
    Route::post('logout', [Authentication::class, 'logout'])
        ->name('logout');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', function () {
        $usersCount = User::count();
        return Inertia::render('Admin/Dashboard', [
            'usersCount' => $usersCount
        ]);
    })->name('admin.dashboard');

    Route::get('admin/users', function(){
        $roleUsers = Role::select('id_role','nama_role')->get();
        $dataUsers = User::select('id_user','username','email','id_role','id_unit')->get();
        return Inertia::render('Admin/Users', [
            'dataUsers' => $dataUsers,
            'roleUsers' => $roleUsers,
        ]);
    })->name('admin.users.page');

});
