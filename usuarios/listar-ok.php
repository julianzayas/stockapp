<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();
requireAdmin();

include '../includes/header.php';
include '../includes/navbar.php';

$usuarios = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY id")->fetchAll();
$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['mensaje']);
?>

<div class="container mt-4">
    <h4>Lista de Usuarios</h4>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['id'] ?></td>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['rol']) ?></td>
                        <td>
                            <a href="editar.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-primary">Editar</a>

                            <?php if ($usuario['id'] == $_SESSION['usuario_id']): ?>
                                <button class="btn btn-sm btn-danger" disabled title="No puedes eliminar tu propio usuario">Eliminar</button>
                            <?php elseif ($usuario['id'] == 1): ?>
                                <button class="btn btn-sm btn-danger" disabled title="No puedes eliminar al administrador principal">Eliminar</button>
                            <?php else: ?>
                                <a href="eliminar.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger"
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
