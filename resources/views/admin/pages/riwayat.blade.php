@extends('admin.main')

@section('content')
    <h2 class="mb-4">Riwayat Ujian</h2>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 fw-bold text-primary">Data Riwayat Ujian</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Status</th>
                            <th>Tahap Akhir</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($riwayat as $r)
                            <tr>

                                <td>{{$no++}}</td>
                                <td>{{ $r->account->nisn }}</td>
                                <td>{{ $r->status }}</td>
                                <td>{{ $r->tahap }}</td>
                                <td>{{ $r->mulai_at }}</td>
                                <td>{{ $r->selesai_at }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailRiwayat{{ $r->account->nisn }}">
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

@endsection
