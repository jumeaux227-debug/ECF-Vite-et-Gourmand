<?php
function track_menu_view($menu_title) {
    $file_path = __DIR__ . '/../data/menu_stats.json';
    
    // Créer le dossier data s'il n'existe pas
    if (!file_exists(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
    }

    // Lire les données existantes
    $stats = [];
    if (file_exists($file_path)) {
        $json_content = file_get_contents($file_path);
        $stats = json_decode($json_content, true) ?? [];
    }

    // Mettre à jour le compteur pour ce menu (Structure type Document NoSQL)
    $date_today = date('Y-m-d');
    
    if (!isset($stats[$menu_title])) {
        $stats[$menu_title] = [
            "total_views" => 0,
            "history" => []
        ];
    }
    
    $stats[$menu_title]["total_views"] += 1;
    
    if (!isset($stats[$menu_title]["history"][$date_today])) {
        $stats[$menu_title]["history"][$date_today] = 0;
    }
    $stats[$menu_title]["history"][$date_today] += 1;

    // Sauvegarder dans le fichier JSON (Simulation NoSQL)
    file_put_contents($file_path, json_encode($stats, JSON_PRETTY_PRINT));
}