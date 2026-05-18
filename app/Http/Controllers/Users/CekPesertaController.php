<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Account;

class CekPesertaController extends Controller
{
    public function cek_peserta(Request $request)
    {
        // Buat validasri input
        $validate = $request->validate([
            'nisn' => 'required|numeric',
            'gelombang' => 'required'
        ]);

        $siswa = Account::where(function ($query) use ($validate) {
            $query->where('nisn', $validate['nisn'])
                  ->orWhere('gelombang', $validate['nisn']);
        })->first();

        if(!$siswa) return back()->withErrors(['nisn' => "Peserta dengan NISN {$validate['nisn']} tidak ditemukan. Hubungi operator"])->withInput();

        if($siswa->gelombang != $validate['gelombang']) return back()->withErrors(['nisn' => 'Akun anda belum masuk pada jadwal gelombang manapun. Hubungi operator'])->withInput();

        $gelombang = $this->cek_gelombang($validate['nisn']);    
        if(!$gelombang) return back()->withErrors(['gelombang' => 'Akun pada gelombang tersebut belum dapat digunakan. Hubungi operator'])->withInput();

        if($gelombang->tanggal_mulai != date('Y-m-d')) return back()->withErrors(['gelombang' => 'Akun anda pada gelombang tersebut belum saatnya atau sudah melewati jadwal. Hubungi operator'])->withInput();

        if($siswa->status == 'nonaktif') return back()->withErrors(['status' => 'Akun anda belum aktif. Hubungi Operator'])->withInput();

        $soal = Soal::whereIn('jenis_soal', ['umum', $siswa->jenis_umum, $siswa->jenis_kejuruan])->count();
        $datetime = Carbon::now()->format('d F Y, H:i') . 'WIB';
    }
}
