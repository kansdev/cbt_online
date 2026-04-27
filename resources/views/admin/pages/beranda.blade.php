@extends('admin.main')

@section('content')
    <h2 class="mb-4">Ringkasan Statistik</h2>
                
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Jumlah Peserta</p>
                        <h4>{{ $peserta }}</h4>
                    </div>
                    <div class="text-primary fs-3"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Peserta Laki-Laki</p>
                        <h4>{{ $laki_laki }}</h4>
                    </div>
                    <div class="text-primary fs-3"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Peserta Perempuan</p>
                        <h4>{{ $perempuan }}</h4>
                    </div>
                    <div class="text-primary fs-3"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>                    
    </div>

    {{-- Message --}}
    @if (session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill fs-5"></i> {{ session('success') }}</div>
    @elseif (session('failed'))
        <div class="alert alert-danger"><i class="bi bi-bug-fill fs-5"></i> {{ session('failed') }}</div>
    @elseif (session('warning'))
        <div class="alert alert-warning"><i class="bi bi-exclamation-diamond fs-5"></i> {{ session('warning') }}</div>
    @endif
    {{-- End Message --}}

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <h4 class="m-0 fw-bold text-primary">Log Sistem</h4>

                <button onclick="event.preventDefault(); document.getElementById('form-hapus').submit();" class="btn btn-danger btn-sm">
                    Hapus Log
                </button>

                <form id="form-hapus" action="{{ route('admin.clear_log') }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>IP Address</th>
                            <th>Browser</th>
                            <th>Waktu</th>
                        </tr>      
                    </thead>
                    <tbody>
                        @foreach ($log as $l)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $l->account->name }}</td>
                                <td>{{ $l->activity }}</td>
                                <td>{{ $l->ip_address }}</td>
                                <td>{{ $l->user_agent }}</td>
                                <td>{{ $l->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection