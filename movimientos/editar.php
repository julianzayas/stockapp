<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: historial.php?mensaje=ID inválido");
    exit;
}

// Obtener el movimiento
$stmt = $pdo->prepare("SELECT * FROM movimientos WHERE id = ?");
$stmt->execute([$id]);
$mov = $stmt->fetch();

if (!$mov) {
    header("Location: historial.php?mensaje=Movimiento no encontrado");
    exit;
}

$accesorios = $pdo->query("SELECT id, CONCAT(marca, ' ', nombre) AS nombre FROM accesorios WHERE activo = 1 ORDER BY nombre")->fetchAll();
$servicios = $pdo->query("SELECT id, CONCAT(marca, ' ', modelo) AS nombre FROM servicios ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $sector = $_POST['sector'];
    $item_id = $_POST['item_id'];
    $cantidad_nueva = (int) $_POST['cantidad'];
    $observacion = $_POST['observacion'];

    // Lógica opcional: revertir stock anterior si era accesorio
    if ($mov['sector'] === 'accesorio') {
        if ($mov['tipo'] === 'entrada') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?")
                ->execute([$mov['cantidad'], $mov['item_id']]);
        } elseif ($mov['tipo'] === 'salida') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = stock_actual + ? WHERE id = ?")
                ->execute([$mov['cantidad'], $mov['item_id']]);
        }
    }

    // Si nuevo sector es accesorio, ajustar stock
    if ($sector === 'accesorio') {
        if ($tipo === 'entrada') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = stock_actual + ? WHERE id = ?")
                ->execute([$cantidad_nueva, $item_id]);
        } elseif ($tipo === 'salida') {
            $pdo->prepare("UPDATE accesorios SET stock_actual = GREATEST(stock_actual - ?, 0) WHERE id = ?")
                ->execute([$cantidad_nueva, $item_id]);
        }
    }

    // Actualizar movimiento
    $stmt = $pdo->prepare("UPDATE movimientos SET tipo = ?, sector = ?, item_id = ?, cantidad = ?, observacion = ? WHERE id = ?");
    $stmt->execute([$tipo, $sector, $item_id, $cantidad_nueva, $observacion, $id]);

    header("Location: historial.php?mensaje=Movimiento actualizado correctamente.");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>Editar Movimiento</h4>

    <form method="POST" class="row g-3">
        <div class="col-12">
            <label class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select" required>
                <option value="entrada" <?= $mov['tipo'] === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= $mov['tipo'] === 'salida' ? 'selected' : '' ?>>Salida</option>
                <option value="servicio" <?= $mov['tipo'] === 'servicio' ? 'selected' : '' ?>>Servicio</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Sector</label>
            <select name="sector" id="sector" class="form-select" required>
                <option value="accesorio" <?= $mov['sector'] === 'accesorio' ? 'selected' : '' ?>>Accesorio</option>
                <option value="servicio" <?= $mov['sector'] === 'servicio' ? 'selected' : '' ?>>Servicio</option>
            </select>
        </div>

        <div class="col-12" id="item-group">
            <label class="form-label">Ítem</label>

            <select name="item_id" class="form-select accesorios">
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= $mov['item_id'] == $a['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="item_id" class="form-select servicios" style="display:none">
                <?php foreach ($servicios as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $mov['item_id'] == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" value="<?= $mov['cantidad'] ?>" required min="1">
        </div>

        <div class="col-12">
            <label class="form-label">Observación</label>
            <input type="text" name="observacion" class="form-control" value="<?= htmlspecialchars($mov['observacion']) ?>">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="historial.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<!-- Lógica de validación dinámica -->
<script>
    const tipo = document.getElementById('tipo');
    const sector = document.getElementById('sector');
    const itemGroup = document.getElementById('item-group');
    const accesorios = itemGroup.querySelector('.accesorios');
    const servicios = itemGroup.querySelector('.servicios');

    function actualizarFormulario() {
        const t = tipo.value;

        if (t === 'servicio') {
            sector.value = 'servicio';
            sector.querySelector('option[value="accesorio"]').disabled = true;
            sector.querySelector('option[value="servicio"]').disabled = false;

            accesorios.style.display = 'none';
            servicios.style.display = 'block';
        } else {
            sector.value = 'accesorio';
            sector.querySelector('option[value="accesorio"]').disabled = false;
            sector.querySelector('option[value="servicio"]').disabled = true;

            accesorios.style.display = 'block';
            servicios.style.display = 'none';
        }
    }

    tipo.addEventListener('change', actualizarFormulario);
    sector.addEventListener('change', actualizarFormulario);
    window.addEventListener('DOMContentLoaded', actualizarFormulario);
</script>

<?php include '../includes/footer.php'; ?>
