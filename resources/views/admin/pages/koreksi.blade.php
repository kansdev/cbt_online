@extends('admin.main')

@section('content')
    <h2 class="mb-4">Koreksi Jawaban</h2>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <h4 class="m-0 fw-bold text-primary">Data Koreksi</h4>

                <div class="d-flex gap-2">
                    <form action="{{ route('admin.koreksi') }}" method="GET" class="d-flex shadow-sm">
                        <input type="text" name="search" class="form-control form-control-sm" 
                            placeholder="Cari nama atau no regis..." value="{{ $search }}">
                        <button type="submit" class="btn btn-primary btn-sm px-4 ms-2">Cari</button>
                        @if($search)
                            <a href="{{ route('admin.koreksi') }}" class="btn btn-secondary btn-sm ms-2">Reset</a>
                        @endif
                    </form>

                    <a href="{{ route('admin.unduh_hasil_jawaban') }}" class="btn btn-success btn-sm d-flex align-items-center">
                        <span>Unduh Hasil</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0" id="dataTable">
                    <thead class="table-light text-center">
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Nama</th>
                            <th colspan="4" class="table-primary">Umum ({{ $detail_jawaban->first()['soal_umum'] ?? 0 }})</th>
                            <th colspan="4" class="table-info">Kejuruan Pertama({{ $detail_jawaban->first()['soal_kejuruan_pertama'] ?? 0 }})</th>
                            <th colspan="4" class="table-info">Kejuruan Kedua({{ $detail_jawaban->first()['soal_kejuruan_kedua'] ?? 0 }})</th>
                            <th rowspan="2" class="align-middle">Total Jawaban Benar</th>
                            <th rowspan="2" class="align-middle">Nilai</th>
                            <th rowspan="2" class="align-middle">Aksi</th>
                        </tr>
                        <tr>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Skor</th>
                            <th>Status</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Skor</th>
                            <th>Status</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Skor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail_jawaban as $d)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $d['nama'] }}</td>
                                <td class="text-center text-success fw-bold">{{ $d['umum']['benar'] }}</td>
                                <td class="text-center text-danger">{{ $d['umum']['salah'] }}</td>
                                <td class="text-center text-danger">{{ $d['skor_umum']}}</td>
                                <td class="text-center">
                                    @if ($d['skor_umum'] > 70)
                                        <span class="badge text-bg-success">PASSED</span>
                                    @else
                                        <span class="badge text-bg-danger">FAILED</span>
                                    @endif
                                </td>
                                <td class="text-center text-success fw-bold">{{ $d['jurusan_pertama']['benar'] }}</td>
                                <td class="text-center text-danger">{{ $d['jurusan_pertama']['salah'] }}</td>
                                <td class="text-center text-danger">{{ $d['skor_jurusan_pertama']}}</td>
                                <td class="text-center text-danger">
                                    @if ($d['skor_jurusan_pertama'] > 70)
                                        <span class="badge text-bg-success">PASSED</span>
                                    @else
                                        <span class="badge text-bg-danger">FAILED</span>
                                    @endif
                                </td>
                                <td class="text-center text-success fw-bold">{{ $d['jurusan_kedua']['benar'] }}</td>
                                <td class="text-center text-danger">{{ $d['jurusan_kedua']['salah'] }}</td>
                                <td class="text-center text-danger">{{ $d['skor_jurusan_kedua']}}</td>
                                <td class="text-center text-danger">
                                    @if ($d['skor_jurusan_kedua'] > 70)
                                        <span class="badge text-bg-success">PASSED</span>
                                    @else
                                        <span class="badge text-bg-danger">FAILED</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">{{ $d['total_benar'] }} / {{  $d['total_soal'] }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $d['nilai'] >= 70 ? 'bg-success' : 'bg-warning text-dark' }} fs-6">
                                        {{ $d['nilai'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailJawaban{{ $d['id_siswa'] }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Menampilkan {{ $detail_jawaban->firstItem() }} - {{ $detail_jawaban->lastItem() }} 
                        dari {{ $detail_jawaban->total() }} data
                    </div>
                    <div>
                        {{-- appends(request()->query()) memastikan ?search=keyword tetap ada di URL --}}
                        {{ $detail_jawaban->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($detail_jawaban as $d)
        <div class="modal fade" id="detailJawaban{{ $d['id_siswa'] }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Jawaban: {{ $d['nama'] }} ({{ $d['nomor_registrasi'] }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Tahap</th>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban Siswa</th>
                                    <th>Kunci</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($d['detail'] as $detail)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center"><small class="badge bg-secondary">{{ strtoupper($detail['tahap']) }}</small></td>
                                        <td>{!! $detail['pertanyaan'] !!}</td>
                                        <td class="text-center fw-bold">{{ $detail['jawaban'] }}</td>
                                        <td class="text-center text-primary fw-bold">{{ $detail['kunci_jawaban'] }}</td>
                                        <td class="text-center">
                                            @if($detail['jawaban'] === $detail['kunci_jawaban'])
                                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Benar</span>
                                            @else
                                                <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Salah</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
