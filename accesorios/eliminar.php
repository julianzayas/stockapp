<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];

// Verificar si el accesorio fue usado en movimientos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE sector = 'accesorio' AND item_id = ?");
$stmt->execute([$id]);
$usado = $stmt->fetchColumn();

if ($usado > 0) {
    $_SESSION['error'] = "Este accesorio no puede eliminarse porque ya fue utilizado en movimientos.";
    header("Location: listar.php");
    exit;
}

// Eliminar definitivamente
$stmt = $pdo->prepare("DELETE FROM accesorios WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['mensaje'] = "Accesorio eliminado correctamente.";
header("Location: listar.php");
exit;
