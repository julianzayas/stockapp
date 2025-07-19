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
</div>

<?php include 'includes/footer.php'; ?>