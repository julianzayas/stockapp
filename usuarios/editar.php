<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
$mensaje = "";

if (!$id) {
    header("Location: listar.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    $mensaje = "Usuario no encontrado.";
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $nueva_password = $_POST['nueva_password'] ?? '';

    if (!empty($nombre) && !empty($email) && in_array($rol, ['admin', 'empleado'])) {
        // Armar sentencia UPDATE base
        $query = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?";
        $params = [$nombre, $email, $rol];

        // Si se indicó nueva contraseña, agregarla
        if (!empty($nueva_password)) {
            $query .= ", password = ?";
            $password_hashed = password_hash($nueva_password, PASSWORD_DEFAULT);
            $params[] = $password_hashed;
        }

        $query .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $mensaje = "Usuario actualizado correctamente.";

        // Refrescar datos
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
    } else {
        $mensaje = "Completa todos los campos correctamente.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h4>Editar Usuario</h4>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-12">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($usuario['nombre']) ?>">
        </div>

        <div class="col-12">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>

        <div class="col-12">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="empleado" <?= $usuario['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Nueva Contraseña (opcional)</label>
            <input type="password" name="nueva_password" class="form-control" placeholder="Dejar vacío si no desea cambiarla">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
