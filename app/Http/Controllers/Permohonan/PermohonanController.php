<?php

namespace App\Http\Controllers\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DetailPermohonan;
use App\Models\Permohonan;
use App\Services\ImageValidation;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        DB::transaction(function () use($req)
        {
            foreach ($req->forms as $form) {
                $req->validate([
                    'forms.*.id_brg' => 'required',
                    'forms.*.jumlah_per' => 'required|numeric',
                ],[
                    'forms.*.*.required' => 'Kolom wajib diisi',
                    'forms.*.*.numeric' => 'Kolom wajib berupa angka',
                ]);
        
                $insert = Permohonan::create([
                    'tgl_permo' => Carbon::now('Asia/Jayapura')->format('Y-m-d'),
                    'status' => 'diproses',
                    'created_at' => Carbon::now('Asia/Jayapura')
                ]);
        
                sleep(1); // delay selama 1 detik lalu melanjutkan ke insertDetails
        
                $insertDetail = DetailPermohonan::create([
                    'id_permo' => $insert->latest()->first()->id_permo,
                    'id_brg' => $form['id_brg'], 
                    'jumlah_per' => $form['jumlah_per'],
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

        });

    }

    public function updatePermohonan(Request $req)
    {
        $permo = Permohonan::find($req->id_permo);
        $details = DetailPermohonan::find($req->id_dp);

        if($req->status === 'diproses')
        {
            $req->validate([
                'id_brg' => 'required',
                'jumlah_per' => 'required|numeric',
            ], [
                '*.required' => 'Kolom wajib diisi',
                '*.numeric' => 'Kolom wajib berupa angka',
            ]);

            $insert = $permo->update([
                'status' => $req->status,
                'updated_at' => Carbon::now('Asia/Jayapura')
            ]);

            $insertDetail = $details->update([
                'id_brg' => $req->id_brg,
                'jumlah_per' => $req->jumlah_per,
                'updated_at' => Carbon::now('Asia/Jayapura')
            ]);

            if ($insert && $insertDetail) {
                return redirect()->back()->with([
                    'notif_status' => 'success',
                    'notif_message' => 'Berhasil update permohonan',
                ]);
            } else {
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'notif_message' => 'Gagal update permohonan',
                ]);
            }
        }
        else
        {
            $barang = Barang::find($req->id_brg);
            // validasi bukti 
            $imageValidation = new ImageValidation();
            
            $req->validate([
                'bukti_permo' => 'required',
            ], [
                '*.required' => 'Kolom wajib diisi',
            ]);
            
            $linkFile = $imageValidation->validateImage($req, 'bukti_permo', $this->file_path);

            $insert = $permo->update([
                'bukti_permo' => $linkFile,
                'status' => 'diterima',
                'updated_at' => Carbon::now('Asia/Jayapura')
            ]);
    
            $insertDetail = $details->update([
                'updated_at' => Carbon::now('Asia/Jayapura')
            ]);

            $updateStok = $barang->update([
                    'stok_brg' => $barang->stok_brg += $req->jumlah_setuju,
            ]); 
    
            if ($insert && $insertDetail && $updateStok) {
                return redirect()->back()->with([
                    'notif_status' => 'success',
                    'notif_message' => 'Berhasil update permohonan',
                ]);
            } else {
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'notif_message' => 'Gagal update permohonan',
                ]);
            }
        }

    }
    public function hapusPermohonan(Request $req)
    {
        
        $insert = Permohonan::find($req->id_permo)->delete();
        $insertDetail = DetailPermohonan::find($req->id_dp)->delete();

        Storage::disk('public')->delete($this->file_path . basename($req->bukti_permo));

        if ($insert && $insertDetail) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil hapus permohonan',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal hapus permohonan',
            ]);
        }
    }

    public function terimaPermohonan(Request $req) 
    {
        // $barang = Barang::find($req->id_brg);

        $req->validate([
            'status' => 'required',
            'ket_permo' => 'required',
            'jumlah_per' => 'required|numeric',
            'jumlah_setuju' => 'required|numeric|max:'.$req->jumlah_per,
        ],[
            'status.required' => 'Kolom wajib diisi',
            'ket_permo.required' => 'Kolom wajib diisi',
            'jumlah_setuju.required' => 'Kolom wajib diisi',
            'jumlah_setuju.max' => 'Melebihi permohonan',
        ]);

        // if($req->status === 'disetujui')
        // {
        //     $barang->update([
        //         'stok_brg' => $barang->stok_brg += $req->jumlah_setuju,
        //     ]);
        // } 

        $insert = Permohonan::find($req->id_permo)->update([
            'status' => $req->status,
            'updated_at' => Carbon::now('Asia/Jayapura'),
        ]);

        $insertDetail = DetailPermohonan::find($req->id_dp)->update([
            'jumlah_setuju' => $req->jumlah_setuju,
            'ket_permo' => $req->ket_permo,
            'updated_at' => Carbon::now('Asia/Jayapura'),
        ]);

        if ($insert && $insertDetail) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil validasi permohonan',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal validasi permohonan',
            ]);
        }
    }

    public function permohonanPDF(Request $req)
    {
        return Inertia::render(
            'Laporan/PdfPermohonan',
            [
                'data' => $req,
                'tanggal' => Carbon::now('Asia/Jayapura')->format('d-m-Y')
            ]
        );
    }
}
