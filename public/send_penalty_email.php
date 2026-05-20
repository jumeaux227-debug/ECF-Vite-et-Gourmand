<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    header('Location: index.php');
    exit();
}

$numero_commande = isset($_GET['id']) ? trim($_GET['id']) : '';

$query = "SELECT c.*, u.nom, u.prenom, u.email 
          FROM commande c 
          JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id 
          WHERE c.numero_commande = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$numero_commande]);
$cmd = $stmt->fetch();

if (!$cmd) {
    die("Commande introuvable.");
}
?>

<div class="container py-5">
    <div class="card shadow border-danger p-5 mx-auto" style="max-width: 750px;">
        <div class="text-center mb-4">
            <span class="display-1 text-danger">✉️</span>
            <h2 class="fw-bold text-danger mt-2">Notification d'Avertissement Envoyée</h2>
            <p class="text-muted">Règle d'engagement Julie & José — Délai de restitution de 10 jours dépassé.</p>
        </div>

        <div class="bg-light p-4 rounded border font-monospace text-dark small mb-4">
            <strong>De :</strong> gestion@julie-jose-rotisserie.fr<br>
            <strong>À :</strong> <?= htmlspecialchars($cmd['email']) ?><br>
            <strong>Objet :</strong> [URGENT] Défaut de restitution de matériel — Commande <?= htmlspecialchars($cmd['numero_commande']) ?><br>
            <hr>
            <p>Bonjour <?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?>,</p>
            
            <p>Sauf erreur de notre part, votre prestation s'est déroulée le <strong><?= date('d/m/Y', strtotime($cmd['date_prestation'])) ?></strong>. Conformément à nos conditions générales de vente acceptées lors de votre commande, le matériel de rôtisserie mis à votre disposition devait nous être retourné dans un délai maximal de 10 jours.</p>
            
            <p class="bg-danger bg-opacity-10 p-3 text-danger border border-danger rounded fw-bold">
                ⚠️ À ce jour, ce délai est dépassé. En l'absence de restitution immédiate sous 48 heures, une pénalité forfaitaire automatique de 600,00 € (six cents euros) vous sera facturée pour le renouvellement du matériel non rendu.
            </p>

            <p>Nous vous invitons à prendre contact de toute urgence avec nos équipes pour convenir d'un rendez-vous de restitution.</p>
            
            <p>Cordialement,<br>Le service logistique — Julie & José Rôtisserie Bordeaux</p>
        </div>

        <div class="text-center">
            <a href="employe_dashboard.php" class="btn btn-secondary px-4">Retourner au tableau de bord</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>