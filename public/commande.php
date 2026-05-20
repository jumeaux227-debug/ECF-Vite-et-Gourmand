<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Client connecté uniquement
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: login.php');
    exit();
}

$menu_id = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;

// Récupération des infos du menu pour les calculs et contraintes
$stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
$stmt->execute([$menu_id]);
$menu = $stmt->fetch();

if (!$menu) {
    header('Location: menus.php');
    exit();
}

// Récupération des infos de l'utilisateur connecté (pour avoir sa ville par défaut)
$stmtUser = $pdo->prepare("SELECT ville FROM utilisateur WHERE utilisateur_id = ?");
$stmtUser->execute([$_SESSION['user_id']]);
$user = $stmtUser->fetch();
$ville_utilisateur = $user['ville'] ?? '';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-4">
                <h2 class="fw-bold mb-2">Finaliser votre commande</h2>
                <p class="text-muted mb-4">Menu sélectionné : <strong class="text-primary"><?= htmlspecialchars($menu['titre']) ?></strong></p>

                <form action="traitement_commande.php" method="POST" id="form-commande">
                    <input type="hidden" name="menu_id" value="<?= $menu['menu_id'] ?>">
                    
                    <div class="mb-3">
                        <label for="quantite" class="form-label fw-bold">Nombre de personnes</label>
                        <input type="number" name="quantite" id="quantite" class="form-control" 
                               value="<?= $menu['nombre_personne_minimum'] ?>" 
                               min="<?= $menu['nombre_personne_minimum'] ?>" required>
                        <div class="form-text text-warning">
                            * Ce menu impose un minimum de <?= $menu['nombre_personne_minimum'] ?> convives.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label fw-bold">Adresse de livraison</label>
                        <input type="text" name="adresse" id="adresse" class="form-control" placeholder="12 Rue Sainte-Catherine" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="ville" class="form-label fw-bold">Ville</label>
                            <input type="text" name="ville" id="ville" class="form-control" value="<?= htmlspecialchars($ville_utilisateur) ?>" required>
                        </div>
                        <div class="col-md-6" id="zone-km" style="display: none;">
                            <label for="kilometres" class="form-label fw-bold">Distance depuis Bordeaux (en km)</label>
                            <input type="number" name="kilometres" id="kilometres" class="form-control" value="0" min="0" step="0.1">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="date_livraison" class="form-label fw-bold">Date souhaitée</label>
                        <input type="date" name="date_livraison" id="date_livraison" class="form-control" required>
                    </div>

                    <div class="card bg-light border-0 p-3 mb-4">
                        <h5 class="fw-bold mb-2">Récapitulatif de facturation</h5>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Prix de base (<span id="out-qty">0</span> x <?= number_format($menu['prix_par_personne'], 2) ?>€) :</span>
                            <span><span id="out-base">0.00</span> €</span>
                        </div>
                        <div class="d-flex justify-content-between small text-success mb-1" id="remise-box" style="display:none;">
                            <span>Remise commerciale (Volume > 5 pers. -10%) :</span>
                            <span>-<span id="out-remise">0.00</span> €</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Frais de livraison :</span>
                            <span><span id="out-livraison">0.00</span> €</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5 text-dark">
                            <span>Total estimé :</span>
                            <span><span id="out-total">0.00</span> €</span>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg text-white fw-bold">Confirmer et payer à la livraison</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const prixParPersonne = <?= (float)$menu['prix_par_personne'] ?>;
    
    const inputQuantite = document.getElementById('quantite');
    const inputVille = document.getElementById('ville');
    const inputKm = document.getElementById('kilometres');
    const zoneKm = document.getElementById('zone-km');

    function calculerEstimation() {
        let qte = parseInt(inputQuantite.value) || 0;
        let ville = inputVille.value.trim().toLowerCase();
        let km = parseFloat(inputKm.value) || 0;

        // 1. Prix de base
        let prixBase = qte * prixParPersonne;
        
        // 2. Application de la règle de remise de 10% si > 5 personnes
        let remise = 0;
        if (qte > 5) {
            remise = prixBase * 0.10;
            document.getElementById('remise-box').style.style = 'block';
        } else {
            document.getElementById('remise-box').style.display = 'none';
        }

        // 3. Application de la règle des frais de livraison kilométriques
        let fraisLivraison = 0;
        if (ville !== 'bordeaux' && ville !== '') {
            zoneKm.style.display = 'block';
            fraisLivraison = 5 + (km * 0.59);
        } else {
            zoneKm.style.display = 'none';
            inputKm.value = 0;
        }

        // 4. Calcul du total final
        let total = (prixBase - remise) + fraisLivraison;

        // Injection dans l'affichage HTML
        document.getElementById('out-qty').innerText = qte;
        document.getElementById('out-base').innerText = prixBase.toFixed(2);
        document.getElementById('out-remise').innerText = remise.toFixed(2);
        document.getElementById('out-livraison').innerText = fraisLivraison.toFixed(2);
        document.getElementById('out-total').innerText = total.toFixed(2);
    }

    // Écouteurs pour mettre à jour le calcul à la moindre modification
    inputQuantite.addEventListener('input', calculerEstimation);
    inputVille.addEventListener('input', calculerEstimation);
    inputKm.addEventListener('input', calculerEstimation);

    // Lancement au chargement initial
    calculerEstimation();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>