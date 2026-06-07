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
            [
                'nomor_registrasi' => '955794', 
                'nama' => 'Muhammad Rafly Wadson', 
                'nisn' => '0118059754', 
                'jurusan_pertama' => 'TJKT', 
                'jurusan_kedua' => 'PPLG', 
                'jenis_umum' => 'umum_an_rpl_tkj', 
                'tanggal_lahir' => '07/06/2026',
                'jenis_kelamin' => 'Laki - Laki', 
                'id_gelombang' => '4', 
                'status' => 'aktif'
            ],     
            [
                'nomor_registrasi' => '123456', 
                'nama' => 'Desi Permatasari', 
                'nisn' => '0123456789', 
                'jurusan_pertama' => 'MP', 
                'jurusan_kedua' => 'AK', 
                'jenis_umum' => 'umum_mp_ak',
                'tanggal_lahir' => '07/06/2026',
                'jenis_kelamin' => 'Perempuan', 
                'id_gelombang' => '4', 
                'status' => 'aktif'
            ],        
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
