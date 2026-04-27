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
                foreach($items as $item) {
                    if($item->jawaban === $item->soal->kunci_jawaban) {
                        $benar++;
                    } else {
                        $salah++;
                    }
                }

                // TOTAL SOAL (bukan jumlah jawaban!)
                $jumlah_soal = Soal::count();

                $jumlah_jawaban = Jawaban::where('id_siswa', $items[0]->id_siswa)->count();

                //Jumlah soal yang di jawab
                $jumlah_soal_acak = SoalAcak::where('id_siswa', $items[0]->id_siswa)->count();

                $soal_tidak_dijawab = $jumlah_soal - $jumlah_soal_acak;
                $nilai = $jumlah_soal > 0
                    ? round(($benar / $jumlah_soal) * 100, 2)
                    : 0;

                return [
                    'id_siswa' => $items[0]->id_siswa,
                    'name' => $items[0]->account->name,
                    'jumlah_soal' => $jumlah_soal,
                    'benar' => $benar,
                    'salah' => $salah,
                    'soal_tidak_dijawab' => $soal_tidak_dijawab ?? '0',
                    'nilai' => $nilai
                ];
            });
    }

    public function headings(): array {
        return ["ID Siswa", "Nama Lengkap", "Jumlah Soal", "Jawaban Benar", "Jawaban Salah", "Tidak Menjawab", "Nilai"];
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
                $sheet->setCellValue('B5', ': Gelombang 3');
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
