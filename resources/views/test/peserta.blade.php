<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mulai Ujian - Sistem Ujian Online</title>
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <style>
            body { 
                background-color: #f4f7f6; 
                font-family: 'Inter', sans-serif; 
            }

            .start-card { 
                border: none; 
                border-radius: 20px; 
                overflow: hidden; 
                box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            }

            .header-gradient { 
                background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%); 
                color: white; 
                padding: 40px 20px; 
            }

            .info-box { 
                background-color: #f8f9fa; 
                border-radius: 12px; 
                padding: 20px; 
                border-left: 5px solid #0d6efd; 
            }

            .rule-list li { 
                margin-bottom: 10px; 
                color: #495057; 
            }
        </style>
    </head>
    <body>

        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    
                    <div class="card start-card">
                        <!-- Header Visual -->
                        <div class="header-gradient text-center">
                            <h1 class="fw-bold mb-2">Latihan USP</h1>
                            <p class="lead mb-0 opacity-75">Tahun Ajaran 2025/2026</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <div class="row g-4">
                                <!-- Kolom Kiri: Detail Peserta -->
                                <div class="col-md-6">
                                    <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Peserta</h5>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block">Nama Lengkap</label>
                                        <span class="fw-semibold fs-5">{{ $siswa->name }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block">Nomor NISN</label>
                                        <span class="fw-semibold fs-5">{{ $siswa->nisn }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block">Mata Pelajaran</label>
                                        <span class="badge bg-primary fs-6">Program Keahlian RPL</span>
                                    </div>
                                    <div class="mb-4">
                                        <label class="text-muted small d-block">Tanggal Ujian</label>
                                        <span class="fw-semibold fs-5">{{ $datetime }}</span>
                                    </div>
                                    
                                    <div class="info-box shadow-sm">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-auto text-primary">Detail Ujian:</strong>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="d-block text-muted">Durasi</small>
                                                <span class="fw-bold">60 Menit</span>
                                            </div>
                                            <div class="col-6 border-start">
                                                <small class="d-block text-muted">Total Soal</small>
                                                <span class="fw-bold">50 Soal</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Tata Tertib -->
                                <div class="col-md-6">
                                    <h5 class="fw-bold mb-3 border-bottom pb-2">Instruksi & Tata Tertib</h5>
                                    <ul class="rule-list small ps-3">
                                        <li>Pastikan koneksi internet Anda stabil selama ujian berlangsung.</li>
                                        <li>Dilarang membuka tab lain atau aplikasi lain di perangkat Anda.</li>
                                        <li>Jawaban akan tersimpan secara otomatis setiap kali Anda menekan tombol 'Selanjutnya'.</li>
                                        <li>Ujian akan berakhir otomatis jika waktu habis.</li>
                                        <li>Klik tombol "Mulai Sekarang" jika Anda sudah siap.</li>
                                    </ul>
                                </div>
                            </div>

                            <hr class="my-5">

                            <!-- Action Button -->
                            <div class="text-center">
                                <p class="text-danger small mb-3">*Setelah menekan tombol mulai, waktu akan segera berjalan.</p>
                                <a id="btnStart" class="btn btn-primary btn-lg px-5 shadow" href="{{ route('ujian.mulai', $siswa->id) }}">
                                    Mulai Sekarang <i class="bi bi-play-fill ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <p class="text-center mt-4 text-muted">CBT Online - V1.0.beta &copy; 2024</p>
                </div>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    </body>
</html>