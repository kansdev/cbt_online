<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\Jawaban;

class AdminController extends Controller
{
    public function index()
    {
        $peserta = Account::count();
        return view('admin.pages.beranda', compact('peserta'));
    }

    public function peserta()
    {
        $peserta = Account::all();
        return view('admin.pages.peserta', compact('peserta'));
    }

    public function soal()
    {
        $soal = Soal::all();
        return view('admin.pages.soal', compact('soal'));
    }

    // Koreksi jawaban peserta
    public function koreksi()
    {
        // $ujian = Ujian::findOrFail($id);
        $jawaban = Jawaban::with(['soals', 'accounts'])
            ->get()
            ->groupBy('id_siswa');

        $detail_jawaban = Jawaban::with(['soals', 'accounts'])
            ->get()
            ->groupBy('id_siswa')
            ->map(function($item) {
                return [
                    'nama' => $item->accounts->name ?? '-',
                    'pertanyaan' => $item->soals->pertanyaan,
                    'jawaban' => $item->jawaban,
                    'kunci_jawaban' => $item->soals->kunci_jawaban
                ];
            });

        $data = $jawaban->map(function($item) {
            $benar = 0;
            $salah = 0;

            foreach ($item as $j) {
                if ($j->jawaban == $j->soal->kunci_jawaban) {
                    $benar++;
                } else {
                    $salah++;
                }
            }

            $total = $item->count();

            return [
                'id_siswa' => $item[0]->id_siswa,
                'nama' => $item[0]->account->name,
                'benar' => $benar,
                'salah' => $salah,
                'total' => $total,
                'nilai' => $total > 0 ? round(($benar / $total) * 100, 2) : 0
            ];
        });
        return view('admin.pages.koreksi', compact('data', 'detail_jawaban'));
    }
}
