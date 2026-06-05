<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Pewawancara;

class PewawancaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pewawancara = [
            ['nip' => '1234', 'nama' => 'Budi Santoso'],
            ['nip' => '4321', 'nama' => 'Siti Aminah'],
        ];

        foreach ($pewawancara as $data) {
            Pewawancara::create($data);
        }
    }
}
