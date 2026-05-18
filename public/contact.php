<?php 
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row g-5 align-items-center">
        <div class="col-lg-5">
            <span class="badge bg-primary px-3 py-2 mb-3">📍 Bordeaux & Alentours</span>
            <h1 class="display-5 fw-bold mb-3">Contactez Julie & José</h1>
            <p class="text-secondary lead mb-4">Une demande pour un événement particulier, un mariage, un repas associatif ou une question sur nos rôtis locaux ? Notre équipe vous répond sous 24h.</p>
            
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-light p-3 rounded-circle text-primary fw-bold">📞</div>
                <div>
                    <span class="text-muted small d-block">Téléphone direct</span>
                    <strong>05 56 00 00 00</strong>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="bg-light p-3 rounded-circle text-primary fw-bold">✉️</div>
                <div>
                    <span class="text-muted small d-block">Courriel général</span>
                    <strong>contact@julie-jose-rotisserie.fr</strong>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 p-4 p-md-5 bg-light">
                <h3 class="fw-bold mb-4">Formulaire de demande</h3>

                <?php if (isset($_POST['send_contact'])): ?>
                    <div class="alert alert-success border-0 shadow-sm p-4 text-start mb-0">
                        <h5 class="fw-bold text-success mb-2">✉️ Message transmis avec succès !</h5>
                        <p class="small text-dark mb-0">
                            Une copie de votre demande (Objet : <strong><?= htmlspecialchars($_POST['titre']) ?></strong>) vient d'être envoyée sur les serveurs de Julie & José. Notre service commercial prendra attache avec vous dans les plus brefs délais.
                        </p>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Votre Adresse Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="nom@exemple.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Objet / Titre de votre demande</label>
                            <input type="text" name="titre" class="form-control form-control-lg" placeholder="Ex: Devis pour un baptême..." required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Description détaillée de votre projet</label>
                            <textarea name="description" rows="5" class="form-control" placeholder="Indiquez ici le nombre de convives, la date souhaitée, les options de rôtisserie..." required></textarea>
                        </div>
                        <button type="submit" name="send_contact" class="btn btn-primary btn-lg text-white fw-bold w-100 shadow-sm">
                            Envoyer ma demande
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>