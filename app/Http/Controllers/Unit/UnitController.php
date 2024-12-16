<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitController extends Controller
{
    //
    public function unitPage()
    {
        $dataUnit = Unit::withCount('users')->get();

        return Inertia::render('Admin/Unit/Index', [
            'dataUnit' => $dataUnit,
        ]);
    }

    public function tambahUnit(Request $req){
        $req->validate([
            'nama_unit' => 'required|unique:unit,nama_unit',
        ],[
            'nama_unit.required' => 'kolom wajib diisi',
            'nama_unit.unique' => $req->nama_unit.' telah terdaftar',
        ]);

        $insert = Unit::create([
            'nama_unit' => $req->nama_unit,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if($insert)
        {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil tambah unit '.$req->nama_unit,
            ]);
        }
        else
        {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal tambah unit '.$req->nama_unit,
            ]);
        }
    }

    public function updateUnit(Request $req)
    {
        $req->validate([
            'nama_unit' => 'required|unique:unit,nama_unit,'.$req->id_unit.',id_unit',
        ], [
            'nama_unit.required' => 'kolom wajib diisi',
            'nama_unit.unique' => $req->nama_unit . ' telah terdaftar',
        ]);

        $insert = Unit::find($req->id_unit)->update([
            'nama_unit' => $req->nama_unit,
            'updated_at' => Carbon::now('Asia/Jayapura')
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update unit '. $req->nama_unit,
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal update unit '. $req->nama_unit,
            ]);
        }
    }
    public function hapusUnit(Request $req)
    {
        $insert = Unit::find($req->id_unit)->delete();

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil hapus unit '.$req->nama_unit,
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal hapus unit '.$req->nama_unit,
            ]);
        }

    }
}
