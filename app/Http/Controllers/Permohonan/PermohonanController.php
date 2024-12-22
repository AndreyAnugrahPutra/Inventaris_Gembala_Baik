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
        $barang = Barang::where('id_brg',$req->id_brg)->get('stok_brg');

        $imageValidation = new ImageValidation();

        $req->validate([
            'tgl_permo' => 'required',
            'bukti_permo' => 'required',
            'id_brg' => 'required',
            'jumlah_per' => 'required|numeric|max:'.$barang[0]['stok_brg'],
        ],[
            '*.required' => 'Kolom wajib diisi',
            'jumlah_per.max' => 'Melebihi Stok! Stok Tersisa : '.$barang[0]['stok_brg']
        ]);

        $linkFile = $imageValidation->validateImage($req,'bukti_permo',$this->file_path);

        $insert = Permohonan::create([
            'tgl_permo' => Carbon::parse($req->tgl_permo)->timezone('Asia/Jayapura')->format('Y-m-d'),
            'bukti_permo' => $linkFile,
            'status' => 'diproses',
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);      

        sleep(1); // delay selama 1 detik lalu melanjutkan ke insertDetails

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

    public function updatePermohonan(Request $req)
    {
        $permo = Permohonan::find($req->id_permo);
        $details = DetailPermohonan::find($req->id_dp);

        $linkFile = $req->bukti_permo;

        $imageValidation = new ImageValidation();

        if($req->hasFile('bukti_permo') && $permo->bukti_permo !== $req->bukti_permo)
        {
            // hapus bukti lama di storage
            Storage::disk('public')->delete($this->file_path . basename($permo->bukti_permo));
            // validasi bukti baru
            $linkFile = $imageValidation->validateImage($req,'bukti_permo',$this->file_path);
        }

        $insert = $permo->update(['tgl_permo' => Carbon::parse($req->tgl_permo)->timezone('Asia/Jayapura')->format('Y-m-d'),
            'bukti_permo' => $linkFile,
            'status' => $req->status??NULL,
            'updated_at' => Carbon::now('Asia/Jayapura')
        ]);

        $insertDetail = $details->update([
            'id_brg' => $req->id_brg,
            'jumlah_per' => $req->jumlah_per,
            'jumlah_setuju' => $req->jumlah_setuju??NULL,
            'ket_permo' => $req->ket_permo??NULL,
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
        $barang = Barang::find($req->id_brg);

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

        if($req->status === 'diterima')
        {
            $barang->update([
                'stok_brg' => $barang->stok_brg - $req->jumlah_setuju,
            ]);
        } 

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
        $data = $req->data;
        // Buat objek FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);

        $pdf->Cell(40, 10, 'Laporan Permohonan Barang Masuk');
        $pdf->Ln();

        // Buat tabel
        $pdf->Cell(7, 10, 'No', 1, 0, 'L');
        $pdf->Cell(32, 10, 'Tanggal Permohonan', 1, 0, 'L');
        $pdf->Cell(25, 10, 'Nama Barang', 1, 0, 'L');
        $pdf->Cell(30, 10, 'Jumlah Permohonan', 1, 0, 'L');
        $pdf->Cell(22, 10, 'Jumlah Setuju', 1, 0, 'L');
        $pdf->Cell(15, 10, 'Satuan', 1, 0, 'L');
        $pdf->Ln();

        foreach ($data as $row) {
            $pdf->Cell(7, 10, $row['index'], 1);
            $pdf->Cell(32, 10, $row['tgl_permo'], 1);
            $pdf->Cell(25, 10, $row['details']['barang']['nama_brg'], 1);
            $pdf->Cell(30, 10, $row['details']['jumlah_per'], 1);
            $pdf->Cell(22, 10, $row['details']['jumlah_setuju'], 1);
            $pdf->Cell(15, 10, $row['details']['barang']['satuan'], 1);
            $pdf->Ln();
        }
        $pdf->Cell(40, 10, 'Laporan Dicetak Pada Tanggal '.Carbon::now('Asia/Jayapura')->format('d-m-Y'));
        $pdf->Output('D','laporan-permohonan-export.pdf');
        exit;
    }
}
