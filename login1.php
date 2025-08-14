<?php
require_once 'config/database.php';
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        if ($user['activo'] != 1) {
            $error = "Cuenta inactiva. Contacte al administrador.";
        } else {
        // Iniciar sesión
        $_SESSION['loggedin'] = true;
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        header("Location: dashboard.php");
        exit;
        }
    } else {
            $error = "Usuario o contraseña incorrectos.";
         }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4 text-center mb-3">
            <img src="assets/img/logo.png" alt="Revolución Móvil" class="img-fluid" style="max-width: 200px; height: auto;">
        </div>
    </div>
</div>

<div class="container mt-5">
    <h3 class="text-center">Ingreso al sistema</h3>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label>Contraseña:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
