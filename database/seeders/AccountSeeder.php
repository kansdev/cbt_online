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
            ['name' => 'Siswa 1', 'nisn' => '0011223344', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Perempuan', 'id_gelombang' => '1', 'status' => 'nonaktif'],
            ['name' => 'Siswa 2', 'nisn' => '0055667788', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '3', 'status' => 'nonaktif'],
            ['name' => 'Siswa 3', 'nisn' => '0099112233', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '2', 'status' => 'nonaktif'],
            ['name' => 'Siswa 4', 'nisn' => '0033445566', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '4', 'status' => 'nonaktif'],
            ['name' => 'Siswa 5', 'nisn' => '0077889911', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-Laki', 'id_gelombang' => '2', 'status' => 'nonaktif'],
        ];
        foreach ($accounts as $account) {
            Account::create($account);
            LogActivityAdmin::create([
                'activity' => "Membuat data akun: {$account['name']} (NISN: {$account['nisn']})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        }
    }
}
