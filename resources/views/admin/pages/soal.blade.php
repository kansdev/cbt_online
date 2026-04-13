@extends('admin.main')

@section('content')
    <h2 class="mb-4">Soal</h2>

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
                            <th>Kategori</th>
                            <th>Soal</th>
                            <th>Status</th>
                        </tr>     
                    </thead>
                    <tbody>
                        @foreach ($soal as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $s->kategori }}</td>
                                <td>{{ $s->pertanyaan }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection