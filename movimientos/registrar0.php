<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$accesorios = $pdo->query("SELECT * FROM accesorios ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $cantidad = (int) $_POST['cantidad'];
    $motivo = $_POST['motivo'];
    $articulo_id = $_POST['articulo_id'];

    // Registrar movimiento
    $stmt = $pdo->prepare("INSERT INTO movimientos (articulo_id, tipo, cantidad, motivo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$articulo_id, $tipo, $cantidad, $motivo]);

    // Actualizar stock en tabla de artículos
    $ajuste = $tipo === 'entrada' ? $cantidad : -$cantidad;
    $pdo->prepare("UPDATE accesorios SET cantidad = cantidad + ? WHERE id = ?")->execute([$ajuste, $articulo_id]);

    header("Location: listar.php");
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4>Registrar Movimiento</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Artículo</label>
            <select name="articulo_id" class="form-control" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Tipo de Movimiento</label>
            <select name="tipo" class="form-control" required>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Cantidad</label>
            <input type="number" name="cantidad" class="form-control" required min="1">
        </div>
        <div class="mb-3">
            <label>Motivo (opcional)</label>
            <input type="text" name="motivo" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>