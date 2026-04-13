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
                        <h4>1,234</h4>
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
                        <h4>1,234</h4>
                    </div>
                    <div class="text-primary fs-3"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>                    
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 fw-bold text-primary">Log Sistem</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Aktivitas</th>
                            <th>IP Address</th>
                        </tr>      
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>2024-06-01 10:15:30</td>
                            <td>Peserta A melakukan login</td>
                            <td>192.168.1.100</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection