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
        <h1 class="display-5 fw-bold">Notre Carte Gastronomique</h1>
        <p class="text-muted lead">Découvrez les créations exclusives et rôtisseries traditionnelles de Julie & José</p>
    </div>

    <div class="card p-4 shadow-sm border-0 mb-5 bg-white border-start border-primary border-4">
        <h5 class="fw-bold mb-3 text-dark">🔍 Affiner votre recherche</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <label for="filter-theme" class="form-label text-muted small fw-bold">Thème</label>
                <select id="filter-theme" class="form-select">
                    <option value="all">Tous les thèmes</option>
                    <?php foreach ($themes as $t): ?>
                        <option value="<?= htmlspecialchars($t['libelle']) ?>"><?= htmlspecialchars($t['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="filter-regime" class="form-label text-muted small fw-bold">Régime alimentaire</label>
                <select id="filter-regime" class="form-select">
                    <option value="all">Tous les régimes</option>
                    <?php foreach ($regimes as $r): ?>
                        <option value="<?= htmlspecialchars($r['libelle']) ?>"><?= htmlspecialchars($r['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="filter-personnes" class="form-label text-muted small fw-bold">Convives minimum</label>
                <input type="number" id="filter-personnes" class="form-control" placeholder="Ex: 4" min="1">
            </div>

            <div class="col-md-3">
                <label for="filter-prix" class="form-label text-muted small fw-bold">Prix max par personne</label>
                <input type="number" id="filter-prix" class="form-control" placeholder="Ex: 50" min="0">
            </div>
        </div>
    </div>

    <div class="row g-4" id="menus-container">
        <?php foreach ($menus as $menu): ?>
            <div class="col-md-6 col-lg-4 menu-item-card" 
                 data-theme="<?= htmlspecialchars($menu['theme_nom'] ?? '') ?>"
                 data-regime="<?= htmlspecialchars($menu['regime_nom'] ?? '') ?>"
                 data-personnes="<?= (int)$menu['nombre_personne_minimum'] ?>"
                 data-prix="<?= (float)$menu['prix_par_personne'] ?>">
                 
                <div class="card h-100 shadow-sm border-0 overflow-hidden card-hover-effect">
                    <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, var(--primary-orange), var(--dark-orange));">
                        <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 fw-bold">ID #<?= $menu['menu_id'] ?></span>
                        <h3 class="h4 fw-bold my-2 text-truncate text-white"><?= htmlspecialchars($menu['titre']) ?></h3>
                        <div class="fs-3 fw-bold text-white"><?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?> €</div>
                        <small class="text-white-50">par convive</small>
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($menu['theme_nom'] ?? 'Classique') ?></span>
                                <span class="badge bg-light text-success border border-success-subtle"><?= htmlspecialchars($menu['regime_nom'] ?? 'Standard') ?></span>
                            </div>
                            <p class="card-text text-secondary small mb-4"><?= htmlspecialchars($menu['description']) ?></p>
                        </div>
                        
                        <div>
                            <hr class="text-muted opacity-25">
                            
                            <ul class="list-unstyled small text-muted mb-4 bg-light p-3 rounded">
                                <li class="mb-1">👤 Minimum : <strong class="text-dark"><?= $menu['nombre_personne_minimum'] ?> personnes</strong></li>
                                <li>🍗 Capacité : <strong class="text-dark"><?= $menu['quantite_restante'] !== null ? $menu['quantite_restante'] . ' parts restantes' : 'Illimitée' ?></strong></li>
                            </ul>
                            
                            <a href="menu_detail.php?id=<?= $menu['menu_id'] ?>" class="btn btn-primary w-100 fw-bold py-2 shadow-sm text-white">
                                🛒 Découvrir la formule
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* Petit effet visuel moderne au survol des cartes */
.card-hover-effect {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card-hover-effect:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeSelect = document.getElementById('filter-theme');
    const regimeSelect = document.getElementById('filter-regime');
    const personnesInput = document.getElementById('filter-personnes');
    const prixInput = document.getElementById('filter-prix');
    const menuCards = document.querySelectorAll('.menu-item-card');

    function filterMenus() {
        const selectedTheme = themeSelect.value;
        const selectedRegime = regimeSelect.value;
        const maxPersonnes = parseInt(personnesInput.value) || 0;
        const maxPrix = parseFloat(prixInput.value) || Infinity;

        menuCards.forEach(card => {
            const cardTheme = card.getAttribute('data-theme');
            const cardRegime = card.getAttribute('data-regime');
            const cardPersonnes = parseInt(card.getAttribute('data-personnes'));
            const cardPrix = parseFloat(card.getAttribute('data-prix'));

            const matchTheme = (selectedTheme === 'all' || cardTheme === selectedTheme);
            const matchRegime = (selectedRegime === 'all' || cardRegime === selectedRegime);
            const matchPersonnes = (maxPersonnes === 0 || cardPersonnes <= maxPersonnes);
            const matchPrix = (cardPrix <= maxPrix);

            if (matchTheme && matchRegime && matchPersonnes && matchPrix) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    themeSelect.addEventListener('change', filterMenus);
    regimeSelect.addEventListener('change', filterMenus);
    personnesInput.addEventListener('input', filterMenus);
    prixInput.addEventListener('input', filterMenus);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>