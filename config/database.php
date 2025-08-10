<?php
// Reporte de errores
// Esto es útil para el desarrollo, pero no debería estar en producción.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// /config/database.php
$host = 'localhost';
$dbname ='stockapp_v3_1'; // Cambiar si el nombre de la base de datos es diferente
$username = 'root';
$password = ''; // Cambiar si tenés contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>