<?php
// local
// $host = 'db';  
// $db   = 'sap_logs';
// $user = 'postgres';
// $pass = 'yourpassword';
// $port = '5432';
// PROD 
$host = '72.60.42.81';  
$db   = 'sap_logs';
$user = 'ntp2026';
$pass = 'admin@123#';
$port = '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}