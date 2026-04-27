<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\Soal;
use App\Models\SoalAcak;
use App\Models\Jawaban;
use App\Models\Ujian;
use App\Models\LogsActivityUser;
use App\Models\SettingWaktuTes;
use App\Models\SettingGelombang;

use App\Imports\SoalImport;
use App\Exports\KoreksiExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index()
    {
        $peserta = Account::count();
        $laki_laki = Account::where('jenis_kelamin', 'Laki-Laki')->count();
        $perempuan = Account::where('jenis_kelamin', 'Perempuan')->count();

        $log = LogsActivityUser::with('account')->orderBy('created_at', 'desc')->get();
        return view('admin.pages.beranda', compact('peserta', 'laki_laki', 'perempuan', 'log'));
    }

    // Clear Log
    public function clear_log() 
    {
        try {
            $logs = LogsActivityUser::query()->delete();

            // Cek jika log kosong tampilkan warning
            if (empty($logs)) {
                return redirect()->back()->with('warning', 'Logs is empty !!!');
            }

            return redirect()->back()->with('success', 'Clear Log Successfully !!!');
        } catch(\Exception $e) {
            return redirect()->back()->with('failed', 'Clear Log Failed !!!');
        }        
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

    public function peserta_aktif() 
    {
        $peserta_aktif = Account::all();
        return view('admin.pages.aktif_peserta', compact('peserta_aktif'));
    }

    public function reset_peserta() {
        $peserta = Ujian::with('account')->get();
        return view('admin.pages.reset', compact('peserta'));
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

    public function aktifkan_seluruh_peserta() 
    {
        try {
            Account::where('status', '!=', 'aktif')->update([
                'status' => 'aktif'
            ]);
            return back()->with('success', 'Semua peserta sudah aktif');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal mengaktifkan peserta. ' . $e->getMessage());
        }
    }

    public function nonaktifkan_seluruh_peserta() 
    {
        try {
            Account::where('status', '!=', 'nonaktif')->update([
                'status' => 'nonaktif'
            ]);
            return back()->with('success', 'Semua peserta sudah di nonaktifkan');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal menonaktifkan peserta. ' . $e->getMessage());
        }
    }

    public function nonaktifkan_peserta($id) 
    {
        try {
            $peserta = Account::findOrFail($id);

            if ($peserta->status === 'nonaktif') return;

            $peserta->update([
                'status' => 'nonaktif'
            ]);

            return redirect()->route('admin.aktif_peserta');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal menonaktifkan peserta. ' . $e->getMessage());
        }
    }

    public function aktifkan_peserta($id) 
    {
        try {
            $peserta = Account::findOrFail($id);

            if ($peserta->status === 'aktif') return;

            $peserta->update([
                'status' => 'aktif'
            ]);

            return redirect()->route('admin.aktif_peserta');
        } catch (\Exception $e) {
            return back()->with('failed', 'Gagal aktifkan peserta. ' . $e->getMessage());
        }
    }

    // Fungsi untuk upload soal dari file Excel
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

    public function reset($id_siswa)
    {
        try {
            // Hapus semua data ujian, soal acak, dan jawaban untuk siswa tersebut
            Jawaban::where('id_siswa', $id_siswa)->delete();
            SoalAcak::with('soal')->where('id_siswa', $id_siswa)->delete();
            Ujian::where('id_siswa', $id_siswa)->delete();

            // dd([$jawaban, $soal, $ujian]);
            return redirect()->back()->with('success', 'Berhasil reset ujian');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal reset ujian : '. $e->getMessage());
        }

    }

    // Unduh hasil jawaban
    public function unduh_hasil_jawaban() 
    {
        return Excel::download(new KoreksiExport, 'hasil_test.xlsx');
    }

    // Settings
    public function settings() {
        $setting_gelombang = SettingGelombang::all();
        return view('admin.pages.settings', compact('setting_gelombang'));
    }

    public function settings_waktu_tes(Request $request) {
        try {
            $validate = $request->validate([
                'gelombang' => 'required',
                'durasi' => 'required|integer',
                'tanggal_mulai' => 'required|date:m/d/y'
            ]);

            $isExist = SettingWaktuTes::where('id_gelombang', $validate['gelombang'])->exists();

            if ($isExist) {
                return redirect()->back()->with('failed', 'Sesi waktu untuk gelombang ini sudah di setting !!!');
            }

            SettingWaktuTes::create([
                'id_gelombang' => $validate['gelombang'],
                'durasi' => $validate['durasi'],
                'tanggal_mulai' => $validate['tanggal_mulai'],
            ]);

            return redirect()->back()->with('success', 'Data durasi berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Data durasi gagal disimpan, keterangan : ' . $e->getMessage());
        }
    }

    public function settings_gelombang(Request $request) {
        try {
            $validate = $request->validate([
                'gelombang' => 'required|array|min:4',
                'gelombang.*' => 'required|integer',
                'status' => 'nullable|array'
            ]);

            $gelombang = $request->input('gelombang');
            $status = $request->input('status', []);

            foreach ($gelombang as $index => $id_gelombang) {
                $isActive = array_key_exists($index, $status) ? 1 : 0;

                SettingGelombang::updateOrCreate(
                    ['id_gelombang' => $id_gelombang],
                    ['status' => $isActive]
                );
            }

            return redirect()->back()->with('success', 'Gelombang berhasil di buat');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
