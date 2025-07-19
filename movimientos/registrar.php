<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// Obtener lista de artículos
$accesorios = $pdo->query("SELECT id, nombre FROM accesorios ORDER BY nombre")->fetchAll();

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $articulo_id = $_POST['articulo_id'];
    $cantidad = (int)$_POST['cantidad'];
    $observacion = $_POST['observacion'];
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    if ($tipo && $articulo_id && $cantidad > 0 && $usuario_id) {
        // Insertar movimiento
        $stmt = $pdo->prepare("INSERT INTO movimientos (articulo_id, tipo, cantidad, observacion, usuario_id) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$articulo_id, $tipo, $cantidad, $observacion, $usuario_id]);

        // Actualizar stock
        if ($tipo === 'entrada') {
            $pdo->prepare("UPDATE accesorios SET cantidad = cantidad + ? WHERE id = ?")->execute([$cantidad, $articulo_id]);
        } else {
            $pdo->prepare("UPDATE accesorios SET cantidad = GREATEST(cantidad - ?, 0) WHERE id = ?")->execute([$cantidad, $articulo_id]);
        }

        $mensaje = "✅ Movimiento registrado exitosamente.";
    } else {
        $mensaje = "⚠️ Completa todos los campos correctamente.";
    }
}
?>

<div class="container mt-4">
    <h4 class="mb-4 text-center">Registrar Nuevo Movimiento</h4>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto">
        <div class="mb-3">
            <label class="form-label">Tipo de Movimiento</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo" id="entrada" value="entrada" required>
                <label class="form-check-label" for="entrada">Entrada</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo" id="salida" value="salida" required>
                <label class="form-check-label" for="salida">Salida</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Artículo</label>
            <select name="articulo_id" class="form-select" required>
                <option value="">Seleccionar</option>
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Observación</label>
            <input type="text" name="observacion" class="form-control" placeholder="Opcional">
        </div>

        <div class="d-flex gap-2 justify-content-start flex-wrap">
            <button type="submit" class="btn btn-success">Registrar Movimiento</button>
            <a href="historial.php" class="btn btn-secondary">Volver al Historial</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
