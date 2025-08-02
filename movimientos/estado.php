<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

if ($_SESSION['rol'] !== 'admin') {
    header("Location: historial.php?mensaje=Acceso denegado");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: historial.php?mensaje=ID inválido");
    exit;
}

// Verificar si el movimiento existe
$stmt = $pdo->prepare("SELECT activo FROM movimientos WHERE id = ?");
$stmt->execute([$id]);
$movimiento = $stmt->fetch();

if (!$movimiento) {
    header("Location: historial.php?mensaje=Movimiento no encontrado");
    exit;
}

// Alternar el estado
$nuevoEstado = $movimiento['activo'] ? 0 : 1;
$stmt = $pdo->prepare("UPDATE movimientos SET activo = ? WHERE id = ?");
$stmt->execute([$nuevoEstado, $id]);

$mensaje = $nuevoEstado ? "Movimiento activado correctamente." : "Movimiento desactivado correctamente.";
header("Location: historial.php?mensaje=" . urlencode($mensaje));
exit;
?>
