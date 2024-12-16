<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// use controller
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\Kategori\KategoriController;
use App\Http\Controllers\Unit\UnitController;
use App\Models\Role;
use App\Models\Unit;
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
        $unitUsers = Unit::select('id_unit','nama_unit')->get();
        $roleUsers = Role::select('id_role','nama_role')->get();
        $dataUsers = User::with('role','unit')->get();
        return Inertia::render('Admin/Users', [
            'unitUsers' => $unitUsers,
            'dataUsers' => $dataUsers,
            'roleUsers' => $roleUsers,
        ]);
    })->name('admin.users.page');

    Route::post('admin/users/tambah', [UserController::class, 'tambahUser'])->name('admin.users.tambah');
    Route::post('admin/users/update', [UserController::class, 'updateUser'])->name('admin.users.update');
    Route::post('admin/users/hapus', [UserController::class, 'hapusUser'])->name('admin.users.hapus');

    Route::get('admin/kategori',[KategoriController::class, 'kategoriPage'])->name('admin.kategori.page');
    Route::post('admin/kategori/tambah',[KategoriController::class, 'tambahKategori'])->name('admin.kategori.tambah');
    Route::post('admin/kategori/update',[KategoriController::class, 'updateKategori'])->name('admin.kategori.update');
    Route::post('admin/kategori/hapus',[KategoriController::class, 'hapusKategori'])->name('admin.kategori.hapus');

    Route::get('admin/unit',[UnitController::class, 'unitPage'])->name('admin.unit.page');
    Route::post('admin/unit/tambah',[UnitController::class, 'tambahunit'])->name('admin.unit.tambah');
    Route::post('admin/unit/update',[UnitController::class, 'updateunit'])->name('admin.unit.update');
    Route::post('admin/unit/hapus',[UnitController::class, 'hapusunit'])->name('admin.unit.hapus');

});
