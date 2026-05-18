<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION STRICTE : Administrateur uniquement (role_id = 1)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header('Location: index.php');
    exit();
}

// 1. Récupération de tous les comptes utilisateurs (hors Admin actuel pour éviter de se bloquer soi-même)
$admin_id = $_SESSION['user_id'];
$query = "SELECT u.*, r.libelle AS role_nom 
          FROM utilisateur u
          JOIN role r ON u.role_id = r.role_id
          WHERE u.utilisateur_id != ?
          ORDER BY u.role_id ASC, u.nom ASC";
$stmt = $pdo->prepare($query);
$stmt->execute([$admin_id]);
$utilisateurs = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">Gestion des Ressources Humaines</h1>
            <p class="text-muted">Créez les comptes de vos collaborateurs et gérez les accès à la plateforme.</p>
        </div>
        <a href="admin_create_employe.php" class="btn btn-primary text-white fw-bold">+ Créer un compte Employé</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center mb-4">Le statut du compte a été mis à jour avec succès !</div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-3">Collaborateurs & Clients enregistrés</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nom / Prénom</th>
                        <th>Email</th>
                        <th>Rôle / Fonction</th>
                        <th>Statut Compte</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></strong></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if ($user['role_id'] == 2): ?>
                                    <span class="badge bg-warning text-dark">💼 Employé / Cuisinier</span>
                                <?php elseif ($user['role_id'] == 1): ?>
                                    <span class="badge bg-danger text-white">👑 Administrateur</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary">👤 Client</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!isset($user['statut_compte']) || $user['statut_compte'] == 1): ?>
                                    <span class="badge bg-success text-white">✔️ Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger text-white">❌ Désactivé</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php 
                                $nouveau_statut = (!isset($user['statut_compte']) || $user['statut_compte'] == 1) ? 0 : 1;
                                $bouton_texte = ($nouveau_statut == 0) ? 'Désactiver' : 'Activer';
                                $bouton_classe = ($nouveau_statut == 0) ? 'btn-outline-danger' : 'btn-success text-white';
                                ?>
                                <a href="admin_toggle_user.php?id=<?= $user['utilisateur_id'] ?>&statut=<?= $nouveau_statut ?>" 
                                   class="btn btn-sm <?= $bouton_classe ?>"
                                   onclick="return confirm('Confirmez-vous le changement de statut pour ce compte ?');">
                                    <?= $bouton_texte ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>