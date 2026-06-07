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

    public function koreksi(Request $request)
    {
        $search = $request->input('search');

        // 1. Ambil ID Siswa secara unik dengan pagination
        // Kita filter berdasarkan relasi account jika ada pencarian
        $paginator = Jawaban::select('id_siswa')
            ->with(['account'])
            ->whereHas('account', function($query) use ($search) {
                if ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                          ->orWhere('nomor_registrasi', 'like', "%{$search}%");
                }
            })
            ->groupBy('id_siswa')
            ->orderBy('id_siswa', 'asc')
            ->paginate(10);

        // 2. Olah data collection di dalam paginator
        $itemsKoreksi = $paginator->getCollection()->map(function($firstItem) {
            $id_siswa = $firstItem->id_siswa;

            // Ambil semua jawaban milik siswa ini beserta relasi soalnya
            $allJawabanSiswa = Jawaban::with('soal')
                ->where('id_siswa', $id_siswa)
                ->get();

            // Ambil total soal yang dialokasikan dari tabel SoalAcak secara dinamis
            $totalUmum = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', 'umum')->count();
            $totalKejuruan = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', 'kejuruan')->count();

            $umum = ['benar' => 0, 'salah' => 0, 'total' => $totalUmum];
            $kejuruan = ['benar' => 0, 'salah' => 0, 'total' => $totalKejuruan];

            foreach($allJawabanSiswa as $item) {
                // Gunakan optional() untuk menghindari error jika soal terhapus di DB
                $kunci = optional($item->soal)->kunci_jawaban;
                $isBenar = $item->jawaban === $kunci;

                if ($item->tahap === 'umum') {
                    $isBenar ? $umum['benar']++ : $umum['salah']++;
                } else {
                    $isBenar ? $kejuruan['benar']++ : $kejuruan['salah']++;
                }
            }

            // Hitung Skor (Skala 100)
            $skor_umum = $totalUmum > 0 ? round(($umum['benar'] / $totalUmum) * 100, 2) : 0;
            $skor_kejuruan = $totalKejuruan > 0 ? round(($kejuruan['benar'] / $totalKejuruan) * 100, 2) : 0;
            
            $total_soal = $totalUmum + $totalKejuruan;
            $total_benar = $umum['benar'] + $kejuruan['benar'];
            $nilai_total = $total_soal > 0 ? round(($total_benar / $total_soal) * 100, 2) : 0;

            return [
                'id_siswa' => $id_siswa,
                'nama' => $firstItem->account->nama ?? 'N/A',
                'nomor_registrasi' => $firstItem->account->nomor_registrasi ?? '-',
                'umum' => $umum,
                'kejuruan' => $kejuruan,
                'soal_umum' => $totalUmum,
                'soal_kejuruan' => $totalKejuruan,
                'skor_umum' => $skor_umum,
                'skor_kejuruan' => $skor_kejuruan,
                'total_benar' => $total_benar,
                'total_soal' => $total_soal,
                'nilai' => $nilai_total,
                // Map detail agar menjadi array murni untuk Blade
                'detail' => $allJawabanSiswa->map(function($j) {
                    return [
                        'tahap' => $j->tahap,
                        'pertanyaan' => optional($j->soal)->pertanyaan ?? 'Soal tidak ditemukan',
                        'jawaban' => $j->jawaban,
                        'kunci_jawaban' => optional($j->soal)->kunci_jawaban ?? '-',
                    ];
                })
            ];
        });

        // 3. Kembalikan data yang sudah diolah ke paginator
        $paginator->setCollection($itemsKoreksi);

        return view('admin.pages.koreksi', [
            'detail_jawaban' => $paginator,
            'search' => $search
        ]);
    }

    // Fungsi untuk menampilkan riwayat ujian peserta
    public function riwayat()
    {
        // Ambil data riwayat ujian berdasakan waktu selesai ujian
        $riwayat = Ujian::with('account')->orderBy('selesai_at', 'desc')->paginate(20);

        // Tampilkan halaman riwayat dengan data riwayat ujian
        return view('admin.pages.riwayat', compact('riwayat'));
    }

    function importSoal(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv',
            ]);

            $file = $request->file('file');
            $import = new SoalImport;
            Excel::import($import, $file);

            return redirect()->back()->with('success', 'Soal berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Soal gagal diimpor! : ' . $e->getMessage());
        }

    }

    // Unduh hasil jawaban
    public function unduh_hasil_jawaban()
    {
        // Unduh hasil jawaban dalam format excel 
        return Excel::download(new KoreksiExport, 'hasil_test.xlsx');
    }
}
