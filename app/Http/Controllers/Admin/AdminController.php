<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\Soal;
use App\Models\SoalAcak;
use App\Models\Jawaban;

use App\Imports\SoalImport;
use App\Models\Ujian;
use Maatwebsite\Excel\Facades\Excel;

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

                $jumlah_jawaban = Jawaban::where('id_siswa', $items[0]->id_siswa)->count();

                //Jumlah soal yang di jawab
                $jumlah_soal_acak = SoalAcak::where('id_siswa', $items[0]->id_siswa)->count();

                $soal_tidak_dijawab = $jumlah_soal - $jumlah_soal_acak;
                $nilai = $jumlah_soal > 0
                    ? round(($benar / $jumlah_soal) * 100, 2)
                    : 0;

                return [
                    'id_siswa' => $items[0]->id_siswa,
                    'name' => $items[0]->account->name,
                    'jumlah_soal' => $jumlah_soal,
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
        return view('admin.pages.koreksi', compact('detail_jawaban', 'jawaban', 'details'));
    }

    // Fungsi untuk menampilkan riwayat ujian peserta
    public function riwayat()
    {
        $riwayat = Ujian::with('account')->get();
        return view('admin.pages.riwayat', compact('riwayat'));
    }

    public function peserta_aktif() {
        $peserta_aktif = Account::all();
        return view('admin.pages.aktif_peserta', compact('peserta_aktif'));
    }

    // Fungsi untuk upload soal dari file Excel
    function importSoal(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $import = new SoalImport;
        Excel::import($import, $file);

        return redirect()->back()->with('success', 'Soal berhasil diimpor!');
    }

}
