<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // 1. On cherche l'utilisateur par son email
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. On vérifie si l'utilisateur existe ET si le mot de passe haché correspond
    if ($user && password_verify($password, $user['password'])) {
        // Succès ! On stocke les infos en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['prenom'];
        $_SESSION['role_id'] = $user['role_id'];

        header('Location: index.php');
        exit();
    } else {
        // Erreur d'identifiants
        header('Location: login.php?error=1');
        exit();
    }
}