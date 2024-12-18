<?php

namespace App\Http\Controllers\Barang;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangController extends Controller
{
    //
    public function barangPage(){
        $dataKategori = Kategori::select('id_ktg','nama_kategori')->get();
        $dataBarang = Barang::with('kategori')->get();

        return Inertia::render('Admin/Barang/Index',[
            'dataBarang' => $dataBarang,
            'dataKategori' => $dataKategori,
        ]);
    }
    public function tambahBarang(Request $req){
        $req->validate([
            'id_ktg' => 'required',
            'nama_brg' => 'required|unique:barang,nama_brg',
            'stok_brg' => 'required|numeric',
            'satuan' => 'required|alpha',
        ],[
            '*.required' => 'Kolom wajib diisi',
            'nama_brg.unique' => $req->nama_brg.' sudah terdaftar',
            '*.numeric' => 'Kolom wajib berupa angka',
            '*.alpha' => 'Kolom wajib berupa huruf',
        ]);

        $insert = Barang::create([
            'id_ktg' => $req->id_ktg,
            'nama_brg' => $req->nama_brg,
            'stok_brg' => $req->stok_brg,
            'satuan' => $req->satuan,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if($insert)
        {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil tambah barang '.$req->nama_brg,
            ]);
        }
        else
        {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal tambah barang ' . $req->nama_brg,
            ]);
        }
    }
    public function updateBarang(Request $req){
        $req->validate([
            'id_ktg' => 'required',
            'nama_brg' => 'required|unique:barang,nama_brg,'.$req->id_brg.',id_brg',
            'stok_brg' => 'required|numeric',
            'satuan' => 'required|alpha',
        ], [
            '*.required' => 'Kolom wajib diisi',
            'nama_brg.unique' => $req->nama_brg . ' sudah terdaftar',
            '*.numeric' => 'Kolom wajib berupa angka',
            '*.alpha' => 'Kolom wajib berupa huruf',
        ]);

        $insert = Barang::find($req->id_brg)->update([
            'id_ktg' => $req->id_ktg,
            'nama_brg' => $req->nama_brg,
            'stok_brg' => $req->stok_brg,
            'satuan' => $req->satuan,
            'updated_at' => Carbon::now('Asia/Jayapura')
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update barang ' . $req->nama_brg,
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal update barang ' . $req->nama_brg,
            ]);
        }
    }
    public function hapusBarang(Request $req)
    {
        $insert = Barang::find($req->id_brg)->delete();

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil hapus barang ' . $req->nama_brg,
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal hapus barang ' . $req->nama_brg,
            ]);
        }
    }
}
