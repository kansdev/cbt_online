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
            ['nomor_registrasi' => '510042', 'nama' => 'Latifah Royyana', 'nisn' => '1111111111', 'jurusan' => 'MP', 'kategori' => 'mp_ak', 'jenis_umum' => 'umum_mp_ak', 'jenis_kejuruan' => 'kejurump_ak', 'jenis_kelamin' => 'Perempuan', 'id_gelombang' => '3', 'status' => 'aktif'],
            // ['nomor_registrasi' => '950940', 'nama' => 'Jasmine Kyla Rendyta', 'nisn' => '0106818889', 'jurusan' => 'AK', 'kategori' => 'mp_ak', 'jenis_umum' => 'umum_mp_ak', 'jenis_kejuruan' => 'kejuruan_mp_ak', 'jenis_kelamin' => 'Laki - Laki', 'id_gelombang' => '3', 'status' => 'aktif'],
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
