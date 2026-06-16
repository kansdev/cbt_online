<?php

namespace App\Exports;

use App\Models\Jawaban;
use App\Models\Soal;
use App\Models\SoalAcak;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class KoreksiExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell, ShouldAutoSize
{
    // Memulai data di baris A7
    public function startCell(): string {
        return 'A7';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Jawaban::with(['soal', 'account'])
            ->get()
            ->groupBy('id_siswa')
            ->map(function($items) {
                $benar = 0;
                $salah = 0;

                // TOTAL SOAL (bukan jumlah jawaban!)
                // $jumlah_soal = Soal::count();

                $allJawabanSiswa = Jawaban::with('soal')->where('id_siswa', $items[0]->id_siswa)->get();

                $totalUmum = SoalAcak::where('id_siswa', $items[0]->id_siswa)->where('tahap', 'umum')->count();
                $totalJurusanPertama = SoalAcak::where('id_siswa', $items[0]->id_siswa)->where('tahap', 'kejuruan_pertama')->distinct('id_soal')->count('id_soal');
                $totalJurusanKedua = SoalAcak::where('id_siswa', $items[0]->id_siswa)->where('tahap', 'kejuruan_kedua')->distinct('id_soal')->count('id_soal');

                $umum = ['benar' => 0, 'salah' => 0, 'total' => $totalUmum];
                $jurusanPertama = ['benar' => 0, 'salah' => 0, 'total' => $totalJurusanPertama];
                $jurusanKedua = ['benar' => 0, 'salah' => 0, 'total' => $totalJurusanKedua];
                foreach($allJawabanSiswa as $item) {
                    // Gunakan optional() untuk menghindari error jika soal terhapus di DB
                    $kunci = optional($item->soal)->kunci_jawaban;
                    $isBenar = $item->jawaban === $kunci;

                    if ($item->tahap === 'umum') {
                        $isBenar ? $umum['benar']++ : $umum['salah']++;
                    } else if($item->tahap === 'kejuruan_pertama') {
                        $isBenar ? $jurusanPertama['benar']++ : $jurusanPertama['salah']++;
                    } else {
                        $isBenar ? $jurusanKedua['benar']++ : $jurusanKedua['salah']++;
                    }
                }
                $skor_umum = $totalUmum > 0 ? round(($umum['benar'] / $totalUmum) * 100, 2) : 0;
                $skor_kejuruan_pertama = $totalJurusanPertama > 0 ? round(($jurusanPertama['benar'] / $totalJurusanPertama) * 100, 2) : 0;
                $skor_kejuruan_kedua = $totalJurusanKedua > 0 ? round(($jurusanKedua['benar'] / $totalJurusanKedua) * 100, 2) : 0;

                $total_soal = $totalUmum + $totalJurusanPertama + $totalJurusanKedua;

                return [
                    'id_siswa' => $items[0]->id_siswa,
                    'nama' => $items[0]->account->nama,
                    'jumlah_soal' => $total_soal,
                    'nilai_umum' => $skor_umum,
                    'nilai_jurusan_pertama' => $skor_kejuruan_pertama,
                    'nilai_jurusan_kedua' => $skor_kejuruan_kedua
                ];
            });
    }

    public function headings(): array {
        return ["ID Siswa", "Nama Lengkap", "Jumlah Soal", "Nilai Umum", "Nilai Jurusan Pertama", "Nilai Jurusan Kedua"];
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Buat judul dokument
                $sheet->mergeCells('A1:G2');
                $sheet->setCellValue('A1', 'Hasil Tes SPMB SMK Nusantara 1 Kota Tangerang');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(20);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center')->setVertical('center');

                // Informasi dokumen
                $sheet->setCellValue('A4', 'Tanggal Tes');
                $sheet->setCellValue('B4', ': ' . date('d m Y'));
                $sheet->setCellValue('A5', 'Gelombang');
                $sheet->setCellValue('B5', ': Gelombang 4');
                $sheet->getStyle('A4:A5')->getFont()->setBold(true);

                // Buat border
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ]
                ];

                // Header table
                $sheet->getStyle('A7:G7')->getFont()->setBold(true);
                $sheet->getStyle('A7:G7')->getAlignment()->setHorizontal('center')->setVertical('center');

                // Memasang border
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A7:G' . $lastRow)->applyFromArray($styleArray);
            }
        ];
    }
}
