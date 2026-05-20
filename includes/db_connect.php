<?php
// Configuration de la base de données
$host = 'localhost';
$db   = 'vite_et_gourmand'; 
$user = 'root';
$pass = ''; // Par défaut vide sur XAMPP/WAMP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Active les erreurs SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne des tableaux associatifs
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Meilleure sécurité contre les injections
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En cas d'erreur, on arrête tout et on affiche un message propre
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}