<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Récupération stricte des avis validés avec le nom de leur auteur
$stmt = $pdo->query("SELECT a.description, a.note, u.prenom, u.nom 
                     FROM avis a 
                     JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id 
                     WHERE a.statut = 'valide' 
                     ORDER BY a.avis_id DESC");
$reviews = $stmt->fetchAll();
?>

<header class="bg-light py-5 border-bottom">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bolder text-dark mb-3">Vite & Gourmand</h1>
                <p class="lead text-muted mb-4">
                    Forts de nos **25 ans d'expérience au cœur de Bordeaux**, nous (Julie et José) mettons notre savoir-faire passionné à votre service. Nous concevons et livrons des repas d'exception, alliant rapidité de service et haute exigence gastronomique pour tous vos événements professionnels et privés.
                </p>
                
                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3): ?>
                    <a class="btn btn-primary btn-lg text-white" href="add_review.php">Laisser un avis client</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<section class="py-5 bg-white">
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Ce que disent nos clients</h2>
            <p class="lead text-muted mb-0">Découvrez les retours d'expérience validés par notre équipe</p>
        </div>
        
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <?php if (empty($reviews)): ?>
                    <p class="text-center text-muted italic">Aucun avis client n'est publié pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="card mb-4 shadow-sm border-0 bg-light">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-bold"><?= htmlspecialchars($review['prenom'] . ' ' . $review['nom']) ?></div>
                                    <div class="text-warning">
                                        <?= str_repeat('★', $review['note']) ?><?= str_repeat('☆', 5 - $review['note']) ?>
                                    </div>
                                </div>
                                <p class="card-text text-secondary">" <?= htmlspecialchars($review['description']) ?> "</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>