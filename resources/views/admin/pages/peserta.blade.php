@extends('admin.main')

@section('content')
    <h2 class="mb-4">Peserta</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahPeserta">Tambah Peserta</button>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload Peserta</button>

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
                            <th>No Registrasi</th>
                            <th>NIS/NISN</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Kategori</th>
                            <th>Gelombang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peserta as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nomor_registrasi }}</td>
                                <td>{{ $p->nisn }}</td>
                                <td>{{ $p->nama }}</td>
                                <td>{{ $p->jurusan }}</td>
                                <td>{{ $p->kategori }}</td>
                                <td>{{ $p->id_gelombang }}</td>
                                <td>
                                    @if ($p->status == 'nonaktif')
                                        <span class="badge bg-danger">Non Active</span>
                                    @elseif ($p->status == 'aktif')
                                        <span class="badge bg-success">Active</span>
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
    <div class="modal fade" id="tambahPeserta" tabindex="-1" aria-labelledby="tambahPesertaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPesertaLabel">Tambah Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN Peserta</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Peserta</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="jurusan" class="form-label">Jurusan</label>
                            <input type="text" class="form-control" id="jurusan" name="jurusan" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.import-peserta') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
