<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'AMANDA AZKIYAH', 'nisn' => '0085370210', 'jurusan' => 'RPL'],
            ['name' => 'ARZICKY ZAPRAN', 'nisn' => '0081663557', 'jurusan' => 'RPL'],
            ['name' => 'EARLY ATHALLAH RIZKY', 'nisn' => '0072499392', 'jurusan' => 'RPL'],
            ['name' => 'FAREL ARDIYANSYAH', 'nisn' => '0083587043', 'jurusan' => 'RPL'],
            ['name' => 'FAUZANA FIKRI', 'nisn' => '0081299921', 'jurusan' => 'RPL'],
            ['name' => 'KHAFIYAN NURUL HAKIM', 'nisn' => '0077650865', 'jurusan' => 'RPL'],
            ['name' => 'LEKSA DIAN TIARA', 'nisn' => '0081262240', 'jurusan' => 'RPL'],
            ['name' => 'MUHAMMAD ABYAN GANI', 'nisn' => '0078100845', 'jurusan' => 'RPL'],
            ['name' => 'MUHAMMAD FADHLIH FABBYAN', 'nisn' => '0087684830', 'jurusan' => 'RPL'],
            ['name' => 'MUHAMMAD QOLBY GHIYAS WIYONO PUTRA', 'nisn' => '0081864295', 'jurusan' => 'RPL'],
            ['name' => 'RADITYA ALIA PRATAMA', 'nisn' => '0088842779', 'jurusan' => 'RPL'],
            ['name' => 'RAFAEL SHAQR AL FARRAS', 'nisn' => '0073970428', 'jurusan' => 'RPL'],
            ['name' => 'RAHMAT SITO PAMBUDI', 'nisn' => '0087069490', 'jurusan' => 'RPL'],
            ['name' => 'REYHAN AHMAD ARDIAN', 'nisn' => '0089543002', 'jurusan' => 'RPL'],
            ['name' => 'SAMIANJAB KALYA DIAR', 'nisn' => '0085919322', 'jurusan' => 'RPL'],
            ['name' => 'SILVIA SABELA NATANEILA', 'nisn' => '0088759877', 'jurusan' => 'RPL'],
            ['name' => 'VINO PRADA SAKTI SAPUTRA', 'nisn' => '0081682254', 'jurusan' => 'RPL'],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
