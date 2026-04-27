@extends('admin.main')

@section('content')
    <h2 class="mb-4">Settings</h2>
    
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
                <h4 class="m-0 fw-bold text-primary">Settings</h4>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs nav-fill mb-4" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="gelombang-tab" data-bs-toggle="tab" data-bs-target="#gelombang-pane" type="button" role="tab">
                        <i class="bi bi-layers me-2"></i>Gelombang
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="waktu-tab" data-bs-toggle="tab" data-bs-target="#waktu-pane" type="button" role="tab">
                        <i class="bi bi-clock-history me-2"></i>Waktu Tes
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="keamanan-tab" data-bs-toggle="tab" data-bs-target="#keamanan-pane" type="button" role="tab">
                        <i class="bi bi-lock-fill me-2"></i>Keamanan
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="settingsTabContent">
                <!-- Panel Gelombang -->
                <div class="tab-pane active show fade" id="gelombang-pane" role="tabpanel" tabindex="0">
                    <form action="{{ route('admin.setting_gelombang') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Nama Gelombang Aktif</label>
                                <select class="form-select mb-2" name="gelombang[0]">
                                    <option value="1" selected>Gelombang 1</option>
                                    <option value="2">Gelombang 2</option>
                                    <option value="3">Gelombang 3</option>
                                    <option value="4">Gelombang 4</option>
                                </select>

                                <select class="form-select mb-2" name="gelombang[1]">
                                    <option value="1">Gelombang 1</option>
                                    <option value="2" selected>Gelombang 2</option>
                                    <option value="3">Gelombang 3</option>
                                    <option value="4">Gelombang 4</option>
                                </select>

                                <select class="form-select mb-2" name="gelombang[2]">
                                    <option value="1">Gelombang 1</option>
                                    <option value="2">Gelombang 2</option>
                                    <option value="3" selected>Gelombang 3</option>
                                    <option value="4">Gelombang 4</option>
                                </select>

                                <select class="form-select mb-2" name="gelombang[3]">
                                    <option value="1">Gelombang 1</option>
                                    <option value="2">Gelombang 2</option>
                                    <option value="3">Gelombang 3</option>
                                    <option value="4" selected>Gelombang 4</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="status[0]" role="switch" {{ isset($setting_gelombang[0]) && $setting_gelombang[0]->status == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="status[1]" role="switch" {{ isset($setting_gelombang[0]) && $setting_gelombang[1]->status == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="status[2]" role="switch" {{ isset($setting_gelombang[0]) && $setting_gelombang[2]->status == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="status[3]" role="switch" {{ isset($setting_gelombang[0]) && $setting_gelombang[3]->status == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">Simpan</button>
                            </div>
                        </div>
                    </form>
                    
                </div>

                <!-- Panel Waktu Tes -->
                <div class="tab-pane fade show" id="waktu-pane" role="tabpanel" tabindex="0">
                    <form action="{{ route('admin.setting_durasi') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label fw-bold">Gelombang</label>
                            <select class="form-select mb-2" name="gelombang">
                                <option value="1" selected>Gelombang 1</option>
                                <option value="2">Gelombang 2</option>
                                <option value="3">Gelombang 3</option>
                                <option value="4">Gelombang 4</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold">Durasi Tes (Menit)</label>
                            <input type="number" class="form-control" name="durasi" placeholder="Contoh: 90">        
                        </div>
                        
                        <div class="mb-2">                            
                            <label class="form-label fw-bold">Jadwal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai">
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">Simpan</button>
                        </div>
                    </form>
                </div>                

                <!-- Panel Gelombang -->
                <div class="tab-pane fade" id="keamanan-pane" role="tabpanel" tabindex="0">
                    <form action="#" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Block Copy / Paste</label>                                
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="statusGelombang" checked>
                                    <label class="form-check-label" for="statusGelombang">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form action="#" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Anti Tab Switch</label>                                
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="antiTabSwitch" checked>
                                    <label class="form-check-label" for="antiTabSwitch">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
