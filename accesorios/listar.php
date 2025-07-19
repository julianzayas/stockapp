<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$stmt = $pdo->query("SELECT a.*, c.nombre AS categoria 
                     FROM accesorios a 
                     LEFT JOIN categorias c ON a.categoria_id = c.id");
$accesorios = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h4>Accesorios en Stock</h4>
    <a href="nuevo.php" class="btn btn-success mb-3">+ Agregar accesorio</a>
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
                <tr>
                    <td><?= htmlspecialchars($a['nombre']) ?></td>
                    <td><?= htmlspecialchars($a['descripcion']) ?></td>
                    <td><?= htmlspecialchars($a['marca']) ?></td>
                    <td><?= htmlspecialchars($a['modelo']) ?></td>
                    <td><?= htmlspecialchars($a['categoria']) ?></td>
                    <td><?= $a['stock_actual'] ?></td>
                    <td>
                        <?= $a['stock_minimo'] ?>
                        <?php if ($a['stock_actual'] < $a['stock_minimo']): ?>
                            <span class="badge bg-danger" title="Stock bajo"><strong>⚠️</strong></span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($a['ubicacion']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que deseas eliminar este artículo?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>