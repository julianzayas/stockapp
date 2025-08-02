<?php
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin(); // Solo usuarios logueados
//include 'config/constantes.php'; // Definiciones de constantes
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container mt-4">
    <h3>Bienvenido, <?= $_SESSION['nombre']; ?></h3>
    <p class="lead">Este es tu panel de control del sistema de stock.</p>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Accesorios registrados</h5>
                    <p class="card-text">
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM accesorios");
                        echo $stmt->fetchColumn();
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Movimientos totales</h5>
                    <p class="card-text">
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM movimientos");
                        echo $stmt->fetchColumn();
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            Últimos movimientos
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php
                $ultimos = $pdo->query("
                    SELECT m.*, u.nombre AS usuario
                    FROM movimientos m
                    LEFT JOIN usuarios u ON m.usuario_id = u.id
                    ORDER BY m.creado_en DESC
                    LIMIT 5
                ")->fetchAll();

                foreach ($ultimos as $m):
                    $descripcion = ucfirst($m['tipo']) . ' - ' . ucfirst($m['sector']) . " ($" . number_format($m['total'], 2) . ")";
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= $descripcion ?>
                        <span class="badge bg-secondary"><?= date('d/m H:i', strtotime($m['creado_en'])) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="movimientos/historial.php" class="btn btn-sm btn-outline-dark mt-3">Ver historial completo</a>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>