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

        $mensaje = "Movimiento registrado exitosamente.";
    } else {
        $mensaje = "Completa todos los campos correctamente.";
    }
}
?>

<div class="container mt-4">
    <h4>Registrar Nuevo Movimiento</h4>
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select" required>
                <option value="">Seleccionar</option>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Artículo</label>
            <select name="articulo_id" class="form-select" required>
                <option value="">Seleccionar</option>
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
        </div>
        <div class="col-12">
            <label class="form-label">Observación</label>
            <input type="text" name="observacion" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Registrar Movimiento</button>
            <a href="historial.php" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
