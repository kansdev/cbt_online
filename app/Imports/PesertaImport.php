<?php

namespace App\Imports;
use App\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    */
    public function model(array $row )
    {
        return new Account([
            'nomor_registrasi' => $row['nomor_registrasi'],
            'nama' => $row['nama'],
            'nisn' => $row['nisn'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'jurusan_pertama' => $row['jurusan_pertama'],
            'jurusan_kedua' => $row['jurusan_kedua'],
            'jenis_umum' => $row['jenis_umum'],
            'tanggal_lahir' => $row['tanggal_lahir'],
            'id_gelombang' => $row['id_gelombang'],
            'status' => $row['status']
        ]);
    }
}
