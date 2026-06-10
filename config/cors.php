<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Izinkan semua rute API

    'allowed_methods' => ['*'], // Izinkan semua metode (POST, GET, dll)

    'allowed_origins' => ['https://test.kansdev.my.id', 'https://wawancara.kansdev.my.id', 'http://localhost:5173'], // IZINKAN URL REACT ANDA

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Set true jika nanti pakai login/sanctum
];
