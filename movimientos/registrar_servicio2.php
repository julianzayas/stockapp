<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

// Obtener todos los servicios
$servicios = $pdo->query("SELECT * FROM servicios ORDER BY marca, modelo")->fetchAll();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = $_POST['servicio_id'];
    $tipo_servicio = $_POST['tipo_servicio'];
    $letras_rojas = isset($_POST['letras_rojas']) ? 1 : 0;
    $pegado_tapa = isset($_POST['pegado_tapa']) ? 1 : 0;
    $monto_extra = floatval($_POST['monto_extra']) ?? 0.00;
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    // Buscar el servicio
    $stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
    $stmt->execute([$servicio_id]);
    $servicio = $stmt->fetch();

    if ($servicio && $tipo_servicio && $usuario_id) {
        $precio_base = floatval($servicio[$tipo_servicio]);
        $total = $precio_base + $monto_extra;
        if ($letras_rojas) $total += 500;
        if ($pegado_tapa) $total += 800;

        // Guardar el movimiento (observación guarda tipo de servicio)
        $stmt = $pdo->prepare("INSERT INTO movimientos (usuario_id, tipo, sector, item_id, cantidad, total, observacion) 
                               VALUES (?, 'servicio', 'servicio', ?, 1, ?, ?)");
        $stmt->execute([$usuario_id, $servicio_id, $total, $tipo_servicio]);

        $mensaje = "✔️ Servicio registrado correctamente.";
    } else {
        $mensaje = "⚠️ Datos incompletos o inválidos.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>Registrar Movimiento de Servicio</h4>

    <?php if ($mensaje): ?>
        <div class="alert alert-info alert-dismissible fade show"><?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-12">
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
                    >
                        <?= $s['marca'] ?> <?= $s['modelo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Tipo de Servicio</label>
            <select name="tipo_servicio" id="tipo_servicio" class="form-select" required>
                <option value="">Seleccionar...</option>
                <option value="activacion">Activación</option>
                <option value="software">Software</option>
                <option value="formatear">Formatear</option>
                <option value="frp">FRP</option>
                <option value="pin_de_carga">Pin de Carga</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Precio Base</label>
            <input type="text" id="precio_base" class="form-control" disabled>
        </div>

        <div class="col-md-6">
            <label class="form-label">Monto adicional</label>
            <input type="number" step="0.01" min="0" name="monto_extra" class="form-control" value="0.00">
        </div>

        <div class="col-md-6 form-check mt-4">
            <input type="checkbox" class="form-check-input" id="letras_rojas" name="letras_rojas">
            <label for="letras_rojas" class="form-check-label">Letras rojas (+$500)</label>
        </div>

        <div class="col-md-6 form-check mt-4">
            <input type="checkbox" class="form-check-input" id="pegado_tapa" name="pegado_tapa">
            <label for="pegado_tapa" class="form-check-label">Pegado de tapa (+$800)</label>
        </div>

        <div class="col-12">
            <label class="form-label">Total estimado</label>
            <input type="text" id="total_servicio" class="form-control" disabled>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Registrar Servicio</button>
            <a href="historial.php" class="btn btn-secondary">Volver</a>
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
