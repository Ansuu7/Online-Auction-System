<?php
declare(strict_types=1);

session_start();

$host = 'localhost';
$dbName = 'auctionhub';
$dbUser = 'root';
$dbPass = ''; // default XAMPP MySQL password is empty

$pdo = new PDO(
    "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
    $dbUser,
    $dbPass
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(32) NOT NULL DEFAULT 'Member',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )"
);

$roleColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch();

if ($roleColumn === false) {
    $pdo->exec(
        "ALTER TABLE users
         ADD COLUMN role VARCHAR(32) NOT NULL DEFAULT 'Member' AFTER password_hash"
    );
} else {
    $pdo->exec(
        "ALTER TABLE users
         MODIFY role VARCHAR(32) NOT NULL DEFAULT 'Member'"
    );
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function pull_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}