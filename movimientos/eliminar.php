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

// Verificar existencia
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ?");
$stmt->execute([$id]);
$mov = $stmt->fetch();

if (!$mov) {
    header("Location: historial.php?mensaje=Movimiento no encontrado");
    exit;
}

// Validar que el accesorio/servicio asociado aún exista y esté activo (si corresponde)
if ($mov['sector'] === 'accesorio') {
    $stmt = $pdo->prepare("SELECT activo FROM accesorios WHERE id = ?");
    $stmt->execute([$mov['item_id']]);
    $accesorio = $stmt->fetch();
    if (!$accesorio) {
        header("Location: historial.php?mensaje=El accesorio ya no existe.");
        exit;
    }
    if ($accesorio['activo'] == 0) {
        header("Location: historial.php?mensaje=El accesorio está desactivado. No se puede eliminar el movimiento.");
        exit;
    }
} elseif ($mov['sector'] === 'servicio') {
    $stmt = $pdo->prepare("SELECT 1 FROM servicios WHERE id = ?");
    $stmt->execute([$mov['item_id']]);
    if (!$stmt->fetch()) {
        header("Location: historial.php?mensaje=El servicio ya no existe. No se puede eliminar el movimiento.");
        exit;
    }
}

// Si es una salida, devolver stock (opcional según lógica)
// Si es una entrada, restar stock
if ($mov['tipo'] === 'entrada' && $mov['sector'] === 'accesorio') {
    $pdo->prepare("UPDATE accesorios SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?")
        ->execute([$mov['cantidad'], $mov['item_id']]);
} elseif ($mov['tipo'] === 'salida' && $mov['sector'] === 'accesorio') {
    $pdo->prepare("UPDATE accesorios SET stock_actual = stock_actual + ? WHERE id = ?")
        ->execute([$mov['cantidad'], $mov['item_id']]);
}

// Eliminar el movimiento
$stmt = $pdo->prepare("DELETE FROM movimientos WHERE id = ?");
$stmt->execute([$id]);

header("Location: historial.php?mensaje=Movimiento eliminado correctamente.");
exit;
?>
