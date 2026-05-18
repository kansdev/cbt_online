<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\SoalAcak;

class ExamController extends Controller
{

    // Konstanta durasi agar mudah diatur di satu tempat (60 menit)
    const DURASI_UJIAN_DETIK = 3600;
    public function mulai_ujian($id_siswa)
    {
        // Ambil data ujian berdasarkan id siswa
        $cek_ujian = Ujian::where('id_siswa', $id_siswa)->first();

        // Cek apakah ujian sudah selesai
        if($cek_ujian && $cek_ujian->status == 'selesai') return view('test.selesai', $this->hitung_skor($id_siswa));

        // Ambil data siswa berdasarkan id siswa
        $siswa = Account::findOrFail($id_siswa);

        // Ambil data soal berdasarkan jenis soal siswa
        // Cek apakah ujian sudah pernah dibuat sebelumnya 
        // Jika belum maka buat ujian baru dengan status mulai dan tahap umum       
        $ujian = Ujian::firstOrCreate(
            ['id_siswa' => $id_siswa],
            [
                'nisn' => $siswa->nisn,
                'status' => 'mulai',
                'tahap' => 'umum',
                'mulai_at' => now(),
            ]
        );

        // Cek apakah ujian belum dimulai, jika belum maka update waktu mulai ujian
        if(!$ujian->mulai_at) $ujian->update(['mulai_at' => now()]);

        // Buat soal acak untuk siswa jika belum ada soal acak dengan jenis soal umum
        $soal_count = SoalAcak::where('id_siswa', $id_siswa)->count();
        if($soal_count == 0) $this->generate_soal($siswa, 'umum');

        // Catat aktivitas mulai ujian
        LogsActivityUser::create([
            'id_siswa' => $siswa->id,
            'activity' => 'mulai_ujian',
            'ip_address' => request()->getClientIp(),
            'user_agent' => request()->userAgent()
        ]);

        // Arahkan ke halaman soal
        return redirect()->route('ujian.soal', $id_siswa);
    }

    public function halaman_soal($id_siswa)
    {
        // Ambil data siswa berdasarkan id siswa
        $siswa = Account::findOrFail($id_siswa);
        
        // Cek apakah ujian sudah pernah dibuat sebelumnya 
        // Jika belum maka buat ujian baru dengan status mulai dan tahap umum
        $ujian = Ujian::firstOrCreate(
            ['id_siswa' => $id_siswa],
            [
                'nisn' => $siswa->nisn,
                'status' => 'mulai',
                'tahap' => 'umum',
                'mulai_at' => now(),
            ]
        );

        // Cek apakah status ujian sudah selesai, jika sudah maka arahkan ke halaman selesai
        if($ujian->status == 'selesai') {
            // Ambil data skor dan semua soal yang sudah di jawab
            $data_selesai = $this->hitung_skor($id_siswa);
            
            // Ambil semua soal yang sudah di acak untuk siswa berdasarkan tahap ujian
            $data_selesai['semua_soal'] = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->orderBy('urutan')->get();

            // Arahkan ke halaman selesai dengan data skor dan semua soal yang sudah di jawab
            return view('test.selesai', $data_selesai);
        }

        // Hitung sisa waktu ujian 
        $waktu_mulai = Carbon::parse($ujian->mulai_at);

        // Hitung sisa waktu ujian dalam detik
        $sisa_waktu = (int) max(0, self::DURASI_UJIAN_DETIK - now()->diffInSeconds($waktu_mulai));

        // Jika sisa waktu habis maka update status ujian menjadi selesai dan arahkan ke halaman selesai
        if($sisa_waktu <= 0) {
            // Update status ujian menjadi selesai
            $ujian->update([
                'status' => 'selesai',
                'selesai_at' => now()
            ]);

            // Ambil data skor dan semua soal yang sudah di jawab
            $data_selesai = $this->hitung_skor($id_siswa);
            $data_selesai['semua_soal'] = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->orderBy('urutan')->get();
            return view('test.selesai', $data_selesai);
        }

        // Cek tahap ujian 
        $this->cek_tahap($siswa, $ujian);

        // Handler halaman jeda dari jenis soal umum ke kejuruan
        if($ujian->tahap == 'jeda') {
            // Cek waktu selesai tahap umum
            // Jika tidak ada waktu selesai tahap umum, tampilkan halaman jeda
            if(!$ujian->waktu_selesai_umum) {
                return view('test.jeda');
            }
            
            // Simpan waktu selesai tahap umum
            $selesai = Carbon::parse($ujian->waktu_selesai_umum);

            // Cek status ujian jika selesai maka tampilkan halaman selesai atau tampilkan halaman jeda jika belum lewat 60 detik
            if(now()->date_diffInSeconds($selesai, false) < 60) {
                // cek status jika sudah selesai maka tampilkan halaman selesai
                if($ujian->status == 'selesai') {
                    // Ambil data skor dan semua soal yang sudah di jawab
                    $data_selesai = $this->hitung_skor($id_siswa);
                    $data_selesai['semua_soal'] = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->orderBy('urutan')->get();
                    return view('test.selesai', $data_selesai);
                }

                // Jika tidak tampilkan halaman jeda dengan waktu selama 60 detik
                return view('test.jeda');
            }

            // jika sudah lewat 60 detik maka lanjut ke tahap kejuruan
            // Update tahap ke kejuruan
            $ujian->update(['tahap' => 'kejuruan']);

            // Generate soal kejuruan jika belum ada
            if(SoalAcak::where('id_siswa', $id_siswa->id)->where('tahap', 'kejuruan')->count() == 0) {
                // Generat soal kejuruan
                $this->generate_soal($siswa, 'kejuruan');
            }

            // Arahkan ke halaman soal untuk tahap kejuruan
            return redirect()->route('ujian.soal', $id_siswa);
        }

        // Soal berikutnya berdasarkan jumlah jawaban yang sudah dijawab
        $jumlah_jawab = Jawaban::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->count();

        // Ambil soal acak berdasarkan urutan soal acak untuk siswa dan tahap ujian
        $soal_acak = SoalAcak::with('soal')
            ->where('id_siswa', $id_siswa)
            ->where('tahap', $ujian->tahap)
            ->orderBy('urutan')
            ->skip($jumlah_jawab)
            ->first();

        // Jika soal habis, cek tahap dan update status
        if(!$soal_acak) {
            // Jika sudah selesai tahap umum sudah selesai tampilkan jeda
            if($ujian->tahap == 'umum') return view('test.jeda', ['waktu_selesai_umum' => $ujian->waktu_selesai_umum]);

            // Jika sudah selesai tahap kejuruan sudah selesai tampilkan halaman selesai
            if($ujian->tahap == 'kejuruan') {
                // Update status ujian menjadi selesai
                $ujian->update([
                    'status' => 'selesai', 
                    'selesai_at' => now()
                ]);
                // Ambil data skor dan semua soal yang sudah di jawab
                $data_selesai = $this->hitung_skor($id_siswa);
                $data_selesai['semua_soal'] = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->orderBy('urutan')->get();
                return view('test.selesai', $data_selesai);
            }
        }
        
        // Ambil semua soal yang sudah di acak untuk siswa berdasarkan tahap ujian
        $semua_soal = SoalAcak::where('id_siswa', $id_siswa)->where('tahap', $ujian->tahap)->orderBy('urutan')->get();

        // Ambil setting anti inspect elemen
        $setting_anti_inspect = SettingAntiInspectElemen::first();

        // Arahkan ke halaman soal dengan data siswa, soal acak, urutan soal, tahap ujian, sisa waktu, semua soal yang sudah di acak untuk siswa berdasarkan tahap ujian, dan setting anti inspect elemen
        return view('test.soal', [
            'siswa' => $siswa,
            'soal' => $soal_acak,
            'urutan' => $soal_acak->urutan ?? 1,
            'tahap' => $ujian->tahap,
            'sisa_waktu' => $sisa_waktu,
            'semua_soal' => $semua_soal,
            'anti_inspect' => $setting_anti_inspect->status ?? 'nonaktif'
        ]);
    }

    private function cek_tahap($siswa, $ujian)
    {
        if($ujian->cek_tahap == 'umum') {
            $total_soal_umum = SoalAcak::where('id_siswa', $siswa->id)->where('tahap', 'umum')->count();
            $jumlah_jawab = Jawaban::where('id_siswa', $siswa->id)->where('tahap', 'umum')->count();

            if($jumlah_jawab == $total_soal_umum && $total_soal_umum > 0) {
                // Update waktu selesai tahap umum
                $ujian->update([
                    'cek_tahap' => 'jeda',
                    'waktu_selesai_umum' => now()
                ]);

                return;
            }
        }

        if($ujian->tahap == 'jeda') {
            if(!$ujian->waktu_selesai_umum) return;

            $selesai = Carbon::parse($ujian->waktu_selesai_umum);
            if(now()->date_diffInSeconds($selesai, false) >= 60) {
                $ujian->hash_update(['tahap' => 'kejuruan']);

                if(SoalAcak::where('id_siswa', $siswa->id)->where('tahap', 'kejuruan')->count() == 0) {
                    // Generat soal kejuruan
                    $this->generate_soal($siswa, 'kejuruan');
                }
                logger()->info('Lanjut ke tahap kejuruan otomatis via cek_tahap', ['id_siswa' => $siswa->id]);
            }
        }
    }

    private function generate_soal($siswa, $tahap)
    {
        $kategori = $this->get_kategori_soal($siswa, $tahap);

        // Optimasi: chunk/get saja id & data yang diperlukan
        $soal = Soal::whereIn('kategori', (array) $kategori)->inRandomOrder()->get();

        // Rekomendasi Masa Depan: Gunakan Bulk Insert untuk performa ribuan siswa
        foreach ($soal as $index => $s) {
            SoalAcak::create([
                'id_siswa' => $siswa->id,
                'id_soal' => $s->id,
                'tahap' => $tahap,
                'urutan' => $index + 1
            ]);
        }
    }

    private function get_kategori_soal($siswa, $tahap)
    {
        $jurusan = strtoupper($siswa->jurusan);
        
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
            return response()->json(['status' => false, 'message' => 'Ujian tidak valid'], 400);
        }

        $sisa_waktu = (int) max(0, self::DURASI_UJIAN_DETIK - now()->diffInSeconds(Carbon::parse($ujian->mulai_at)));

        if($sisa_waktu <= 0 || $ujian->status == 'selesai') {
            if($ujian->status != 'selesai') {
                $ujian->update([
                    'status' => 'selesai',
                    'selesai_at' => now()
                ]);
            }
            return response()->json(['status' => false, 'message' => 'Waktu habis, ujian sudah selesai'], 400);
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

        return response()->json(['status' => true]);
    }

    public function reset_ujian($id_siswa)
    {
        Ujian::where('id_siswa', $id_siswa)->delete();
        SoalAcak::where('id_siswa', $id_siswa)->delete(); // Perbaikan: hapus dengan ->with()
        Jawaban::where('id_siswa', $id_siswa)->delete();

        return response()->json(['status' => true, 'message' => 'Ujian berhasil direset']);
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
