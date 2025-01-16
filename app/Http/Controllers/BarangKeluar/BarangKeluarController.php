<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\DetailBarangKeluar;
use App\Models\DetailPermohonan;
use App\Models\Permohonan;
use App\Services\ImageValidation;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BarangKeluarController extends Controller
{
    //
    private $file_path = 'upload/barang_keluar/bukti/';

    public function tambahPermohonan(Request $req)
    {
        $db = DB::transaction(function () use ($req)
        {
            $req->validate([
                'tgl_bk' => 'required',
            ],
            [
                'tgl_bk.required' => 'Tanggal wajib diisi',
            ]);
            
            $insert = BarangKeluar::create([
                'tgl_bk' => Carbon::parse($req->tgl_bk)->timezone('Asia/Jayapura')->format('Y-m-d'),
                'id_user' => auth()->guard()->user()->id_user,
                'bukti_bk' => NULL,
                'status_bk' => 'diproses',
                'created_at' => Carbon::now('Asia/Jayapura')
            ]);

            foreach($req->forms as $form)
            {
                $barang = Barang::find($form['id_brg']);
                $stok_brg = $barang->stok_brg ?? 0;

                $req->validate([
                    'forms.*.id_brg' => 'required',
                    'forms.*.jum_bk' => 'required|numeric|max:' . $stok_brg,
                ], [
                    'forms.*.*.required' => 'Kolom wajib diisi',
                    'forms.*.jum_bk.max' => 'Melebihi Stok! Stok Tersisa :max '
                ]);

                $detailPermo = DetailPermohonan::where('id_brg', $form['id_brg'])->first();
                $cekBarang = Permohonan::where('id_permo', $detailPermo->id_permo)->where('status','diterima')->first();


                if(!$cekBarang)
                {
                    $insert->latest()->first()->delete();

                    return redirect()->back()->with([
                        'notif_status' => 'error',
                        'notif_message' => 'Barang belum masuk inventaris!',
                    ]);
                }
                else
                {
                    $insertDetail = DetailBarangKeluar::create([
                        'id_bk' => $insert->latest()->first()->id_bk,
                        'id_brg' => $form['id_brg'],
                        'jum_bk' => $form['jum_bk'],
                        'jum_setuju_bk' => NULL,
                        'ket_bk' => NULL,
                        'created_at' => Carbon::now('Asia/Jayapura')
                    ]);
                    sleep(1);
                }
            }

            if ($insert && $insertDetail) {
                return redirect()->back()->with([
                    'notif_status' => 'success',
                    'notif_message' => 'Berhasil tambah permohonan barang keluar',
                ]);
            } else {
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'notif_message' => 'Gagal tambah permohonan barang keluar',
                ]);
            }
        });
    }

    public function updatePermohonan(Request $req)
    {
        if($req->status_bk==='diproses')
        {
            foreach($req->forms[0] as $form)
            {
                $barang = Barang::find($form['id_brg']);
    
                $req->validate([
                    'forms.*.*.id_brg' => 'required',
                    'forms.*.*.jum_bk' => 'required|numeric|max:' . $barang->stok_brg,
                ], [
                    'forms.*.*.*.required' => 'Kolom wajib diisi',
                    'forms.*.*.jum_bk.max' => 'Melebihi Stok! Stok Tersisa :max '
                ]);
    
                $insert = BarangKeluar::find($req->id_bk)->update([
                    'updated_at' => Carbon::now('Asia/Jayapura')
                ]);

                $insertDetail = DetailBarangKeluar::find($form['id_dbk'])->update([
                    'id_brg' => $form['id_brg'],
                    'jum_bk' => $form['jum_bk'],
                    'updated_at' => Carbon::now('Asia/Jayapura')
                ]);
            }

            if ($insert && $insertDetail) {
                return redirect()->back()->with([
                    'notif_status' => 'success',
                    'notif_message' => 'Berhasil update permohonan barang keluar',
                ]);
            } else {
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'notif_message' => 'Gagal update permohonan barang keluar',
                ]);
            }
        }
        else 
        {
            $imageValidation = new ImageValidation();
            $linkFile = NULL;
    
            $req->validate([
                'bukti_bk' => 'required',
                'tgl_diterima' => 'required',
            ],[
                '*.required' => 'Kolom wajib diisi',
            ]);

            $linkFile = $imageValidation->validateImage($req, 'bukti_bk', $this->file_path);
    
            $insert = BarangKeluar::find($req->id_bk)->update([
                'tgl_diterima' => Carbon::parse($req->tgl_diterima)->timezone('Asia/Jayapura')->format('Y-m-d'),
                'bukti_bk' => $linkFile,
                'status_bk' => 'diterima',
                'updated_at' => Carbon::now('Asia/Jayapura')
            ]);

            foreach($req->forms[0] as $form)
            {
                $barang = Barang::find($form['id_brg']);

                $detailBK = DetailBarangKeluar::find($form['id_dbk']);

                $detailBK->update([
                    'updated_at' => Carbon::now('Asia/Jayapura'),
                ]);
    
                $updateStok = $barang->update([
                    'stok_brg' => $barang->stok_brg -= $req->jum_setuju_bk,
                    'updated_at' => Carbon::now('Asia/Jayapura')
                ]);
            }

            if ($insert && $updateStok) {
                return redirect()->back()->with([
                    'notif_status' => 'success',
                    'notif_message' => 'Berhasil update permohonan barang keluar',
                ]);
            } else {
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'notif_message' => 'Gagal update permohonan barang keluar',
                ]);
            }
        }
    }

    public function hapusPermohonan(Request $req)
    {
        $barang = BarangKeluar::find($req->id_bk)->delete();
        $detail = DetailBarangKeluar::where('id_bk',$req->id_bk)->delete();
        $storage = true;

        if($req->bukti_bk)
        {
            $storage =  Storage::disk('public')->delete($this->file_path . basename($req->bukti_bk));
        }


        if($barang && $detail && $storage) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil hapus permohonan barang keluar',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal hapus permohonan barang keluar',
            ]);
        }  
    }

    public function validasiPermohonan(Request $req)
    {
        $keterangan = $req->ket_bk;

        $req->validate([
            'status_bk' => 'required',
        ], [
            'status_bk.required' => 'Kolom wajib diisi',
        ]);

        if($req->status_bk==='ditolak')
        {
            $keterangan = $req->ket_bk??'ditolak';
        }

        foreach($req->forms[0] as $form)
        {
            $req->validate([
                'ket_bk' => 'required',
                'forms[0].*.jum_setuju_bk' => 'required|numeric|max:'.$form['jum_bk'],
            ], [
                'forms.*.*.jum_setuju_bk.required' => 'Kolom wajib diisi',
                'forms.*.*.jum_setuju_bk.max' => 'Melebihi permohonan',
            ]);
    
            $insert = BarangKeluar::find($req->id_bk)->update([
                'status_bk' => $req->status_bk,
                'updated_at' => Carbon::now('Asia/Jayapura'),
            ]);
    
            $insertDetail = DetailBarangKeluar::find($form['id_dbk'])->update([
                'jum_setuju_bk' => $form['jum_setuju_bk']??0,
                'ket_bk' => $keterangan,
                'updated_at' => Carbon::now('Asia/Jayapura'),
            ]);
        }

        if ($insert && $insertDetail) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil validasi permohonan barang keluar',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal validasi permohonan barang keluar',
            ]);
        }
    }

    public function barangKeluarPDF(Request $req)
    {
        return Inertia::render('Laporan/PdfBarangKeluar',[
            'data' => $req,
            'tanggal' => Carbon::now('Asia/Jayapura')->format('d-m-Y')
        ]);
    }
}
