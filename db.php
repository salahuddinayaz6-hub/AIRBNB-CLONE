<?php
// db.php - Database connection
$host = 'localhost';
$db   = 'rsoa_rsoa278_16';
$user = 'rsoa_rsoa278_16';
$pass = '123456';
$charset = 'utf8mb4';
 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERR_MODE            => PDO::ERR_MODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
 
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // For security reasons, don't show the actual error message in production
     die("Connection failed: " . $e->getMessage());
}
?>
 
