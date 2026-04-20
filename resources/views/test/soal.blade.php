@extends('test.main')

@section('content')
    <!-- Judul & Timer -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold mb-0">Ujian Satuan Pendidikan</h3>
            <span class="badge bg-primary">Latihan Soal</span>
        </div>

        

        <div class="text-end">
            <small class="text-muted d-block">Sisa Waktu</small>
            <h5 id="timer" class="text-danger fw-bold mb-0"></h5>
        </div>
    </div>

    {{-- Tampilan tombol untuk menampilkan jumlah soal dari nomor 1 sampai sekian berdasarkan tahap ujian  --}}

    {{-- <div class="mb-3">
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNomor">
            Lihat Nomor Soal
        </button>
    </div> --}}
    <!-- Kartu Soal -->
    <div class="card quiz-card p-4 p-md-5">
        <div class="mb-4">
            <span class="text-muted fw-bold">Pertanyaan Ke {{ $nomor }}</span>
            <h4 class="mt-2 lh-base">{{ $soal->soal->pertanyaan }}</h4>
        </div>

        <form id="formJawaban">
            <input type="hidden" id="id_siswa" value="{{ $soal->id_siswa }}">
            <input type="hidden" id="id_soal" value="{{ $soal->id_soal }}">
            <input type="hidden" id="urutan" value="{{ $soal->urutan }}">
            <input type="hidden" id="tahap" value="{{ $soal->tahap }}">
            @php
                $jawaban = [
                    'A' => $soal->soal->jawaban_a,
                    'B' => $soal->soal->jawaban_b,
                    'C' => $soal->soal->jawaban_c,
                    'D' => $soal->soal->jawaban_d,
                    'E' => $soal->soal->jawaban_e,
                ];
            @endphp

            @foreach (['A','B','C','D','E'] as $opt)
                <div class="option-container">
                    <input type="radio" class="btn-check" name="jawaban" id="opt{{ $loop->index + 1 }}" value="{{ $opt }}" @if ($jawaban_saat_ini && $jawaban_saat_ini->jawaban == $opt)
                checked
            @endif>
                    <label class="option-label" for="opt{{ $loop->index + 1 }}">
                        <span class="option-badge">{{ $opt }}</span>
                        <span>{{ $jawaban[$opt] }}</span>
                    </label>
                </div>
            @endforeach

            <!-- Navigasi -->
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-primary btn-lg px-5 shadow-sm" onclick="simpanJawaban()">
                    Selanjutnya<i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="modal fade" id="modalNomor" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="d-flex flex-wrap gap-2">

                        @foreach ($semua_soal as $index => $s)
                            @php
                                $sudah = in_array($s->id_soal, $jawaban_user);
                            @endphp

                            <a href="{{ route('ujian.soal', $s->id_siswa) }}?no={{ $s->urutan }}"
                            class="btn btn-sm {{ $sudah ? 'btn-success' : 'btn-secondary' }}">
                                {{ $index + 1 }}
                            </a>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function simpanJawaban() {
            let jawaban = document.querySelector('input[name="jawaban"]:checked');
            if (!jawaban) {
                alert('Pilih jawaban dulu!');
                return;
            }

            fetch('/ujian/simpan_jawaban', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id_siswa: document.getElementById('id_siswa').value,
                    id_soal: document.getElementById('id_soal').value,
                    jawaban: jawaban.value,
                    urutan: document.getElementById('urutan').value,
                    tahap: document.getElementById('tahap').value
                })
            })
            .then(res => res.json())
            .then(res => {
                console.log('RESPONSE:', res);

                if (res.status) {
                    console.log('jawaban berhasil disimpan');
                    location.reload();
                } else {
                    console.log('gagal:', res.message);
                    alert(res.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
            });
        }
    </script>

    <script>
        let sisa = {{ $sisa_waktu }}; // Ini adalah total detik dari PHP
        const display = document.getElementById('timer');

        const x = setInterval(function() {
            if (sisa > 0) {
                sisa--;

                // 1. Hitung Jam, Menit, dan Detik
                // Math.floor digunakan untuk membulatkan angka ke bawah
                let hours = Math.floor(sisa / 3600);
                let minutes = Math.floor((sisa % 3600) / 60);
                let seconds = sisa % 60;

                // 2. Tambahkan angka "0" di depan jika angka di bawah 10 agar formatnya konsisten (01, 02, dst)
                let displayHours = hours < 10 ? "0" + hours : hours;
                let displayMinutes = minutes < 10 ? "0" + minutes : minutes;
                let displaySeconds = seconds < 10 ? "0" + seconds : seconds;

                // 3. Tampilkan di layar
                // Jika durasi di bawah 1 jam, cukup tampilkan Menit:Detik saja
                if (hours > 0) {
                    display.innerText = `${displayHours}:${displayMinutes}:${displaySeconds}`;
                } else {
                    display.innerText = `${displayMinutes}:${displaySeconds}`;
                }

            } else {
                clearInterval(x);
                display.innerText = "Waktu Habis";

                // 🔥 redirect ke backend biar trigger controller
                setTimeout(() => {
                    window.location.href = "/ujian/mulai/1";
                }, 1000);
            }
        }, 1000);
    </script>
@endsection
