<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id']) && isset($_GET['role'])) {
    $id = (int)$_GET['id'];
    $new_role = (int)$_GET['role'];

    $stmt = $pdo->prepare("UPDATE utilisateur SET role_id = ? WHERE utilisateur_id = ?");
    $stmt->execute([$new_role, $id]);
}

header('Location: admin_users.php?msg=updated');
exit();