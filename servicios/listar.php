<?php
// Mostrar errores de PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

include '../includes/header.php';
include '../includes/navbar.php';

$servicios = $pdo->query("SELECT * FROM servicios ORDER BY creado_en DESC")->fetchAll();
?>

<div class="container mt-4">
    <h4>Servicios Registrados</h4>
    <a href="crear.php" class="btn btn-primary mb-3">Nuevo Servicio</a>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Activación</th>
                    <th>Software</th>
                    <th>FRP</th>
                    <th>Formatear</th>
                    <th>Pin</th>
                    <th>Extras</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['marca']) ?></td>
                    <td><?= htmlspecialchars($s['modelo']) ?></td>
                    <td>$<?= number_format($s['activacion'], 2) ?></td>
                    <td>$<?= number_format($s['software'], 2) ?></td>
                    <td>$<?= number_format($s['frp'], 2) ?></td>
                    <td>$<?= number_format($s['formatear'], 2) ?></td>
                    <td>$<?= number_format($s['pin_de_carga'], 2) ?></td>
                    <td>
                        <?php if ($s['letras_rojas']): ?>
                            <span class="badge bg-warning text-dark">Letras Rojas</span>
                        <?php endif; ?>
                        <?php if ($s['pegado_tapa']): ?>
                            <span class="badge bg-info text-dark">Pegado Tapa</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="editar.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmar eliminación del servicio?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
