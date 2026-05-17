<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// 1. Récupération des menus avec les noms des thèmes et régimes (via ton MCD)
$query = "SELECT m.menu_id, m.titre, m.description, m.nombre_personne_minimum, m.prix_par_personne, m.quantite_restante,
                 t.libelle AS theme_nom, 
                 r.libelle AS regime_nom
          FROM menu m
          LEFT JOIN theme t ON m.theme_id = t.theme_id
          LEFT JOIN regime r ON m.regime_id = r.regime_id";
$stmt = $pdo->query($query);
$menus = $stmt->fetchAll();

// 2. Récupération des thèmes pour générer les filtres de l'interface
$themes = $pdo->query("SELECT * FROM theme")->fetchAll();

// 3. Récupération des régimes pour générer les filtres de l'interface
$regimes = $pdo->query("SELECT * FROM regime")->fetchAll();
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Notre Carte Gastronomique</h1>
        <p class="text-muted">Découvrez les créations exclusives de Julie & José</p>
    </div>

    <div class="card p-4 shadow-sm border-0 mb-5 bg-light">
        <h5 class="fw-bold mb-3">Filtres de recherche</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Thème</label>
                <select id="filter-theme" class="form-select form-select-sm">
                    <option value="all">Tous les thèmes</option>
                    <?php foreach ($themes as $t): ?>
                        <option value="<?= htmlspecialchars($t['libelle']) ?>"><?= htmlspecialchars($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Régime alimentaire</label>
                <select id="filter-regime" class="form-select form-select-sm">
                    <option value="all">Tous les régimes</option>
                    <?php foreach ($regimes as $r): ?>
                        <option value="<?= htmlspecialchars($r['libelle']) ?>"><?= htmlspecialchars($r['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Convives minimum</label>
                <input type="number" id="filter-personnes" class="form-control form-control-sm" placeholder="Ex: 4" min="1">
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Prix max par personne</label>
                <input type="number" id="filter-prix" class="form-control form-control-sm" placeholder="Ex: 50" min="0">
            </div>
        </div>
    </div>

    <div class="row g-4" id="menus-container">
        <?php foreach ($menus as $menu): ?>
            <div class="col-md-4 menu-item-card" 
                 data-theme="<?= htmlspecialchars($menu['theme_nom'] ?? '') ?>"
                 data-regime="<?= htmlspecialchars($menu['regime_nom'] ?? '') ?>"
                 data-personnes="<?= (int)$menu['nombre_personne_minimum'] ?>"
                 data-prix="<?= (float)$menu['prix_par_personne'] ?>">
                 
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge bg-primary"><?= htmlspecialchars($menu['theme_nom'] ?? 'Classique') ?></span>
                            <span class="badge bg-success"><?= htmlspecialchars($menu['regime_nom'] ?? 'Standard') ?></span>
                        </div>
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($menu['titre']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($menu['description']) ?></p>
                        
                        <hr class="text-muted opacity-25">
                        
                        <div class="small text-muted mb-2">
                            Minimum : <strong><?= $menu['nombre_personne_minimum'] ?> pers.</strong><br>
                            Stock restant : <strong><?= $menu['quantite_restante'] ?> commandes</strong>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold fs-5 text-dark"><?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?> €<span class="text-muted small fs-6">/pers</span></span>
                            <a href="menu_detail.php?id=<?= $menu['menu_id'] ?>" class="btn btn-sm btn-outline-primary">Voir le menu</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sélection des inputs de filtrage
    const themeSelect = document.getElementById('filter-theme');
    const regimeSelect = document.getElementById('filter-regime');
    const personnesInput = document.getElementById('filter-personnes');
    const prixInput = document.getElementById('filter-prix');
    
    // Sélection de toutes les cartes de menus
    const menuCards = document.querySelectorAll('.menu-item-card');

    function filterMenus() {
        const selectedTheme = themeSelect.value;
        const selectedRegime = regimeSelect.value;
        const maxPersonnes = parseInt(personnesInput.value) || 0;
        const maxPrix = parseFloat(prixInput.value) || Infinity;

        menuCards.forEach(card => {
            // Récupération des data-attributes de la carte
            const cardTheme = card.getAttribute('data-theme');
            const cardRegime = card.getAttribute('data-regime');
            const cardPersonnes = parseInt(card.getAttribute('data-personnes'));
            const cardPrix = parseFloat(card.getAttribute('data-prix'));

            // Validation de chaque critère
            const matchTheme = (selectedTheme === 'all' || cardTheme === selectedTheme);
            const matchRegime = (selectedRegime === 'all' || cardRegime === selectedRegime);
            const matchPersonnes = (maxPersonnes === 0 || cardPersonnes <= maxPersonnes);
            const matchPrix = (cardPrix <= maxPrix);

            // Si la carte valide TOUS les critères, on l'affiche, sinon on la cache
            if (matchTheme && matchRegime && matchPersonnes && matchPrix) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Écoute des événements sur tous les champs
    themeSelect.addEventListener('change', filterMenus);
    regimeSelect.addEventListener('change', filterMenus);
    personnesInput.addEventListener('input', filterMenus);
    prixInput.addEventListener('input', filterMenus);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>