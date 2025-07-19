<?php
// /logout.php
require_once 'config/database.php'; // Conexión a la base de datos - Copilot
require_once 'config/auth.php'; // Autenticación - Copilot
session_start();
// Destruir la sesión
session_unset(); // Destruye todas las variables de sesión - Copilot
session_destroy();
// Redirigir al login
header("Location: login.php");
exit;