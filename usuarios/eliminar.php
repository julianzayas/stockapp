<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();
requireAdmin();

include '../includes/header.php';
include '../includes/navbar.php';

$id = $_GET['id'] ?? null;
$usuarioActual = $_SESSION['usuario_id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: listar.php');
    exit;
}

// Evitar eliminar el usuario logueado o el admin principal (ID 1)
if ($id == $usuarioActual || $id == 1) {
    echo "<div class='container mt-4'><div class='alert alert-warning'>No puedes eliminar este usuario.</div></div>";
    include '../includes/footer.php';
    exit;
}

// Verificar si tiene movimientos registrados
$stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE usuario_id = ?");
$stmt->execute([$id]);
$movimientos = $stmt->fetchColumn();

if ($movimientos > 0) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>No se puede eliminar este usuario porque tiene movimientos registrados.</div></div>";
    include '../includes/footer.php';
    exit;
}

// Eliminar usuario
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
if ($stmt->execute([$id])) {
    header('Location: listar.php?mensaje=eliminado');
    exit;
} else {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Ocurrió un error al intentar eliminar el usuario.</div></div>";
}

include '../includes/footer.php';
?>
