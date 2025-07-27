<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de servicio inválido.";
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];

// Verificar si el servicio tiene movimientos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE sector = 'servicio' AND item_id = ?");
$stmt->execute([$id]);
$tiene_movimientos = $stmt->fetchColumn() > 0;

if ($tiene_movimientos) {
    $_SESSION['error'] = "No puedes eliminar este servicio porque ya tiene movimientos registrados.";
    header("Location: listar.php");
    exit;
}

// Eliminar servicio
$stmt = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['mensaje'] = "Servicio eliminado correctamente.";
header("Location: listar.php");
exit;
