<?php
$base_url = '../';
$page_title = 'Suivre un produit';
include '../includes/header.php';
require_once '../config/db.php';

$produit = null;
$etapes = [];
$avis = [];
$erreur = '';

if (isset($_GET['code']) && !empty($_GET['code'])) {
    $code = htmlspecialchars($_GET['code']);
    
    // Récupérer le produit
    $stmt = mysqli_prepare($conn, "SELECT p.*, u.nom as producteur_nom FROM produits p JOIN users u ON p.producteur_id = u.id WHERE p.code_unique = ?");
    mysqli_stmt_bind_param($stmt, "s", $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $produit = mysqli_fetch_assoc($result);
        $produit_id = $produit['id'];
        
        // Enregistrer la recherche dans l'historique si l'utilisateur est connecté
        if (isset($_SESSION['user'])) {
            $user_id = $_SESSION['user']['id'];
            // Vérifier si cette recherche n'a pas déjà été faite récemment (évite les doublons)
            $check_hist = mysqli_prepare($conn, "SELECT id FROM historique_recherche WHERE user_id = ? AND produit_id = ? AND date_recherche > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
            mysqli_stmt_bind_param($check_hist, "ii", $user_id, $produit_id);
            mysqli_stmt_execute($check_hist);
            if (mysqli_num_rows(mysqli_stmt_get_result($check_hist)) == 0) {
                $ins_hist = mysqli_prepare($conn, "INSERT INTO historique_recherche (user_id, produit_id, code_recherche) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($ins_hist, "iis", $user_id, $produit_id, $code);
                mysqli_stmt_execute($ins_hist);
                mysqli_stmt_close($ins_hist);
            }
            mysqli_stmt_close($check_hist);
        }
        
        // Récupérer les étapes de traçabilité
        $stmt_etapes = mysqli_prepare($conn, "SELECT e.*, u.nom as acteur_nom, u.role as acteur_role FROM etapes_tracabilite e JOIN users u ON e.acteur_id = u.id WHERE e.produit_id = ? ORDER BY e.date_etape ASC");
        mysqli_stmt_bind_param($stmt_etapes, "i", $produit_id);
        mysqli_stmt_execute($stmt_etapes);
        $result_etapes = mysqli_stmt_get_result($stmt_etapes);
        while ($row = mysqli_fetch_assoc($result_etapes)) {
            $etapes[] = $row;
        }
        mysqli_stmt_close($stmt_etapes);
        
        // Récupérer les avis consommateurs
        $stmt_avis = mysqli_prepare($conn, "SELECT * FROM avis WHERE produit_id = ? ORDER BY date_avis DESC");
        mysqli_stmt_bind_param($stmt_avis, "i", $produit_id);
        mysqli_stmt_execute($stmt_avis);
        $result_avis = mysqli_stmt_get_result($stmt_avis);
        while ($row = mysqli_fetch_assoc($result_avis)) {
            $avis[] = $row;
        }
        mysqli_stmt_close($stmt_avis);
    } else {
        $erreur = "Aucun produit trouvé avec ce code.";
    }
    mysqli_stmt_close($stmt);
}
?>

<main class="dashboard container">
  <div class="text-center anim" style="margin-bottom: 2rem;">
    <h1 class="section-title" style="display:flex;align-items:center;justify-content:center;gap:.5rem;"><?php echo get_icon('search', '1em', 'var(--text-1)'); ?> Suivre un produit</h1>
    <p class="section-desc mx-auto">Entrez le code unique du produit ou scannez son QR code pour voir tout son parcours.</p>
  </div>

  <div class="search-box anim">
    <form action="" method="GET" style="display:flex; width:100%; gap:.8rem;">
      <input type="text" name="code" id="code-input" class="form-input" placeholder="Ex: PROD_6839a1b2c3" value="<?php echo isset($_GET['code']) ? htmlspecialchars($_GET['code']) : ''; ?>" required>
      <button type="submit" class="btn btn-primary">Rechercher</button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('qr-scanner-container').style.display='block'; startQRScanner();"><?php echo get_icon('camera', '1.2em'); ?> Scanner</button>
    </form>
  </div>

  <div id="qr-scanner-container" style="display:none;" class="anim text-center">
    <div id="qr-reader"></div>
    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('qr-scanner-container').style.display='none';">Fermer le scanner</button>
  </div>

  <?php if ($erreur): ?>
    <div class="alert alert-error anim text-center mx-auto" style="max-width:600px;">
      <?php echo $erreur; ?>
    </div>
  <?php endif; ?>

  <?php if ($produit): ?>
    <div class="card anim" style="max-width:800px; margin:0 auto;">
      <div class="product-header">
        <div class="product-info">
          <h1><?php echo htmlspecialchars($produit['nom']); ?></h1>
          <div class="product-meta">
            <span style="display:flex;align-items:center;gap:.3rem;"><span style="color:var(--caribbean)"><?php echo get_icon('pin', '1em'); ?></span> <?php echo htmlspecialchars($produit['origine']); ?></span>
            <span style="display:flex;align-items:center;gap:.3rem;"><span style="color:var(--caribbean)"><?php echo get_icon('tag', '1em'); ?></span> <?php echo htmlspecialchars($produit['categorie']); ?></span>
            <span style="display:flex;align-items:center;gap:.3rem;"><span style="color:var(--caribbean)"><?php echo get_icon('farmer', '1.2em'); ?></span> <?php echo htmlspecialchars($produit['producteur_nom']); ?></span>
            <?php 
              // Affichage du statut de péremption si défini
              if (!empty($produit['date_peremption'])) {
                $peremption = new DateTime($produit['date_peremption']);
                $aujourdhui = new DateTime();
                $diff = $aujourdhui->diff($peremption);
                $jours_restants = (int)$diff->format('%R%a');
                
                $badge_class = 'badge-ok';
                $badge_text = 'Valide';
                
                if ($jours_restants < 0) {
                    $badge_class = 'badge-expired';
                    $badge_text = 'Expiré';
                } elseif ($jours_restants <= 7) {
                    $badge_class = 'badge-warn';
                    $badge_text = 'Expire bientôt';
                }
                echo '<span style="display:flex;align-items:center;gap:.3rem;"><span style="color:var(--caribbean)">'.get_icon('clock', '1em').'</span> Exp. ' . date('d/m/Y', strtotime($produit['date_peremption'])) . ' <span class="badge '.$badge_class.'">'.$badge_text.'</span></span>';
              }
            ?>
          </div>
        </div>
        <div class="qr-container">
          <?php $qr_url = SITE_URL . '/pages/consulter_produit.php?code=' . urlencode($produit['code_unique']); ?>
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode($qr_url); ?>&color=001F1B&bgcolor=F1F7F6" alt="QR Code" width="100" height="100">
          <div style="font-size:0.7rem; color:var(--rich-black); margin-top:0.5rem; font-family:monospace;"><?php echo htmlspecialchars($produit['code_unique']); ?></div>
        </div>
      </div>

      <div style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; border-bottom: 1px solid hsla(160,50%,50%,.1); padding-bottom: 0.5rem;">Parcours du produit</h3>
        <?php if (empty($etapes)): ?>
          <p style="color:var(--text-3); font-style:italic;">Aucune étape enregistrée pour le moment.</p>
        <?php else: ?>
          <div class="timeline">
            <?php foreach ($etapes as $index => $etape): ?>
              <div class="timeline-item">
                <div class="timeline-dot <?php echo $index === count($etapes) - 1 ? 'active' : ''; ?>">
                  <?php 
                    // Icônes pour chaque étape du cycle de vie
                    $icones = [
                      'recolte' => get_icon('plante', '1.2em'), 'transport' => get_icon('truck', '1.2em'), 'stockage' => get_icon('box', '1.2em'),
                      'transformation' => get_icon('factory', '1.2em'), 'distribution' => get_icon('cart', '1.2em'), 'vente' => get_icon('bag', '1.2em')
                    ];
                    echo $icones[$etape['etape']] ?? get_icon('pin', '1.2em');
                  ?>
                </div>
                <div class="timeline-content">
                  <h4><?php echo ucfirst($etape['etape']); ?></h4>
                  <div class="meta">
                    Par <strong><?php echo htmlspecialchars($etape['acteur_nom']); ?></strong> (<?php echo ucfirst($etape['acteur_role']); ?>)<br>
                    Le <?php echo date('d/m/Y à H:i', strtotime($etape['date_etape'])); ?> — <?php echo get_icon('pin', '1em', 'var(--text-1)'); ?> <?php echo htmlspecialchars($etape['lieu']); ?>
                  </div>
                  <?php if (!empty($etape['description'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($etape['description'])); ?></p>
                  <?php endif; ?>
                  <?php if (!empty($etape['date_peremption'])): ?>
                    <p style="margin-top:0.5rem; font-size:0.8rem; color:var(--caribbean);">
                      <span style="display:flex;align-items:center;gap:.3rem;"><?php echo get_icon('warn', '1em', 'var(--caribbean)'); ?> Nouvelle date de péremption : <?php echo date('d/m/Y', strtotime($etape['date_peremption'])); ?></span>
                    </p>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div>
        <h3 style="margin-bottom: 1rem; border-bottom: 1px solid hsla(160,50%,50%,.1); padding-bottom: 0.5rem;">Avis consommateurs</h3>
        <?php if (empty($avis)): ?>
          <p style="color:var(--text-3); font-style:italic; margin-bottom:1.5rem;">Aucun avis pour le moment. Soyez le premier !</p>
        <?php else: ?>
          <div style="display:flex; flex-direction:column; gap:1rem; margin-bottom:1.5rem;">
            <?php foreach ($avis as $av): ?>
              <div style="background:hsla(160,50%,50%,.05); padding:1rem; border-radius:var(--radius-sm); border-left:3px solid var(--caribbean);">
                <div style="display:flex; justify-content:space-between; margin-bottom:0.3rem;">
                  <strong><?php echo htmlspecialchars($av['nom_consommateur']); ?></strong>
                  <span style="color:var(--warning);"><?php echo str_repeat('★', $av['note']) . str_repeat('☆', 5 - $av['note']); ?></span>
                </div>
                <p style="font-size:0.9rem; color:var(--text-2);"><?php echo nl2br(htmlspecialchars($av['commentaire'])); ?></p>
                <div style="font-size:0.75rem; color:var(--text-3); margin-top:0.5rem;"><?php echo date('d/m/Y', strtotime($av['date_avis'])); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Formulaire d'avis -->
        <div style="background:var(--surface); padding:1.5rem; border-radius:var(--radius-md); border:1px solid hsla(160,50%,50%,.1);">
          <h4 style="margin-bottom:1rem; font-size:1rem;">Laissez votre avis</h4>
          <form id="review-form" action="../actions/ajouter_avis.php" method="POST">
            <input type="hidden" name="produit_id" value="<?php echo $produit['id']; ?>">
            <input type="hidden" name="code" value="<?php echo $produit['code_unique']; ?>">
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
              <div class="form-group" style="margin-bottom:0;">
                <label>Votre nom</label>
                <input type="text" name="nom" class="form-input" required>
              </div>
              <div class="form-group" style="margin-bottom:0;">
                <label>Note</label>
                <select name="note" class="form-select" required>
                  <option value="5">5/5 Très satisfait</option>
                  <option value="4">4/5 Satisfait</option>
                  <option value="3">3/5 Moyen</option>
                  <option value="2">2/5 Déçu</option>
                  <option value="1">1/5 Très déçu</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Commentaire</label>
              <textarea name="commentaire" class="form-textarea" style="min-height:80px;" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Publier l'avis</button>
          </form>
        </div>
      </div>

    </div>
  <?php endif; ?>
</main>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<?php include '../includes/footer.php'; ?>
