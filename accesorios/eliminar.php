<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM accesorios WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: listar.php");
exit;