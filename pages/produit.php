<?php
$base_url = '../';
$page_title = 'Gestion Produit';
include '../includes/header.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();
$user_id = get_user_id();
$role = get_user_role();

// Cas 1: Recherche par code (utilisé par les intermédiaires)
if (isset($_GET['code_search'])) {
    $code = htmlspecialchars($_GET['code_search']);
    $code_esc = mysqli_real_escape_string($conn, $code);
    $res = mysqli_query($conn, "SELECT id FROM produits WHERE code_unique = '$code_esc'");
    if (mysqli_num_rows($res) > 0) {
        $id = mysqli_fetch_assoc($res)['id'];
        header("Location: produit.php?id=" . $id);
        exit();
    } else {
        header("Location: dashboard.php?erreur=introuvable");
        exit();
    }
}

// Cas 2: Accès par ID
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$produit_id = intval($_GET['id']);

// Récupérer les étapes du cycle de vie du produit
$prod_id_esc = intval($produit_id);
$result = mysqli_query($conn, "SELECT p.*, u.nom as producteur_nom FROM produits p JOIN users u ON p.producteur_id = u.id WHERE p.id = $prod_id_esc");

if (mysqli_num_rows($result) === 0) {
    die("Produit introuvable.");
}

$produit = mysqli_fetch_assoc($result);
$est_proprietaire = ($produit['producteur_id'] == $user_id);

// Si détruit
$est_detruit = strpos(strtolower($produit['description'] ?? ''), 'détruit') !== false;
?>

<main class="dashboard container" style="padding-bottom:4rem;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
      <a href="dashboard.php" style="color:var(--text-3); font-size:0.9rem;">← Retour au Dashboard</a>
      <h1 style="margin-top:0.5rem; display:flex; align-items:center; gap:.5rem;">
        <?php echo htmlspecialchars($produit['nom']); ?>
        <?php if ($est_detruit): ?>
          <span class="badge badge-expired">PRODUIT DÉTRUIT</span>
        <?php endif; ?>
      </h1>
    </div>
    
    <?php if ($est_proprietaire && !$est_detruit): ?>
      <div style="display:flex; gap:.5rem;">
        <button class="btn btn-secondary btn-sm" onclick="openModal('modal-edit')"><?php echo get_icon('edit', '1.2em'); ?> Modifier</button>
        <button class="btn btn-danger btn-sm" onclick="openModal('modal-delete')"><?php echo get_icon('delete', '1.2em'); ?> Déclarer détruit / Supprimer</button>
      </div>
    <?php endif; ?>
  </div>

  <div class="product-layout">
    
    <!-- Infos principales -->
    <div style="display:flex; flex-direction:column; gap:2rem;">
      <div class="card anim">
        <h3 style="margin-bottom:1rem; border-bottom:1px solid hsla(160,50%,50%,.1); padding-bottom:.5rem;">Fiche d'identité</h3>
        <div class="info-grid">
          <div>
            <div style="font-size:.8rem; color:var(--text-3);">Code Unique (QR)</div>
            <div style="font-family:monospace; color:var(--caribbean); font-weight:600;"><?php echo $produit['code_unique']; ?></div>
          </div>
          <div>
            <div style="font-size:.8rem; color:var(--text-3);">Catégorie & Type</div>
            <div><?php echo htmlspecialchars($produit['categorie']) . ' - ' . htmlspecialchars($produit['type']); ?></div>
          </div>
          <div>
            <div style="font-size:.8rem; color:var(--text-3);">Quantité Initiale</div>
            <div><?php echo htmlspecialchars($produit['quantite']); ?></div>
          </div>
          <div>
            <div style="font-size:.8rem; color:var(--text-3);">Lieu d'origine</div>
            <div><?php echo htmlspecialchars($produit['origine']); ?></div>
          </div>
          <div>
            <div style="font-size:.8rem; color:var(--text-3);">Producteur</div>
            <div><?php echo htmlspecialchars($produit['producteur_nom']); ?></div>
          </div>
          <div>
            <div style="font-size:.8rem; color:var(--text-3);">Date de péremption</div>
            <div><?php echo empty($produit['date_peremption']) ? 'Non définie' : date('d/m/Y', strtotime($produit['date_peremption'])); ?></div>
          </div>
        </div>
        <?php if (!empty($produit['description'])): ?>
          <div style="margin-top:1.5rem;">
            <div style="font-size:.8rem; color:var(--text-3);">Description / Notes</div>
            <div style="padding:.8rem; background:hsla(160,50%,50%,.05); border-radius:var(--radius-sm); margin-top:.3rem;">
              <?php echo nl2br(htmlspecialchars($produit['description'])); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Ajouter une étape (seulement si non détruit et si l'utilisateur a le bon rôle) -->
      <?php if (!$est_detruit && in_array($role, ['cooperative', 'transporteur', 'transformateur', 'distributeur'])): ?>
        <div class="card anim stagger" style="border-color:var(--caribbean);">
          <h3 style="margin-bottom:1rem; color:var(--caribbean); display:flex; align-items:center; gap:.5rem;"><?php echo get_icon('plus', '1.2em', 'var(--caribbean)'); ?> Enregistrer votre intervention</h3>
          <form action="../actions/ajouter_etape.php" method="POST">
            <input type="hidden" name="produit_id" value="<?php echo $produit_id; ?>">
            <input type="hidden" name="etape" value="<?php 
              if ($role === 'cooperative') echo 'stockage';
              elseif ($role === 'transporteur') echo 'transport';
              elseif ($role === 'transformateur') echo 'transformation';
              elseif ($role === 'distributeur') echo 'distribution';
            ?>">
            
            <div class="form-group">
              <label>Lieu actuel (Ville, Entrepôt, Usine...)</label>
              <input type="text" name="lieu" class="form-input" required>
            </div>
            
            <div class="form-group">
              <label>Description de l'action / Conditions</label>
              <textarea name="description" class="form-textarea" placeholder="Ex: Stocké à 5°C, Transformé en pâte..." required></textarea>
            </div>

            <?php if ($role === 'transformateur'): ?>
              <div class="form-group" style="padding:1rem; background:hsla(40,90%,55%,.1); border-radius:var(--radius-sm); border:1px solid hsla(40,90%,55%,.2);">
                <label style="color:var(--warning); display:flex; align-items:center; gap:.5rem;"><?php echo get_icon('warn', '1.2em', 'var(--warning)'); ?> Nouvelle date de péremption (Suite à la transformation)</label>
                <input type="date" name="date_peremption" class="form-input" min="<?php echo date('Y-m-d'); ?>" required>
              </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:.5rem;">Valider l'étape de <?php echo $role; ?></button>
          </form>
        </div>
      <?php endif; ?>

    </div>

    <!-- Sidebar : QR Code & Actions -->
    <div style="display:flex; flex-direction:column; gap:2rem;">
      <div class="card anim text-center">
        <h3 style="margin-bottom:1rem;">QR Code</h3>
        <?php $qr_url = SITE_URL . '/pages/consulter_produit.php?code=' . urlencode($produit['code_unique']); ?>
        <div class="qr-container mx-auto" style="display:inline-block; margin-bottom:1rem;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($qr_url); ?>&color=001F1B&bgcolor=F1F7F6" alt="QR Code">
        </div>
        <p style="font-size:.8rem; color:var(--text-2);">Imprimez ce code et collez-le sur le lot. Tous les acteurs le scanneront.</p>
        <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=<?php echo urlencode($qr_url); ?>" download="QR_<?php echo $produit['code_unique']; ?>" target="_blank" class="btn btn-secondary btn-sm" style="margin-top:1rem; width:100%; justify-content:center;"><?php echo get_icon('print', '1.2em'); ?> Imprimer le code</a>
      </div>
    </div>

  </div>
</main>

<!-- Modals -->
<?php if ($est_proprietaire): ?>
  <div class="modal-overlay" id="modal-edit">
    <div class="modal">
      <h3>Modifier les infos du produit</h3>
      <form action="../actions/modifier_produit.php" method="POST">
        <input type="hidden" name="produit_id" value="<?php echo $produit_id; ?>">
        <div class="form-group">
          <label>Nom</label>
          <input type="text" name="nom" class="form-input" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
        </div>
        <div class="form-group">
          <label>Type / Variété</label>
          <input type="text" name="type" class="form-input" value="<?php echo htmlspecialchars($produit['type']); ?>" required>
        </div>
        <div class="form-group">
          <label>Notes supplémentaires</label>
          <textarea name="description" class="form-textarea"><?php echo htmlspecialchars($produit['description']); ?></textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit')">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal-overlay" id="modal-delete">
    <div class="modal" style="border-color:var(--danger);">
      <h3 style="color:var(--danger); display:flex; align-items:center; gap:.5rem;"><?php echo get_icon('warn', '1.2em', 'var(--danger)'); ?> Produit détruit ou perdu ?</h3>
      <p style="color:var(--text-2); font-size:0.9rem; margin-bottom:1.5rem;">
        Si ce lot a été détruit dans la réalité (accident, avarié, etc.), vous pouvez le déclarer comme tel. 
        Pour garder l'historique, il ne sera pas effacé de la base, mais marqué comme DÉTRUIT. 
        Si vous voulez vraiment l'effacer définitivement, utilisez le bouton de suppression complète.
      </p>
      
      <div style="display:flex; flex-direction:column; gap:1rem;">
        <form action="../actions/modifier_produit.php" method="POST" style="margin:0;">
          <input type="hidden" name="produit_id" value="<?php echo $produit_id; ?>">
          <input type="hidden" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>">
          <input type="hidden" name="type" value="<?php echo htmlspecialchars($produit['type']); ?>">
          <input type="hidden" name="description" value="<?php echo htmlspecialchars($produit['description'] . "\n\n[DÉTRUIT]"); ?>">
          <button type="submit" class="btn btn-secondary" style="width:100%; justify-content:center;"><?php echo get_icon('box', '1.2em'); ?> Marquer comme DÉTRUIT (Garder historique)</button>
        </form>

        <form action="../actions/supprimer_produit.php" method="POST" style="margin:0;">
          <input type="hidden" name="produit_id" value="<?php echo $produit_id; ?>">
          <button type="submit" class="btn btn-danger" style="width:100%; justify-content:center;" onclick="return confirm('Êtes-vous SÛR de vouloir EFFACER DÉFINITIVEMENT ce produit et tout son historique ?');"><?php echo get_icon('delete', '1.2em'); ?> Effacer définitivement de la base</button>
        </form>
      </div>

      <div class="modal-actions" style="margin-top:1rem;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modal-delete')" style="background:transparent; border:none;">Annuler</button>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
