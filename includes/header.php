<?php 
// On démarre la session au tout début du header pour qu'elle soit active sur toutes les pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Vite et Gourmand</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php" style="color: var(--main-orange);">Vite et Gourmand</a>
        
        <div class="navbar-nav ms-auto align-items-center">
            <a class="nav-link" href="index.php">Accueil</a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="nav-link text-dark ms-3">
                    Bonjour, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                </span>
                <a class="btn btn-outline-danger btn-sm ms-3" href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a class="btn btn-primary text-white ms-3" href="login.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>