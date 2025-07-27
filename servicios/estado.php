
<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT activo FROM servicios WHERE id = ?");
$stmt->execute([$id]);
$servicio = $stmt->fetch();

if (!$servicio) {
    $_SESSION['error'] = "Servicio no encontrado.";
    header("Location: listar.php");
    exit;
}

$nuevo_estado = $servicio['activo'] ? 0 : 1;
$stmt = $pdo->prepare("UPDATE servicios SET activo = ? WHERE id = ?");
$stmt->execute([$nuevo_estado, $id]);

$_SESSION['mensaje'] = $nuevo_estado ? "Servicio activado correctamente." : "Servicio desactivado correctamente.";
header("Location: listar.php");
exit;
?>