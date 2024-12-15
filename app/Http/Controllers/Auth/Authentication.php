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
            User::where('username',$req->username)->update(['is_login' => 1]);
            $notification = [
                'notif_status' => 'success',
                'notif_message' => 'Selamat Datang '.$req->username,
            ];

            $req->session()->regenerate();

            return redirect()->route('dashboard')->with($notification);
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
        User::where('username',auth()->guard()->user()->username)->update(['is_login' => 0]);
        $notification = [
                'notif_status' => 'success',
                'notif_message' => 'Berhasil Logout',
        ]; 
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->with($notification);
    }
}
