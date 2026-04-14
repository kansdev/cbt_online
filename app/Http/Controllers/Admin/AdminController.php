<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\Soal;
use App\Models\SoalAcak;
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
        $jawaban = Jawaban::with(['account', 'soal'])
            ->orderBy('id_siswa', 'asc')
            ->get();

        $details = Jawaban::with(['account', 'soal'])
            ->get()
            ->map(function ($item) {
                return [
                    'id_siswa' => $item->id_siswa,
                    'name' => $item->account->name,
                    'pertanyaan' => $item->soal->pertanyaan,
                    'jawaban' => $item->jawaban,
                    'kunci_jawaban' => $item->soal->kunci_jawaban,
                ];
            });

        $detail_jawaban = Jawaban::with(['soal', 'account'])
            ->get()
            ->groupBy('id_siswa')
            ->map(function($items) {
                $benar = 0;
                $salah = 0;
                foreach($items as $item) {
                    if($item->jawaban === $item->soal->kunci_jawaban) {
                        $benar++;
                    } else {
                        $salah++;
                    }
                }

                // TOTAL SOAL (bukan jumlah jawaban!)
                $jumlah_soal = Soal::count();

                //Jumlah soal yang di jawab
                $jumlah_soal_acak = SoalAcak::where('id_siswa', $items[0]->id_siswa)->count();

                $nilai = $jumlah_soal_acak > 0
                    ? round(($benar / $jumlah_soal_acak) * 100, 2)
                    : 0;

                return [
                    'id_siswa' => $items[0]->id_siswa,
                    'name' => $items[0]->account->name,
                    'jumlah_soal' => $jumlah_soal,
                    'benar' => $benar,
                    'salah' => $salah,
                    'jumlah_soal_acak' => $jumlah_soal_acak,
                    'nilai' => $nilai,
                    'detail' => $items->map(function($item) {
                        return [
                            'pertanyaan' => $item->soal->pertanyaan,
                            'jawaban' => $item->jawaban,
                            'kunci_jawaban' => $item->soal->kunci_jawaban,
                        ];
                    }),
                ];
            });
        return view('admin.pages.koreksi', compact('detail_jawaban', 'jawaban', 'details'));
    }
}
