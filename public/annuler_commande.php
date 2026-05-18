<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

// Récupération de la chaîne de caractères (Puisque numero_commande est un varchar(50))
$numero_commande = isset($_GET['id']) ? trim($_GET['id']) : '';
$utilisateur_id = $_SESSION['user_id'];

// 1. Récupération avec le bon champ de ton MCD
$stmt = $pdo->prepare("SELECT * FROM commande WHERE numero_commande = ? AND utilisateur_id = ?");
$stmt->execute([$numero_commande, $utilisateur_id]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: espace_client.php?error=' . urlencode("Commande introuvable ou accès refusé."));
    exit();
}

// 2. Vérification du statut
$statut = $commande['statut'] ?? 'en_attente';
if ($statut !== 'en_attente') {
    header('Location: espace_client.php?error=' . urlencode("Cette commande a déjà été prise en charge et ne peut plus être annulée."));
    exit();
}

// 3. Passage en annulé avec le bon champ
$stmtUpdate = $pdo->prepare("UPDATE commande SET statut = 'annule' WHERE numero_commande = ?");

if ($stmtUpdate->execute([$numero_commande])) {
    // Réintégration de la quantité (nombre_personne dans ton MCD) dans le stock restant du menu
    $stmtStock = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante + ? WHERE menu_id = ?");
    $stmtStock->execute([$commande['nombre_personne'], $commande['menu_id']]);

    header('Location: espace_client.php?success=1');
} else {
    header('Location: espace_client.php?error=' . urlencode("Une erreur technique est survenue."));
}
exit();