<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Extraction et sécurisation de l'identifiant du menu passé dans l'URL
$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($menu_id <= 0) {
    header('Location: menus.php');
    exit();
}

// 1. Récupération des informations principales du menu selon ton MCD
$queryMenu = "SELECT m.*, t.libelle AS theme_nom, r.libelle AS regime_nom 
              FROM menu m
              LEFT JOIN theme t ON m.theme_id = t.theme_id
              LEFT JOIN regime r ON m.regime_id = r.regime_id
              WHERE m.menu_id = ?";
$stmtMenu = $pdo->prepare($queryMenu);
$stmtMenu->execute([$menu_id]);
$menu = $stmtMenu->fetch();

// Si le menu n'existe pas en base de données, on arrête tout
if (!$menu) {
    echo "<div class='container py-5'><div class='alert alert-danger text-center'>Ce menu n'existe pas ou a été retiré.</div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

// L'emplacement est corrigé ici : Le menu existe bien, on enregistre sa consultation pour l'étape 4
require_once __DIR__ . '/../includes/log_tracker.php';
track_menu_view($menu['titre']);

// 2. Récupération des plats associés à ce menu via la table associative menu_plat
$queryPlats = "SELECT p.* FROM plat p
               JOIN menu_plat mp ON p.plat_id = mp.plat_id
               WHERE mp.menu_id = ?";
$stmtPlats = $pdo->prepare($queryPlats);
$stmtPlats->execute([$menu_id]);
$plats = $stmtPlats->fetchAll();
?>

<div class="container py-5">
    <div class="mb-4">
        <a href="menus.php" class="btn btn-outline-secondary btn-sm">← Retour à la carte</a>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-primary px-3 py-2"><?= htmlspecialchars($menu['theme_nom'] ?? 'Événement') ?></span>
                <span class="badge bg-success px-3 py-2"><?= htmlspecialchars($menu['regime_nom'] ?? 'Classique') ?></span>
            </div>
            
            <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($menu['titre']) ?></h1>
            <p class="lead text-secondary mb-5"><?= htmlspecialchars($menu['description']) ?></p>

            <h3 class="fw-bold mb-4 border-bottom pb-2">Composition du menu</h3>
            <?php if (empty($plats)): ?>
                <p class="text-muted italic">La liste détaillée des plats composants ce menu sera bientôt disponible.</p>
            <?php else: ?>
                <div class="row g-4 mb-5">
                    <?php foreach ($plats as $plat): ?>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm bg-light">
                                <div class="card-body p-4">
                                    <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($plat['titre_plat']) ?></h5>
                                    
                                    <?php
                                    $queryAllergene = "SELECT a.libelle FROM allergene a 
                                                       JOIN plat_allergene pa ON a.allergene_id = pa.allergene_id 
                                                       WHERE pa.plat_id = ?";
                                    $stmtAllergene = $pdo->prepare($queryAllergene);
                                    $stmtAllergene->execute([$plat['plat_id']]);
                                    $allergenes = $stmtAllergene->fetchAll(PDO::FETCH_COLUMN);
                                    ?>
                                    
                                    <div class="mt-3">
                                        <span class="text-muted small fw-bold d-block mb-1">Allergènes :</span>
                                        <?php if (!empty($allergenes)): ?>
                                            <?php foreach ($allergenes as $all): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill me-1 small">
                                                    <?= htmlspecialchars($all) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small italic">Aucun allergène signalé</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-light mb-4 text-center">
                <span class="text-muted small uppercase fw-bold">Tarif dégressif</span>
                <div class="my-2">
                    <span class="display-6 fw-bold text-primary"><?= number_format($menu['prix_par_personne'], 2, ',', ' ') ?> €</span>
                    <span class="text-muted small"> / convive</span>
                </div>
                <div class="text-muted small mb-4">
                    Minimum de commande : <strong><?= (int)$menu['nombre_personne_minimum'] ?> personnes</strong>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="commande.php?menu_id=<?= $menu['menu_id'] ?>" class="btn btn-primary btn-lg w-100 text-white fw-bold shadow-sm">
                        Commander ce menu
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning small text-start mb-3">
                        L'accès à l'espace de commande nécessite une authentification préalable.
                    </div>
                    <a href="login.php" class="btn btn-outline-primary w-100 fw-bold mb-2">Se connecter</a>
                    <a href="register.php" class="btn btn-link btn-sm w-100 text-muted">Créer un compte client</a>
                <?php endif; ?>
            </div>

            <div class="card border-warning bg-warning bg-opacity-10 p-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-2 text-warning-dark">
                    <strong class="fs-5">⚠️ Conditions impératives</strong>
                </div>
                <p class="small text-dark mb-3">
                    Afin de garantir la fraîcheur de nos produits locaux de la région de Bordeaux, Julie & José requièrent une planification stricte pour ce menu.
                </p>
                <div class="small bg-white p-3 rounded border border-warning border-opacity-20 text-secondary">
                    Stock d'engagements restants : <strong class="text-dark"><?= (int)$menu['quantite_restante'] ?> formules disponibles</strong>.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>