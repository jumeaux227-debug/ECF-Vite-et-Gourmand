<?php
require_once __DIR__ . '/../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. On récupère et on nettoie les données
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname  = htmlspecialchars(trim($_POST['lastname']));
    $email     = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password  = $_POST['password'];

    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($password)) {
        
        // 2. ÉTAPE CRUCIALE : Le hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 3. Préparation de la requête (on met role_id = 3 pour un client par défaut)
        // Vérifie bien que tes noms de colonnes correspondent à ton structure.sql
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
            // Si l'email existe déjà par exemple
            header('Location: register.php?error=email_taken');
            exit();
        }
    } else {
        header('Location: register.php?error=empty_fields');
        exit();
    }
}