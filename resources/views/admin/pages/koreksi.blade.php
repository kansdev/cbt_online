@extends('admin.main')

@section('content')
    <h2 class="mb-4">Koreksi Jawaban</h2>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 fw-bold text-primary">Data Koreksi</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Nilai</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach ($data as $d)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d['nama'] }}</td>
                                <td class="text-success fw-bold">{{ $d['benar'] }}</td>
                                <td class="text-danger fw-bold">{{ $d['salah'] }}</td>
                                <td class="fw-bold">{{ $d['nilai'] }}</td>
                                <td>
                                    <a href="#detailJawaban{{ $d['id_siswa'] }}"
                                    class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailJawaban{{ $d['id_siswa'] }}">
                                    Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($data as $d)
        <div class="modal fade" id="detailJawaban{{ $d['id_siswa'] }}" tabindex="-1" aria-labelledby="detailJawabanLabel{{ $d['id_siswa'] }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jawaban</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Soal</th>
                                <th>Jawaban Peserta</th>
                                <th>Kunci Jawaban</th>
                                <th>Koreksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_jawaban[$d['id_siswa']] ?? [] as $j)
                                <tr>
                                    <td>{{ $j->soal->pertanyaan }}</td>
                                    <td>{{ $j->jawaban }}</td>
                                    <td>{{ $j->soal->kunci_jawaban }}</td>
                                    <td>
                                        @if ($j->jawaban === $j->soal->kunci_jawaban)
                                            <span class="badge bg-success">Benar</span>
                                        @else
                                            <span class="badge bg-danger">Salah</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endsection
