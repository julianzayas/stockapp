<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;

if (!$id || $id == $_SESSION['usuario_id']) {
    header("Location: listar.php");
    exit;
}

// Validar que el usuario no esté ya inactivo
$stmt = $pdo->prepare("SELECT activo FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario || $usuario['activo'] == 0) {
    header("Location: listar.php");
    exit;
}

// Verificar si tiene movimientos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE usuario_id = ?");
$stmt->execute([$id]);
if ($stmt->fetchColumn() > 0) {
    // Desactivar si tiene movimientos
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: listar.php");
    exit;
} else {
    // Si no tiene movimientos, eliminar directamente
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: listar.php");
    exit;
}
?>
