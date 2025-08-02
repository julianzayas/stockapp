<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: historial.php?mensaje=ID inválido");
    exit;
}

// Obtener el movimiento
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ? AND sector = 'accesorio'");
$stmt->execute([$id]);
$mov = $stmt->fetch();

if (!$mov) {
    header("Location: historial.php?mensaje=Movimiento no encontrado o inválido");
    exit;
}

// Obtener accesorios activos
$accesorios = $pdo->query("SELECT id, CONCAT(marca, ' ', nombre) AS nombre FROM accesorios WHERE activo = 1 ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $item_id = $_POST['item_id'];
    $cantidad_nueva = (int) $_POST['cantidad'];
    $observacion = $_POST['observacion'];

    // Reversar movimiento anterior
    if ($mov['tipo'] === 'entrada') {
        $pdo->prepare("UPDATE accesorios SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?")
            ->execute([$mov['cantidad'], $mov['item_id']]);
    } elseif ($mov['tipo'] === 'salida') {
        $pdo->prepare("UPDATE accesorios SET stock_actual = stock_actual + ? WHERE id = ?")
            ->execute([$mov['cantidad'], $mov['item_id']]);
    }

    // Aplicar nuevo movimiento
    if ($tipo === 'entrada') {
        $pdo->prepare("UPDATE accesorios SET stock_actual = stock_actual + ? WHERE id = ?")
            ->execute([$cantidad_nueva, $item_id]);
    } elseif ($tipo === 'salida') {
        $pdo->prepare("UPDATE accesorios SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?")
            ->execute([$cantidad_nueva, $item_id]);
    }

    // Guardar cambio en movimientos
    $stmt = $pdo->prepare("UPDATE movimientos SET tipo = ?, item_id = ?, cantidad = ?, observacion = ? WHERE id = ?");
    $stmt->execute([$tipo, $item_id, $cantidad_nueva, $observacion, $id]);

    header("Location: historial.php?mensaje=Movimiento de accesorio actualizado.");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>Editar Movimiento de Accesorio</h4>

    <form method="POST" class="row g-3">
        <div class="col-12">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select" required>
                <option value="entrada" <?= $mov['tipo'] === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= $mov['tipo'] === 'salida' ? 'selected' : '' ?>>Salida</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Accesorio</label>
            <select name="item_id" class="form-select" required>
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= $a['id'] == $mov['item_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" value="<?= $mov['cantidad'] ?>" required>
        </div>

        <div class="col-12">
            <label class="form-label">Observación</label>
            <input type="text" name="observacion" class="form-control" value="<?= htmlspecialchars($mov['observacion']) ?>">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="historial.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
