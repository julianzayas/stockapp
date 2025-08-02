<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$stmt = $pdo->query("SELECT m.*, a.nombre AS accesorio, u.nombre AS usuario 
                     FROM movimientos m
                     LEFT JOIN accesorios a ON m.item_id = a.id
                     LEFT JOIN usuarios u ON m.usuario_id = u.id
                     ORDER BY m.creado_en DESC");
$movimientos = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h4 class="mb-3">Historial de Movimientos</h4>
    <a href="registrar.php" class="btn btn-primary mb-3">Nuevo Movimiento</a>

    <?php if (count($movimientos) > 0): ?>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>Fecha</th>
                        <th>Artículo</th>
                        <th>Tipo</th>
                        <th>Cant.</th>
                        <th>Usuario</th>
                        <th>Obs.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($m['creado_en'])) ?></td>
                            <td class="text-wrap"><?= htmlspecialchars($m['accesorio']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= $m['tipo'] === 'entrada' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($m['tipo']) ?>
                                </span>
                            </td>
                            <td class="text-center"><?= $m['cantidad'] ?></td>
                            <td class="text-wrap"><?= htmlspecialchars($m['usuario'] ?? '-') ?></td>
                            <td class="text-wrap"><?= htmlspecialchars($m['observacion']) ?></td>
                            <td>
                                <a href="editar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="eliminar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este movimiento?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay movimientos registrados aún.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
