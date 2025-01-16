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
        $dataPermo = DetailPermohonan::with('permohonan','barang')->get();
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
            $req->validate([
                'tgl_permo' => 'required',
            ],
            [
                'tgl_permo.required' => 'Tanggal wajib diisi'
            ]);
            foreach ($req->forms as $form) {
                $req->validate([
                    'forms.*.id_brg' => 'required',
                    'forms.*.jumlah_per' => 'required|numeric',
                ],[
                    'forms.*.*.required' => 'Kolom wajib diisi',
                    'forms.*.*.numeric' => 'Kolom wajib berupa angka',
                ]);
            }
        
            $insert = Permohonan::create([
                'tgl_permo' => Carbon::parse($req->tgl_permo)->timezone('Asia/Jayapura')->format('Y-m-d'),
                'status' => 'diproses',
                'created_at' => Carbon::now('Asia/Jayapura')
            ]);
            
            foreach ($req->forms as $form) {
                $insertDetail = DetailPermohonan::create([
                    'id_permo' => $insert->latest()->first()->id_permo,
                    'id_brg' => $form['id_brg'], 
                    'jumlah_per' => $form['jumlah_per'],
                    'created_at' => Carbon::now('Asia/Jayapura')
                ]);
                sleep(1);
            }
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

        });

    }

    public function updatePermohonan(Request $req)
    {
        // dd($req->forms[0]);
        if($req->status === 'diproses')
        {
            foreach($req->forms[0] as $form)
            {
                $permo = Permohonan::find($form->id_permo);
                $details = DetailPermohonan::find($form->id_dp);

                $req->validate([
                    'forms.*.id_brg' => 'required',
                    'forms.*.jumlah_per' => 'required|numeric',
                ], [
                    'forms.*.*.required' => 'Kolom wajib diisi',
                    'forms.*.*.numeric' => 'Kolom wajib berupa angka',
                ]);
    
                $insert = $permo->update([
                    'updated_at' => Carbon::now('Asia/Jayapura')
                ]);
    
                $insertDetail = $details->update([
                    'id_brg' => $form->id_brg,
                    'jumlah_per' => $form->jumlah_per,
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
        }
        else
        {
            // validasi bukti 
            $imageValidation = new ImageValidation();
            
            $req->validate([
                'bukti_permo' => 'required',
            ], [
                'bukti_permo.required' => 'Kolom wajib diisi',
            ]);
            
            $linkFile = $imageValidation->validateImage($req, 'bukti_permo', $this->file_path);

            $permo = Permohonan::find($req->id_permo);

            $insert = $permo->update([
                'bukti_permo' => $linkFile,
                'status' => 'diterima',
                'tgl_diterima' => Carbon::parse($req->tgl_diterima)->timezone('Asia/Jayapura')->format('Y-m-d'),
                'updated_at' => Carbon::now('Asia/Jayapura')
            ]);

            foreach ($req->forms[0] as $form)
            {
                $barang = Barang::find($form['id_brg']);

                $details = DetailPermohonan::find($form['id_dp']);

                $insertDetail = $details->update([
                    'updated_at' => Carbon::now('Asia/Jayapura')
                ]);
    
                $updateStok = $barang->update([
                    'stok_brg' => $barang->stok_brg += $form['jumlah_setuju'],
                ]); 
        
            }
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
        $insertDetail = DetailPermohonan::where('id_permo',$req->id_permo)->delete();

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
        // dd($req->forms[0]);
        $keterangan = $req->ket_permo;

        $req->validate([
            'status' => 'required',
        ], [
            'status.required' => 'Kolom wajib diisi',
        ]);

        foreach($req->forms[0] as $form)
        {
            if($req->status==='ditolak')
            {
                $keterangan = $req->ket_permo??'ditolak';
            }
            else
            {
                $req->validate([
                    'status' => 'required',
                    'ket_permo' => 'required',
                    'forms[0].*.jumlah_setuju' => 'required|numeric|max:'.$form['jumlah_per'],
                ],[
                    'status.required' => 'Kolom wajib diisi',
                    'ket_permo.required' => 'Kolom wajib diisi',
                    'forms.*.*.jumlah_setuju.required' => 'Kolom wajib diisi',
                    'forms.*.*.jumlah_setuju.max' => 'Melebihi permohonan',
                ]);
            }
    
            $insert = Permohonan::find($form['id_permo'])->update([
                'status' => $req->status,
                'updated_at' => Carbon::now('Asia/Jayapura'),
            ]);
    
            $insertDetail = DetailPermohonan::find($form['id_dp'])->update([
                'jumlah_setuju' => $form['jumlah_setuju']??0,
                'ket_permo' => $keterangan,
                'updated_at' => Carbon::now('Asia/Jayapura'),
            ]);
        }

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
