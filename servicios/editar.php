<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

$id = (int) $_GET['id'];

// Obtener datos actuales del servicio
$stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->execute([$id]);
$servicio = $stmt->fetch();

if (!$servicio) {
    $_SESSION['error'] = "Servicio no encontrado.";
    header("Location: listar.php");
    exit;
}

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

    $stmt = $pdo->prepare("UPDATE servicios SET marca = ?, modelo = ?, activacion = ?, software = ?, frp = ?, formatear = ?, pin_de_carga = ?, letras_rojas = ?, pegado_tapa = ? WHERE id = ?");
    $stmt->execute([$marca, $modelo, $activacion, $software, $frp, $formatear, $pin, $letras, $pegado, $id]);

    $_SESSION['mensaje'] = "Servicio actualizado correctamente.";
    header("Location: listar.php");
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4>Editar Servicio</h4>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Marca</label>
            <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($servicio['marca']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Modelo</label>
            <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($servicio['modelo']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Activación ($)</label>
            <input type="number" step="0.01" name="activacion" class="form-control" value="<?= $servicio['activacion'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Software ($)</label>
            <input type="number" step="0.01" name="software" class="form-control" value="<?= $servicio['software'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">FRP/Cuenta ($)</label>
            <input type="number" step="0.01" name="frp" class="form-control" value="<?= $servicio['frp'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Formatear ($)</label>
            <input type="number" step="0.01" name="formatear" class="form-control" value="<?= $servicio['formatear'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Pin de carga ($)</label>
            <input type="number" step="0.01" name="pin_de_carga" class="form-control" value="<?= $servicio['pin_de_carga'] ?>">
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="letras_rojas" value="1" <?= $servicio['letras_rojas'] ? 'checked' : '' ?>>
                <label class="form-check-label">Letras Rojas</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="pegado_tapa" value="1" <?= $servicio['pegado_tapa'] ? 'checked' : '' ?>>
                <label class="form-check-label">Pegado de tapa</label>
            </div>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
