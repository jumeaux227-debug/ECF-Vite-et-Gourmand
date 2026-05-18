<?php 
require_once __DIR__ . '/../includes/header.php';

// PROTECTION : Réservé à l'Admin (role_id = 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header('Location: index.php');
    exit();
}

// Chargement des données statistiques JSON
$file_path = __DIR__ . '/../data/menu_stats.json';
$stats = [];
if (file_exists($file_path)) {
    // Le paramètre JSON_UNESCAPED_UNICODE est souvent utilisé à l'écriture, 
    // ici on s'assure de lire proprement le contenu
    $json_content = file_get_contents($file_path);
    $stats = json_decode($json_content, true) ?? [];
}

// Préparation des données pour Chart.js
$labels = [];
$data_views = [];

foreach ($stats as $menu_title => $info) {
    // On force le décodage au cas où une chaîne contiendrait encore des séquences \uXXXX
    $clean_title = json_decode('"' . $menu_title . '"') ?? $menu_title;
    $labels[] = $clean_title;
    $data_views[] = $info['total_views'] ?? 0;
}
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold">Statistiques d'Activité</h1>
        <span class="badge bg-danger p-2">Espace Décisionnel Admin</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="fw-bold mb-4">Popularité des Menus (Nombre de consultations)</h5>
                <canvas id="chartMenus" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="fw-bold mb-3">Données brutes</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Menu</th>
                                <th class="text-end">Vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($stats)): ?>
                                <tr><td colspan="2" class="text-center text-muted small py-3">Aucune donnée enregistrée</td></tr>
                            <?php endif; ?>
                            <?php foreach($stats as $title => $info): ?>
                                <tr>
                                    <td><small class="fw-bold"><?= htmlspecialchars($title) ?></small></td>
                                    <td class="text-end"><span class="badge bg-primary"><?= $info['total_views'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartMenus').getContext('2d');
    
    // Récupération des données PHP converties proprement en tableaux JS
    const menuLabels = <?= json_encode($labels) ?>;
    const menuData = <?= json_encode($data_views) ?>;

    new Chart(ctx, {
        type: 'bar', // Type de graphique exigé (Histogramme / Barres)
        data: {
            labels: menuLabels,
            datasets: [{
                label: 'Nombre de consultations',
                data: menuData,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1 // Évite les nombres à virgule pour des vues uniques
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>