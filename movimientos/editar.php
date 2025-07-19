<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$id = $_GET['id'] ?? null;
$mensaje = "";

if (!$id) {
    header("Location: historial.php");
    exit;
}

// Obtener el movimiento existente
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ?");
$stmt->execute([$id]);
$movimiento = $stmt->fetch();

if (!$movimiento) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Movimiento no encontrado.</div></div>";
    include '../includes/footer.php';
    exit;
}

// Obtener lista de artículos
$accesorios = $pdo->query("SELECT id, nombre FROM accesorios ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $articulo_id = $_POST['articulo_id'];
    $cantidad = (int)$_POST['cantidad'];
    $observacion = $_POST['observacion'];

    if ($tipo && $articulo_id && $cantidad > 0) {
        $stmt = $pdo->prepare("UPDATE movimientos SET tipo = ?, articulo_id = ?, cantidad = ?, observacion = ? WHERE id = ?");
        $stmt->execute([$tipo, $articulo_id, $cantidad, $observacion, $id]);

        $mensaje = "Movimiento actualizado exitosamente.";
    } else {
        $mensaje = "Completa todos los campos correctamente.";
    }
}
?>

<div class="container mt-4">
    <h4>Editar Movimiento</h4>
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-12">
            <label class="form-label">Artículo</label>
            <select name="articulo_id" class="form-select" required>
                <option value="">Seleccionar</option>
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= $a['id'] == $movimiento['articulo_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select" required>
                <option value="entrada" <?= $movimiento['tipo'] === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= $movimiento['tipo'] === 'salida' ? 'selected' : '' ?>>Salida</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" value="<?= $movimiento['cantidad'] ?>" required>
        </div>

        <div class="col-12">
            <label class="form-label">Observación</label>
            <input type="text" name="observacion" class="form-control" value="<?= htmlspecialchars($movimiento['observacion']) ?>" required>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="historial.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
