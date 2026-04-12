<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $soals = [
            ['kategori' => 'umum', 'pertanyaan' => 'Soal umum 1', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'umum', 'pertanyaan' => 'Soal umum 2', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'umum', 'pertanyaan' => 'Soal umum 3', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'rpl', 'pertanyaan' => 'Soal RPL 1', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'rpl', 'pertanyaan' => 'Soal RPL 2', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
        ];

        foreach ($soals as $s) {
            \App\Models\Soal::create($s);
        }
    }
}
