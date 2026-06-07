<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Account;
use App\Models\Ujian;

use App\Imports\SoalImport;
use App\Imports\PesertaImport;
use App\Exports\KoreksiExport;
use Maatwebsite\Excel\Facades\Excel;

class ParticipantController extends Controller
{    
    public function peserta()
    {
        // Ambil data peserta dengan pagination 20 data per halaman dan urutkan berdasarkan nama
        $peserta = Account::orderBy('nama', 'asc')->paginate(20);

        // Tampilkan halaman peserta dengan data peserta
        return view('admin.pages.peserta', compact('peserta'));
    }

    public function peserta_aktif()
    {
        // Ambil data peserta yang aktif dengan pagination 20 data per halaman dan urutkan
        $peserta_aktif = Account::orderBy('nama', 'asc')->paginate(20);
        
        // Tampilkan halaman peserta aktif dengan data peserta aktif
        return view('admin.pages.aktif_peserta', compact('peserta_aktif'));
        // dd($peserta_aktif);
    }

     public function reset_peserta()
    {
        // $peserta = Ujian::with('account')->orderBy('account.nama', 'asc')->paginate(20);
        $peserta = Account::has('ujian')->with('ujian')->orderBy('nama', 'asc')->paginate(20);
        return view('admin.pages.reset', compact('peserta'));
    }

    public function tambah_peserta(Request $request) 
    {
        try {
            $validate = $request->validate([
                'nomor_registrasi' => 'required',
                'nama' => 'required',
                'nisn' => 'required',
                'jenis_kelamin' => 'required',
                'jurusan' => 'required',
                'kategori' => 'required',
                'jenis_umum' => 'required',
                'jenis_kejuruan' => 'required',
                'tanggal_lahir' => 'required',
                'id_gelombang' => 'required',
                'status' => 'required'
            ]);

            Account::create($validate);

            return redirect()->route('admin.peserta')->with('success', 'Berhasil menambahkan peserta baru. ');
        } catch (\Exception $e) {
            return redirect()->route('admin.peserta')->with('failed', 'Gagal menambahkan peserta baru. ' . $e->getMessage());
        }
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

    public function aktifkan_peserta_pergelombang(Request $request)
    {
        try {
            $validated = $request->validate([
                'gelombang' => 'required'
            ]);

            Account::where('gelombang', $validated['gelombang'])->update(['status' => 'aktif']);

            return redirect()->back()->with('success', 'Berhasil aktifkan peserta dengan gelombang ' . $validated['gelombang']);

        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Gagal mengaktifkan peserta !!!');
        }
    }

    public function nonaktifkan_peserta_pergelombang(Request $request)
    {
        try {
            $validated = $request->validate([
                'gelombang' => 'required'
            ]);

            Account::where('gelombang', $validated['gelombang'])->update(['status' => 'nonaktif']);

            return redirect()->back()->with('success', 'Berhasil aktifkan peserta dengan gelombang ' . $validated['gelombang']);

        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Gagal mengaktifkan peserta !!!');
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
    function importPeserta(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv',
            ]);

            $file = $request->file('file');
            $import = new PesertaImport;
            Excel::import($import, $file);

            return redirect()->back()->with('success', 'Peserta berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Peserta gagal diimpor! : ' . $e->getMessage());
        }

    }


}
