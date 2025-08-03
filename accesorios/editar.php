<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: listar.php");
    exit;
}

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM accesorios WHERE id = ?");
$stmt->execute([$id]);
$accesorio = $stmt->fetch();

if (!$accesorio) {
    echo "Accesorio no encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE accesorios SET nombre=?, descripcion=?, marca=?, modelo=?, categoria_id=?, stock_actual=?, stock_minimo=?, ubicacion=? WHERE id=?");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['marca'], // Asegurar que marca tenga un valor por defecto
        $_POST['modelo'], // Asegurar que modelo tenga un valor por defecto
        $_POST['categoria_id'],
        $_POST['stock_actual'], // Asegurar que stock_actual tenga un valor por defecto
        $_POST['stock_minimo'], // Asegurar que stock_minimo tenga un valor por defecto
        $_POST['ubicacion'],
        $id
    ]);
    $_SESSION['mensaje'] = "Accesorio actualizado correctamente.";
    header("Location: listar.php");
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4>Editar Accesorio</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($accesorio['nombre']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control"><?= htmlspecialchars($accesorio['descripcion']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Marca</label>
            <input type="text" name="marca" class="form-control" required value="<?= htmlspecialchars($accesorio['marca']) ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Modelo</label>
            <input type="text" name="modelo" class="form-control" required value="<?= htmlspecialchars($accesorio['modelo']) ?>">
        </div>
        <div class="mb-3">
            <label>Categoría</label>
            <select name="categoria_id" class="form-control">
                <option value="">-- Sin categoría --</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $accesorio['categoria_id'] ? 'selected' : '' ?>>
                        <?= $c['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock_actual" class="form-control" value="<?= $accesorio['stock_actual'] ?>">
        </div>
        <div class="mb-3">
            <label for="stock_minimo">Stock Mínimo</label>
            <input type="number" name="stock_minimo" class="form-control" value="<?= $accesorio['stock_minimo'] ?>" min="0" required>
        </div>
        <div class="mb-3">
            <label>Ubicación física</label>
            <select name="ubicacion" class="form-control">
                <option value="Local" <?= $accesorio['ubicacion'] == 'Local' ? 'selected' : '' ?>>Local</option>
                <option value="Deposito" <?= $accesorio['ubicacion'] == 'Deposito' ? 'selected' : '' ?>>Deposito</option>
                
            </select>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>