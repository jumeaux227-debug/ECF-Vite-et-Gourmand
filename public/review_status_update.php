<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// Sécurité : Admin ou Employé uniquement
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $avis_id = (int)$_GET['id'];
    $status = $_GET['status'];

    // On valide que le statut envoyé est correct pour éviter les injections
    if (in_array($status, ['valide', 'refuse'])) {
        $stmt = $pdo->prepare("UPDATE avis SET statut = ? WHERE avis_id = ?");
        $stmt->execute([$status, $avis_id]);
    }
}

header('Location: admin_reviews.php');
exit();