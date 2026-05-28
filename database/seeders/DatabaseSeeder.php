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
        // User::factory(2)->create();

        $soals = [
            ['kategori' => 'mtk_umum', 'jenis_soal' => 'umum', 'pertanyaan' => 'Soal umum 1', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'bindo_manajemen', 'jenis_soal' => 'umum_mp_ak', 'pertanyaan' => 'Soal umum 2', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'bindo_seni', 'jenis_soal' => 'umum_an_dkv_bp', 'pertanyaan' => 'Soal umum 3', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'bindo_teknik', 'jenis_soal' => 'umum_rpl_tkj', 'pertanyaan' => 'Soal umum 4', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'binggris_manajemen', 'jenis_soal' => 'umum_mp_ak', 'pertanyaan' => 'Soal umum 5', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'binggris_seni', 'jenis_soal' => 'umum_an_dkv_bp', 'pertanyaan' => 'Soal umum 6', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'binggris_teknik', 'jenis_soal' => 'umum_rpl_tkj', 'pertanyaan' => 'Soal umum 7', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'an_dkv_bp', 'jenis_soal' => 'kejuruan_an_dkv_bp', 'pertanyaan' => 'Soal jurusan 1', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'mp_ak', 'jenis_soal' => 'kejuruan_mp_ak', 'pertanyaan' => 'Soal jurusan 2', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
            ['kategori' => 'rpl_tkj', 'jenis_soal' => 'kejuruan_rpl_tkj', 'pertanyaan' => 'Soal jurusan 3', 'gambar' => '', 'jawaban_a' => 'Jawaban A', 'jawaban_b' => 'Jawaban B', 'jawaban_c' => 'Jawaban C', 'jawaban_d' => 'Jawaban D', 'jawaban_e' => 'Jawaban E', 'kunci_jawaban' => 'A'],
        ];

        foreach ($soals as $s) {
            \App\Models\Soal::create($s);
        }
    }
}
