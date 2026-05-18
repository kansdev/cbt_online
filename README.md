<p align="center"><a href="#" target="_blank"><img src="resources/img/logo.png" width="400" alt="Laravel Logo"></a></p>

## Ringkas Peningkatan (Apa yang Diperbaiki?) Update 18/05/2025

Sering coding membuat aplikasi computer based test untuk sistem tes berbasis komputer yang diharapkan berguna untuk seluruh instansi yang ingin melakukan tes secara online menggunakan komputer.

Berikut adalah beberapa perbaikan yang dilakukan :

- Abstraksi `hitung_skor` : Menghilangkan ratusan baris kode duplikat. Sekarang perhitungan nilai dipusatkan pada satu fungsi privat internal.
- Keamanan Query Relasional: Penambahan penutup fungsi `where(function($query) ...)` menjamin data antar-siswa tidak bocor saat pencarian berbasis NISN atau Nomor Registrasi.

- Efisiensi Memori (Database RAM): Penghapusan `with('soal')`sebelum memanggil `count()` meringankan kerja server MySQL Anda secara drastis saat menangani traffic tinggi.

- Pembersihan Logika Kadaluwarsa: Menghilangkan kode periksa ulang sisa waktu fungsi `simpan_jawaban`.

## DEMO

Link demo belum tersedia.

## License [MIT](https://choosealicense.com/licenses/mit/)

Hak cipta (c) [tahun] [nama lengkap]

Dengan ini diberikan izin, tanpa biaya, kepada siapa pun yang memperoleh salinannya dari perangkat lunak ini dan file dokumentasi terkait ("Perangkat Lunak"), untuk menangani dalam Perangkat Lunak tanpa batasan, termasuk tanpa batasan hak-hak untuk menggunakan, menyalin, memodifikasi, menggabungkan, penerbitkan, mendistribusikan, mensublisensikan, dan/atau menjual salinan Perangkat Lunak, dan untuk mengizinkan orang-orang yang memiliki Perangkat Lunak tersebut dilengkapi untuk melakukan hal tersebut, dengan tunduk pada kondisi-kondisi berikut:

Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam semua salinan atau sebagian besar dari Perangkat Lunak.

PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA GARANSI APA PUN, BAIK TERSURAT MAUPUN TERSIRAT TERSIRAT, TERMASUK NAMUN TIDAK TERBATAS PADA GARANSI KELAYAKAN DAGANG, KESESUAIAN UNTUK TUJUAN TERTENTU DAN TIDAK ADANYA PELANGGARAN HAK CIPTA. DALAM KEADAAN APA PUN, PENULIS ATAU PEMEGANG HAK CIPTA TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN, ATAU HAL LAINNYA TANGGUNG JAWAB, BAIK DALAM TINDAKAN KONTRAK, PERBUATAN MELANGGAR HUKUM ATAU LAINNYA, YANG TIMBUL DARI, BERKAITAN DENGAN ATAU BERHUBUNGAN DENGAN PERANGKAT LUNAK ATAU PENGGUNAAN ATAU TRANSAKSI LAINNYA DALAM PERANGKAT LUNAK.
