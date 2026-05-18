<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Réservé aux Employés (role_id = 2) et Admin (role_id = 1)
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: index.php');
    exit();
}

// Récupération des filtres depuis l'URL
$filtre_statut = isset($_GET['statut']) ? trim($_GET['statut']) : '';
$filtre_client = isset($_GET['client']) ? trim($_GET['client']) : '';

// Construction de la requête SQL dynamique
$sql = "SELECT c.*, m.titre AS menu_titre, u.nom AS client_nom, u.prenom AS client_prenom, u.email AS client_email
        FROM commande c
        JOIN menu m ON c.menu_id = m.menu_id
        JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
        WHERE 1=1";

$params = [];

if ($filtre_statut !== '') {
    $sql .= " AND c.statut = ?";
    $params[] = $filtre_statut;
}

if ($filtre_client !== '') {
    $sql .= " AND (u.nom LIKE ? OR u.prenom LIKE ?)";
    $params[] = "%$filtre_client%";
    $params[] = "%$filtre_client%";
}

$sql .= " ORDER BY c.date_commande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll();
?>

<div class="container-fluid py-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">Espace Gestion Rôtisserie</h1>
            <p class="text-muted">Gérez les commandes, le cycle de vie des prestations et le suivi du matériel.</p>
        </div>
        <span class="badge bg-dark p-2">Accès Personnel</span>
    </div>

    <div class="card shadow-sm border-0 p-3 mb-4 bg-light">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filtrer par Client (Nom / Prénom)</label>
                <input type="text" name="client" class="form-control" value="<?= htmlspecialchars($filtre_client) ?>" placeholder="Ex: Dupond...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filtrer par Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" <?= $filtre_statut === 'en_attente' ? 'selected' : '' ?>>⏳ En attente</option>
                    <option value="accepte" <?= $filtre_statut === 'accepte' ? 'selected' : '' ?>>👨‍🍳 Acceptée / En préparation</option>
                    <option value="termine" <?= $filtre_statut === 'termine' ? 'selected' : '' ?>>✅ Terminée / Livrée</option>
                    <option value="annule" <?= $filtre_statut === 'annule' ? 'selected' : '' ?>>❌ Annulée</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary text-white w-100 fw-bold">Rechercher</button>
                <a href="employe_dashboard.php" class="btn btn-outline-secondary w-100">Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Client</th>
                        <th>Menu commandé</th>
                        <th>Date Prestation</th>
                        <th>Total</th>
                        <th>Statut Actuel</th>
                        <th>Prêt Matériel</th>
                        <th class="text-end">Actions de Suivi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commandes)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucune commande ne correspond à ces critères.</td></tr>
                    <?php else: ?>
                        <?php foreach ($commandes as $cmd): ?>
                            <?php 
                            // Calcul de la règle stricte des 10 jours pour le matériel
                            $date_prestation_timestamp = strtotime($cmd['date_prestation']);
                            $jours_passes = (time() - $date_prestation_timestamp) / 86400; // 86400 secondes = 1 jour
                            $alerte_materiel_declenchee = ($cmd['pret_materiel'] == 1 && $cmd['restitution_materiel'] == 0 && $jours_passes > 10 && $cmd['statut'] === 'termine');
                            ?>
                            <tr class="<?= $alerte_materiel_declenchee ? 'table-danger' : '' ?>">
                                <td><small class="fw-bold text-secondary"><?= $cmd['numero_commande'] ?></small></td>
                                <td>
                                    <span class="fw-bold d-block"><?= htmlspecialchars($cmd['client_nom'] . ' ' . $cmd['client_prenom']) ?></span>
                                    <small class="text-muted"><?= htmlspecialchars($cmd['client_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($cmd['menu_titre']) ?> <span class="badge bg-light text-dark">x<?= $cmd['nombre_personne'] ?></span></td>
                                <td><?= date('d/m/Y', $date_prestation_timestamp) ?></td>
                                <td><span class="fw-bold text-primary"><?= number_format($cmd['prix_menu'], 2, ',', ' ') ?> €</span></td>
                                <td>
                                    <?php 
                                    if ($cmd['statut'] === 'en_attente') echo '<span class="badge bg-warning text-dark">En attente</span>';
                                    if ($cmd['statut'] === 'accepte') echo '<span class="badge bg-info text-white">Acceptée</span>';
                                    if ($cmd['statut'] === 'termine') echo '<span class="badge bg-success text-white">Terminée</span>';
                                    if ($cmd['statut'] === 'annule') echo '<span class="badge bg-danger text-white">Annulée</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if ($cmd['pret_materiel'] == 1): ?>
                                        <?php if ($cmd['restitution_materiel'] == 1): ?>
                                            <span class="badge bg-light text-success">✔️ Rendu</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-danger">⚠️ Non rendu</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">Aucun</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <?php if ($cmd['statut'] === 'en_attente'): ?>
                                            <a href="update_status.php?id=<?= urlencode($cmd['numero_commande']) ?>&action=accepte" class="btn btn-sm btn-success text-white">Accepter</a>
                                        <?php endif; ?>
                                        
                                        <?php if ($cmd['statut'] === 'accepte'): ?>
                                            <a href="update_status.php?id=<?= urlencode($cmd['numero_commande']) ?>&action=termine" class="btn btn-sm btn-primary text-white">Clôturer / Livrée</a>
                                        <?php endif; ?>

                                        <?php if ($cmd['pret_materiel'] == 1 && $cmd['restitution_materiel'] == 0 && $cmd['statut'] === 'termine'): ?>
                                            <a href="update_status.php?id=<?= urlencode($cmd['numero_commande']) ?>&action=restitue" class="btn btn-sm btn-outline-success">Marquer Rendu</a>
                                        <?php endif; ?>

                                        <?php if ($alerte_materiel_declenchee): ?>
                                            <a href="send_penalty_email.php?id=<?= urlencode($cmd['numero_commande']) ?>" class="btn btn-sm btn-danger fw-bold animate-pulse">⚠️ Facturer 600€</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>