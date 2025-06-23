<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'veata_shop',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4'
    ],
    
    'app' => [
        'url' => getenv('APP_URL') ?: 'http://localhost',
        'name' => 'VEATA Shop',
        'env' => getenv('APP_ENV') ?: 'production',
        'debug' => getenv('APP_DEBUG') ?: false,
        'timezone' => 'Europe/Moscow'
    ],
    
    'mail' => [
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => getenv('SMTP_PORT') ?: 587,
        'username' => getenv('SMTP_USER') ?: '',
        'password' => getenv('SMTP_PASS') ?: '',
        'encryption' => 'tls',
        'from' => [
            'address' => getenv('SMTP_USER') ?: '',
            'name' => 'VEATA Shop'
        ]
    ],
    
    'upload' => [
        'path' => __DIR__ . '/../public/uploads',
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
        'max_size' => 5 * 1024 * 1024 // 5MB
    ],
    
    'session' => [
        'lifetime' => 120,
        'path' => '/',
        'domain' => null,
        'secure' => true,
        'httponly' => true
    ]
]; 