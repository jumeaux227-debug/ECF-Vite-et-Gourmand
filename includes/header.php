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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Julie & José - Rôtisserie</title>

    <style>
        :root {
            --primary-orange: #E67E22; /* Orange rôtisserie chaleureux */
            --dark-orange: #D35400;    /* Orange foncé pour les survols (hover) */
            --light-bg: #F8F9FA;       /* Fond gris très clair / blanc cassé */
        }

        body {
            background-color: var(--light-bg);
            color: #333333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Barre de navigation blanche soulignée d'orange */
        .navbar {
            background-color: #FFFFFF !important;
            border-bottom: 3px solid var(--primary-orange) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .navbar-brand {
            color: #2C3E50 !important;
            font-size: 1.35rem !important;
        }

        /* Optimisation de la taille pour faire tenir tous les rôles sur une seule ligne */
        .nav-link {
            color: #333333 !important;
            font-size: 0.9rem !important; /* Taille légèrement réduite */
            white-space: nowrap;          /* Empêche le texte de sauter à la ligne */
            transition: color 0.2s ease-in-out;
        }

        .nav-link:hover, .nav-link:focus {
            color: var(--primary-orange) !important;
        }

        /* Boutons personnalisés Orange */
        .btn-primary {
            background-color: var(--primary-orange) !important;
            border-color: var(--primary-orange) !important;
            color: #FFFFFF !important;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--dark-orange) !important;
            border-color: var(--dark-orange) !important;
        }

        .btn-outline-primary {
            color: var(--primary-orange) !important;
            border-color: var(--primary-orange) !important;
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: var(--primary-orange) !important;
            color: #FFFFFF !important;
        }

        /* Ajustements globaux des utilitaires de texte et fonds Bootstrap */
        .text-primary {
            color: var(--primary-orange) !important;
        }

        .bg-primary {
            background-color: var(--primary-orange) !important;
        }

        .badge.bg-primary {
            background-color: var(--primary-orange) !important;
            color: #FFFFFF !important;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="index.php">Julie & José</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item"><a class="nav-link fw-bold ms-1" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link fw-bold ms-1" href="menus.php">Nos Menus</a></li>
                <li class="nav-item"><a class="nav-link fw-bold ms-1" href="contact.php">Contact</a></li>

                <?php if (isset($_SESSION['role_id'])): ?>
                    <?php if($_SESSION['role_id'] == 1): ?>
                        <li class="nav-item"><a class="nav-link text-danger fw-bold ms-1" href="admin_users.php">Panel Admin</a></li>
                        <li class="nav-item"><a class="nav-link text-danger fw-bold ms-1" href="admin_rh.php">👑 Gestion RH</a></li>
                        <li class="nav-item"><a class="nav-link text-success fw-bold ms-1" href="admin_stats.php">📈 Stats NoSQL</a></li>
                    <?php endif; ?>

                    <?php if($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
                        <li class="nav-item"><a class="nav-link text-warning fw-bold ms-1" href="admin_reviews.php">Modération Avis</a></li>
                    <?php endif; ?>

                    <?php if($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
                        <li class="nav-item"><a class="nav-link text-warning fw-bold ms-1" href="employe_dashboard.php">💼 Espace Employé</a></li>
                    <?php endif; ?>

                    <?php if($_SESSION['role_id'] == 3): ?>
                        <li class="nav-item"><a class="nav-link text-success fw-bold ms-1" href="add_review.php">Laisser un avis</a></li>
                        <li class="nav-item"><a class="nav-link text-primary fw-bold ms-1" href="espace_client.php">Mes Commandes</a></li>
                    <?php endif; ?>

                    <li class="nav-item ms-3 text-muted" style="font-size: 0.85rem;">Bonjour, <strong><?= htmlspecialchars($_SESSION['prenom'] ?? 'Utilisateur') ?></strong></li>
                    <li class="nav-item ms-2"><a class="btn btn-outline-danger btn-sm py-1 px-2" style="font-size: 0.8rem;" href="logout.php">Déconnexion</a></li>

                <?php else: ?>
                    <li class="nav-item ms-2"><a class="btn btn-outline-primary btn-sm" href="login.php">Connexion</a></li>
                    <li class="nav-item ms-2"><a class="btn btn-primary btn-sm text-white" href="register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>