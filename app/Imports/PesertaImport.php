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
            'jurusan' => $row['jurusan'],
            'kategori' => $row['kategori'],
            'jenis_umum' => $row['jenis_umum'],
            'jenis_kejuruan' => $row['jenis_kejuruan'],
            'tanggal_lahir' => $row['tanggal_lahir'],
            'id_gelombang' => $row['id_gelombang'],
            'status' => $row['status']
        ]);
    }
}
