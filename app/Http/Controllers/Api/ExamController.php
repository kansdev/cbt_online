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
        try {
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
            $soal_count = SoalAcak::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->count();

            if($soal_count == 0) {
                $this->generate_soal($siswa, $ujian->tahap);

                // Cek lagi setelah generate, jika masih 0 berarti database soal kamu kosong untuk kategori tersebut
                $recheck = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->count();
                if($recheck == 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal generate soal. Pastikan bank soal untuk kategori jurusan ini tersedia.',
                        'tahap' => $ujian->tahap
                    ], 500);
                }
            }

            // Update lock generate soal
            logger()->info('GENERATE', [
                'siswa' => $siswa->id,
                'tahap' => $ujian->tahap
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ujian dimulai',
                'tahap' => $ujian->tahap
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }

    }

    public function halaman_soal(Request $request, $id_siswa)
    {
        $siswa = Account::findOrFail($id_siswa);
        $ujian = Ujian::where('id_siswa', $id_siswa)->first();

        if (!$ujian) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ujian belum dimulai'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK TAHAP
        |--------------------------------------------------------------------------
        */

        $this->cek_tahap($siswa, $ujian);
        $ujian->refresh();

        /*
        |--------------------------------------------------------------------------
        | CEK STATUS SELESAI
        |--------------------------------------------------------------------------
        */
        if ($ujian->status == 'selesai') {

            return response()->json([
                'status' => 'selesai'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG SISA WAKTU
        |--------------------------------------------------------------------------
        */
        $waktu_mulai = Carbon::parse($ujian->mulai_at);
        $detik_berlalu = now()->timestamp - $waktu_mulai->timestamp;
        $sisa_waktu = self::DURASI_UJIAN_DETIK - $detik_berlalu;
        if ($sisa_waktu <= 0) {

            $ujian->update([
                'status' => 'selesai',
                'selesai_at' => now()
            ]);

            return response()->json([
                'status' => 'selesai',
                'message' => 'Waktu habis'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA MASIH JEDA
        |--------------------------------------------------------------------------
        */
        if ($ujian->tahap == 'jeda_umum') {

            return response()->json([
                'status' => 'transisi',
                'next_tahap' => 'kejuruan_pertama'
            ]);
        }

        // if ($ujian->tahap == 'jeda_kejuruan') {

        //     return response()->json([
        //         'status' => 'transisi',
        //         'next_tahap' => 'kejuruan_kedua'
        //     ]);
        // }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SOAL
        |--------------------------------------------------------------------------
        */

        $urutan = $request->query('urutan');

        if ($urutan) {

            $soal_acak = SoalAcak::with('soal')
                ->where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->where('urutan', $urutan)
                ->first();

        } else {

            $id_soal_terjawab = Jawaban::where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap) // Filter tahap ujian
                ->pluck('id_soal');

            $soal_acak = SoalAcak::with('soal')
                ->where('id_siswa', $id_siswa)
                ->where('tahap', $ujian->tahap)
                ->whereNotIn('id_soal', $id_soal_terjawab)
                ->orderBy('urutan')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA SOAL HABIS
        |--------------------------------------------------------------------------
        */
        if (!$soal_acak) {

            // jika kejuruan kedua selesai
            if ($ujian->tahap == 'kejuruan_kedua') {

                $ujian->update([
                    'status' => 'selesai',
                    'waktu_selesai_jurusan_kedua' => now(),
                    'selesai_at' => now()
                ]);

                return response()->json([
                    'status' => 'selesai', //
                    'message' => 'Tes selesai'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Soal tidak ditemukan'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status' => 'success',
            'data' => [
                'soal' => $soal_acak,
                'tahap' => $ujian->tahap,
                'sisa_waktu' => $sisa_waktu,
                'daftar_navigasi' => SoalAcak::where('id_siswa', $id_siswa)
                    ->where('tahap', $ujian->tahap)
                    ->select('urutan', 'id_soal')
                    ->get()
            ]
        ]);
    }

    private function cek_tahap($siswa, $ujian)
    {
        /*
        |--------------------------------------------------------------------------
        | CEK PINDAH DARI UMUM -> JEDA
        |--------------------------------------------------------------------------
        */
        if ($ujian->tahap == 'umum') {

            $soal_umum = SoalAcak::where('id_siswa', $siswa->id)
                ->where('tahap', 'umum')
                // ->pluck('id_soal')
                ->count();

            // $total_soal_umum = $id_soal_umum->count();

            // $jumlah_jawab = Jawaban::where('id_siswa', $siswa->id)
            //     ->whereIn('id_soal', $id_soal_umum)
            //     ->count();
            $jumlah_jawab = Jawaban::where('id_siswa', $siswa->id)
                ->where('tahap', 'umum')
                ->distinct('id_soal')
                ->count('id_soal');

            // Logger
            logger()->info("Catatan total soal umum dan jumlah jawaban", [
                'total_soal_umum' => $soal_umum,
                'jumlah_jawab' => $jumlah_jawab,
            ]);

            if ($jumlah_jawab >= $soal_umum && $soal_umum > 0) {

                $ujian->update([
                    'tahap' => 'jeda_umum',
                    'waktu_selesai_umum' => now()
                ]);
                // $ujian->refresh();
                logger()->info('PINDAH KE JEDA');
                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CEK PINDAH DARI JEDA -> KEJURUAN PERTAMA
        |--------------------------------------------------------------------------
        */

        if ($ujian->tahap == 'jeda_umum') {

            if (!$ujian->waktu_selesai_umum) {
                return;
            }

            $detikLalu = Carbon::parse(
                $ujian->waktu_selesai_umum
            )->diffInSeconds(now());

            logger()->info("Catatan jeda waktu ", [
                'detik_jeda' => $detikLalu
            ]);

            if ($detikLalu >= 60) {

                $ujian->update([
                    'tahap' => 'kejuruan_pertama' // Update ke kejuruan pertama
                ]);

                $ujian->refresh();

                // generate soal kejuruan jika belum ada
                // Update mencegah gagal update dan duplikasi data
                DB::transaction(function() use ($siswa) {
                    $cekSoal = SoalAcak::where('id_siswa', $siswa->id)
                        ->where('tahap', 'kejuruan_pertama')
                        ->lockForUpdate()
                        ->count();

                    if ($cekSoal == 0) {
                        $this->generate_soal($siswa, 'kejuruan_pertama');
                    }
                });

                logger()->info('PINDAH KE KEJURUAN PERTAMA');
                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CEK PINDAH DARI KEJURUAN PERTAMA -> JEDA
        |--------------------------------------------------------------------------
        */
        if ($ujian->tahap == 'kejuruan_pertama') {

            $soal_kejuruan_pertama = SoalAcak::where('id_siswa', $siswa->id)
                ->where('tahap', 'kejuruan_pertama')
                // ->pluck('id_soal');
                ->count();

            $jumlah_jawab = Jawaban::where('id_siswa', $siswa->id)
                ->where('tahap', 'kejuruan_pertama')
                ->distinct('id_soal')
                ->count('id_soal');

            logger()->info("Catatan total soal kejuruan pertama dan jumlah jawaban ", [
                'total_soal_kejuruan_pertama' => $soal_kejuruan_pertama,
                'jumlah_jawab' => $jumlah_jawab,
            ]);

            if ($jumlah_jawab >= $soal_kejuruan_pertama && $soal_kejuruan_pertama > 0) {

                $ujian->update([
                    'tahap' => 'jeda_kejuruan',
                    'waktu_selesai_jurusan_pertama' => now()
                ]);

                $ujian->refresh();
                logger()->info('PINDAH KE JEDA');
                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CEK PINDAH DARI JEDA -> KEJURUAN KEDUA
        |--------------------------------------------------------------------------
        */
        if ($ujian->tahap == 'jeda_kejuruan') {

            if (!$ujian->waktu_selesai_jurusan_pertama) {
                return;
            }

            $detikLalu = Carbon::parse(
                $ujian->waktu_selesai_jurusan_pertama
            )->diffInSeconds(now());

            logger()->info("Catatan jeda waktu ", [
                'detik_jeda' => $detikLalu
            ]);

            if ($detikLalu >= 60) {

                $ujian->update([
                    'tahap' => 'kejuruan_kedua' // Update ke kejuruan kedua
                ]);

                $ujian->refresh();

                // generate soal kejuruan jika belum ada
                DB::transaction(function() use ($siswa) {
                    $cekSoal = SoalAcak::where('id_siswa', $siswa->id)
                        ->where('tahap', 'kejuruan_kedua')
                        ->lockForUpdate()
                        ->count();

                    if ($cekSoal == 0) {
                        $this->generate_soal($siswa, 'kejuruan_kedua');
                    }
                });

                logger()->info('PINDAH KE KEJURUAN KEDUA');
                return;
            }
        }
    }

    private function generate_soal($siswa, $tahap)
    {
        $kategori = $this->get_kategori_soal($siswa, $tahap);

        if (empty($kategori)) {
            // Update log info
            logger()->info('Kategori kosong ', [
                'siswa' => $siswa->id,
                'tahap' => $tahap,
                'time' => now()->format('H:i:s.u')
            ]);
            return;
        }

        // Soal dengan
        if($tahap == 'umum') {
            $soal = Soal::whereIn('kategori', $kategori)
                ->inRandomOrder()
                ->get();
        } else {
            $soal = Soal::whereIn('kategori', $kategori)
                ->inRandomOrder()
                ->limit(10)
                ->get();
        }

        if ($soal->isEmpty()) {
            // Update log info
            logger()->info('Bank soal kosong untuk kategori : ' . implode(',', $kategori), [
                'siswa' => $siswa->id,
                'tahap' => $tahap,
                'kategori' => implode(',', $kategori),
                'time' => now()->format('H:i:s.u')
            ]);
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
        $jurusan_1 = strtoupper($siswa->jurusan_pertama);
        $jurusan_2 = strtoupper($siswa->jurusan_kedua);
        // Debug:
        logger()->info("Jurusan Siswa: " . $jurusan_1 . " dan " . $jurusan_2 );
        switch ($tahap) {
            case 'umum':
                return match ($jurusan_1) {
                    'PPLG', 'TJKT' => ['mtk_umum', 'bindo_teknik', 'binggris_teknik'],
                    'DKV', 'AN', 'BP' => ['mtk_umum', 'bindo_seni', 'binggris_seni'],
                    'MP', 'AK' => ['mtk_umum', 'bindo_manajemen', 'binggris_manajemen'],

                    default => []
                };

            case 'kejuruan_pertama':
                return match ($jurusan_1) {
                    'PPLG' => ['jurusan_rpl'],
                    'TJKT' => ['jurusan_tjkt'],
                    'DKV'  => ['jurusan_dkv'],
                    'BP'   => ['jurusan_bp'],
                    'AN'   => ['jurusan_an'],
                    'MP'   => ['jurusan_mp'],
                    'AK'   => ['jurusan_ak'],
                    default => []
                };

            case 'kejuruan_kedua':
                return match($jurusan_2) {
                    'PPLG' => ['jurusan_rpl'],
                    'TJKT' => ['jurusan_tjkt'],
                    'DKV'  => ['jurusan_dkv'],
                    'BP'   => ['jurusan_bp'],
                    'AN'   => ['jurusan_an'],
                    'MP'   => ['jurusan_mp'],
                    'AK'   => ['jurusan_ak'],
                    default => []
                };
        }

        return [];
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

        $siswa = Account::findOrFail($request->id_siswa);
        $this->cek_tahap($siswa, $ujian);
        $ujian->refresh();

        return response()->json([
            'status' => true
        ], 200);
    }

    public function simpan_batch(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'id_siswa' => 'required',
            'jawaban' => 'required|array'
        ]);

        try {

            // ===============================
            // SIMPAN JAWABAN
            // ===============================
            foreach ($request->jawaban as $id_soal => $jawab) {

                $soalAcak = SoalAcak::where('id_siswa', $request->id_siswa)
                    ->where('id_soal', $id_soal)
                    ->first();

                if (!$soalAcak) {
                    continue;
                }

                Jawaban::updateOrCreate(
                    [
                        'id_siswa' => $request->id_siswa,
                        'id_soal' => $id_soal,
                    ],
                    [
                        'tahap'   => $soalAcak->tahap,
                        'jawaban' => $jawab['pilihan'],
                        'urutan'  => $jawab['urutan'],
                    ]
                );
            }

            // ===============================
            // AMBIL DATA
            // ===============================
            $siswa = Account::findOrFail($request->id_siswa);

            $ujian = Ujian::where('id_siswa', $request->id_siswa)
                ->first();

            // ===============================
            // CEK PERPINDAHAN TAHAP
            // ===============================
            $this->cek_tahap($siswa, $ujian);

            $ujian->refresh();

            logger()->info("Catatan ", [
                'tahap' => $ujian->tahap
            ]);

            // ===============================
            // CEK SELESAI KEJURUAN
            // ===============================
            if ($ujian->tahap == 'kejuruan_kedua') {

                $totalKejuruanPertama = SoalAcak::where('id_siswa', $siswa->id)
                    ->where('tahap', 'kejuruan_pertama')
                    ->count();

                $jawabanKejuruanPertama = Jawaban::where('id_siswa', $siswa->id)
                    ->where('tahap', 'kejuruan_pertama')
                    ->distinct('id_soal')
                    ->count('id_soal');

                $totalKejuruanKedua = SoalAcak::where('id_siswa', $siswa->id)
                    ->where('tahap', 'kejuruan_kedua')
                    ->count();

                $jawabanKejuruanKedua = Jawaban::where('id_siswa', $siswa->id)
                    ->where('tahap', 'kejuruan_kedua')
                    ->distinct('id_soal')
                    ->count('id_soal');

                logger()->info("Catatan total kejuruan pertama, jawaban kejuruan pertama dan total kejuruan kedua dan jawawban kejuruan kedua", [
                    'totalKejuruanPertama' => $totalKejuruanPertama,
                    'jawabanKejuruanPertama' => $jawabanKejuruanPertama,
                    'totalKejuruanKedua' => $totalKejuruanKedua,
                    'jawabanKejuruanKedua' => $jawabanKejuruanKedua
                ]);

                // ===================================
                // JIKA SUDAH SELESAI SEMUA
                // ===================================
                if ($jawabanKejuruanKedua >= $totalKejuruanKedua && $totalKejuruanKedua > 0) {

                    $ujian->update([
                        'status' => 'selesai',
                        'waktu_selesai_jurusan_kedua' => now(),
                        'selesai_at' => now()
                    ]);

                    $ujian->refresh();

                    DB::commit();

                    return response()->json([
                        'status' => 'selesai',
                        'message' => 'Tes selesai'
                    ]);
                }
            }

            // ===============================
            // COMMIT
            // ===============================
            DB::commit();

            // ===============================
            // RESPONSE JEDA
            // ===============================
            if ($ujian->tahap == 'jeda_umum') {

                return response()->json([
                    'status' => 'jeda_umum',
                    'message' => 'Tahap umum selesai'
                ]);
            }

            if ($ujian->tahap == 'jeda_kejuruan') {

                return response()->json([
                    'status' => 'jeda_kejuruan',
                    'message' => 'Masuk jeda kejuruan'
                ]);
            }

            // ===============================
            // RESPONSE SUCCESS
            // ===============================
            return response()->json([
                'status' => 'success',
                'message' => 'Jawaban berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();
            logger()->error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan jawaban',
                'line' => $e->getLine(),
                'file' => $e->getFile()
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
