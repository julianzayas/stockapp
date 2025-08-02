<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// Filtros
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_sector = $_GET['sector'] ?? '';

$where = [];
$params = [];

if ($filtro_tipo !== '') {
    $where[] = "m.tipo = ?";
    $params[] = $filtro_tipo;
}

if ($filtro_sector !== '') {
    $where[] = "m.sector = ?";
    $params[] = $filtro_sector;
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Consulta con JOIN condicional según sector
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
?>

<div class="container mt-4">
    <h4>Historial de Movimientos</h4>

    <div class="mb-3">
        <a href="registrar_accesorio.php" class="btn btn-outline-primary btn-sm me-2">
            + Accesorio
        </a>
        <a href="registrar_servicio.php" class="btn btn-outline-success btn-sm">
            + Servicio
        </a>
    </div>

    <div class="mb-3">
        <form class="row row-cols-lg-auto g-2 align-items-end">
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
            <div class="col">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="historial.php" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['mensaje']) ?>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Sector</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Total ($)</th>
                    <th>Usuario</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($m['creado_en'])) ?></td>
                        <td><?= ucfirst($m['tipo']) ?></td>
                        <td><?= ucfirst($m['sector']) ?></td>
                        <td><?= htmlspecialchars($m['descripcion']) ?></td>
                        <td><?= $m['cantidad'] ?></td>
                        <td><?= number_format($m['total'], 2) ?></td>
                        <td><?= htmlspecialchars($m['usuario']) ?></td>
                        <td><?= htmlspecialchars($m['observacion']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
