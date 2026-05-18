@extends('admin.main')

@section('content')
    <h2 class="mb-4">Soal</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#uploadModal">
        Upload Soal
    </button>

    <a href="#" class="btn btn-success mb-3" onclick="return confirm('Apakah anda yakin ingin menghapus semua soal ?')">
        Hapus Soal
        <form id="deleteForm" action="{{ route('exam.delete-all') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </a>

    @if (session('failed'))
        <div class="alert alert-danger">{{ session('failed') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 fw-bold text-primary">Data Soal</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Jenis Soal</th>
                            <th>Kategori</th>
                            <th>Soal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($soal as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $s->jenis_soal}}</td>
                                <td>{{ $s->kategori }}</td>
                                <td>{{ $s->pertanyaan }}</td>                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upload Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Soal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.import-soal') }}" method="POST" enctype="multipart/form-data">
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

    </div>
@endsection
