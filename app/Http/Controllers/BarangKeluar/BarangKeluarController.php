<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\DetailBarangKeluar;
use App\Services\ImageValidation;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    //
    private $file_path = 'upload/barang_keluar/bukti/';

    public function tambahPermohonan(Request $req)
    {
        // dd($req->forms);
        // foreach ($req->forms as $form)
        // {
        //     $barang = Barang::find($form['id_brg']);
        //     dd($form['index']);
        // }

        $db = DB::transaction(function () use ($req)
        {
            foreach($req->forms as $form)
            {
                $barang = Barang::find($form['id_brg']);
        
                $req->validate([
                    'forms.*.id_brg' => 'required',
                    'forms.'.$form['nomor'].'.jum_bk' => 'required|numeric|max:' . $barang->stok_brg,
                ], [
                    'forms.*.required' => 'Kolom wajib diisi',
                    'forms.'.$form['nomor'].'.jum_bk.max' => 'Melebihi Stok! Stok Tersisa :max '
                ]);
        
                $insert = BarangKeluar::create([
                    'tgl_bk' => Carbon::now('Asia/Jayapura')->format('Y-m-d'),
                    'id_user' => auth()->guard()->user()->id_user,
                    'bukti_bk' => NULL,
                    'status_bk' => 'diproses '.$form['nomor'],
                    'created_at' => Carbon::now('Asia/Jayapura')
                ]);
    
        
                sleep(1); // delay selama 1 detik lalu melanjutkan ke insertDetails
        
                $insertDetail = DetailBarangKeluar::create([
                    'id_bk' => $insert->latest()->first()->id_bk,
                    'id_brg' => $form['id_brg'],
                    'jum_bk' => $form['jum_bk'],
                    'jum_setuju_bk' => NULL,
                    'ket_bk' => 'data ke-' . $form['nomor'],
                    'created_at' => Carbon::now('Asia/Jayapura')
                ]);
            }

            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil tambah permohonan barang keluar',
            ]);
        });

        if ($db) {
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
    }

    public function updatePermohonan(){}
    public function hapusPermohonan(){}
    public function validasiPermohonan(Request $req)
    {
        $barang = Barang::find($req->id_brg);

        $req->validate([
            'status_bk' => 'required',
            'ket_bk' => 'required',
            'jum_bk' => 'required|numeric',
            'jum_setuju_bk' => 'required|numeric|max:' . $req->jum_bk,
        ], [
            'status_bk.required' => 'Kolom wajib diisi',
            'ket_bk.required' => 'Kolom wajib diisi',
            'jum_setuju_bk.required' => 'Kolom wajib diisi',
            'jum_setuju_bk.max' => 'Melebihi permohonan',
        ]);

        if ($req->status_bk === 'diterima') {
            $barang->update([
                'stok_brg' => $barang->stok_brg - $req->jum_setuju_bk,
            ]);
        }

        $insert = BarangKeluar::find($req->id_bk)->update([
            'status_bk' => $req->status_bk,
            'updated_at' => Carbon::now('Asia/Jayapura'),
        ]);

        $insertDetail = DetailBarangKeluar::find($req->id_dbk)->update([
            'jum_setuju_bk' => $req->jum_setuju_bk,
            'ket_bk' => $req->ket_bk,
            'updated_at' => Carbon::now('Asia/Jayapura'),
        ]);

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
        $data = $req->data;
        // Buat objek FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);

        $pdf->Cell(40, 10, 'Laporan Barang Keluar');
        $pdf->Ln();

        // Buat tabel
        $pdf->Cell(7, 10, 'No', 1, 0, 'L');
        $pdf->Cell(32, 10, 'Tanggal Permohonan', 1, 0, 'L');
        $pdf->Cell(25, 10, 'Nama User', 1, 0, 'L');
        $pdf->Cell(25, 10, 'Nama Unit', 1, 0, 'L');
        $pdf->Cell(25, 10, 'Nama Barang', 1, 0, 'L');
        $pdf->Cell(34, 10, 'Jumlah Barang Keluar', 1, 0, 'L');
        $pdf->Cell(25, 10, 'Jumlah Disetujui', 1, 0, 'L');
        $pdf->Cell(15, 10, 'Satuan', 1, 0, 'L');
        $pdf->Ln();

        foreach ($data as $row) {
            $pdf->Cell(7, 10, $row['index'], 1);
            $pdf->Cell(32, 10, $row['tgl_bk'], 1);
            $pdf->Cell(25, 10, $row['user']['username'], 1);
            $pdf->Cell(25, 10, $row['user']['unit']['nama_unit'], 1);
            $pdf->Cell(25, 10, $row['details']['barang']['nama_brg'], 1);
            $pdf->Cell(34, 10, $row['details']['jum_bk'], 1);
            $pdf->Cell(25, 10, $row['details']['jum_setuju_bk'], 1);
            $pdf->Cell(15, 10, $row['details']['barang']['satuan'], 1);
            $pdf->Ln();
        }
        $pdf->Cell(40, 10, 'Laporan Dicetak Pada Tanggal ' . Carbon::now('Asia/Jayapura')->format('d-m-Y'));
        $pdf->Output('D', 'laporan-barang-keluar-export.pdf');
        exit;
    }
}
