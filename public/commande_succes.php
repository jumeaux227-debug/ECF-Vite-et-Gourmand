<?php 
require_once __DIR__ . '/../includes/header.php';

$total = isset($_GET['total']) ? htmlspecialchars($_GET['total']) : '0.00';
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-5 bg-light">
                <div class="text-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                </div>
                <h1 class="fw-bold mb-3">Commande validée !</h1>
                <p class="text-muted mb-4">Julie & José vous remercient pour votre confiance. Votre commande est enregistrée dans notre système de préparation.</p>
                
                <div class="border rounded p-3 mb-4 bg-white">
                    <span class="text-muted d-block small">Montant à régler à la livraison :</span>
                    <strong class="fs-4 text-primary"><?= number_format((float)$total, 2, ',', ' ') ?> €</strong>
                </div>

                <a href="menus.php" class="btn btn-primary text-white px-4 fw-bold">Retourner à la carte</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>