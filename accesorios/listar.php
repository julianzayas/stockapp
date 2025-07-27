<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$accesorios = $pdo->query("SELECT * FROM accesorios ORDER BY nombre")->fetchAll();
?>

<div class="container mt-4">
    <h4>Lista de Accesorios</h4>
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert alert-success"> <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?> </div>
    <?php elseif (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"> <?= $_SESSION['error']; unset($_SESSION['error']); ?> </div>
    <?php endif; ?>

    <a href="nuevo.php" class="btn btn-primary mb-3">Agregar Accesorio</a>
    <div class="table-responsive">
        <table class="table table bordered table-hover table-striped">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Stock</th>
                    <th>Stock Mínimo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accesorios as $a): ?>
                    <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE sector = 'accesorio' AND item_id = ?");
                        $stmt->execute([$a['id']]);
                        $tiene_movimientos = $stmt->fetchColumn() > 0;
                    ?>
                    <tr class="<?= $a['activo'] ? '' : 'table-secondary' ?>">
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['marca']) ?></td>
                        <td><?= htmlspecialchars($a['modelo']) ?></td>
                        <td>
                            <?= (int) $a['stock_actual'] ?>
                            <?php if ($a['stock_actual'] <= $a['stock_minimo']): ?>
                                <span class="badge bg-danger" title="Stock bajo"><strong>⚠️</strong></span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int) $a['stock_minimo'] ?></td>
                        <td>
                            <?php if ($a['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <?php if ($tiene_movimientos): ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Ya tiene movimientos">Eliminar</button>
                            <?php else: ?>
                                <a href="eliminar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este accesorio?')">Eliminar</a>
                            <?php endif; ?>
                            <a href="estado.php?id=<?= $a['id'] ?>" class="btn btn-sm <?= $a['activo'] ? 'btn-outline-secondary' : 'btn-outline-success' ?>">
                                <?= $a['activo'] ? 'Desactivar' : 'Activar' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
