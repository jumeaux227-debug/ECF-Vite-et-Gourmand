<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Client connecté uniquement (role_id = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = (int)$_POST['menu_id'];
    $nombre_personne = (int)$_POST['quantite']; // Reçu du formulaire de commande
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $kilometres = isset($_POST['kilometres']) ? (float)$_POST['kilometres'] : 0.0;
    $date_prestation = $_POST['date_livraison']; // Date de l'événement (date_prestation dans ton MCD)
    $utilisateur_id = $_SESSION['user_id'];

    // 1. Récupération des données du menu (Nom de colonne exact de ton phpMyAdmin : quantite_restante)
    $stmt = $pdo->prepare("SELECT prix_par_personne, nombre_personne_minimum, quantite_restante FROM menu WHERE menu_id = ?");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch();

    if (!$menu) {
        die("Erreur : Menu introuvable.");
    }

    // SÉCURITÉ : Si la quantité restante est NULL en BDD, on la force à 0 en PHP pour éviter les bugs
    $quantite_disponible = ($menu['quantite_restante'] !== null) ? (int)$menu['quantite_restante'] : 0;

    // 2. Validations strictes basées sur les règles métiers du projet
    if ($nombre_personne < $menu['nombre_personne_minimum']) {
        die("Erreur : Le nombre de convives est inférieur au minimum requis (" . $menu['nombre_personne_minimum'] . " personnes).");
    }

    if ($nombre_personne > $quantite_disponible) {
        die("Erreur : Notre capacité de production pour ce menu est insuffisante (Maximum restant : " . $quantite_disponible . ").");
    }

    // 3. Algorithme des remises (Règle BTS : -10% si plus de 5 convives)
    $prix_base = $nombre_personne * $menu['prix_par_personne'];
    if ($nombre_personne > 5) {
        $prix_apres_remise = $prix_base * 0.90; 
    } else {
        $prix_apres_remise = $prix_base;
    }

    // 4. Barème de livraison kilométrique hors Bordeaux
    $prix_livraison = 0;
    if (strtolower($ville) !== 'bordeaux') {
        $prix_livraison = 5.0 + ($kilometres * 0.59);
    }

    // Montant final stocké dans prix_menu selon les colonnes de ton MCD
    $prix_menu = $prix_apres_remise + $prix_livraison;

    // Génération d'un numéro de commande unique (Type VARCHAR(50) dans ton MCD)
    $numero_commande = 'CMD-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    $date_commande = date('Y-m-d'); // Variable renommée pour correspondre au MCD
    $statut = 'en_attente';

    // 5. CORRECTION ICI : date_commande au lieu de date_commande
    $query = "INSERT INTO commande (
                numero_commande, 
                date_commande, 
                date_prestation, 
                heure_livraison, 
                prix_menu, 
                nombre_personne, 
                prix_livraison, 
                statut, 
                pret_materiel, 
                restitution_materiel, 
                utilisateur_id, 
                menu_id
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?)";
    
    $stmtInsert = $pdo->prepare($query);
    
    $params = [
        $numero_commande,
        $date_commande, // Paramètre aligné
        $date_prestation,
        '12:00', // Heure de livraison par défaut
        $prix_menu,
        $nombre_personne,
        $prix_livraison,
        $statut,
        $utilisateur_id,
        $menu_id
    ];

    if ($stmtInsert->execute($params)) {
        // Mise à jour de la colonne quantite_restante dans la table menu
        $stmtUpdateStock = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante - ? WHERE menu_id = ?");
        $stmtUpdateStock->execute([$nombre_personne, $menu_id]);

        header('Location: commande_succes.php?total=' . urlencode($prix_menu));
        exit();
    } else {
        die("Une erreur technique s'est produite lors de l'enregistrement de la commande.");
    }
} else {
    header('Location: menus.php');
    exit();
}