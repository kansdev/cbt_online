<?php

namespace App\Imports;

use App\Models\Soal;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SoalImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    */
    public function model(array $row )
    {
        return new Soal([
            'kategori' => $row['kategori'],
            'pertanyaan' => $row['pertanyaan'],
            'jawaban_a' => $row['jawaban_a'],
            'jawaban_b' => $row['jawaban_b'],
            'jawaban_c' => $row['jawaban_c'],
            'jawaban_d' => $row['jawaban_d'],
            'jawaban_e' => $row['jawaban_e'],
            'kunci_jawaban' => $row['kunci_jawaban']
        ]);
    }
}
