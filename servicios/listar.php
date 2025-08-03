<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$servicios = $pdo->query("SELECT * FROM servicios ORDER BY marca, modelo")->fetchAll();
?>

<div class="container mt-4">
    <h4>Lista de Servicios</h4>

    <!-- Mensajes de actualización -->
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"> 
            <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php elseif (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert"> 
            <?= $_SESSION['error']; unset($_SESSION['error']); ?> 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <a href="crear.php" class="btn btn-primary mb-3">+ Agregar servicio</a>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Activación</th>
                    <th>Software</th>
                    <th>FRP</th>
                    <th>Formatear</th>
                    <th>Pin de carga</th>
                    <th>Letras Rojas</th>
                    <th>Pegado Tapa</th>
                    <!-- <th>Extras</th> -->
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                    <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE sector = 'servicio' AND item_id = ?");
                        $stmt->execute([$s['id']]);
                        $tiene_movimientos = $stmt->fetchColumn() > 0;
                    ?>
                    <tr class="<?= $s['activo'] ? '' : 'table-secondary' ?>">
                        <td><?= htmlspecialchars($s['marca']) ?></td>
                        <td><?= htmlspecialchars($s['modelo']) ?></td>
                        <td><?= number_format($s['activacion'], 2) ?></td>
                        <td><?= number_format($s['software'], 2) ?></td>
                        <td><?= number_format($s['frp'], 2) ?></td>
                        <td><?= number_format($s['formatear'], 2) ?></td>
                        <td><?= number_format($s['pin_de_carga'], 2) ?></td>
                        <td><?= $s['letras_rojas'] ? 'Sí' : 'No' ?></td>
                        <td><?= $s['pegado_tapa'] ? 'Sí' : 'No' ?></td>
                        <!-- <td>
                            <?php if ($s['letras_rojas']): ?>
                                <span class="badge bg-warning text-dark">Letras Rojas</span>
                            <?php endif; ?>
                            <?php if ($s['pegado_tapa']): ?>
                                <span class="badge bg-info text-dark">Pegado Tapa</span>
                            <?php endif; ?>
                        </td> -->
                        <td>
                            <?php if ($s['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Editar -->
                            <a href="editar.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <!-- Eliminar -->
                            <?php if ($tiene_movimientos): ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Ya tiene movimientos">Eliminar</button>
                            <?php else: ?>
                                <a href="eliminar.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este servicio?')">Eliminar</a>
                            <?php endif; ?>
                            <!-- Activar/Desactivar -->
                            <a href="estado.php?id=<?= $s['id'] ?>" class="btn btn-sm <?= $s['activo'] ? 'btn-outline-secondary' : 'btn-outline-success' ?>">
                                <?= $s['activo'] ? 'Desactivar' : 'Activar' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
