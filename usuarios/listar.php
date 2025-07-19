<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();
requireAdmin();

include '../includes/header.php';
include '../includes/navbar.php';

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY creado_en ASC")->fetchAll();
$usuario_actual = $_SESSION['usuario_id'];
?>

<div class="container mt-4">
    <h4>Listado de Usuarios</h4>
    <a href="crear.php" class="btn btn-success mb-3">Nuevo Usuario</a>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['rol'] ?></td>
                    <td><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></td>
                    <td>
    <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Editar</a>

    <?php if ($u['id'] == $_SESSION['usuario_id']): ?>
        <button class="btn btn-sm btn-danger" disabled title="No puedes eliminar tu propio usuario">Eliminar</button>
    <?php else: ?>
        <a href="eliminar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
           onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
    <?php endif; ?>

    <?php if ($u['rol'] !== 'admin'): ?>
        <?php if ($u['activo']): ?>
            <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                <a href="estado.php?id=<?= $u['id'] ?>&accion=desactivar" class="btn btn-sm btn-warning"
                   onclick="return confirm('¿Desactivar este usuario?');">Desactivar</a>
            <?php else: ?>
                <button class="btn btn-sm btn-warning" disabled title="No puedes desactivarte a ti mismo">Desactivar</button>
            <?php endif; ?>
        <?php else: ?>
            <a href="estado.php?id=<?= $u['id'] ?>&accion=activar" class="btn btn-sm btn-success"
               onclick="return confirm('¿Activar este usuario?');">Activar</a>
        <?php endif; ?>
    <?php endif; ?>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
