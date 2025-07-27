<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $activacion = $_POST['activacion'] ?? 0;
    $software = $_POST['software'] ?? 0;
    $frp = $_POST['frp'] ?? 0;
    $formatear = $_POST['formatear'] ?? 0;
    $pin = $_POST['pin_de_carga'] ?? 0;
    $letras = isset($_POST['letras_rojas']) ? 1 : 0;
    $pegado = isset($_POST['pegado_tapa']) ? 1 : 0;

    if ($marca && $modelo) {
        $stmt = $pdo->prepare("INSERT INTO servicios (marca, modelo, activacion, software, frp, formatear, pin_de_carga, letras_rojas, pegado_tapa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$marca, $modelo, $activacion, $software, $frp, $formatear, $pin, $letras, $pegado]);
        $mensaje = "Servicio agregado correctamente.";
    } else {
        $mensaje = "Marca y modelo son obligatorios.";
    }
}
?>

<div class="container mt-4">
    <h4>Agregar Servicio</h4>
    <?php if ($mensaje): ?>
        <div class="alert alert-info"> <?= $mensaje ?> </div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-12">
            <label class="form-label">Marca</label>
            <input type="text" name="marca" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Modelo</label>
            <input type="text" name="modelo" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Activación ($)</label>
            <input type="number" name="activacion" class="form-control" step="0.01">
        </div>
        <div class="col-12">
            <label class="form-label">Software ($)</label>
            <input type="number" name="software" class="form-control" step="0.01">
        </div>
        <div class="col-12">
            <label class="form-label">FRP - Cuenta ($)</label>
            <input type="number" name="frp" class="form-control" step="0.01">
        </div>
        <div class="col-12">
            <label class="form-label">Formatear ($)</label>
            <input type="number" name="formatear" class="form-control" step="0.01">
        </div>
        <div class="col-12">
            <label class="form-label">Pin de carga ($)</label>
            <input type="number" name="pin_de_carga" class="form-control" step="0.01">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="letras_rojas" class="form-check-input" id="letras">
                <label class="form-check-label" for="letras">Letras rojas</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="pegado_tapa" class="form-check-input" id="pegado">
                <label class="form-check-label" for="pegado">Pegado de tapa</label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Agregar</button>
            <a href="listar.php" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
