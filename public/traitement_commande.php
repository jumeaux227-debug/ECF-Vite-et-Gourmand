<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Client connecté uniquement
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = (int)$_POST['menu_id'];
    $quantite = (int)$_POST['quantite'];
    $adresse = trim($_POST['adresse']);
    $ville = trim($_POST['ville']);
    $kilometres = isset($_POST['kilometres']) ? (float)$_POST['kilometres'] : 0.0;
    $date_livraison = $_POST['date_livraison'];
    $utilisateur_id = $_SESSION['user_id'];

    // 1. Récupération des informations du menu pour sécuriser les tarifs et contraintes
    $stmt = $pdo->prepare("SELECT prix_par_personne, nombre_personne_minimum, quantite_restante FROM menu WHERE menu_id = ?");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch();

    if (!$menu) {
        die("Erreur : Menu introuvable.");
    }

    // 2. Validation stricte des critères du PDF
    if ($quantite < $menu['nombre_personne_minimum']) {
        die("Erreur : Le nombre de convives est inférieur au minimum requis pour ce menu (" . $menu['nombre_personne_minimum'] . " personnes).");
    }

    if ($quantite > $menu['quantite_restante']) {
        die("Erreur : Notre capacité de production pour ce menu est insuffisante pour votre demande (Maximum restant : " . $menu['quantite_restante'] . ").");
    }

    // 3. Application de l'algorithme de calcul exigé par le PDF
    $prix_base = $quantite * $menu['prix_par_personne'];

    // Règle de remise de 10% si > 5 personnes
    if ($quantite > 5) {
        $remise = $prix_base * 0.10;
        $prix_apres_remise = $prix_base - $remise;
    } else {
        $prix_apres_remise = $prix_base;
    }

    // Règle du barème kilométrique pour les frais de livraison
    $frais_livraison = 0;
    if (strtolower($ville) !== 'bordeaux') {
        $frais_livraison = 5.0 + ($kilometres * 0.59);
    }

    // Calcul du montant total final à stocker
    $prix_total = $prix_apres_remise + $frais_livraison;

    // 4. Insertion en Base de données selon la structure stricte de ton MCD
    $query = "INSERT INTO commande (date_livraison, quantite, prix_total, adresse_livraison, utilisateur_id, menu_id) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = $pdo->prepare($query);
    
    if ($stmtInsert->execute([$date_livraison, $quantite, $prix_total, $adresse . ' ' . $ville, $utilisateur_id, $menu_id])) {
        
        // Exigence implicite de gestion des stocks : déduire la quantité commandée du stock existant
        $stmtUpdateStock = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante - ? WHERE menu_id = ?");
        $stmtUpdateStock->execute([$quantite, $menu_id]);

        // Redirection vers une page de confirmation de commande
        header('Location: commande_succes.php?total=' . urlencode($prix_total));
        exit();
    } else {
        die("Une erreur technique est survenue lors de l'enregistrement de votre commande.");
    }
} else {
    header('Location: menus.php');
    exit();
}