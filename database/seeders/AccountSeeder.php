<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Account;
use App\Models\LogActivityAdmin;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['nomor_registrasi' => '188672', 'nama' => 'Ghina Alifatunnisa', 'nisn' => '1122334455', 'jurusan' => 'DKV', 'kategori' => 'an_dkv_bp', 'jenis_umum' => 'umum_an_dkv_bp', 'jenis_kejuruan' => 'kejuruan_an_dkv_bp', 'jenis_kelamin' => 'Perempuan', 'id_gelombang' => '3', 'status' => 'aktif'],
        ];
        foreach ($accounts as $account) {
            Account::create($account);
            LogActivityAdmin::create([
                'activity' => "Membuat data akun: {$account['nama']} (NISN: {$account['nisn']})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        }
    }
}
