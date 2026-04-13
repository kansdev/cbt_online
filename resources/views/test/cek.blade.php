<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Peserta - Sistem Ujian</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <style>
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                height: 100vh;
                display: flex;
                align-items: center;
                font-family: 'Inter', sans-serif;
            }
            .login-card {
                border: none;
                border-radius: 20px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                overflow: hidden;
            }
            .login-header {
                background-color: #0d6efd;
                padding: 30px;
                color: white;
                text-align: center;
            }
            .form-control-lg {
                border-radius: 12px;
                border: 2px solid #eee;
                padding: 15px;
                font-size: 1rem;
            }
            .form-control-lg:focus {
                border-color: #0d6efd;
                box-shadow: none;
            }
            .btn-login {
                border-radius: 12px;
                padding: 12px;
                font-weight: 600;
                transition: 0.3s;
            }
        </style>
    </head>
    <body>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">

                    <div class="card login-card">
                        <!-- Header Login -->
                        <div class="login-header">
                            <h4 class="fw-bold mb-0">CBT Online</h4>
                            <p class="small mb-0 opacity-75">Silakan masuk untuk memulai</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <form action="{{ route('ujian.cek_peserta') }}" method="POST">
                                <div class="mb-4">
                                    <label for="nisn" class="form-label fw-semibold text-muted">Username / No. NISN</label>
                                    <input type="text" class="form-control form-control-lg" id="nisn" name="nisn" placeholder="Contoh: 0085370210" required autocomplete="off"
                                    >
                                    <div class="form-text mt-2">
                                        Gunakan NISN yang ada di prunus digidaw.
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg btn-login shadow-sm">
                                        Masuk ke Ujian
                                    </button>
                                </div>
                            </form>
                            @if ($errors->any())
                                <div class="alert alert-danger mt-4">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-4 text-muted small">
                        &copy; 2024CBT Online - Dibuat oleh M Ade Maulana, S.Kom - Versi 1.0 Beta
                    </div>

                </div>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    </body>
</html>
