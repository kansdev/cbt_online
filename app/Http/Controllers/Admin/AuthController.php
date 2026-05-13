<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) return redirect()->route('admin.index');
        return view('admin.login');
    }

    public function login_process(Request $request) 
    {
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($validate)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.index'))->with('success', 'Login Berhasil, selamat datang ' . Auth::user()->name);
        }

        return back()->with('failed', 'Login gagal, silahkan di coba kembali');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Anda sudah logout');
    }
}
