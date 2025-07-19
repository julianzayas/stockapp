<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();
requireAdmin(); // Solo admin puede crear usuarios

include '../includes/header.php';
include '../includes/navbar.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    if ($nombre && $email && $password && in_array($rol, ['admin', 'empleado'])) {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $mensaje = "Ya existe un usuario con ese correo.";
        } else {
            // Insertar usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $email, $hash, $rol]);
            header("Location: listar.php");
            exit;
        }
    } else {
        $mensaje = "Completa todos los campos correctamente.";
    }
}
?>

<div class="container mt-4">
    <h4>Registrar Usuario</h4>
    <?php if ($mensaje): ?>
        <div class="alert alert-warning"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-12">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="col-12">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="col-12">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="col-12">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="empleado">Empleado</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success">Registrar</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>