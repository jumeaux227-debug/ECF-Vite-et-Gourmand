<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: index.php');
    exit();
}

$numero_commande = isset($_GET['id']) ? trim($_GET['id']) : '';
$action = isset($_GET['action']) ? trim($_GET['action']) : '';

if (empty($numero_commande)) {
    die("Paramètres invalides.");
}

if ($action === 'accepte') {
    $stmt = $pdo->prepare("UPDATE commande SET statut = 'accepte', pret_materiel = 1 WHERE numero_commande = ?");
    $stmt->execute([$numero_commande]);
} 
elseif ($action === 'termine') {
    $stmt = $pdo->prepare("UPDATE commande SET statut = 'termine' WHERE numero_commande = ?");
    $stmt->execute([$numero_commande]);
} 
elseif ($action === 'restitue') {
    $stmt = $pdo->prepare("UPDATE commande SET restitution_materiel = 1 WHERE numero_commande = ?");
    $stmt->execute([$numero_commande]);
}

header('Location: employe_dashboard.php');
exit();