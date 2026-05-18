<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Client connecté uniquement (role_id = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

$utilisateur_id = $_SESSION['user_id'];

// Récupération de l'historique avec les vrais noms de colonnes de ton MCD : numero_commande et date_commande
$query = "SELECT c.*, m.titre AS menu_titre 
          FROM commande c
          JOIN menu m ON c.menu_id = m.menu_id
          WHERE c.utilisateur_id = ?
          ORDER BY c.date_commande DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$utilisateur_id]);
$commandes = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">Mon Espace Client</h1>
            <p class="text-muted">Consultez vos commandes et gérez vos réservations en cours.</p>
        </div>
        <a href="menus.php" class="btn btn-primary text-white fw-bold">+ Nouvelle commande</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center mb-4">L'opération a été effectuée avec succès !</div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-3">Historique de mes commandes</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>N° Commande</th>
                        <th>Menu</th>
                        <th>Date Livraison</th>
                        <th>Prix Menu</th>
                        <th>Suivi Réel</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commandes)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Vous n'avez pas encore passé de commande.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commandes as $cmd): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($cmd['numero_commande']) ?></strong></td>
                                <td><span class="fw-bold text-dark"><?= htmlspecialchars($cmd['menu_titre']) ?></span></td>
                                <td><?= date('d/m/Y', strtotime($cmd['data_prestation'])) ?></td>
                                <td><span class="fw-bold text-primary"><?= number_format($cmd['prix_menu'], 2, ',', ' ') ?> €</span></td>
                                <td>
                                    <?php 
                                    $statut = $cmd['statut'] ?? 'en_attente'; 
                                    if ($statut === 'en_attente') {
                                        echo '<span class="badge bg-warning text-dark">⏳ En attente</span>';
                                    } elseif ($statut === 'accepte') {
                                        echo '<span class="badge bg-info text-white">👨‍🍳 En préparation</span>';
                                    } elseif ($statut === 'termine') {
                                        echo '<span class="badge bg-success text-white">✅ Livrée</span>';
                                    } elseif ($statut === 'annule') {
                                        echo '<span class="badge bg-danger text-white">❌ Annulée</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($statut === 'en_attente'): ?>
                                        <a href="modifier_commande.php?id=<?= urlencode($cmd['numero_commande']) ?>" class="btn btn-sm btn-outline-secondary me-1">Modifier</a>
                                        <a href="annuler_commande.php?id=<?= urlencode($cmd['numero_commande']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">Annuler</a>
                                    <?php else: ?>
                                        <span class="text-muted small italic">Verrouillée</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>