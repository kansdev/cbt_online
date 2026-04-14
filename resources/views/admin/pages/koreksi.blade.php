@extends('admin.main')

@section('content')
    <h2 class="mb-4">Koreksi Jawaban</h2>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 fw-bold text-primary">Data Koreksi</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0" id="dataTable">
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
                        @foreach ($detail_jawaban as $index => $d)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{$d['name']}}</td>
                                <td>{{$d['benar']}}</td>
                                <td>{{$d['salah']}}</td>
                                <td>{{$d['nilai']}}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailJawaban{{ $d['id_siswa'] }}">
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($detail_jawaban as $d)
        <div class="modal fade" id="detailJawaban{{ $d['id_siswa'] }}" tabindex="-1" aria-labelledby="detailJawabanLabel{{ $d['id_siswa'] }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailJawabanLabel{{ $d['id_siswa'] }}">Detail Jawaban - {{ $d['name'] }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban Siswa</th>
                                    <th>Kunci Jawaban</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($d['detail'] as $detail)
                                    <tr>
                                        <td>{{ $detail['pertanyaan'] }}</td>
                                        <td>{{ $detail['jawaban'] }}</td>
                                        <td>{{ $detail['kunci_jawaban'] }}</td>
                                        <td>
                                            @if ($detail['jawaban'] === $detail['kunci_jawaban'])
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
