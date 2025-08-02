<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// --- Resumen general ---
$resumen = [
    'entrada' => ['cantidad' => 0, 'movs' => 0],
    'salida' => ['cantidad' => 0, 'movs' => 0],
    'servicio' => ['cantidad' => 0, 'total' => 0.00, 'movs' => 0]
];

$sqlResumen = "SELECT tipo, sector, SUM(cantidad) AS cant, SUM(total) AS tot, COUNT(*) AS movs FROM movimientos GROUP BY tipo, sector";
foreach ($pdo->query($sqlResumen)->fetchAll() as $r) {
    if ($r['tipo'] === 'entrada' || $r['tipo'] === 'salida') {
        $resumen[$r['tipo']]['cantidad'] += $r['cant'];
        $resumen[$r['tipo']]['movs'] += $r['movs'];
    } elseif ($r['tipo'] === 'servicio') {
        $resumen['servicio']['cantidad'] += $r['cant'];
        $resumen['servicio']['total'] += $r['tot'];
        $resumen['servicio']['movs'] += $r['movs'];
    }
}

// --- Resumen por día (últimos 7 días) ---
$sqlDiario = "
    SELECT DATE(creado_en) AS fecha, tipo, sector, 
           SUM(cantidad) AS cantidad, 
           SUM(total) AS total
    FROM movimientos
    WHERE creado_en >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY fecha, tipo, sector
    ORDER BY fecha DESC
";
$resumenDiario = $pdo->query($sqlDiario)->fetchAll(PDO::FETCH_GROUP);

// --- Resumen por mes (últimos 6 meses) ---
$sqlMensual = "
    SELECT DATE_FORMAT(creado_en, '%Y-%m') AS mes, tipo, sector, 
           SUM(cantidad) AS cantidad, 
           SUM(total) AS total
    FROM movimientos
    WHERE creado_en >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY mes, tipo, sector
    ORDER BY mes DESC
";
$resumenMensual = $pdo->query($sqlMensual)->fetchAll(PDO::FETCH_GROUP);
?>

<div class="container mt-4">
    <h4>📊 Resumen General de Movimientos</h4>

    <!-- Tarjetas de resumen -->
    <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
        <div class="col">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Entradas</h5>
                    <p>Movimientos: <?= $resumen['entrada']['movs'] ?></p>
                    <p>Unidades: <?= $resumen['entrada']['cantidad'] ?></p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">Salidas</h5>
                    <p>Movimientos: <?= $resumen['salida']['movs'] ?></p>
                    <p>Unidades: <?= $resumen['salida']['cantidad'] ?></p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">Servicios</h5>
                    <p>Movimientos: <?= $resumen['servicio']['movs'] ?></p>
                    <p>Total facturado: $<?= number_format($resumen['servicio']['total'], 2) ?></p>
                    <p>Realizados: <?= $resumen['servicio']['cantidad'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen diario -->
    <h5 class="mt-5">📅 Actividad diaria (últimos 7 días)</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Sector</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resumenDiario as $fecha => $grupos): ?>
                    <?php foreach ($grupos as $r): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($fecha)) ?></td>
                            <td><?= ucfirst($r['tipo']) ?></td>
                            <td><?= ucfirst($r['sector']) ?></td>
                            <td><?= $r['cantidad'] ?></td>
                            <td><?= $r['total'] !== null ? '$' . number_format($r['total'], 2) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Resumen mensual -->
    <h5 class="mt-5">🗓️ Actividad mensual (últimos 6 meses)</h5>
    <div class="table-responsive mb-5">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mes</th>
                    <th>Tipo</th>
                    <th>Sector</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resumenMensual as $mes => $grupos): ?>
                    <?php foreach ($grupos as $r): ?>
                        <tr>
                            <td><?= $mes ?></td>
                            <td><?= ucfirst($r['tipo']) ?></td>
                            <td><?= ucfirst($r['sector']) ?></td>
                            <td><?= $r['cantidad'] ?></td>
                            <td><?= $r['total'] !== null ? '$' . number_format($r['total'], 2) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
