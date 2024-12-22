<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Authentication extends Controller
{
    //
    public function submitLogin(Request $req)
    {   
        $loginForm = $req->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ],[
            'username.required' => 'Harap isi username',
            'username.exists' => $req->username.' tidak terdaftar',
            'password.required' => 'Harap isi password',
        ]);

        if(Auth::attempt($loginForm))
        {
            $notification = [
                'notif_status' => 'success',
                'notif_message' => 'Selamat Datang '.$req->username,
            ];

            $req->session()->regenerate();

            switch(auth()->guard()->user()->id_role)
            {
                case 1 : return redirect()->route('admin.dashboard')->with($notification);
                break;
                case 2 : return redirect()->route('guru.dashboard')->with($notification);
                break;
                case 3 : return redirect()->route('kepsek.dashboard')->with($notification);
                break;
                case 4 : return redirect()->route('bendahara.dashboard')->with($notification);
                break;
            }

        }
        else
        {
            $notification = [
                'notif_status' => 'error',
                'notif_message' => 'Login Gagal',
            ];   

            return redirect()->back()->with($notification)->withErrors([
                'password' => 'Password Salah'
            ]);
        }
    }

    public function logout()
    {
        $notification = [
                'notif_status' => 'success',
                'notif_message' => 'Berhasil Logout',
        ]; 
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->with($notification);
    }
}
