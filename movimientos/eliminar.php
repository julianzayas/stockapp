<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: historial.php");
    exit;
}

// Obtener el movimiento existente
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ?");
$stmt->execute([$id]);
$movimiento = $stmt->fetch();

if (!$movimiento) {
    include '../includes/header.php';
    include '../includes/navbar.php';
    echo "<div class='container mt-4'><div class='alert alert-danger'>Movimiento no encontrado.</div></div>";
    include '../includes/footer.php';
    exit;
}

// Eliminar el movimiento
$stmt = $pdo->prepare("DELETE FROM movimientos WHERE id = ?");
$stmt->execute([$id]);

// Redirigir con mensaje
header("Location: historial.php?mensaje=Movimiento eliminado exitosamente.");
exit;
?>
