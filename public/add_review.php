<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

// PROTECTION : Seuls les clients connectés (role_id = 3) peuvent laisser un avis
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: index.php');
    exit();
}

$message = "";
$message_class = "";

// Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = (int)$_POST['note'];
    $description = trim($_POST['description']);
    $utilisateur_id = $_SESSION['user_id'];

    if ($note >= 1 && $note <= 5 && !empty($description)) {
        // Insertion en BDD (statut par défaut 'en_attente')
        $stmt = $pdo->prepare("INSERT INTO avis (utilisateur_id, description, note, statut) VALUES (?, ?, ?, 'en_attente')");
        
        if ($stmt->execute([$utilisateur_id, $description, $note])) {
            $message = "Votre avis a bien été enregistré ! Il sera visible après validation par notre équipe.";
            $message_class = "alert-success";
        } else {
            $message = "Une erreur est survenue lors de l'envoi.";
            $message_class = "alert-danger";
        }
    } else {
        $message = "Veuillez remplir tous les champs correctement (note entre 1 et 5).";
        $message_class = "alert-warning";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4">
                <h2 class="fw-bold text-center mb-4" style="color: var(--main-orange);">Donnez votre avis</h2>
                <p class="text-muted text-center">Votre retour est précieux pour Julie & José !</p>

                <?php if (!empty($message)): ?>
                    <div class="alert <?= $message_class ?> text-center" role="alert">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form action="add_review.php" method="POST">
                    <div class="mb-3">
                        <label for="note" class="form-label fw-bold">Note</label>
                        <select name="note" id="note" class="form-select" required>
                            <option value="5">★★★★★ (5/5) - Excellent !</option>
                            <option value="4">★★★★☆ (4/5) - Très bon</option>
                            <option value="3">★★★☆☆ (3/5) - Correct</option>
                            <option value="2">★★☆☆☆ (2/5) - Moyen</option>
                            <option value="1">★☆☆☆☆ (1/5) - Décevant</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Votre commentaire</label>
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Racontez-nous votre expérience avec Vite & Gourmand..." required></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary text-white font-weight-bold">Envoyer mon avis</button>
                        <a href="index.php" class="btn btn-outline-secondary">Retour à l'accueil</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>