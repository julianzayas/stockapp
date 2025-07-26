<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$accesorios = $pdo->query("SELECT a.*, c.nombre AS categoria 
                     FROM accesorios a 
                     LEFT JOIN categorias c ON a.categoria_id = c.id")->fetchAll();
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
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Ubicación</th>
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
                    <?php if ($a['activo']): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['descripcion']) ?></td>
                        <td><?= htmlspecialchars($a['marca']) ?></td>
                        <td><?= htmlspecialchars($a['modelo']) ?></td>
                        <td><?= htmlspecialchars($a['categoria']) ?></td>
                        <td>
                            <?= (int) $a['stock_actual'] ?>
                            <?php if ($a['stock_actual'] < $a['stock_minimo']): ?>
                                <span class="badge bg-danger" title="Stock bajo"><strong>⚠️</strong></span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int) $a['stock_minimo'] ?></td>
                        <td><?= htmlspecialchars($a['ubicacion']) ?></td>
                        <td>
                            <a href="editar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <?php if ($tiene_movimientos): ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Ya tiene movimientos">Eliminar</button>
                            <?php else: ?>
                                <a href="eliminar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este accesorio?')">Eliminar</a>
                            <?php endif; ?>
                            <a href="desactivar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-secondary">Desactivar</a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <tr class="table-secondary">
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['descripcion']) ?></td>
                        <td><?= htmlspecialchars($a['marca']) ?></td>
                        <td><?= htmlspecialchars($a['modelo']) ?></td>
                        <td><?= htmlspecialchars($a['categoria']) ?></td>
                        <td><?= (int) $a['stock_actual'] ?></td>
                        <td><?= (int) $a['stock_minimo'] ?></td>
                        <td><?= htmlspecialchars($a['ubicacion']) ?></td>
                        <td colspan="2" class="text-muted text-center">
                            <p>Accesorio desactivado</p>
                            <a href="desactivar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-success">Activar</a>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
