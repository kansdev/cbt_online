<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard - Bootstrap 5.3</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>
    <body>

    <div class="d-flex min-vh-100" id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom border-secondary border-opacity-25">
                <i class="bi bi-cpu-fill me-2"></i> <strong>CBT</strong> Online
            </div>

            {{-- Menu --}}
            @include('admin.layout.menu')
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top">
                <div class="container-fluid">
                    <button class="btn btn-outline-dark btn-sm" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name=Admin" alt="mdo" width="32" height="32" class="rounded-circle me-2">
                                <strong>Admin</strong>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="#">Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#">Keluar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @yield('content')
            </div>

            <footer class="bg-white border-top py-3 mt-auto shadow-sm ">
                <div class="container-fluid">
                    <p class="mb-0 text-center text-muted">&copy; 2023 Your Company. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/toggle.js') }}"></script>
    </body>
</html>
