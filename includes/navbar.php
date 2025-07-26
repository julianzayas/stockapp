<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>/dashboard.php">StockApp</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/accesorios/listar.php">Accesorios</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/servicios/listar.php">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/movimientos/historial.php">Movimientos</a></li>
                <?php if (isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/usuarios/listar.php">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/categorias/listar.php">Categorías</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text me-3">Hola, <?= $_SESSION['nombre']; ?></span>
            <a class="btn btn-outline-light" href="<?= BASE_URL ?>/logout.php">Salir</a>
        </div>
    </div>
</nav>