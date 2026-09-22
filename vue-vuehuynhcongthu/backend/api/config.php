<?php
declare(strict_types=1);

// Thay đổi các giá trị này nếu tài khoản MySQL trên XAMPP của bạn khác mặc định.
const DB_HOST = '127.0.0.1';
const DB_NAME = 'vuon_uom_xanh';
const DB_USER = 'root';
const DB_PASSWORD = '';

function db(): PDO {
    static $connection = null;
    if ($connection instanceof PDO) return $connection;

    $connection = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    return $connection;
}
