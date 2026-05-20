<?php
session_start(); // 1. TRÈS IMPORTANT : Doit être la première ligne
require_once __DIR__ . '/../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // 2. On cherche l'utilisateur (Assure-toi que la table s'appelle bien 'utilisateur')
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 3. Vérification
    if ($user && password_verify($password, $user['password'])) {
        
        // 4. ON REMPLIT LA SESSION ICI
        $_SESSION['user_id'] = $user['utilisateur_id']; // Vérifie que c'est bien 'id' en BDD
        
        // ATTENTION ICI : Vérifie si ta colonne s'appelle 'prenom' ou 'nom' ou 'username'
        $_SESSION['prenom'] = $user['prenom']; 
        
        $_SESSION['role_id'] = $user['role_id'];

        // 5. Redirection
        header('Location: index.php');
        exit();
    } else {
        header('Location: login.php?error=identifiants_incorrects');
        exit();
    }
}
