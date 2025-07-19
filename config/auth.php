<?php
// /config/auth.php
session_start();

//$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");

function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function isAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
        header("Location: ../index.php");
        exit;
    }
}

function requireEmpleado() {
    if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'empleado') {
        header("Location: ../index.php");
        exit;
    }
}

?>