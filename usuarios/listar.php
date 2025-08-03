<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();
requireAdmin();

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY creado_en ASC")->fetchAll();
$usuario_actual = $_SESSION['usuario_id'];

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4>Usuarios</h4>
    <a href="crear.php" class="btn btn-success mb-3">+ Agregar usuario</a>
    <table class="table table-sm table-bordered table-hover table-striped">
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
                        <!-- Editar -->
                        <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Editar</a>

                        <!-- Eliminar -->
                        <?php if ($u['id'] == $_SESSION['usuario_id']): ?>
                            <button class="btn btn-sm btn-danger" disabled title="No puedes eliminar tu propio usuario">Eliminar</button>
                        <?php elseif ($u['id'] == 1): ?>
                            <button class="btn btn-sm btn-danger" disabled title="No puedes eliminar al administrador principal">Eliminar</button>
                        <?php else: ?>
                            <a href="eliminar.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                        <?php endif; ?>

                        <!-- Cambiar estado -->
                        <?php if ($u['rol'] !== 'admin'): ?>
                            <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                                <a href="estado.php?id=<?= $u['id'] ?>&accion=<?= $u['activo'] ? 'desactivar' : 'activar' ?>"
                                class="btn btn-sm <?= $u['activo'] ? 'btn-outline-secondary' : 'btn-outline-success' ?>"
                                onclick="return confirm('¿<?= $u['activo'] ? 'Desactivar' : 'Activar' ?> este usuario?');">
                                    <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled title="No puedes modificar tu propio estado">
                                    Desactivar
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
