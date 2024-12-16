<?php

namespace App\Http\Controllers\Kategori;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KategoriController extends Controller
{
    //
    public function kategoriPage()
    {
        $dataKategori = Kategori::withCount('barang');

        return Inertia::render('Admin/Kategori/Index', [
            'dataKategori' => $dataKategori
        ]);
    }
}
