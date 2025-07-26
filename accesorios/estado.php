<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT activo FROM accesorios WHERE id = ?");
$stmt->execute([$id]);
$accesorio = $stmt->fetch();

if (!$accesorio) {
    $_SESSION['error'] = "Accesorio no encontrado.";
    header("Location: listar.php");
    exit;
}

$nuevo_estado = $accesorio['activo'] ? 0 : 1;
$stmt = $pdo->prepare("UPDATE accesorios SET activo = ? WHERE id = ?");
$stmt->execute([$nuevo_estado, $id]);

$_SESSION['mensaje'] = $nuevo_estado ? "Accesorio activado correctamente." : "Accesorio desactivado correctamente.";
header("Location: listar.php");
exit;
