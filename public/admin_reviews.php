<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Seuls l'Admin (1) et l'Employé (2) ont accès
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: index.php');
    exit();
}

// Tri par avis_id 
$stmt = $pdo->query("SELECT a.avis_id, a.description, a.note, a.statut, u.prenom, u.nom FROM avis a JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id ORDER BY a.avis_id DESC");
$reviews = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Modération des Avis</h1>
        <span class="badge bg-warning text-dark p-2">Espace Personnel</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Client</th>
                        <th>Avis</th>
                        <th>Note</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Aucun avis déposé pour le moment.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($review['prenom'] . ' ' . $review['nom']) ?></strong></td>
                        <td><p class="mb-0 text-wrap" style="max-width: 400px;"><?= htmlspecialchars($review['description']) ?></p></td>
                        <td><span class="text-warning">★</span> <?= $review['note'] ?>/5</td>
                        <td>
                            <?php if($review['statut'] === 'valide'): ?>
                                <span class="badge bg-success">Validé</span>
                            <?php elseif($review['statut'] === 'refuse'): ?>
                                <span class="badge bg-danger">Refusé</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($review['statut'] === 'en_attente'): ?>
                                <a href="review_status_update.php?id=<?= $review['avis_id'] ?>&status=valide" class="btn btn-sm btn-success">Valider</a>
                                <a href="review_status_update.php?id=<?= $review['avis_id'] ?>&status=refuse" class="btn btn-sm btn-outline-danger">Refuser</a>
                            <?php else: ?>
                                <span class="text-muted text-sm">Traité</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>