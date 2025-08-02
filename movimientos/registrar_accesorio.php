<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// Obtener accesorios activos
$accesorios = $pdo->query("SELECT id, nombre, marca FROM accesorios WHERE activo = 1 ORDER BY marca, nombre")->fetchAll();

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accesorio_id = $_POST['accesorio_id'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    $cantidad = (int) ($_POST['cantidad'] ?? 0);
    $observacion = $_POST['observacion'] ?? '';
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    if ($accesorio_id && $tipo && $cantidad > 0 && $usuario_id) {
        // Total como placeholder, podría calcularse si se desea más adelante
        $total = 0.00;

        // Insertar en movimientos
        $stmt = $pdo->prepare("INSERT INTO movimientos (usuario_id, tipo, sector, item_id, cantidad, total, observacion)
                               VALUES (?, ?, 'accesorio', ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $tipo, $accesorio_id, $cantidad, $total, $observacion]);

        // Actualizar stock del accesorio
        if ($tipo === 'entrada') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = stock_actual + ? WHERE id = ?")
                ->execute([$cantidad, $accesorio_id]);
        } elseif ($tipo === 'salida') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?")
                ->execute([$cantidad, $accesorio_id]);
        }

        $mensaje = "Movimiento registrado correctamente.";
    } else {
        $mensaje = "Completa todos los campos obligatorios.";
    }
}
?>

<div class="container mt-4">
    <h4>Registrar Movimiento de Accesorio</h4>
    <?php if ($mensaje): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $mensaje ?>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Accesorio (Marca / Nombre)</label>
            <select name="accesorio_id" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>">
                        <?= htmlspecialchars($a['marca'] . ' ' . $a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de Movimiento</label>
            <select name="tipo" class="form-select" required>
                <option value="">Seleccionar...</option>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Observación</label>
            <textarea name="observacion" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-success">Registrar Movimiento</button>
            <a href="historial.php" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
