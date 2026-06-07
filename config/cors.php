<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Izinkan semua rute API

    'allowed_methods' => ['*'], // Izinkan semua metode (POST, GET, dll)

    'allowed_origins' => ['http://localhost:5174'], // IZINKAN URL REACT ANDA

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Set true jika nanti pakai login/sanctum
];
