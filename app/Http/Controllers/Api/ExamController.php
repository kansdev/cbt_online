<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Account;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\SoalAcak;

use App\Models\LogsActivityUser;
use App\Models\SettingAntiInspectElement;

class ExamController extends Controller
{

    // Konstanta durasi agar mudah diatur di satu tempat (60 menit)
    const DURASI_UJIAN_DETIK = 3600;

    public function mulai_ujian($id_siswa)
    {
        $siswa = Account::findOrFail($id_siswa);
        
        // 1. Ambil atau buat data ujian
        $ujian = Ujian::firstOrCreate(
            ['id_siswa' => $id_siswa],
            [
                'nisn' => $siswa->nisn,
                'status' => 'mulai',
                'tahap' => 'umum',
                'mulai_at' => now(),
            ]
        );

        // 2. PAKSA GENERATE jika soal belum ada
        $soal_count = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->count();
        if($soal_count == 0) {
            $this->generate_soal($siswa, $ujian->tahap);
            
            // Cek lagi setelah generate, jika masih 0 berarti database soal kamu kosong untuk kategori tersebut
            $recheck = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->count();
            if($recheck == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal generate soal. Pastikan bank soal untuk kategori jurusan ini tersedia.'
                ], 500);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ujian dimulai',
            'tahap' => $ujian->tahap
        ], 200);
    }

    public function halaman_soal(Request $request, $id_siswa)
    {
        $siswa = Account::findOrFail($id_siswa);
        $ujian = Ujian::where('id_siswa', $id_siswa)->first();

        if (!$ujian) return response()->json(['status' => 'error', 'message' => 'Ujian belum dimulai'], 404);
        
        // 1. JALANKAN CEK TAHAP DI AWAL
        // Ini akan mengubah status 'jeda' -> 'kejuruan' jika waktu sudah > 60 detik
        $this->cek_tahap($siswa, $ujian);
        
        // 2. REFRESH DATA UJIAN
        // Penting! Agar variabel $ujian sinkron dengan perubahan status yang dilakukan di cek_tahap tadi
        $ujian->refresh();

        if ($ujian->status == 'selesai') return response()->json(['status' => 'selesai', 'result' => $this->hitung_skor($id_siswa)], 200);

        // Hitung Sisa Waktu
        $waktu_mulai = Carbon::parse($ujian->mulai_at);
        $sekarang = now();
        $detik_berlalu = $sekarang->timestamp - $waktu_mulai->timestamp;
        $sisa_waktu = (int) (self::DURASI_UJIAN_DETIK - $detik_berlalu);

        if ($sisa_waktu <= 0) {
            $ujian->update(['status' => 'selesai', 'selesai_at' => now()]);
            return response()->json(['status' => 'selesai', 'message' => 'Waktu habis'], 200);
        }

        // Logika Pengambilan Soal
        $urutan_diminta = $request->query('urutan');

        if ($urutan_diminta) {
            $soal_acak = SoalAcak::with(['soal', 'jawaban_user' => function($q) use ($id_siswa) {
                $q->where('id_siswa', $id_siswa);
            }])
            ->where('id_siswa', $id_siswa)
            ->where('tahap', $ujian->tahap) // Ini akan otomatis mencari 'kejuruan' jika sudah berubah
            ->where('urutan', $urutan_diminta)
            ->first();
        } else {
            $id_soal_terjawab = Jawaban::where('id_siswa', $id_siswa)->pluck('id_soal');
            $soal_acak = SoalAcak::with('soal')
                ->where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->whereNotIn('id_soal', $id_soal_terjawab)
                ->orderBy('urutan')
                ->first();
        }

        // 3. LOGIKA TRANSISI
        if (!$soal_acak) {
            // Jika status masih 'umum' tapi soal habis, panggil cek_tahap untuk masuk ke 'jeda'
            if ($ujian->tahap == 'umum') {
                $this->cek_tahap($siswa, $ujian);
                return response()->json([
                    'status' => 'transisi', 
                    'next_tahap' => 'jeda', 
                    'message' => 'Tahap umum selesai, masuk waktu jeda...'
                ]);
            }

            // Jika status sudah 'jeda', kirim transisi agar React tampilkan timer jeda
            if ($ujian->tahap == 'jeda') {
                return response()->json([
                    'status' => 'transisi', 
                    'next_tahap' => 'jeda'
                ]);
            }

            // Jika tahap sudah 'kejuruan' tapi soal tidak ada, berarti ada error saat generate
            return response()->json(['status' => 'error', 'message' => 'Soal kejuruan tidak ditemukan']);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'soal' => $soal_acak,
                'tahap' => $ujian->tahap,
                'sisa_waktu' => $sisa_waktu,
                'daftar_navigasi' => SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->select('urutan', 'id_soal')->get()
            ]
        ], 200);
    }

    private function cek_tahap($siswa, $ujian)
    {
        if($ujian->tahap == 'umum') {
            $total_soal_umum = SoalAcak::where('id_siswa', $siswa->id)->where('tahap', 'umum')->count();
            $jumlah_jawab = Jawaban::where('id_siswa', $siswa->id)->where('tahap', 'umum')->count();

            if($jumlah_jawab >= $total_soal_umum && $total_soal_umum > 0) {
                // Update waktu selesai tahap umum
                $ujian->update([
                    'tahap' => 'jeda',
                    'waktu_selesai_umum' => now()
                ]);
            }
        }

        if ($ujian->tahap == 'jeda') {
            if (!$ujian->waktu_selesai_umum) return;

            $selesai = Carbon::parse($ujian->waktu_selesai_umum);
            $sekarang = now();

            // Gunakan diffInSeconds tanpa parameter kedua atau pastikan urutannya benar
            $detikLalu = $selesai->diffInSeconds($sekarang);

            if ($detikLalu >= 60) {
                // 1. Update ke database
                $ujian->update([
                    'tahap' => 'kejuruan'
                ]);

                // 2. REFRESH instance agar $ujian->tahap berubah menjadi 'kejuruan' di baris kode selanjutnya
                $ujian->refresh();

                // 3. Cek dan Generate Soal
                $cekSoal = SoalAcak::where('id_siswa', $siswa->id)
                                ->where('tahap', 'kejuruan')
                                ->count();

                if ($cekSoal == 0) {
                    $this->generate_soal($siswa, 'kejuruan');
                }
                
                logger()->info('Transisi Jeda ke Kejuruan Sukses', ['id_siswa' => $siswa->id]);
            }
        }
    }

    private function generate_soal($siswa, $tahap)
    {
        $kategori = $this->get_kategori_soal($siswa, $tahap);

        if (empty($kategori)) {
            \Log::error("Kategori kosong untuk siswa: " . $siswa->id);
            return;
        }

        $soal = Soal::whereIn('kategori', $kategori)->inRandomOrder()->get();

        if ($soal->isEmpty()) {
            \Log::error("Bank soal kosong untuk kategori: " . implode(',', $kategori));
            return;
        }

        foreach ($soal as $index => $s) {
            SoalAcak::create([
                'id_siswa' => $siswa->id,
                'id_soal'  => $s->id,
                'tahap'    => $tahap,
                'urutan'   => $index + 1
            ]);
        }
    }

    private function get_kategori_soal($siswa, $tahap)
    {
        $jurusan = strtoupper($siswa->jurusan);
        // Debug: 
        \Log::info("Jurusan Siswa: " . $jurusan);
        $kategoriMap = [
            'umum' => [
                'PPLG' => ['mtk_umum', 'bindo_teknik', 'binggris_teknik'],
                'TJKT' => ['mtk_umum', 'bindo_teknik', 'binggris_teknik'],
                'DKV'  => ['mtk_umum', 'bindo_seni', 'binggris_seni'],
                'BP'   => ['mtk_umum', 'bindo_seni', 'binggris_seni'],
                'AN'   => ['mtk_umum', 'bindo_seni', 'binggris_seni'],
                'MP'   => ['mtk_umum', 'bindo_manajemen', 'binggris_manajemen'],
                'AK'   => ['mtk_umum', 'bindo_manajemen', 'binggris_manajemen'],
            ],
            'kejuruan' => [
                'PPLG' => ['rpl_tkj'],
                'TJKT' => ['rpl_tkj'],
                'DKV'  => ['an_dkv_bp'],
                'BP'   => ['an_dkv_bp'],
                'AN'   => ['an_dkv_bp'],
                'MP'   => ['mp_ak'],
                'AK'   => ['mp_ak'],
            ]
        ];

        return $kategoriMap[$tahap][$jurusan] ?? [];
    }

    public function simpan_jawaban(Request $request)
    {
        $ujian = Ujian::where('id_siswa', $request->id_siswa)->first();
        
        if(!$ujian || !$ujian->mulai_at) {
            return response()->json([
                'status' => false, 
                'message' => 'Ujian tidak valid'
            ], 400);
        }

        $sisa_waktu = (int) max(0, self::DURASI_UJIAN_DETIK - now()->diffInSeconds(Carbon::parse($ujian->mulai_at)));

        if($sisa_waktu <= 0 || $ujian->status == 'selesai') {
            if($ujian->status != 'selesai') {
                $ujian->update([
                    'status' => 'selesai',
                    'selesai_at' => now()
                ]);
            }
            return response()->json([
                'status' => false, 
                'message' => 'Waktu habis, ujian sudah selesai'
            ], 400);
        }

        logger()->info('Menyimpan jawaban', [
            'id_siswa' => $request->id_siswa,
            'id_soal' => $request->id_soal,
            'jawaban' => $request->jawaban,
            'tahap' => $ujian->tahap
        ]);

        Jawaban::updateOrCreate(
            ['id_siswa' => $request->id_siswa, 'id_soal' => $request->id_soal],
            ['jawaban' => $request->jawaban, 'urutan' => $request->urutan, 'tahap' => $ujian->tahap]
        );

        return response()->json([
            'status' => true
        ], 200);
    }

    public function simpan_batch(Request $request)
    {
        try {
            $id_siswa = $request->id_siswa;
            $jawaban_input = $request->jawaban; // Berupa object { "id_soal": "a" }

            // 1. Validasi minimal
            if (!$id_siswa) {
                return response()->json(['status' => 'error', 'message' => 'ID Siswa tidak ditemukan'], 400);
            }

            // 2. Loop simpan jawaban
            if (!empty($jawaban_input)) {
            foreach ($jawaban_input as $id_soal => $data) {
                // $data sekarang berisi ['pilihan' => '...', 'urutan' => ...]
                
                Jawaban::updateOrCreate(
                    [
                        'id_siswa' => $id_siswa, 
                        'id_soal'  => $id_soal
                    ],
                    [
                        // PASTIKAN MENGAMBIL INDEX 'pilihan'
                        'jawaban' => $data['pilihan'],                         
                        // PASTIKAN MENGAMBIL INDEX 'urutan'
                        'urutan'  => $data['urutan'],                         
                        'tahap'   => 'umum',
                        'updated_at' => now()
                    ]
                );
            }
        }

            // 3. Update status Ujian ke Jeda
            $ujian = Ujian::where('id_siswa', $id_siswa)->first();
            if ($ujian) {
                $ujian->update([
                    'tahap' => 'jeda',
                    'waktu_selesai_umum' => now()
                ]);
            }

            return response()->json([
                'status' => 'jeda',
                'message' => 'Data berhasil disimpan, masuk waktu jeda.'
            ], 200);

        } catch (\Exception $e) {
            // Ini akan mengirimkan pesan error asli ke React agar bisa dibaca di Tab Network
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function reset_ujian($id_siswa)
    {
        Ujian::where('id_siswa', $id_siswa)->delete();
        SoalAcak::where('id_siswa', $id_siswa)->delete(); // Perbaikan: hapus dengan ->with()
        Jawaban::where('id_siswa', $id_siswa)->delete();

        return response()->json([
            'status' => true, 
            'message' => 'Ujian berhasil direset'
        ], 200);
    }

    public function cek_gelombang($nisn)
    {
        // Perbaikan: Logical grouping pada orWhere agar Query JOIN tidak bocor data
        return DB::table('accounts')
                ->join('setting_gelombang', 'accounts.id_gelombang', '=', 'setting_gelombang.id_gelombang')
                ->join('setting_duration', 'setting_gelombang.id_gelombang', '=', 'setting_duration.id_gelombang')
                ->where(function($query) use ($nisn) {
                    $query->where('accounts.nisn', $nisn)
                          ->orWhere('accounts.nomor_registrasi', $nisn);
                })
                ->select('accounts.*', 'setting_gelombang.*', 'setting_duration.*')
                ->first();
    }

    // Fungsi Baru: Mencegah Duplikasi Kode Hitung Nilai
    private function hitung_skor($id_siswa)
    {
        $soal = Soal::count();
        $jawaban = Jawaban::with('soal')->where('id_siswa', $id_siswa)->get();

        $benar = $jawaban->filter(function($j) {
            return $j->soal && ($j->jawaban == $j->soal->kunci_jawaban);
        })->count();

        $total = $jawaban->count();
        $skor = $total > 0 ? round(($benar / $total) * 100) : 0;

        return [
            'soal' => $soal,
            'benar' => $benar,
            'total' => $total,
            'salah' => $total - $benar,
            'skor' => $skor
        ];
    }
}
