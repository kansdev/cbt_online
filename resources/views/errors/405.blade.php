<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>405 - Aksi Tidak Diizinkan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="text-center">
        <h1 class="display-1 text-danger fw-bold">405</h1>
        <h3 class="mb-3">Aksi Tidak Diizinkan</h3>
        <p class="text-muted">Request tidak sesuai. Mungkin kamu mengakses URL secara langsung.</p>

        <a href="{{ url()->previous() }}" class="btn btn-primary me-2">
            Kembali
        </a>
    </div>

</body>
</html>