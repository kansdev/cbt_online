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
                return [
                    'id_siswa' => $items[0]->id_siswa,
                    'name' => $items[0]->account->name,
                    'benar' => $benar,
                    'salah' => $salah,
                    // Nilai bisa dihitung berdasarkan jumlah benar, misalnya setiap soal benar bernilai 10 dengan total soal 50 jika benar semua maka nilainya seratus
                    // Hitung jumlah soal - jumlah salah, lalu kalikan dengan 10
                    'nilai' => ( $benar / $items->count() ) * 100,
                    'detail' => $items->map(function($item) {
                        return [
                            'pertanyaan' => $item->soal->pertanyaan,
                            'jawaban' => $item->jawaban,
                            'kunci_jawaban' => $item->soal->kunci_jawaban,
                        ];
                    }),
                ];
            });

        // foreach($detail_jawaban as $key => $value) {
        //     echo "ID Siswa: " . $value['id_siswa'] . "\n"; echo"<br>";
        //     echo "Siswa: " . $value['name'] . "\n"; echo"<br>";
        //     echo "Benar: " . $value['benar'] . "\n"; echo"<br>";
        //     echo "Salah: " . $value['salah'] . "\n"; echo"<br>";
        //     echo "Nilai: " . $value['nilai'] . "\n"; echo"<br>";
        //     echo "Detail Jawaban:\n";echo"<br>";
        //     foreach($value['detail'] as $detail) {
        //         echo "Pertanyaan: " . $detail['pertanyaan'] . "\n"; echo"<br>";
        //         echo "Jawaban: " . $detail['jawaban'] . "\n"; echo"<br>";
        //         echo "Kunci Jawaban: " . $detail['kunci_jawaban'] . "\n"; echo"<br>";
        //         echo "-------------------------\n";echo"<br>";
        //     }
        // }
        return view('admin.pages.koreksi', compact('detail_jawaban', 'jawaban', 'details'));
    }
}
