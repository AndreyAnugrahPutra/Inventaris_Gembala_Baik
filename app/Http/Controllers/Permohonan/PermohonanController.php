<?php

namespace App\Http\Controllers\Permohonan;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermohonanController extends Controller
{
    //
    public function permohonanPage()
    {
        $dataPermo = Permohonan::with('details')->get();

        return Inertia::render('Admin/Permohonan/Index', [
            'dataPermo' => $dataPermo,
        ]);
    }
    public function tambahPermohonan(){}
    public function validasiPermohonan(){}
    public function updatePermohonan(){}
    public function hapusPermohonan(){}
}
