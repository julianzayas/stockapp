<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: listar.php");
exit;
// Code to display a success message after deletion - Copilot
?>
<?php include '../includes/header.php'; ?> 
<?php include '../includes/navbar.php'; ?>
<div class="container mt-4">
    <h4>Eliminar Categoría</h4>
    <p>La categoría ha sido eliminada correctamente.</p>
    <a href="listar.php" class="btn btn-primary">Volver a la lista</a>
</div>
<?php include '../includes/footer.php'; ?>
