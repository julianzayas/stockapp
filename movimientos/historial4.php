<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// Filtros
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_sector = $_GET['sector'] ?? '';
$mostrar_inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] === '1';

$where = [];
$params = [];

if (!$mostrar_inactivos) {
    $where[] = "m.activo = 1";
}

if ($filtro_tipo !== '') {
    $where[] = "m.tipo = ?";
    $params[] = $filtro_tipo;
}

if ($filtro_sector !== '') {
    $where[] = "m.sector = ?";
    $params[] = $filtro_sector;
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Consulta
$sql = "
    SELECT m.*, u.nombre AS usuario,
        CASE
            WHEN m.sector = 'accesorio' THEN (SELECT CONCAT(a.marca, ' ', a.nombre) FROM accesorios a WHERE a.id = m.item_id)
            WHEN m.sector = 'servicio' THEN (SELECT CONCAT(s.marca, ' ', s.modelo) FROM servicios s WHERE s.id = m.item_id)
            ELSE 'N/A'
        END AS descripcion
    FROM movimientos m
    LEFT JOIN usuarios u ON m.usuario_id = u.id
    $where_sql
    ORDER BY m.creado_en DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();

// Totales por tipo y sector
$resumen = [
    'entrada' => ['cantidad' => 0, 'movs' => 0],
    'salida' => ['cantidad' => 0, 'movs' => 0],
    'servicio' => ['cantidad' => 0, 'total' => 0.00, 'movs' => 0]
];

$stmt = $pdo->query("SELECT tipo, sector, SUM(cantidad) as cant, SUM(total) as tot, COUNT(*) as movs FROM movimientos GROUP BY tipo, sector");
$resultados = $stmt->fetchAll();

foreach ($resultados as $r) {
    if ($r['tipo'] === 'entrada' || $r['tipo'] === 'salida') {
        $resumen[$r['tipo']]['cantidad'] += $r['cant'];
        $resumen[$r['tipo']]['movs'] += $r['movs'];
    } elseif ($r['tipo'] === 'servicio') {
        $resumen['servicio']['cantidad'] += $r['cant'];
        $resumen['servicio']['total'] += $r['tot'];
        $resumen['servicio']['movs'] += $r['movs'];
    }
}

?>

<div class="container mt-4">
    <h4>Historial de Movimientos</h4>

    <!-- Botones rápidos -->
    <div class="mb-3">
        <a href="registrar_accesorio.php" class="btn btn-outline-primary btn-sm me-2">+ Accesorio</a>
        <a href="registrar_servicio.php" class="btn btn-outline-success btn-sm">+ Servicio</a>
    </div>

    <!-- Filtros -->
    <form class="row row-cols-lg-auto g-2 align-items-end mb-3" method="GET">
        <div class="col">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
                <option value="">Todos</option>
                <option value="entrada" <?= $filtro_tipo === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= $filtro_tipo === 'salida' ? 'selected' : '' ?>>Salida</option>
                <option value="servicio" <?= $filtro_tipo === 'servicio' ? 'selected' : '' ?>>Servicio</option>
            </select>
        </div>
        <div class="col">
            <label class="form-label">Sector</label>
            <select name="sector" class="form-select">
                <option value="">Todos</option>
                <option value="accesorio" <?= $filtro_sector === 'accesorio' ? 'selected' : '' ?>>Accesorios</option>
                <option value="servicio" <?= $filtro_sector === 'servicio' ? 'selected' : '' ?>>Servicios</option>
            </select>
        </div>
        <div class="col form-check mt-4">
            <input type="checkbox" class="form-check-input" id="inactivos" name="inactivos" value="1" <?= $mostrar_inactivos ? 'checked' : '' ?>>
            <label class="form-check-label" for="inactivos">Ver inactivos</label>
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="historial.php" class="btn btn-outline-secondary">Limpiar</a>
        </div>
    </form>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['mensaje']) ?>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Totales -->
    <div class="container mt-4">
        <h5>📋 Resumen de movimientos</h5>
        <div class="row row-cols-1 row-cols-md-3 g-3">
            <div class="col">
                <div class="card border-success h-100">
                    <div class="card-body">
                        <h6 class="card-title text-success">Entradas de Accesorios</h6>
                        <p class="card-text mb-1">Movimientos: <?= $resumen['entrada']['movs'] ?></p>
                        <p class="card-text">Total ingresado: <strong><?= $resumen['entrada']['cantidad'] ?></strong> unidades</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card border-danger h-100">
                    <div class="card-body">
                        <h6 class="card-title text-danger">Salidas de Accesorios</h6>
                        <p class="card-text mb-1">Movimientos: <?= $resumen['salida']['movs'] ?></p>
                        <p class="card-text">Total retirado: <strong><?= $resumen['salida']['cantidad'] ?></strong> unidades</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h6 class="card-title text-primary">Servicios Realizados</h6>
                        <p class="card-text mb-1">Movimientos: <?= $resumen['servicio']['movs'] ?></p>
                        <p class="card-text">Ingresos: <strong>$<?= number_format($resumen['servicio']['total'], 2) ?></strong><br>
                        Total servicios: <?= $resumen['servicio']['cantidad'] ?></p>
                    </div>
                </div>
            </div>
            </br>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Sector</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Total ($)</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $m): ?>
                    <tr class="<?= $m['activo'] ? '' : 'table-secondary' ?>">
                        <td><?= date('d/m/Y H:i', strtotime($m['creado_en'])) ?></td>
                        <td><?= ucfirst($m['tipo']) ?></td>
                        <td><?= ucfirst($m['sector']) ?></td>
                        <td><?= htmlspecialchars($m['descripcion']) ?></td>
                        <td><?= $m['cantidad'] ?></td>
                        <td><?= number_format($m['total'], 2) ?></td>
                        <td><?= htmlspecialchars($m['usuario']) ?></td>
                        <td>
                            <?php if ($_SESSION['rol'] === 'admin'): ?>
                                <?php if ($m['sector'] === 'accesorio'): ?>
                                <a href="editar_accesorio.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">✏️</a>
                                <?php elseif ($m['sector'] === 'servicio'): ?>
                                <a href="editar_servicio.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">✏️</a>
                                <?php endif; ?>

                                
                                <a href="editar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">✏️</a>
                                <a href="estado.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-warning" title="Activar/Desactivar" onclick="return confirm('¿Deseas cambiar el estado?')">🔄</a>
                                <a href="eliminar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este movimiento?')">🗑️</a>
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
