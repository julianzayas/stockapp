<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();
requireAdmin();

include '../includes/header.php';
include '../includes/navbar.php';

$stmt = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY nombre");
$usuarios = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h4>Usuarios del Sistema</h4>

    <?php if (isset($_GET['eliminado'])): ?>
        <div class="alert alert-success">Usuario eliminado correctamente.</div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] === 'autoborrado'): ?>
        <div class="alert alert-warning">No puedes eliminar tu propio usuario.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['rol'] === 'admin' ? 'Administrador' : 'Usuario' ?></td>
                        <td>
                            <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                            <?php if ($u['id'] == $_SESSION['usuario_id']): ?>
                                <button class="btn btn-sm btn-danger" disabled title="No puedes eliminar tu propio usuario">Eliminar</button>
                            <?php elseif ($u['id'] == 1): ?>
                                <button class="btn btn-sm btn-danger" disabled title="No puedes eliminar al administrador principal">Eliminar</button>
                            <?php else: ?>
                                <a href="eliminar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="crear.php" class="btn btn-success mt-3">Agregar Usuario</a>
</div>

<?php include '../includes/footer.php'; ?>
