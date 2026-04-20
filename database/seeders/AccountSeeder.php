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
            ['name' => 'AMANDA AZKIYAH', 'nisn' => '0085370210', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Perempuan', 'status' => 'nonaktif'],
            ['name' => 'ARZICKY ZAPRAN', 'nisn' => '0081663557', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'EARLY ATHALLAH RIZKY', 'nisn' => '0072499392', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'FAREL ARDIYANSYAH', 'nisn' => '0083587043', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'FAUZANA FIKRI', 'nisn' => '0081299921', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-Laki', 'status' => 'nonaktif'],
            ['name' => 'KHAFIYAN NURUL HAKIM', 'nisn' => '0077650865', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],            
            ['name' => 'LEKSA DIAN TIARA', 'nisn' => '0081262240', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Perempuan', 'status' => 'nonaktif'],
            ['name' => 'MUHAMMAD ABYAN GANI', 'nisn' => '0078100845', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'MUHAMMAD FADHLIH FABBYAN', 'nisn' => '0087684830', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'MUHAMMAD QOLBY GHIYAS WIYONO PUTRA', 'nisn' => '0081864295', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'RADITYA ALIA PRATAMA', 'nisn' => '0088842779', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'RAFAEL SHAQR AL FARRAS', 'nisn' => '0073970428', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'RAHMAT SITO PAMBUDI', 'nisn' => '0087069490', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'REYHAN AHMAD ARDIAN', 'nisn' => '0089543002', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'SAMIANJAB KALYA DIAR', 'nisn' => '0085919322', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif'],
            ['name' => 'SILVIA SABELA NATANEILA', 'nisn' => '0088759877', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Perempuan', 'status' => 'nonaktif'],
            ['name' => 'VINO PRADA SAKTI SAPUTRA', 'nisn' => '0081682254', 'jurusan' => 'RPL', 'jenis_kelamin' => 'Laki-laki', 'status' => 'nonaktif']
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
