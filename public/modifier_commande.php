<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Client connecté uniquement (role_id = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

$numero_commande = isset($_GET['id']) ? trim($_GET['id']) : '';
$utilisateur_id = $_SESSION['user_id'];

// 1. Récupération de la commande et des infos du menu associé
$query = "SELECT c.*, m.titre AS menu_titre, m.prix_par_personne, m.nombre_personne_minimum, m.quantite_restante 
          FROM commande c
          JOIN menu m ON c.menu_id = m.menu_id
          WHERE c.numero_commande = ? AND c.utilisateur_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$numero_commande, $utilisateur_id]);
$commande = $stmt->fetch();

// Sécurités de base
if (!$commande) {
    header('Location: espace_client.php?error=' . urlencode("Commande introuvable."));
    exit();
}

if (($commande['statut'] ?? 'en_attente') !== 'en_attente') {
    header('Location: espace_client.php?error=' . urlencode("Cette commande est déjà en cours de traitement et ne peut plus être modifiée."));
    exit();
}

// 2. Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_nombre = (int)$_POST['nombre_personne'];
    $ancien_nombre = (int)$commande['nombre_personne'];
    
    // Calcul de la capacité de production disponible réelle (stock actuel + ce que le client avait déjà bloqué)
    $stock_virtuel = (int)$commande['quantite_restante'] + $ancien_nombre;

    // Validation des règles métiers
    if ($nouveau_nombre < $commande['nombre_personne_minimum']) {
        $error = "Le nombre de convives ne peut pas être inférieur au minimum de " . $commande['nombre_personne_minimum'] . " personnes.";
    } elseif ($nouveau_nombre > $stock_virtuel) {
        $error = "Notre capacité de production maximale pour ce menu est de " . $stock_virtuel . " convives.";
    } else {
        // Recalcul du prix avec la remise de -10% si > 5 personnes
        $prix_base = $nouveau_nombre * $commande['prix_par_personne'];
        if ($nouveau_nombre > 5) {
            $prix_apres_remise = $prix_base * 0.90;
        } else {
            $prix_apres_remise = $prix_base;
        }

        // On conserve le prix de la livraison initialement calculé
        $nouveau_prix_total = $prix_apres_remise + (float)$commande['prix_livraison'];

        // Ajustement du stock de sécurité dans la table menu
        $difference_stock = $nouveau_nombre - $ancien_nombre;

        // Début d'une transaction SQL pour assurer la cohérence des deux updates
        $pdo->beginTransaction();
        try {
            // Update Commande
            $stmtUpdateCmd = $pdo->prepare("UPDATE commande SET nombre_personne = ?, prix_menu = ? WHERE numero_commande = ?");
            $stmtUpdateCmd->execute([$nouveau_nombre, $nouveau_prix_total, $numero_commande]);

            // Update Stock Menu
            $stmtUpdateStock = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante - ? WHERE menu_id = ?");
            $stmtUpdateStock->execute([$difference_stock, $commande['menu_id']]);

            $pdo->commit();
            header('Location: espace_client.php?success=1');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Une erreur technique est survenue lors de la modification.";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-4">
                <a href="espace_client.php" class="btn btn-outline-secondary btn-sm">← Annuler et retourner</a>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <h3 class="fw-bold mb-2">Modifier ma commande</h3>
                <p class="text-muted small mb-4">Commande : <strong><?= htmlspecialchars($commande['numero_commande']) ?></strong> (<?= htmlspecialchars($commande['menu_titre']) ?>)</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger small text-center"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label for="nombre_personne" class="form-label fw-bold text-dark">Nombre de convives / personnes</label>
                        <input type="number" class="form-control form-control-lg text-center" id="nombre_personne" name="nombre_personne" 
                               value="<?= (int)$commande['nombre_personne'] ?>" 
                               min="<?= (int)$commande['nombre_personne_minimum'] ?>" required>
                        <div class="form-text mt-2 small text-muted">
                            * Prix unitaire : <?= number_format($commande['prix_par_personne'], 2, ',', ' ') ?> € / pers.<br>
                            * Minimum requis : <?= $commande['nombre_personne_minimum'] ?> personnes.<br>
                            * Rappel : -10% de remise à partir de 6 personnes !
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 text-white fw-bold shadow-sm">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>