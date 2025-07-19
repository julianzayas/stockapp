<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$id]);
$categoria = $stmt->fetch();

if (!$categoria) {
    echo "Categoría no encontrada.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE categorias SET nombre=? WHERE id=?");
    $stmt->execute([$_POST['nombre'], $id]);
    header("Location: listar.php");
    exit;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4>Editar Categoría</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>