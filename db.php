<?php
$host = 'localhost'; // o '127.0.0.1'
$dbname = 'gustavopl';
$username = 'root';
$password = '123456';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
