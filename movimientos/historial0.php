<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// Filtros
$where = [];
$params = [];

if (!empty($_GET['tipo'])) {
    $where[] = "m.tipo = ?";
    $params[] = $_GET['tipo'];
}

if (!empty($_GET['articulo_id'])) {
    $where[] = "m.articulo_id = ?";
    $params[] = $_GET['articulo_id'];
}

if (!empty($_GET['desde'])) {
    $where[] = "m.fecha >= ?";
    $params[] = $_GET['desde'] . " 00:00:00";
}

if (!empty($_GET['hasta'])) {
    $where[] = "m.fecha <= ?";
    $params[] = $_GET['hasta'] . " 23:59:59";
}

if (!empty($_GET['motivo'])) {
    $where[] = "m.motivo LIKE ?";
    $params[] = "%" . $_GET['motivo'] . "%";
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT m.*, a.nombre AS articulo
          FROM movimientos m
          JOIN accesorios a ON m.articulo_id = a.id
          $whereSQL
          ORDER BY m.fecha DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();

$accesorios = $pdo->query("SELECT id, nombre FROM accesorios ORDER BY nombre")->fetchAll();
?>

<div class="container mt-4">
    <h4>Historial de Movimientos</h4>
    <a href="nuevo.php" class="btn btn-success mb-3">+ Nuevo movimiento</a>

    <!-- Formulario de filtros -->
    <form class="row row-cols-md-auto g-3 mb-4" method="GET">
        <div class="col">
            <select name="tipo" class="form-select">
                <option value="">Tipo</option>
                <option value="entrada" <?= ($_GET['tipo'] ?? '') === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                <option value="salida" <?= ($_GET['tipo'] ?? '') === 'salida' ? 'selected' : '' ?>>Salida</option>
            </select>
        </div>
        <div class="col">
            <select name="articulo_id" class="form-select">
                <option value="">Artículo</option>
                <?php foreach ($accesorios as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($_GET['articulo_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <input type="date" name="desde" value="<?= $_GET['desde'] ?? '' ?>" class="form-control" placeholder="Desde">
        </div>
        <div class="col">
            <input type="date" name="hasta" value="<?= $_GET['hasta'] ?? '' ?>" class="form-control" placeholder="Hasta">
        </div>
        <div class="col">
            <input type="text" name="motivo" value="<?= $_GET['motivo'] ?? '' ?>" class="form-control" placeholder="Motivo">
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="historial.php" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Artículo</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Motivo</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movimientos as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['articulo']) ?></td>
                    <td><?= $m['tipo'] === 'entrada' ? '🟢 Entrada' : '🔴 Salida' ?></td>
                    <td><?= $m['cantidad'] ?></td>
                    <td><?= htmlspecialchars($m['motivo']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($m['fecha'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
