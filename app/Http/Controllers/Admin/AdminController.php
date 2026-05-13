<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Account;
use App\Models\Soal;
use App\Models\SoalAcak;
use App\Models\Jawaban;
use App\Models\Ujian;
use App\Models\LogsActivityUser;
use App\Models\SettingWaktuTes;
use App\Models\SettingGelombang;
use App\Models\SettingAntiInspectElement;

use App\Imports\SoalImport;
use App\Imports\PesertaImport;
use App\Exports\KoreksiExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index()
    {
        $stats = Account::selectRaw(
            "Count(*) as total,
            SUM(CASE WHEN jenis_kelamin = 'Laki - Laki' THEN 1 ELSE 0 END) as laki_laki,
            SUM(CASE WHEN jenis_kelamin = 'Perempuan' THEN 1 ELSE 0 END) as perempuan"
        )->first();

        $log = LogsActivityUser::with('account')->latest()->limit(10)->get();
        return view('admin.pages.beranda', [
            'peserta' => $stats->total,
            'laki_laki' => $stats->laki_laki,
            'perempuan' => $stats->perempuan,
            'log' => $log
        ]);
    }

    // Clear Log
    public function clear_log()
    {
        // Bersihkan seluruh aktifitas log pengguna
        $logs = LogsActivityUser::query()->delete();

        // Cek jika log kosong tampilkan pesan
        if (empty($logs)) return redirect()->back()->with('warning', 'Logs is empty !!!');

        // Tampilkan pesan berhasil membersihkan log
        return redirect()->back()->with('success', 'Clear Log Successfully !!!');
    }
}
