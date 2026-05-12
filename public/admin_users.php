<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Si pas connecté OU si pas admin (role_id 1), on dégage !
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header('Location: index.php');
    exit();
}

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT utilisateur_id, prenom, nom, email, role_id FROM utilisateur");
$users = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Gestion des Utilisateurs</h1>
        <span class="badge bg-danger p-2">Espace Administration</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Nom Complet</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= $user['utilisateur_id'] ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php if($user['role_id'] == 1): ?>
                                <span class="badge bg-danger text-white">Admin</span>
                            <?php elseif($user['role_id'] == 2): ?>
                                <span class="badge bg-warning text-dark">Employé</span>
                            <?php else: ?>
                                <span class="badge bg-info text-white">Client</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary">Modifier</button>
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>