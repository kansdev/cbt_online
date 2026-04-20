@extends('admin.main')

@section('content')
    @if (session('failed'))
        <div class="alert alert-danger">
            {{ session('failed') }}
        </div>
    @elseif(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <h2 class="mb-4">Peserta</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#aktifPeserta" >Aktifkan Seluruh Peserta</button>

    <button class="btn btn-danger mb-3" data-bs-toggle="modal" data-bs-target="#nonaktifPeserta" >Non-Aktifkan Seluruh Peserta</button>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 fw-bold text-primary">Data Peserta</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peserta_aktif as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nisn }}</td>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->jurusan }}</td>
                                <td>
                                    @if ($p->status === 'nonaktif')
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#aktif_{{ $p->nisn }}" class="badge bg-danger">Non-Aktif</a>
                                    @elseif($p->status === 'aktif')
                                        <a href="{{ route('admin.aktif_peserta.one_nonaktif', $p->id)}}" class="badge bg-success">Aktif</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <!-- Modal -->
    <div class="modal fade" id="aktifPeserta" tabindex="-1" aria-labelledby="aktifPesertaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aktifPesertaLabel">Aktifkan Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ Route('admin.aktif_peserta.aktif') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <p>Apakah Anda yakin ingin mengaktifkan seluruh peserta?</p>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Aktifkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nonaktifPeserta" tabindex="-1" aria-labelledby="nonaktifPesertaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nonaktifPesertaLabel">Non-Aktifkan Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ Route('admin.aktif_peserta.nonaktif') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <p>Apakah Anda yakin ingin menon-aktifkan seluruh peserta?</p>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Non-Aktifkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach ($peserta_aktif as $p)
        <div class="modal fade" id="aktif_{{ $p->nisn }}" tabindex="-1" aria-labelledby="aktifLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="aktifLabel">Aktifkan {{ $p->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="#" method="POST">
                            @csrf
                            @method('PUT')
                            <p>Apakah Anda yakin ingin aktifkan peserta ini ?</p>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm">Aktifkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    @endforeach
@endsection
