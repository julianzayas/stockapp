<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

// --- FILTROS DINÁMICOS ---
$filtros = [];
$params = [];

if (!empty($_GET['tipo'])) {
    $filtros[] = "m.tipo = ?";
    $params[] = $_GET['tipo'];
}

if (!empty($_GET['sector'])) {
    $filtros[] = "m.sector = ?";
    $params[] = $_GET['sector'];
}

$incluir_inactivos = isset($_GET['incluir_inactivos']);

$where = count($filtros) ? 'WHERE ' . implode(' AND ', $filtros) : '';

// --- CONSULTA PRINCIPAL ---
$sql = "
    SELECT 
        m.*, 
        u.nombre AS usuario_nombre,
        u.rol AS usuario_rol,
        a.nombre AS accesorio_nombre,
        s.marca AS servicio_marca,
        s.modelo AS servicio_modelo,
        m.tipo AS tipo_mov,
        m.sector AS sector_mov,
        m.observacion
    FROM movimientos m
    LEFT JOIN usuarios u ON m.usuario_id = u.id
    LEFT JOIN accesorios a ON m.item_id = a.id AND m.sector = 'accesorio' " . (!$incluir_inactivos ? "AND a.activo = 1" : "") . "
    LEFT JOIN servicios s ON m.item_id = s.id AND m.sector = 'servicio' " . (!$incluir_inactivos ? "AND s.activo = 1" : "") . "
    $where
    ORDER BY m.creado_en DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();

// Calcular resumen de totales
/* $resumen = [
    'entrada' => ['cantidad' => 0, 'movs' => 0],
    'salida' => ['cantidad' => 0, 'movs' => 0],
    'servicio' => ['cantidad' => 0, 'total' => 0.00, 'movs' => 0]
];

$res = $pdo->query("SELECT tipo, sector, SUM(cantidad) as cant, SUM(total) as tot, COUNT(*) as movs FROM movimientos GROUP BY tipo, sector");
foreach ($res->fetchAll() as $r) {
    if ($r['tipo'] === 'entrada' || $r['tipo'] === 'salida') {
        $resumen[$r['tipo']]['cantidad'] += $r['cant'];
        $resumen[$r['tipo']]['movs'] += $r['movs'];
    } elseif ($r['tipo'] === 'servicio') {
        $resumen['servicio']['cantidad'] += $r['cant'];
        $resumen['servicio']['total'] += $r['tot'];
        $resumen['servicio']['movs'] += $r['movs'];
    }
} */
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>📦 Historial de Movimientos</h4>

    <!-- Botones de Registro -->
    <div class="mb-3">
        <a href="registrar_accesorio.php" class="btn btn-success me-2">Movimiento de Stock</a>
        <a href="registrar_servicio.php" class="btn btn-primary me-2">Registrar Servicio</a>
        <a href="resumenes.php" class="btn btn-outline-dark">📊 Ver resúmenes</a>

    </div>

    <!-- Resumen de movimientos -->
    <!-- <div class="container mb-4">
        <h5>📋 Resumen de movimientos</h5>
        <div class="row row-cols-1 row-cols-md-3 g-3">
            <div class="col">
                <div class="card border-success">
                    <div class="card-body">
                        <h6 class="card-title text-success">Entradas</h6>
                        <p>Movimientos: <?= $resumen['entrada']['movs'] ?></p>
                        <p>Unidades: <?= $resumen['entrada']['cantidad'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger">Salidas</h6>
                        <p>Movimientos: <?= $resumen['salida']['movs'] ?></p>
                        <p>Unidades: <?= $resumen['salida']['cantidad'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Servicios</h6>
                        <p>Movimientos: <?= $resumen['servicio']['movs'] ?></p>
                        <p>Total: $<?= number_format($resumen['servicio']['total'], 2) ?></p>
                        <p>Realizados: <?= $resumen['servicio']['cantidad'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Filtros -->
    <h5><i class="bi bi-funnel"></i> ⚙️ Filtrar Movimientos</h5>
    <p>Utiliza los filtros para buscar movimientos específicos.</p>
    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
                <option value="">Todos</option>
                <option value="entrada" <?= ($_GET['tipo'] ?? '') === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= ($_GET['tipo'] ?? '') === 'salida' ? 'selected' : '' ?>>Salida</option>
                <option value="servicio" <?= ($_GET['tipo'] ?? '') === 'servicio' ? 'selected' : '' ?>>Servicio</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Sector</label>
            <select name="sector" class="form-select">
                <option value="">Todos</option>
                <option value="accesorio" <?= ($_GET['sector'] ?? '') === 'accesorio' ? 'selected' : '' ?>>Accesorios</option>
                <option value="servicio" <?= ($_GET['sector'] ?? '') === 'servicio' ? 'selected' : '' ?>>Servicios</option>
            </select>
        </div>
        <div class="col-md-2 form-check pt-4">
            <input type="checkbox" name="incluir_inactivos" class="form-check-input" id="incluir_inactivos"
                <?= isset($_GET['incluir_inactivos']) ? 'checked' : '' ?>>
            <label for="incluir_inactivos" class="form-check-label">Incluir inactivos</label>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-secondary flex-fill">Filtrar</button>
            <a href="historial.php" class="btn btn-outline-danger flex-fill">Reset</a>
        </div>
    </form>

    <!-- Tabla de movimientos -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Sector</th>
                    <th>Descripción</th>
                    <th>Cant.</th>
                    <th>Total</th>
                    <th>Usuario</th>
                    <th>Obs.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $m): ?>
                    <tr class="<?= $m['tipo_mov'] === 'entrada' ? 'table-success' : ($m['tipo_mov'] === 'salida' ? 'table-danger' : 'table-primary') ?>">
                        <td><?= date('d/m/Y H:i', strtotime($m['creado_en'])) ?></td>
                        <!-- <td><?= ucfirst($m['tipo_mov']) ?></td> -->
                        <td class="text-center">
                            <?php if ($m['tipo'] === 'servicio'): ?>
                                <span class="badge bg-<?= $m['tipo'] === 'servicio' ? 'primary' : 'danger' ?>">
                                    <?= ucfirst($m['tipo']) ?>
                                </span>
                            <?php elseif ($m['sector_mov'] === 'accesorio'): ?>
                                <span class="badge bg-<?= $m['tipo'] === 'entrada' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($m['tipo']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= ucfirst($m['sector_mov']) ?></td>
                        <td>
                            <?php if ($m['sector_mov'] === 'accesorio'): ?>
                                <?= htmlspecialchars($m['accesorio_nombre']) ?>
                            <?php elseif ($m['sector_mov'] === 'servicio'): ?>
                                <?= htmlspecialchars(ucfirst($m['observacion'])) ?> (<?= $m['servicio_marca'] ?> <?= $m['servicio_modelo'] ?>)
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $m['cantidad'] ?></td>
                        <td><?= $m['total'] !== null ? '$' . number_format($m['total'], 2) : '-' ?></td>
                        <td><?= htmlspecialchars($m['usuario_nombre']) ?></td>
                        <td class="text-wrap"><?= htmlspecialchars($m['observacion']) ?></td>
                        <td>
                            <?php if ($_SESSION['rol'] === 'admin'): ?>
    
                                <?php if ($m['sector_mov'] === 'accesorio'): ?>
                                    <a href="editar_accesorio.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                                <?php else: ?>
                                    <a href="editar_servicio.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                                <?php endif; ?>
                                <a href="eliminar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar movimiento?')">🗑️</a>
                            <?php else: ?>
                                <span class="text-muted">Sin permisos</span>
                            <?php endif; ?>
                        
                            </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
