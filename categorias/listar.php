<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
?>

<div class="container mt-4">
    <h4>Categorías</h4>
    <a href="nuevo.php" class="btn btn-success mb-3">+ Agregar categoría</a>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>