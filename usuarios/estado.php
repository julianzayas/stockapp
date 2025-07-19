<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();
requireAdmin();

$id = $_GET['id'] ?? null;
$accion = $_GET['accion'] ?? null;

if (!$id || !in_array($accion, ['activar', 'desactivar'])) {
    header("Location: listar.php");
    exit;
}

// Evitar que un usuario se desactive a sí mismo
if ($id == $_SESSION['usuario_id']) {
    header("Location: listar.php?error=no-autodesactivar");
    exit;
}

$activo = ($accion === 'activar') ? 1 : 0;

$stmt = $pdo->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
$stmt->execute([$activo, $id]);

header("Location: listar.php");
exit;
?>
