<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB TAMBAHKAN INI
use Illuminate\Support\Facades\Validator; // Opsi untuk validasi manual jika perlu

use App\Models\Account; // WAJIB DIIMPORT
use App\Models\Ujian; // WAJIB DIIMPORT
use App\Models\Soal;    // WAJIB DIIMPORT
use Carbon\Carbon;      // WAJIB DIIMPORT

class CekPesertaController extends Controller
{
    public function cek_peserta(Request $request)
    {
        // Buat validasi input
        $validate = $request->validate([
            'nisn' => 'required|numeric',
            'gelombang' => 'required'
        ]);

        $siswa = Account::where(function ($query) use ($validate) {
            $query->where('nisn', $validate['nisn'])->orWhere('nomor_registrasi', $validate['nisn']);
        })
        ->where('id_gelombang', $validate['gelombang'])
        ->first();

        if(!$siswa) return response()->json([
            'message' => "Peserta dengan NISN {$validate['nisn']} tidak ditemukan. Hubungi operator"
        ], 404);

        $ujian = Ujian::where('id_siswa', $siswa->id)->first();

        if($siswa->id_gelombang != $validate['gelombang']) return response()->json([
            'message' => 'Akun anda belum masuk pada jadwal gelombang manapun. Hubungi operator'
        ], 400);

        $gelombang = $this->cek_gelombang($validate['nisn']);
        if(!$gelombang) return response()->json([
            'message' => 'Akun pada gelombang tersebut belum dapat digunakan. Hubungi operator'
        ], 400);

        if($gelombang->tanggal_mulai != date('Y-m-d')) return response()->json([
            'message' => 'Akun anda pada gelombang tersebut belum saatnya atau sudah melewati jadwal. Hubungi operator'
        ], 400);

        if($siswa->status == 'nonaktif') return response()->json([
            'message' => 'Akun anda belum aktif. Hubungi Operator'
        ], 400);

        $soal = Soal::whereIn('jenis_soal', ['umum', $siswa->jenis_umum, $siswa->jurusan_pertama, $siswa->jurusan_kedua])->count();
        $datetime = Carbon::now()->format('d F Y, H:i') . 'WIB';

        return response()->json([
            'message' => 'Peserta ditemukan',
            'data' => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn,
                'gelombang' => $siswa->id_gelombang,
                'jenis_umum' => $siswa->jenis_umum,
                'jurusan_pertama' => $siswa->jurusan_pertama,
                'jurusan_kedua' => $siswa->jurusan_kedua,
                'status' => $siswa->status,
                'jumlah_soal' => $soal,
                'datetime' => $datetime,
                'nomor_pendaftaran' => $siswa->nomor_registrasi,
                'status_ujian' => $ujian?->status
            ]
        ], 200);
    }

    public function persiapan_ujian($id_siswa) {
        $siswa = DB::table('accounts')
                ->join('setting_gelombang', 'accounts.id_gelombang', '=', 'setting_gelombang.id_gelombang')
                ->join('setting_duration', 'setting_gelombang.id_gelombang', '=', 'setting_duration.id_gelombang')
                ->where('accounts.id', $id_siswa)
                ->select(
                    'accounts.id',
                    'accounts.nama',
                    'accounts.nisn',
                    'accounts.nomor_registrasi',
                    'accounts.jenis_kejuruan',
                    'setting_duration.durasi',
                    'setting_gelombang.nama_gelombang'
                )
                ->first();

        if (!$siswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data peserta tidak ditemukan'
            ], 404);
        }

        $jumlah_soal = 50;
        $datetime = Carbon::now()->translatedFormat('d F Y, H:i'). 'WIB';

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn,
                'gelombang' => $siswa->id_gelombang,
                'jenis_umum' => $siswa->jenis_umum,
                'jenis_kejuruan' => $siswa->jenis_kejuruan,
                'status' => $siswa->status,
                'jumlah_soal' => $jumlah_soal,
                'datetime' => $datetime
            ]
        ]);
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
}
