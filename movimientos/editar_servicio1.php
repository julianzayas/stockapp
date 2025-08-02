<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: historial.php?mensaje=ID inválido");
    exit;
}

// Obtener movimiento
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ? AND sector = 'servicio'");
$stmt->execute([$id]);
$mov = $stmt->fetch();

if (!$mov) {
    header("Location: historial.php?mensaje=Movimiento no encontrado o inválido");
    exit;
}

// Obtener servicios
$servicios = $pdo->query("SELECT id, CONCAT(marca, ' ', modelo) AS nombre FROM servicios ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $cantidad = (int) $_POST['cantidad'];
    $total = (float) $_POST['total'];
    $observacion = $_POST['observacion'];

    $stmt = $pdo->prepare("UPDATE movimientos SET item_id = ?, cantidad = ?, total = ?, observacion = ? WHERE id = ?");
    $stmt->execute([$item_id, $cantidad, $total, $observacion, $id]);

    header("Location: historial.php?mensaje=Movimiento de servicio actualizado.");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>Editar Movimiento de Servicio</h4>

    <form method="POST" class="row g-3">
        <div class="col-12">
            <label class="form-label">Servicio</label>
            <select name="item_id" class="form-select" required>
                <?php foreach ($servicios as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id'] == $mov['item_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" value="<?= $mov['cantidad'] ?>" required>
        </div>

        <div class="col-12">
            <label class="form-label">Total ($)</label>
            <input type="number" step="0.01" name="total" class="form-control" value="<?= $mov['total'] ?>" required>
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
