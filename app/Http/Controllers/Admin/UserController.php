<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function tambahUser(Request $req)
    {
        $req->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6',
            'id_role' => 'required',
            'id_unit' => 'required',
        ], [
            '*.required' => 'Kolom wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'username.unique' => $req->username.' telah terdaftar',
            'nama.unique' => $req->nama. ' telah terdaftar',
            'email.unique' => $req->email.' telah terdaftar',
        ]);

        $insert = User::create([
            'username' => $req->username,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'id_role' => $req->id_role,
            'id_unit' => $req->id_unit,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if($insert)
        {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan ' .$req->username,
            ]);
        }
        else
        {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal menambahkan data',
            ]);
        }
    }

    public function updateUser(Request $req)
    {
        $user = User::find($req->id_user);

        if ($req->password !== null) {
            $req->validate([
                'password' => 'min:8'
            ], [
                'password.min' => 'Password minimal 6 karakter',
            ]);

            $user->password = Hash::make($req->password);
        }

        $data = $req->validate([
            'username' => 'required|unique:users,username,'.$req->id_user.',id_user',
            'email' => 'required|unique:users,email,'.$req->id_user.',id_user',
            'id_role' => 'required',
            'id_unit' => 'required',
        ], [
            '*.required' => 'Kolom wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'username.unique' => $req->username . ' telah terdaftar',
            'email.unique' => 'email telah terdaftar',
        ]);  
        
        $insert = $user->update($data);

        if($insert)
        {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update data ' .$req->username,
            ]);
        }
        else
        {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal menambahkan data',
            ]);
        }
    }

    public function hapusUser(Request $req)
    {
        $user = User::find($req->id)->delete();

        if ($user) {
            return back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menghapus pengguna!',
            ]);
        } else {
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal menghapus pengguna',
            ]);
        }
    }
}