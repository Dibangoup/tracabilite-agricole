<?php
$base_url = '../';
$page_title = 'Ajouter un produit';
include '../includes/header.php';
require_once '../includes/functions.php';

verifier_connexion();

// Seuls les producteurs peuvent ajouter un produit initial
if (get_user_role() !== 'producteur') {
    header("Location: dashboard.php?erreur=non_autorise");
    exit();
}
?>

<main class="dashboard container" style="padding-bottom:4rem;">
  <div class="dash-header anim">
    <h1 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('box', '1em', 'var(--caribbean)'); ?> Enregistrer une nouvelle production</h1>
    <p>Créez la fiche d'identité de votre produit pour démarrer sa traçabilité.</p>
  </div>

  <div style="display:flex; gap:2rem; flex-wrap:wrap; align-items:flex-start;">
    
    <!-- Formulaire d'ajout -->
    <div class="card anim" style="flex:1; min-width:320px;">
      <form action="../actions/ajouter_produit.php" method="POST">
        <div class="form-group">
          <label>Nom du produit (Ex: Cacao Lot #A12, Tomates grappe)</label>
          <input type="text" name="nom" class="form-input" required>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
          <div class="form-group">
            <label>Catégorie</label>
            <select name="categorie" class="form-select" required>
              <option value="Cacao">Cacao</option>
              <option value="Café">Café</option>
              <option value="Anacarde">Anacarde (Cajou)</option>
              <option value="Fruits">Fruits</option>
              <option value="Légumes">Légumes</option>
              <option value="Céréales">Céréales</option>
              <option value="Autre">Autre</option>
            </select>
          </div>
          <div class="form-group">
            <label>Variété / Type exact</label>
            <input type="text" name="type" class="form-input" placeholder="Ex: Forastero, Arabica..." required>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
          <div class="form-group">
            <label>Quantité / Volume</label>
            <input type="text" name="quantite" class="form-input" placeholder="Ex: 500 kg, 50 sacs" required>
          </div>
          <div class="form-group">
            <label>Lieu d'origine (Plantation)</label>
            <input type="text" name="origine" class="form-input" placeholder="Ex: Soubré, Daloa..." required>
          </div>
        </div>

        <div class="form-group">
          <label>Date de péremption estimée (Optionnel)</label>
          <input type="date" name="date_peremption" class="form-input" min="<?php echo date('Y-m-d'); ?>">
          <small style="color:var(--text-3); font-size:0.8rem; display:block; margin-top:0.3rem;">Peut être mise à jour plus tard par le transformateur.</small>
        </div>

        <div class="form-group">
          <label>Description / Notes supplémentaires</label>
          <textarea name="description" class="form-textarea" placeholder="Conditions de culture, label bio, etc."></textarea>
        </div>

        <div style="margin-top:2rem; display:flex; gap:1rem;">
          <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;"><?php echo get_icon('check', '1.2em'); ?> Générer le code et enregistrer</button>
          <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
        </div>
      </form>
    </div>

    <!-- Info sidebar -->
    <div class="card anim stagger" style="width:100%; max-width:350px; background:hsla(155,80%,41%,.05); border-color:var(--caribbean);">
      <h3 style="color:var(--caribbean); margin-bottom:1rem; display:flex; align-items:center; gap:.5rem;"><?php echo get_icon('info', '1.2em'); ?> Ce qui va se passer</h3>
      <ul style="display:flex; flex-direction:column; gap:1rem; font-size:0.9rem; color:var(--text-2);">
        <li style="display:flex; gap:.8rem;">
          <span style="font-weight:bold; color:var(--caribbean);">1.</span>
          <p>Le système va générer un <strong>code unique</strong> (QR Code) pour ce lot spécifique.</p>
        </li>
        <li style="display:flex; gap:.8rem;">
          <span style="font-weight:bold; color:var(--caribbean);">2.</span>
          <p>La première étape de traçabilité <strong>"Récolte"</strong> sera automatiquement enregistrée à votre nom.</p>
        </li>
        <li style="display:flex; gap:.8rem;">
          <span style="font-weight:bold; color:var(--caribbean);">3.</span>
          <p>Vous pourrez imprimer le QR Code et l'attacher à la marchandise pour le transporteur.</p>
        </li>
      </ul>
    </div>

  </div>
</main>

<?php include '../includes/footer.php'; ?>
