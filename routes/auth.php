<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// use controller
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\Barang\BarangController;
use App\Http\Controllers\BarangKeluar\BarangKeluarController;
use App\Http\Controllers\Kategori\KategoriController;
use App\Http\Controllers\Permohonan\PermohonanController;
use App\Http\Controllers\Unit\UnitController;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\DetailBarangKeluar;
use App\Models\DetailPermohonan;
use App\Models\Kategori;
use App\Models\Permohonan;
use App\Models\Role;
use App\Models\Unit;
// use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::post('/', [Authentication::class, 'submitLogin'])->name('login.submit');
});

Route::middleware('auth')->group(function () {

    Route::post('laporan/permohonan/pdf', [PermohonanController::class, 'permohonanPDF'])->name('laporan.permohonan.pdf');
    Route::post('laporan/barang/pdf', [BarangController::class, 'barangPDF'])->name('laporan.barang.pdf');
    Route::post('laporan/barang-keluar/pdf', [BarangKeluarController::class, 'barangKeluarPDF'])->name('laporan.barang_keluar.pdf');

    
    Route::get('logout', [Authentication::class, 'logout']);
    Route::post('logout', [Authentication::class, 'logout'])
    ->name('logout');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', function () {
        $barangCount = Barang::count();
        $usersCount = User::count();
        return Inertia::render('Admin/Dashboard', [
            'barangCount' => $barangCount,
            'usersCount' => $usersCount,
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

    Route::get('admin/barang',[BarangController::class, 'barangPage'])->name('admin.barang.page');
    Route::post('admin/barang/tambah',[BarangController::class, 'tambahbarang'])->name('admin.barang.tambah');
    Route::post('admin/barang/update',[BarangController::class, 'updatebarang'])->name('admin.barang.update');
    Route::post('admin/barang/hapus',[BarangController::class, 'hapusbarang'])->name('admin.barang.hapus');

    Route::get('admin/barang-keluar', function(){
        $dataBarangKeluar = DetailBarangKeluar::with('barang','barangKeluar','barangKeluar.user:id_user,username,id_unit','barangKeluar.user.unit:id_unit,nama_unit')->get();
        return Inertia::render('Admin/BarangKeluar/Index',[
            'dataBarangKeluar' => $dataBarangKeluar,
        ]);
    })->name('admin.barang_keluar.page');

    Route::post('admin/barang_keluar/validasi', [BarangKeluarController::class, 'validasiPermohonan'])->name('admin.barang_keluar.validasi');


    Route::get('admin/kategori',[KategoriController::class, 'kategoriPage'])->name('admin.kategori.page');
    Route::post('admin/kategori/tambah',[KategoriController::class, 'tambahKategori'])->name('admin.kategori.tambah');
    Route::post('admin/kategori/update',[KategoriController::class, 'updateKategori'])->name('admin.kategori.update');
    Route::post('admin/kategori/hapus',[KategoriController::class, 'hapusKategori'])->name('admin.kategori.hapus');

    Route::get('admin/unit',[UnitController::class, 'unitPage'])->name('admin.unit.page');
    Route::post('admin/unit/tambah',[UnitController::class, 'tambahunit'])->name('admin.unit.tambah');
    Route::post('admin/unit/update',[UnitController::class, 'updateunit'])->name('admin.unit.update');
    Route::post('admin/unit/hapus',[UnitController::class, 'hapusunit'])->name('admin.unit.hapus');

    Route::get('admin/permohonan',[PermohonanController::class, 'permohonanPage'])->name('admin.permohonan.page');
    Route::post('admin/permohonan/tambah',[PermohonanController::class, 'tambahPermohonan'])->name('admin.permohonan.tambah');
    Route::post('admin/permohonan/update',[PermohonanController::class, 'updatePermohonan'])->name('admin.permohonan.update');
    Route::post('admin/permohonan/hapus',[PermohonanController::class, 'hapusPermohonan'])->name('admin.permohonan.hapus');
    
    Route::get('admin/laporan/barang',function (){
        $dataBarang = Barang::with('kategori')->get();
        $dataKategori = Kategori::select('id_ktg', 'nama_kategori')->get();

        return Inertia::render('Laporan/Barang',[
            'dataBarang' => $dataBarang,
            'dataKategori' => $dataKategori,
        ]);
    })->name('admin.laporan.barang');
    
    Route::get('admin/laporan/barang_keluar',function (){
        $dataBarangKeluar = DetailBarangKeluar::with('barangKeluar', 'barang', 'barangKeluar.user', 'barangKeluar.user.unit')->get();
        
        return Inertia::render('Laporan/BarangKeluar',[
            'dataBarangKeluar' => $dataBarangKeluar,
        ]);
    })->name('admin.laporan.barang_keluar');

    Route::get('admin/laporan/permohonan',function (){
        $dataPermo = DetailPermohonan::with('permohonan','barang','barang.kategori')->get();
        $dataBarang = Barang::with('kategori')->get();
        return Inertia::render('Laporan/Permohonan', [
            'dataPermo' => $dataPermo,
            'dataBarang' => $dataBarang,
        ]);
    })->name('admin.laporan.permohonan');

});

Route::middleware(['auth', 'kepsek'])->group(function () {

    Route::get('kepsek/dashboard', function () {
        $permohonanCount = Permohonan::count();
        return Inertia::render('Bendahara/Dashboard', [
            'permohonanCount' => $permohonanCount,
        ]);
    })->name('kepsek.dashboard');

    Route::get('kepsek/laporan/barang', function () {
        $dataBarang = Barang::with('kategori')->get();
        $dataKategori = Kategori::select('id_ktg', 'nama_kategori')->get();

        return Inertia::render('Laporan/Barang', [
            'dataBarang' => $dataBarang,
            'dataKategori' => $dataKategori,
        ]);
    })->name('kepsek.laporan.barang');

    Route::get('kepsek/laporan/barang_keluar', function () {
        $dataBarangKeluar = DetailBarangKeluar::with('barangKeluar', 'barang', 'barangKeluar.user', 'barangKeluar.user.unit')->get();

        return Inertia::render('Laporan/BarangKeluar', [
            'dataBarangKeluar' => $dataBarangKeluar,
        ]);
    })->name('kepsek.laporan.barang_keluar');

    Route::get('kepsek/laporan/permohonan', function () {
        $dataPermo = DetailPermohonan::with('permohonan', 'barang', 'barang.kategori')->get();
        $dataBarang = Barang::with('kategori')->get();
        return Inertia::render('Laporan/Permohonan', [
            'dataPermo' => $dataPermo,
            'dataBarang' => $dataBarang,
        ]);
    })->name('kepsek.laporan.permohonan');

});

Route::middleware(['auth', 'bendahara'])->group(function () {

    Route::get('/bendahara/dashboard', function(){
        $permohonanCount = Permohonan::count();
        return Inertia::render('Bendahara/Dashboard', [
            'permohonanCount' => $permohonanCount,
        ]);
    })->name('bendahara.dashboard');

    Route::get('bendahara/validasi-permohonan',function(){
        $dataPermohonan = DetailPermohonan::with('permohonan','barang')->get();
        return Inertia::render('Bendahara/Permohonan/Index',[
            'dataPermo' => $dataPermohonan
        ]);
    })->name('bendahara.permohonan.page');

    Route::post('bendara/validasi-permohonan/terima', [PermohonanController::class, 'terimaPermohonan'])->name('bendahara.permohonan.terima');

    Route::get('bendahara/laporan/barang', function () {
        $dataBarang = Barang::with('kategori')->get();
        $dataKategori = Kategori::select('id_ktg', 'nama_kategori')->get();

        return Inertia::render('Laporan/Barang', [
            'dataBarang' => $dataBarang,
            'dataKategori' => $dataKategori,
        ]);
    })->name('bendahara.laporan.barang');

    Route::get('bendahara/laporan/barang_keluar', function () {
        $dataBarangKeluar = DetailBarangKeluar::with('barangKeluar', 'barang', 'barangKeluar.user', 'barangKeluar.user.unit')->get();

        return Inertia::render('Laporan/BarangKeluar', [
            'dataBarangKeluar' => $dataBarangKeluar,
        ]);
    })->name('bendahara.laporan.barang_keluar');

    Route::get('bendahara/laporan/permohonan', function () {
        $dataPermo = DetailPermohonan::with('permohonan', 'barang', 'barang.kategori')->get();
        $dataBarang = Barang::with('kategori')->get();
        return Inertia::render('Laporan/Permohonan', [
            'dataPermo' => $dataPermo,
            'dataBarang' => $dataBarang,
        ]);
    })->name('bendahara.laporan.permohonan');

});

Route::middleware(['auth', 'guru'])->group(function () {

    Route::get('/guru/dashboard', function(){
        $barangKeluarCount = BarangKeluar::where('id_user', auth()->guard()->user()->id_user)->count();
        return Inertia::render('Guru/Dashboard', [
            'barangKeluarCount' => $barangKeluarCount,
        ]);
    })->name('guru.dashboard');

    Route::get('/guru/profile', function () {
        $dataProfile = User::with('unit')->withCount('barangKeluar')->find(auth()->guard()->user()->id_user);
        return Inertia::render('Guru/Profile', [
            'dataProfile' => $dataProfile,
        ]);
    })->name('guru.profile');

    Route::get('/guru/permohonan', function(){
        $dataPermo = DetailBarangKeluar::with('barangKeluar','barang')->get();
        $dataBarang = Barang::get(['id_brg', 'nama_brg']);
        return Inertia::render('Guru/Permohonan/Index', [
            'dataPermo' => $dataPermo,
            'dataBarang' => $dataBarang,
        ]);
    })->name('guru.permohonan.page');

    Route::post('guru/permohonan/tambah',[BarangKeluarController::class, 'tambahPermohonan'])->name('guru.permohonan.tambah');
    Route::post('guru/permohonan/update',[BarangKeluarController::class, 'updatePermohonan'])->name('guru.permohonan.update');
    Route::post('guru/permohonan/hapus',[BarangKeluarController::class, 'hapusPermohonan'])->name('guru.permohonan.hapus');
});
