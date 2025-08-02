<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

// Calcular resumen de totales
$resumen = [
    'entrada' => ['cantidad' => 0, 'movs' => 0],
    'salida' => ['cantidad' => 0, 'movs' => 0],
    'servicio' => ['cantidad' => 0, 'total' => 0.00, 'movs' => 0]
];

$stmt = $pdo->query("SELECT tipo, sector, SUM(cantidad) as cant, SUM(total) as tot, COUNT(*) as movs FROM movimientos GROUP BY tipo, sector");
foreach ($stmt->fetchAll() as $r) {
    if ($r['tipo'] === 'entrada' || $r['tipo'] === 'salida') {
        $resumen[$r['tipo']]['cantidad'] += $r['cant'];
        $resumen[$r['tipo']]['movs'] += $r['movs'];
    } elseif ($r['tipo'] === 'servicio') {
        $resumen['servicio']['cantidad'] += $r['cant'];
        $resumen['servicio']['total'] += $r['tot'];
        $resumen['servicio']['movs'] += $r['movs'];
    }
}

// Obtener todos los movimientos con nombres
$stmt = $pdo->query("
    SELECT 
        m.*, 
        a.nombre AS accesorio_nombre, 
        s.marca AS servicio_marca, 
        s.modelo AS servicio_modelo, 
        m.tipo AS tipo_mov,
        m.sector AS sector_mov,
        u.nombre AS usuario_nombre
    FROM movimientos m
    LEFT JOIN accesorios a ON m.sector = 'accesorio' AND m.item_id = a.id
    LEFT JOIN servicios s ON m.sector = 'servicio' AND m.item_id = s.id
    LEFT JOIN usuarios u ON m.usuario_id = u.id
    ORDER BY m.creado_en DESC
");
$movimientos = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>📦 Historial de Movimientos</h4>

    <!-- Botones de Registro -->
    <div class="mb-3">
        <a href="registrar.php" class="btn btn-success me-2">Registrar Movimiento de Accesorio</a>
        <a href="registrar_servicio.php" class="btn btn-primary">Registrar Movimiento de Servicio</a>
    </div>

    <!-- Resumen de totales -->
    <div class="container mt-4">
        <h5>📋 Resumen de movimientos</h5>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
            <div class="col">
                <div class="card border-success h-100">
                    <div class="card-body">
                        <h6 class="card-title text-success">Entradas</h6>
                        <p class="card-text mb-1">Movimientos: <?= $resumen['entrada']['movs'] ?></p>
                        <p class="card-text">Unidades: <strong><?= $resumen['entrada']['cantidad'] ?></strong></p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-danger h-100">
                    <div class="card-body">
                        <h6 class="card-title text-danger">Salidas</h6>
                        <p class="card-text mb-1">Movimientos: <?= $resumen['salida']['movs'] ?></p>
                        <p class="card-text">Unidades: <strong><?= $resumen['salida']['cantidad'] ?></strong></p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Servicios</h6>
                        <p class="card-text mb-1">Movimientos: <?= $resumen['servicio']['movs'] ?></p>
                        <p class="card-text mb-1">Total: <strong>$<?= number_format($resumen['servicio']['total'], 2) ?></strong></p>
                        <p class="card-text">Cantidad: <?= $resumen['servicio']['cantidad'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="tipo" class="form-select">
                <option value="">Todos los tipos</option>
                <option value="entrada" <?= ($_GET['tipo'] ?? '') === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= ($_GET['tipo'] ?? '') === 'salida' ? 'selected' : '' ?>>Salida</option>
                <option value="servicio" <?= ($_GET['tipo'] ?? '') === 'servicio' ? 'selected' : '' ?>>Servicio</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="sector" class="form-select">
                <option value="">Todos los sectores</option>
                <option value="accesorio" <?= ($_GET['sector'] ?? '') === 'accesorio' ? 'selected' : '' ?>>Accesorios</option>
                <option value="servicio" <?= ($_GET['sector'] ?? '') === 'servicio' ? 'selected' : '' ?>>Servicios</option>
            </select>
        </div>
        <div class="col-md-3 form-check pt-2">
            <input type="checkbox" name="incluir_inactivos" class="form-check-input" id="incluir_inactivos"
                <?= isset($_GET['incluir_inactivos']) ? 'checked' : '' ?>>
            <label for="incluir_inactivos" class="form-check-label">Incluir inactivos</label>
        </div>
        <div class="col-md-3">
            <button class="btn btn-secondary w-100">Filtrar</button>
            <a href="historial.php" class="btn btn-outline-danger flex-fill">Reset</a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Sector</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Observación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $m): ?>
                    <tr class="<?= $m['tipo_mov'] === 'entrada' ? 'table-success' : ($m['tipo_mov'] === 'salida' ? 'table-danger' : 'table-primary') ?>">
                        <td><?= date('d/m/Y H:i', strtotime($m['creado_en'])) ?></td>
                        <td><?= ucfirst($m['tipo_mov']) ?></td>
                        <td><?= ucfirst($m['sector_mov']) ?></td>
                        <td>
                            <?php if ($m['sector_mov'] === 'accesorio'): ?>
                                <?= htmlspecialchars($m['accesorio_nombre']) ?>
                            <?php elseif ($m['sector_mov'] === 'servicio'): ?>
                                <?= htmlspecialchars(ucfirst($m['observacion'])) ?>
                                <?= htmlspecialchars($m['servicio_marca']) ?>
                                <?= htmlspecialchars($m['servicio_modelo']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= $m['cantidad'] ?></td>
                        <td><?= $m['total'] !== null ? '$' . number_format($m['total'], 2) : '-' ?></td>
                        <td><?= htmlspecialchars($m['usuario_nombre']) ?></td>
                        <td>
                            <?php if ($m['sector_mov'] === 'accesorio'): ?>
                                <a href="editar_accesorio.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                            <?php elseif ($m['sector_mov'] === 'servicio'): ?>
                                <a href="editar_servicio.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                            <?php endif; ?>
                            <a href="eliminar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar movimiento?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
