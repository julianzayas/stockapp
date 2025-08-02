<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// Obtener servicios activos
$servicios = $pdo->query("SELECT id, marca, modelo FROM servicios WHERE activo = 1 ORDER BY marca, modelo")->fetchAll();

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = $_POST['servicio_id'] ?? null;
    $tipo_servicio = $_POST['tipo_servicio'] ?? null;
    $observacion = $_POST['observacion'] ?? '';
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    if ($servicio_id && $tipo_servicio && $usuario_id) {
        // Obtener precio según el tipo
        $stmt = $pdo->prepare("SELECT $tipo_servicio FROM servicios WHERE id = ?");
        $stmt->execute([$servicio_id]);
        $precio = $stmt->fetchColumn();

        if ($precio === false) {
            $mensaje = "El tipo de servicio seleccionado no es válido.";
        } else {
            // Insertar en movimientos
            $stmt = $pdo->prepare("INSERT INTO movimientos (usuario_id, tipo, sector, item_id, cantidad, total, observacion)
                                   VALUES (?, 'servicio', 'servicio', ?, 1, ?, ?)");
            $stmt->execute([$usuario_id, $servicio_id, $precio, $observacion]);

            $mensaje = "Servicio registrado exitosamente.";
        }
    } else {
        $mensaje = "Completa todos los campos obligatorios.";
    }
}
?>

<div class="container mt-4">
    <h4>Registrar Servicio Realizado</h4>
    <?php if ($mensaje): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $mensaje ?>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Servicio (Marca / Modelo)</label>
            <select name="servicio_id" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($servicios as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['marca'] . ' ' . $s['modelo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de Servicio</label>
            <select name="tipo_servicio" class="form-select" required>
                <option value="">Seleccionar...</option>
                <option value="activacion">Activación</option>
                <option value="software">Software</option>
                <option value="frp">FRP / Cuenta</option>
                <option value="formatear">Formatear</option>
                <option value="pin_de_carga">Pin de Carga</option>
                <option value="letras_rojas">Letras Rojas</option>
                <option value="pegado_tapa">Pegado de Tapa</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Observación</label>
            <textarea name="observacion" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-success">Registrar Servicio</button>
            <a href="historial.php" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
