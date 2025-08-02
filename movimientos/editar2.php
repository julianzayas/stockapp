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
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ?");
$stmt->execute([$id]);
$mov = $stmt->fetch();

if (!$mov) {
    header("Location: historial.php?mensaje=Movimiento no encontrado");
    exit;
}

$mensaje = "";

// Obtener opciones
$accesorios = $pdo->query("SELECT id, CONCAT(marca, ' ', nombre) AS nombre FROM accesorios WHERE activo = 1 ORDER BY nombre")->fetchAll();
$servicios = $pdo->query("SELECT id, CONCAT(marca, ' ', modelo) AS nombre FROM servicios ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $sector = $_POST['sector'];
    $item_id = $_POST['item_id'];
    $cantidad_nueva = (int) $_POST['cantidad'];
    $observacion = $_POST['observacion'];
    $usuario_id = $_SESSION['usuario_id'];

    // Si el movimiento era de accesorio, actualizar el stock
    if ($mov['sector'] === 'accesorio') {
        $diferencia = $cantidad_nueva - $mov['cantidad'];
        if ($tipo === 'entrada') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = stock_actual + ? WHERE id = ?")
                ->execute([$diferencia, $mov['item_id']]);
        } elseif ($tipo === 'salida') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?")
                ->execute([$diferencia * -1, $mov['item_id']]);
        }
    }

    // Actualizar movimiento
    $stmt = $pdo->prepare("UPDATE movimientos SET tipo = ?, sector = ?, item_id = ?, cantidad = ?, observacion = ? WHERE id = ?");
    $stmt->execute([$tipo, $sector, $item_id, $cantidad_nueva, $observacion, $id]);

    header("Location: historial.php?mensaje=Movimiento actualizado");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>Editar Movimiento</h4>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select" required>
                <option value="entrada" <?= $mov['tipo'] === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= $mov['tipo'] === 'salida' ? 'selected' : '' ?>>Salida</option>
                <option value="servicio" <?= $mov['tipo'] === 'servicio' ? 'selected' : '' ?>>Servicio</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Sector</label>
            <select name="sector" class="form-select" required>
                <option value="accesorio" <?= $mov['sector'] === 'accesorio' ? 'selected' : '' ?>>Accesorios</option>
                <option value="servicio" <?= $mov['sector'] === 'servicio' ? 'selected' : '' ?>>Servicios</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Artículo / Servicio</label>
            <select name="item_id" class="form-select" required>
                <?php if ($mov['sector'] === 'accesorio'): ?>
                    <?php foreach ($accesorios as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $a['id'] == $mov['item_id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre']) ?></option>
                    <?php endforeach; ?>
                <?php elseif ($mov['sector'] === 'servicio'): ?>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $mov['item_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nombre']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-md-4">
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
