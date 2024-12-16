<?php

namespace App\Http\Controllers\Kategori;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KategoriController extends Controller
{
    //
    public function kategoriPage()
    {
        $dataKategori = Kategori::withCount('barang')->get();

        return Inertia::render('Admin/Kategori/Index', [
            'dataKategori' => $dataKategori
        ]);
    }

    public function tambahKategori(Request $req)
    {
        $req->validate([
            'nama' => 'required|unique:kategori,nama_kategori',
        ], [
            '*.required' => 'Kolom Wajib Diisi',
            'nama.unique' => $req->nama . ' telah terdaftar'
        ]);

        $insert = Kategori::create([
            'nama_kategori' => $req->nama,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil tambah kategori ' . $req->nama,
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal tambah kategori '.$req->nama,
            ]);
        }
    }

    public function updateKategori(Request $req)
    {
        $req->validate([
            'nama' => 'required|unique:kategori,nama_kategori,'.$req->id_ktg.',id_ktg',
        ],[
            '*.required' => 'Kolom Wajib Diisi',
            'nama.unique' => $req->nama.' telah terdaftar'
        ]);

        $insert = Kategori::find($req->id_ktg)->update([
            'nama_kategori' => $req->nama,
            'updated_at' => Carbon::now('Asia/Jayapura')
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update kategori '.$req->nama,
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal update kategori',
            ]);
        }
    }
    public function hapusKategori(Request $req)
    {
        $insert = Kategori::find($req->id_ktg)->delete();

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil hapus kategori '.$req->nama,
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal hapus kategori',
            ]);
        }
    }
}
