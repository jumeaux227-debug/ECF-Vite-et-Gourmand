<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];
$statut = (int)$_GET['statut'];

// Mise à jour sécurisée de l'état du compte
$stmt = $pdo->prepare("UPDATE utilisateur SET statut_compte = ? WHERE utilisateur_id = ?");
$stmt->execute([$statut, $id]);

header('Location: admin_rh.php?success=1');
exit();