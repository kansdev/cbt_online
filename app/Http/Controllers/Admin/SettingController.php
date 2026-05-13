<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Account;
use App\Models\SettingWaktuTes;
use App\Models\SettingGelombang;
use App\Models\SettingAntiInspectElement;

class SettingController extends Controller
{
    public function settings() {
        $setting_gelombang = SettingGelombang::all();
        $setting_anti_inspect = SettingAntiInspectElement::first();

        return view('admin.pages.settings', compact('setting_gelombang', 'setting_anti_inspect'));
    }

    public function settings_waktu_tes(Request $request) {
        // Buat validasi untuk input gelombang, durasi dan tanggal mulai
         $validate = $request->validate([
            'gelombang' => 'required',
            'durasi' => 'required|integer',
            'tanggal_mulai' => 'required|date:m/d/y'
        ]);

        // Jika gelombang pada waktu tersebut sudah di setting maka tampilkan pesan 
        $isExist = SettingWaktuTes::where('tanggal_mulai', $validate['tanggal_mulai'])->exists();

        // Pesan jika sesi waktu untuk gelombang ini sudah di setting 
        if ($isExist) return redirect()->back()->with('failed', 'Sesi waktu untuk gelombang ini sudah di setting !!!');

        // Simpan data setting waktu tes ke database
        SettingWaktuTes::create([
            'id_gelombang' => $validate['gelombang'],
            'durasi' => $validate['durasi'],
            'tanggal_mulai' => $validate['tanggal_mulai'],
        ]);

        // Tampilkan pesan berhasil menyimpan data
        return redirect()->back()->with('success', 'Data durasi berhasil disimpan');
    }

    public function settings_gelombang(Request $request)
    {
        // validasi input gelombang, pastikan gelombang berupa array dan minimal 4 gelombang
        $validate = $request->validate([
            'gelombang' => 'required|array|min:4',
            'gelombang.*' => 'required|integer',
            'status' => 'nullable|array'
        ]);

        // Ambil data gelombang dan status dari request
        $gelombang = $validate['gelombang'];
        $status = $validate['status'] ?? [];

        // Loop melalui setiap gelombang dan update atau buat data setting gelombang        
        foreach ($gelombang as $index => $id_gelombang) {

            // Cek jika gelombang di centang maka status akan 1
            $isActive = array_key_exists($index, $status) ? 1 : 0;

            // Update atau buat data setting gelombang
            SettingGelombang::updateOrCreate(
                ['id_gelombang' => $id_gelombang],
                ['status' => $isActive]
            );
        }

        // Tampilkan pesan berhasil menyimpan data
        return redirect()->back()->with('success', 'Gelombang berhasil di buat');
    }

    public function settings_anti_inspect_element(Request $request)
    {
        // Jika cekbox di centang maka value akan 1, jika tidak dicentang value akan 0
        $value = $request->has('anti_inspect') ? 1 : 0;

        // update atau buat data setting anti inspect element
        SettingAntiInspectElement::updateOrCreate(
            ['id' =>  '1'],
            ['status' => $value]
        );

        // Jika berhasil maka tampilkan pesan berhasil
        return redirect()->back()->with('success', 'Berhasil diaktifkan');
    }
}
