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
    <title>Login - Revolución Móvil</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eceff1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .login-logo {
            text-align: center;
            background-color: inherit;
            margin-bottom: 20px;
        }
        .login-logo img {
            max-width: 100%;
            height: auto;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            margin-top: 15px;
            padding: 10px;
            background-color: #1976d2;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1565c0;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        
        <!-- Logo -->
        <div class="login-logo">
            <img src="assets/img/logo.png" alt="Revolución Móvil">
        </div>

        <!-- Formulario de Login -->
        <form method="post" action="login.php">
            <label for="email">Correo:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

    </div>
</body>
</html>
