<?php
session_start();

// Verificamos si el usuario está logueado
if (isset($_SESSION['usuario_id'])) {
    // Redirigir al dashboard o al historial
    header('Location: dashboard.php'); // o 'movimientos/historial.php'
} else {
    // Si no está logueado, enviar al login
    header('Location: login.php');
}
exit;