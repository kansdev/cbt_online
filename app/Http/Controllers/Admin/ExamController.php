<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Account;
use App\Models\Soal;
use App\Models\SoalAcak;
use App\Models\Jawaban;
use App\Models\Ujian;

use App\Imports\SoalImport;
use App\Exports\KoreksiExport;
use Maatwebsite\Excel\Facades\Excel;

class ExamController extends Controller
{
    public function soal()
    {
        $soal = Soal::all();
        return view('admin.pages.soal', compact('soal'));
    }

    public function reset($id_siswa)
    {
        // Hapus semua data ujian, soal acak, dan jawaban untuk siswa tersebut
        DB::transaction(function () use ($id_siswa) {
            Jawaban::where('id_siswa', $id_siswa)->delete();
            SoalAcak::with('soal')->where('id_siswa', $id_siswa)->delete();
            Ujian::where('id_siswa', $id_siswa)->delete();
        });

        // Tampilkan pesan berhasil reset ujian
        return redirect()->back()->with('success', 'Berhasil reset ujian');
    }

    // Koreksi jawaban peserta
    public function koreksi()
    {
        // Ambil data jawaban 
        $jawaban = Jawaban::with(['account', 'soal'])
            ->paginate(10)
            ->orderBy('id_siswa', 'asc')
            ->get();

        // Ambil data detail jawaban untuk setiap siswa
        $details = Jawaban::with(['account', 'soal'])
            ->get()
            ->map(function ($item) {
                return [
                    'id_siswa' => $item->id_siswa,
                    'nama' => $item->account->nama,
                    'pertanyaan' => $item->soal->pertanyaan,
                    'jawaban' => $item->jawaban,
                    'kunci_jawaban' => $item->soal->kunci_jawaban,
                ];
            });

        // Ambil data detail jawaban untuk setiap siswa dan hitung jumlah benar, salah dan nilai
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
                // $jumlah_soal = Soal::count();

                // Jumlah jawaban yang di jawab
                $jumlah_jawaban = Jawaban::where('id_siswa', $items[0]->id_siswa)->count();

                //Jumlah soal yang di jawab
                $jumlah_soal_acak = SoalAcak::where('id_siswa', $items[0]->id_siswa)->count();

                // Julah soal yang tidak di jawab
                $soal_tidak_dijawab = 50 - $jumlah_soal_acak;
                
                // Hitung nilai dengan rumus (benar / total soal) * 100
                $nilai = 50 > 0 ? round(($benar / 50) * 100, 2) : 0;

                // Kembalikan data detail jawaban beserta jumlah benar, salah dan nilai untuk setiap siswa
                return [
                    'id_siswa' => $items[0]->id_siswa,
                    'nama' => $items[0]->account->nama,
                    'jumlah_soal' => 50,
                    'benar' => $benar,
                    'salah' => $salah,
                    'soal_tidak_dijawab' => $soal_tidak_dijawab,
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

        // Tampilkan halaman koreksi dengan data detail benar, salah dan nilai untuk setiap siswa
        return view('admin.pages.koreksi', compact('detail_jawaban', 'jawaban', 'details'));
    }

    // Fungsi untuk menampilkan riwayat ujian peserta
    public function riwayat()
    {
        // Ambil data riwayat ujian berdasakan waktu selesai ujian
        $riwayat = Ujian::with('account')->paginate(10)->orderBy('selesai_at', 'desc')->get();

        // Tampilkan halaman riwayat dengan data riwayat ujian
        return view('admin.pages.riwayat', compact('riwayat'));
    }

    // Unduh hasil jawaban
    public function unduh_hasil_jawaban()
    {
        // Unduh hasil jawaban dalam format excel 
        return Excel::download(new KoreksiExport, 'hasil_test.xlsx');
    }
}
