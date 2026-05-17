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

                <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                    <a class="nav-link text-danger fw-bold ms-2" href="admin_users.php">
                        <i class="bi bi-shield-lock"></i> Panel Admin
                    </a>
                <?php endif; ?>

                <?php if($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
                    <a class="nav-link text-warning fw-bold ms-2" href="admin_reviews.php">Modération Avis</a>
                <?php endif; ?>

                <a class="btn btn-outline-danger btn-sm ms-3" href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a class="btn btn-primary text-white ms-3" href="login.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>