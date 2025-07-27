<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

// Obtener categorías
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
//$stock_minimo = $_POST['stock_minimo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO accesorios (nombre, descripcion, marca, modelo, categoria_id, stock_actual, stock_minimo, ubicacion)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['marca'] ?: null,
        $_POST['modelo'] ?: null,
        $_POST['categoria_id'] ?: null,
        $_POST['stock_actual'] ?? 0,
        $_POST['stock_minimo'] ?: 0,
        $_POST['ubicacion']
    ]);
    header("Location: listar.php");
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4>Nuevo Accesorio</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Marca</label>
            <input type="text" name="marca" class="form-control">
        </div>
        <div class="mb-3">
            <label>Modelo</label>
            <input type="text" name="modelo" class="form-control">
        </div>
        <div class="mb-3">
            <label>Categoría</label>
            <select name="categoria_id" class="form-control">
                <option value="">-- Sin categoría --</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock_actual" id="stock_actual" class="form-control" min="0" value="0" required>
        </div>
        <div class="mb-3">
            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
            <input type="number" name="stock_minimo" id="stock_minimo" class="form-control" min="0" value="0" required>
        </div>
        <div class="mb-3">
            <label>Ubicación física</label>
            <select name="ubicacion" class="form-control">
                <option value="">-- Sin ubicación --</option>
                <option value="Local">Local</option>
                <option value="Deposito">Deposito</option>
            </select>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>