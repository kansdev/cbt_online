<div class="list-group list-group-flush mt-3">
    <div class="menu-label">Menu Utama</div>
    <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.index') active @endif" href="{{ route('admin.index') }}">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>
    <a class="list-group-item list-group-item-action bg-transparent" data-bs-toggle="collapse" href="#menuPeserta" role="button" aria-expanded="false">
        <i class="bi bi-people-fill"></i> Peserta
    </a>

    {{-- Data Peserta --}}
    <div class="collapse ps-3 @if (in_array(Route::currentRouteName(), ['admin.peserta', 'admin.aktif_peserta', 'admin.reset_peserta'])) show @endif" id="menuPeserta">
        <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.peserta') active @endif" href="{{ route('admin.peserta') }}">
            <i class="bi bi-circle-fill"></i> Data Peserta
        </a>

        <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.aktif_peserta') active @endif" href="{{ route('admin.aktif_peserta') }}">
            <i class="bi bi-circle-fill"></i> Aktif Peserta
        </a>

        <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.reset_peserta') active @endif" href="{{ route('admin.reset_peserta') }}">
            <i class="bi bi-circle-fill"></i> Reset Peserta
        </a>
    </div>

    <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.soal') active @endif" href="{{ route('admin.soal') }}">
        <i class="bi bi-question-circle-fill"></i> Soal
    </a>

    <div class="menu-label">Pelaksanaan</div>
    
    <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.koreksi') active @endif" href="{{ route('admin.koreksi') }}">
        <i class="bi bi-pencil-square"></i> Koreksi
    </a>

    <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.riwayat') active @endif" href="{{ route('admin.riwayat') }}">
        <i class="bi bi-clock-history"></i> Riwayat
    </a>

    <div class="menu-label">Sistem</div>
    <a class="list-group-item list-group-item-action bg-transparent @if (Route::currentRouteName() == 'admin.settings') active @endif" href="{{ route('admin.settings') }}">
        <i class="bi bi-gear-fill"></i> Settings
    </a>
    <a class="list-group-item list-group-item-action bg-transparent text-danger-emphasis" href="#!">
        <i class="bi bi-box-arrow-left"></i> Keluar
    </a>
</div>