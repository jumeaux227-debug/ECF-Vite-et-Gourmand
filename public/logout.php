<?php
session_start();
// On détruit toutes les variables de session
session_unset();
// On détruit la session elle-même
session_destroy();

// On redirige vers l'accueil
header('Location: index.php');
exit();