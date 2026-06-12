@extends('admin.main')

@section('content')
    <h2 class="mb-4">Peserta</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahPeserta">Tambah Peserta</button>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload Peserta</button>

    @if (session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill fs-5"></i> {{ session('success') }}</div>
    @elseif (session('failed'))
        <div class="alert alert-danger"><i class="bi bi-bug-fill fs-5"></i> {{ session('failed') }}</div>
    @endif

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
                            <th>Jurusan Pertama</th>
                            <th>Jurusan Kedua</th>
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
                                <td>{{ $p->jurusan_pertama }}</td>
                                <td>{{ $p->jurusan_kedua }}</td>
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
                    <form action="{{ route('admin.tambah_peserta') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nomor_registrasi" class="form-label">No. Registrasi</label>
                            <input type="text" class="form-control" id="nomor_registrasi" name="nomor_registrasi" required>
                        </div>
                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN Peserta</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Peserta</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                        <div class="mb-3">
                            <label for="jurusan_pertama" class="form-label">Jurusan</label>
                            <select class="form-control" name="jurusan_pertama" id="jurusan_pertama">
                                <option>-- Pilih --</option>
                                <option value="MP">Manajemen Perkantoran</option>
                                <option value="AK">Akuntansi</option>
                                <option value="AN">Animasi</option>
                                <option value="TJKT">Teknik Jaringan Komputer & Telekomunikasi</option>
                                <option value="DKV">Desain Komunikasi Visual</option>
                                <option value="BP">Broadcasting & Perfilman</option>
                                <option value="PPLG">Pengembangan Perangkat Lunak & Gim</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jurusan_kedua" class="form-label">Jurusan</label>
                            <select class="form-control" name="jurusan_kedua" id="jurusan_kedua">
                                <option>-- Pilih --</option>
                                <option value="MP">Manajemen Perkantoran</option>
                                <option value="AK">Akuntansi</option>
                                <option value="AN">Animasi</option>
                                <option value="TJKT">Teknik Jaringan Komputer & Telekomunikasi</option>
                                <option value="DKV">Desain Komunikasi Visual</option>
                                <option value="BP">Broadcasting & Perfilman</option>
                                <option value="PPLG">Pengembangan Perangkat Lunak & Gim</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_umum" class="form-label">Jenis Soal Umum</label>
                            <select class="form-control" name="jenis_umum" id="jenis_umum">
                                <option>-- Pilih --</option>
                                <option value="umum_mp_ak">Manajemen Perkantoran</option>
                                <option value="umum_mp_ak">Akuntansi</option>
                                <option value="umum_an_dkv_bp">Animasi</option>
                                <option value="umum_rpl_tjkt">Teknik Jaringan Komputer & Telekomunikasi</option>
                                <option value="umum_an_dkv_bp">Desain Komunikasi Visual</option>
                                <option value="umum_an_dkv_bp">Broadcasting & Perfilman</option>
                                <option value="umum_rpl_tjkt">Pengembangan Perangkat Lunak & Gim</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                                <option value="Laki - Laki">Laki - Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_gelombang" class="form-label">Gelombang</label>
                            <select class="form-control" name="id_gelombang" id="id_gelombang">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option>-- Pilih --</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectJurusan = document.getElementById('jurusan');
            const kategori = document.getElementById('kategori');
            const kodeSoalUmum = document.getElementById('jenis_umum');
            const kodeSoalKejuruan = document.getElementById('jenis_kejuruan');

            const jurusan = {
                'MP': {kategori: 'mp_ak', jenis_soal_umum: 'umum_mp_ak', jenis_soal_kejuruan: 'kejuruan_mp_ak'},
                'AK': {kategori: 'mp_ak', jenis_soal_umum: 'umum_mp_ak', jenis_soal_kejuruan: 'kejuruan_mp_ak'},
                'AN': {kategori: 'an_dkv_bp', jenis_soal_umum: 'umum_an_dkv_bp', jenis_soal_kejuruan: 'kejuruan_an_dkv_bp'},
                'DKV': {kategori: 'an_dkv_bp', jenis_soal_umum: 'umum_an_dkv_bp', jenis_soal_kejuruan: 'kejuruan_an_dkv_bp'},
                'BP': {kategori: 'an_dkv_bp', jenis_soal_umum: 'umum_an_dkv_bp', jenis_soal_kejuruan: 'kejuruan_an_dkv_bp'},
                'PPLG': {kategori: 'rpl_tkj', jenis_soal_umum: 'umum_rpl_tkj', jenis_soal_kejuruan: 'kejuruan_rpl_tkj'},
                'TJKT': {kategori: 'rpl_tkj', jenis_soal_umum: 'umum_rpl_tkj', jenis_soal_kejuruan: 'kejuruan_rpl_tkj'}
            };

            selectJurusan.addEventListener('change', function(){
                const data = jurusan[this.value];
                if (data) {
                    kategori.value = data.kategori;
                    kodeSoalUmum.value = data.jenis_soal_umum;
                    kodeSoalKejuruan.value = data.jenis_soal_kejuruan;
                } else {
                    kategori.value = '';
                    kodeSoalUmum.value = '';
                    kodeSoalKejuruan.value = '';
                }
            });
        });
    </script>
@endsection
