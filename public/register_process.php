<?php
// 1. ON FORCE L'AFFICHAGE DES ERREURS PARTOUT
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. ON INCLUT LE FICHIER DE CONNEXION
require_once __DIR__ . '/../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On récupère et on nettoie les données
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname  = htmlspecialchars(trim($_POST['lastname']));
    $email     = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password  = $_POST['password'];

    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($password)) {
        
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Préparation de la requête
        $sql = "INSERT INTO utilisateur (email, password, prenom, nom, role_id) VALUES (:email, :pass, :prenom, :nom, 3)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([
                ':email'  => $email,
                ':pass'   => $hashedPassword,
                ':prenom' => $firstname,
                ':nom'    => $lastname
            ]);

            // Redirection vers le login avec un message de succès
            header('Location: login.php?registration=success');
            exit();

        } catch (PDOException $e) {
            // S'il y a une erreur SQL, elle va s'afficher ici au lieu de faire une page blanche !
            die("Erreur SQL lors de l'insertion : " . $e->getMessage());
        }
    } else {
        header('Location: register.php?error=empty_fields');
        exit();
    }
} else {
    die("Le formulaire n'a pas été envoyé en POST.");
}