<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

// Traer los movimientos con datos del artículo y usuario
$stmt = $pdo->query("SELECT m.*, a.nombre AS articulo, u.nombre AS usuario 
                     FROM movimientos m
                     LEFT JOIN accesorios a ON m.articulo_id = a.id
                     LEFT JOIN usuarios u ON m.usuario_id = u.id
                     ORDER BY m.fecha DESC");
$movimientos = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h4>Historial de Movimientos</h4>
    <a href="registrar.php" class="btn btn-primary mb-3">Nuevo Movimiento</a>

    <?php if (count($movimientos) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Artículo</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Usuario</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($m['fecha'])) ?></td>
                            <td><?= htmlspecialchars($m['articulo']) ?></td>
                            <td>
                                <span class="badge bg-<?= $m['tipo'] === 'entrada' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($m['tipo']) ?>
                                </span>
                            </td>
                            <td><?= $m['cantidad'] ?></td>
                            <td><?= htmlspecialchars($m['usuario'] ?? 'Desconocido') ?></td>
                            <td><?= htmlspecialchars($m['observacion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay movimientos registrados.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
