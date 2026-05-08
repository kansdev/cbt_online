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
            ['nomor_registrasi' => '112233', 'nama' => 'Siswa 1', 'nisn' => '0011223344', 'jurusan' => 'RPL', 'kategori' => 'rpl_tkj', 'jenis_umum' => 'umum_rpl_tkj', 'jenis_kejuruan' => 'kejuruan_rpl_tkj', 'jenis_kelamin' => 'Perempuan', 'id_gelombang' => '3', 'status' => 'nonaktif'],
            ['nomor_registrasi' => '223311', 'nama' => 'Siswa 2', 'nisn' => '0055667788', 'jurusan' => 'MP', 'kategori' => 'mp_ak', 'jenis_umum' => 'umum_rpl_tkj', 'jenis_kejuruan' => 'kejuruan_mp_ak', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '3', 'status' => 'nonaktif'],
            ['nomor_registrasi' => '332211', 'nama' => 'Siswa 3', 'nisn' => '0033445566', 'jurusan' => 'AK', 'kategori' => 'mp_ak', 'jenis_umum' => 'umum_rpl_tkj', 'jenis_kejuruan' => 'kejuruan_mp_ak', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '3', 'status' => 'nonaktif'],
            ['nomor_registrasi' => '445566', 'nama' => 'Siswa 4', 'nisn' => '0099112233', 'jurusan' => 'AN', 'kategori' => 'an_dkv_bp', 'jenis_umum' => 'umum_an_dkv_bp', 'jenis_kejuruan' => 'kejuruan_an_dkv_bp', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '3', 'status' => 'nonaktif'],
            ['nomor_registrasi' => '556644', 'nama' => 'Siswa 5', 'nisn' => '0044556677', 'jurusan' => 'DKV', 'kategori' => 'an_dkv_bp', 'jenis_umum' => 'umum_an_dkv_bp', 'jenis_kejuruan' => 'kejuruan_an_dkv_bp', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '3', 'status' => 'nonaktif'],
            ['nomor_registrasi' => '664455', 'nama' => 'Siswa 6', 'nisn' => '0088991122', 'jurusan' => 'BP', 'kategori' => 'an_dkv_bp', 'jenis_umum' => 'umum_an_dkv_bp', 'jenis_kejuruan' => 'kejuruan_an_dkv_bp', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '3', 'status' => 'nonaktif'],
            ['nomor_registrasi' => '778899', 'nama' => 'Siswa 7', 'nisn' => '0022334455', 'jurusan' => 'TJKT', 'kategori' => 'rpl_tkj', 'jenis_umum' => 'umum_rpl_tkj', 'jenis_kejuruan' => 'kejuruan_rpl_tkj', 'jenis_kelamin' => 'Laki-laki', 'id_gelombang' => '3', 'status' => 'nonaktif']
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
