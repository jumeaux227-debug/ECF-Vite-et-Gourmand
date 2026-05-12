<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';


if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];

    
    if ($id_to_delete === $_SESSION['user_id']) {
        header('Location: admin_users.php?error=self_delete');
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE utilisateur_id = ?");
    $stmt->execute([$id_to_delete]);
}

header('Location: admin_users.php?msg=deleted');
exit();