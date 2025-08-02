<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: historial.php");
    exit;
}

// Obtener movimiento
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ? AND tipo = 'servicio' AND sector = 'servicio'");
$stmt->execute([$id]);
$movimiento = $stmt->fetch();

if (!$movimiento) {
    include '../includes/header.php';
    include '../includes/navbar.php';
    echo "<div class='container mt-4'><div class='alert alert-danger'>Movimiento no encontrado.</div></div>";
    include '../includes/footer.php';
    exit;
}

// Obtener servicio asociado
$stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->execute([$movimiento['item_id']]);
$servicio_actual = $stmt->fetch();

// Obtener todos los servicios
$servicios = $pdo->query("SELECT * FROM servicios ORDER BY marca, modelo")->fetchAll();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = $_POST['servicio_id'];
    $tipo_servicio = $_POST['tipo_servicio'];
    $letras_rojas = isset($_POST['letras_rojas']) ? 1 : 0;
    $pegado_tapa = isset($_POST['pegado_tapa']) ? 1 : 0;
    $monto_extra = floatval($_POST['monto_extra']) ?? 0.00;

    // Obtener nuevo servicio
    $stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
    $stmt->execute([$servicio_id]);
    $servicio = $stmt->fetch();

    if ($servicio && $tipo_servicio) {
        $precio_base = floatval($servicio[$tipo_servicio]);
        $total = $precio_base + $monto_extra;
        if ($letras_rojas) $total += 500;
        if ($pegado_tapa) $total += 800;

        $stmt = $pdo->prepare("UPDATE movimientos 
            SET item_id = ?, total = ?, observacion = ? 
            WHERE id = ?");
        $stmt->execute([$servicio_id, $total, $tipo_servicio, $id]);

        header("Location: historial.php?mensaje=Servicio actualizado correctamente.");
        exit;
    } else {
        $mensaje = "Error al actualizar el movimiento.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>Editar Movimiento de Servicio</h4>

    <?php if ($mensaje): ?>
        <div class="alert alert-warning alert-dismissible fade show"><?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="mb-3">
                <label class="form-label">Dispositivo</label>
                <select name="servicio_id" id="servicio_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            data-activacion="<?= $s['activacion'] ?>"
                            data-software="<?= $s['software'] ?>"
                            data-formatear="<?= $s['formatear'] ?>"
                            data-frp="<?= $s['frp'] ?>"
                            data-pin_de_carga="<?= $s['pin_de_carga'] ?>"
                            <?= $s['id'] == $movimiento['item_id'] ? 'selected' : '' ?>
                        >
                            <?= $s['marca'] ?> <?= $s['modelo'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Servicio</label>
                <select name="tipo_servicio" id="tipo_servicio" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option value="activacion" <?= $movimiento['observacion'] == 'activacion' ? 'selected' : '' ?>>Activación</option>
                    <option value="software" <?= $movimiento['observacion'] == 'software' ? 'selected' : '' ?>>Software</option>
                    <option value="formatear" <?= $movimiento['observacion'] == 'formatear' ? 'selected' : '' ?>>Formatear</option>
                    <option value="frp" <?= $movimiento['observacion'] == 'frp' ? 'selected' : '' ?>>FRP</option>
                    <option value="pin_de_carga" <?= $movimiento['observacion'] == 'pin_de_carga' ? 'selected' : '' ?>>Pin de Carga</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Precio Base</label>
                <input type="text" id="precio_base" class="form-control" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Monto adicional</label>
                <input type="number" step="0.01" min="0" name="monto_extra" class="form-control" value="0.00">
            </div>

            <div class="row mb-3">
                <div class="col-6 form-check">
                    <input type="checkbox" class="form-check-input" id="letras_rojas" name="letras_rojas">
                    <label for="letras_rojas" class="form-check-label">Letras rojas (+$500)</label>
                </div>

                <div class="col-6 form-check">
                    <input type="checkbox" class="form-check-input" id="pegado_tapa" name="pegado_tapa">
                    <label for="pegado_tapa" class="form-check-label">Pegado de tapa (+$800)</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Total estimado</label>
                <input type="text" id="total_servicio" class="form-control" disabled>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="historial.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </form>
</div>

<script>
    const servicioSelect = document.getElementById('servicio_id');
    const tipoSelect = document.getElementById('tipo_servicio');
    const letrasRojas = document.getElementById('letras_rojas');
    const pegadoTapa = document.getElementById('pegado_tapa');
    const montoExtra = document.querySelector('input[name="monto_extra"]');
    const totalField = document.getElementById('total_servicio');
    const precioBaseField = document.getElementById('precio_base');

    function calcularTotal() {
        const servicio = servicioSelect.selectedOptions[0];
        const tipo = tipoSelect.value;

        if (!servicio || !tipo) {
            precioBaseField.value = '';
            totalField.value = '';
            return;
        }

        let precioBase = parseFloat(servicio.dataset[tipo]) || 0;
        let total = precioBase;

        if (letrasRojas.checked) total += 500;
        if (pegadoTapa.checked) total += 800;
        if (montoExtra.value) total += parseFloat(montoExtra.value) || 0;

        precioBaseField.value = `$${precioBase.toFixed(2)}`;
        totalField.value = `$${total.toFixed(2)}`;
    }

    servicioSelect.addEventListener('change', calcularTotal);
    tipoSelect.addEventListener('change', calcularTotal);
    letrasRojas.addEventListener('change', calcularTotal);
    pegadoTapa.addEventListener('change', calcularTotal);
    montoExtra.addEventListener('input', calcularTotal);

    window.addEventListener('DOMContentLoaded', calcularTotal);
</script>

<?php include '../includes/footer.php'; ?>
