<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $activacion = $_POST['activacion'] ?? 0;
    $software = $_POST['software'] ?? 0;
    $frp = $_POST['frp'] ?? 0;
    $formatear = $_POST['formatear'] ?? 0;
    $pin = $_POST['pin_de_carga'] ?? 0;
    $letras = isset($_POST['letras_rojas']) ? 1 : 0;
    $pegado = isset($_POST['pegado_tapa']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO servicios (marca, modelo, activacion, software, frp, formatear, pin_de_carga, letras_rojas, pegado_tapa, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([$marca, $modelo, $activacion, $software, $frp, $formatear, $pin, $letras, $pegado]);

    $_SESSION['mensaje'] = "Servicio agregado correctamente.";
    header("Location: listar.php");
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4>Nuevo Servicio</h4>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Marca</label>
            <input type="text" name="marca" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Modelo</label>
            <input type="text" name="modelo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Activación ($)</label>
            <input type="number" step="0.01" name="activacion" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Software ($)</label>
            <input type="number" step="0.01" name="software" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">FRP/Cuenta ($)</label>
            <input type="number" step="0.01" name="frp" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Formatear ($)</label>
            <input type="number" step="0.01" name="formatear" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Pin de carga ($)</label>
            <input type="number" step="0.01" name="pin_de_carga" class="form-control">
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="letras_rojas" value="1">
                <label class="form-check-label">Letras Rojas</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="pegado_tapa" value="1">
                <label class="form-check-label">Pegado de tapa</label>
            </div>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-success">Agregar Servicio</button>
            <a href="listar.php" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
