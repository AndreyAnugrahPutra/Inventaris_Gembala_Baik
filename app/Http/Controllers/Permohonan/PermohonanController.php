<?php

namespace App\Http\Controllers\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DetailPermohonan;
use App\Models\Permohonan;
use App\Services\ImageValidation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermohonanController extends Controller
{
    //
    private $file_path = 'upload/permohonan/bukti/';

    public function permohonanPage()
    {
        $dataPermo = Permohonan::with('details','details.barang')->get();
        $dataBarang = Barang::get(['id_brg', 'nama_brg']);
        return Inertia::render('Admin/Permohonan/Index', [
            'dataPermo' => $dataPermo,
            'dataBarang' => $dataBarang,
        ]);
    }

    public function tambahPermohonan(Request $req)
    {

        $imageValidation = new ImageValidation();

        $req->validate([
            '*' => 'required'
            // 'tgl_permo' =>'required',
            // 'id_brg' => 'required',
            // 'jumlah_per' => 'required',
        ],[
            '*.required' => 'Kolom wajib diisi'
        ]);

        $linkFile = $imageValidation->validateImage($req,'bukti_permo',$this->file_path);

        $insert = Permohonan::create([
            'tgl_permo' => Carbon::parse($req->tgl_permo)->timezone('Asia/Jayapura')->format('Y-m-d'),
            'bukti_permo' => $linkFile,
            'status' => 'diproses',
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);      

        sleep(1);

        $insertDetail = DetailPermohonan::create([
            'id_permo' => $insert->latest()->first()->id_permo,
            'id_brg' => $req->id_brg, 
            'jumlah_per' => $req->jumlah_per, 
            'jumlah_setuju' => NULL,
            'ket_permo' => NULL,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if ($insert && $insertDetail) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil tambah permohonan',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal tambah permohonan',
            ]);
        }


    }
    public function validasiPermohonan(){}
    public function updatePermohonan(){}
    public function hapusPermohonan(){}
}
