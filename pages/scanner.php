<?php
$base_url = '../';
$page_title = 'Scanner QR';
include '../includes/header.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();
$user_id = get_user_id();
$role = get_user_role();

// Bloquer producteurs et consommateurs
if ($role === 'producteur' || $role === 'consommateur') {
    header("Location: dashboard.php");
    exit();
}

// Étapes autorisées selon le rôle
function etapes_par_role(string $role): array {
    switch ($role) {
        case 'cooperative':    return ['stockage'];
        case 'transporteur':   return ['transport'];
        case 'transformateur': return ['transformation'];
        case 'distributeur':   return ['distribution', 'vente'];
        default:               return [];
    }
}

// Label lisible pour chaque étape
function label_etape(string $etape): string {
    $labels = [
        'recolte' => 'Récolte', 'stockage' => 'Stockage', 'transport' => 'Transport',
        'transformation' => 'Transformation', 'distribution' => 'Distribution', 'vente' => 'Vente'
    ];
    return $labels[$etape] ?? ucfirst($etape);
}

// Icône pour chaque étape
function icone_etape(string $etape): string {
    $icones = [
        'recolte' => '🌱', 'stockage' => '🏠', 'transport' => '🚚',
        'transformation' => '🏭', 'distribution' => '📦', 'vente' => '🛒'
    ];
    return $icones[$etape] ?? '📋';
}

// Label du rôle
function label_role(string $role): string {
    $labels = [
        'cooperative' => '🤝 Coopérative', 'transporteur' => '🚚 Transporteur',
        'transformateur' => '🏭 Transformateur', 'distributeur' => '📦 Distributeur'
    ];
    return $labels[$role] ?? ucfirst($role);
}

$erreur = '';
$succes = '';
$produit = null;
$etapes_existantes = [];

// Récupérer le produit via ?code=
$code = trim($_GET['code'] ?? '');
if ($code) {
    $code_esc = mysqli_real_escape_string($conn, $code);
    $result = mysqli_query($conn, "SELECT p.*, u.nom as producteur_nom FROM produits p JOIN users u ON p.producteur_id = u.id WHERE p.code_unique = '$code_esc'");
    
    if (mysqli_num_rows($result) > 0) {
        $produit = mysqli_fetch_assoc($result);
        $produit_id = intval($produit['id']);
        
        // Récupérer les étapes existantes
        $result_etapes = mysqli_query($conn, "SELECT e.*, u.nom as acteur_nom, u.role as acteur_role FROM etapes_tracabilite e JOIN users u ON e.acteur_id = u.id WHERE e.produit_id = $produit_id ORDER BY e.date_etape ASC");
        while ($row = mysqli_fetch_assoc($result_etapes)) {
            $etapes_existantes[] = $row;
        }
    } else {
        $erreur = "Aucun produit trouvé pour le code <strong>" . htmlspecialchars($code) . "</strong>.";
    }
}

// Traitement du formulaire d'ajout d'étape
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $produit) {
    $etape = trim($_POST['etape'] ?? '');
    $date_etape = trim($_POST['date_etape'] ?? '');
    $lieu = trim($_POST['lieu'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    $date_peremption = null;
    if ($role === 'transformateur' && !empty($_POST['date_peremption'])) {
        $date_peremption = $_POST['date_peremption'];
    }
    
    if (empty($etape) || empty($date_etape) || empty($lieu)) {
        $erreur = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!in_array($etape, etapes_par_role($role))) {
        $erreur = "Votre rôle ne peut pas enregistrer une étape de type <strong>" . label_etape($etape) . "</strong>.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO etapes_tracabilite (produit_id, acteur_id, etape, description, lieu, date_etape, date_peremption) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iisssss", $produit_id, $user_id, $etape, $description, $lieu, $date_etape, $date_peremption);
        
        if (mysqli_stmt_execute($stmt)) {
            // Mettre à jour la date de péremption du produit si besoin
            if ($date_peremption) {
                $update = mysqli_prepare($conn, "UPDATE produits SET date_peremption = ? WHERE id = ?");
                mysqli_stmt_bind_param($update, "si", $date_peremption, $produit_id);
                mysqli_stmt_execute($update);
            }
            
            $succes = "Étape <strong>" . label_etape($etape) . "</strong> enregistrée avec succès pour le produit <strong>" . htmlspecialchars($produit['nom']) . "</strong> !";
            
            // Recharger les étapes
            $etapes_existantes = [];
            $result_etapes = mysqli_query($conn, "SELECT e.*, u.nom as acteur_nom, u.role as acteur_role FROM etapes_tracabilite e JOIN users u ON e.acteur_id = u.id WHERE e.produit_id = $produit_id ORDER BY e.date_etape ASC");
            while ($row = mysqli_fetch_assoc($result_etapes)) {
                $etapes_existantes[] = $row;
            }
        } else {
            $erreur = "Erreur lors de l'enregistrement : " . mysqli_stmt_error($stmt);
        }
    }
}
?>

<main class="dashboard container" style="padding-bottom:4rem;">
  <div class="dash-header anim">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
      <div>
        <a href="dashboard.php" style="color:var(--text-3); font-size:0.9rem;">← Retour au Dashboard</a>
        <h1 style="margin-top:0.5rem; display:flex; align-items:center; gap:.5rem;"><?php echo get_icon('camera', '1em', 'var(--caribbean)'); ?> Scanner un produit</h1>
        <p>Scannez le QR code sur l'emballage ou saisissez le code pour enregistrer votre action.</p>
      </div>
      <div>
        <span style="background:hsla(155,80%,41%,.1); color:var(--caribbean); border-radius:20px; padding:4px 14px; font-size:.85rem; font-weight:600;">
          <?php echo label_role($role); ?>
        </span>
      </div>
    </div>
  </div>

  <?php if ($erreur): ?>
    <div class="alert alert-error anim"><?php echo $erreur; ?></div>
  <?php endif; ?>
  <?php if ($succes): ?>
    <div class="alert alert-success anim"><?php echo $succes; ?></div>
  <?php endif; ?>

  <!-- ═══════════════════════════════════════
       CAS 1 : Aucun produit trouvé encore → Scanner caméra + saisie manuelle
  ═══════════════════════════════════════ -->
  <?php if (!$produit): ?>

  <div class="card anim" style="margin-bottom:2rem;" id="scanner-card">
    <h3 style="margin-bottom:1rem; display:flex; align-items:center; gap:.5rem;">
      <?php echo get_icon('camera', '1.2em', 'var(--caribbean)'); ?> Scanner via la caméra
    </h3>
    <div id="qr-reader" style="width:100%; max-width:500px; margin:0 auto; border-radius:var(--radius-md); overflow:hidden;"></div>
    <p id="scan-status" style="text-align:center; font-size:.85rem; color:var(--text-3); margin-top:.8rem;">
      Pointez la caméra arrière vers le QR code du produit...
    </p>
    <div style="text-align:center; margin-top:.8rem;">
      <button type="button" id="btn-stop-camera" class="btn btn-secondary btn-sm" onclick="arreterCamera()" style="display:none;">
        ✕ Fermer la caméra
      </button>
    </div>
  </div>

  <div class="card anim stagger">
    <h3 style="margin-bottom:1rem; display:flex; align-items:center; gap:.5rem;">
      <?php echo get_icon('search', '1.2em', 'var(--caribbean)'); ?> Ou saisir le code manuellement
    </h3>
    <form method="GET" class="search-box" style="margin:0; max-width:100%;">
      <input type="text" name="code" class="form-input" placeholder="Ex: PROD_6839a1b2c3..." required value="<?php echo htmlspecialchars($code); ?>">
      <button type="submit" class="btn btn-primary">Chercher</button>
    </form>
  </div>

  <!-- ═══════════════════════════════════════
       CAS 2 : Produit trouvé → fiche + formulaire d'action + timeline
  ═══════════════════════════════════════ -->
  <?php else: ?>

  <!-- Fiche du produit -->
  <div class="card anim" style="border-left:4px solid var(--caribbean); margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1rem;">
      <div>
        <h2 style="display:flex; align-items:center; gap:.5rem; margin-bottom:.3rem;">
          <?php echo get_icon('check', '1em', 'var(--caribbean)'); ?> <?php echo htmlspecialchars($produit['nom']); ?>
        </h2>
        <span style="font-family:monospace; color:var(--caribbean); font-weight:600; font-size:.9rem;"><?php echo htmlspecialchars($produit['code_unique']); ?></span>
      </div>
      <?php if (!empty($produit['date_peremption'])): 
        $peremption = new DateTime($produit['date_peremption']);
        $aujourdhui = new DateTime();
        $diff = $aujourdhui->diff($peremption);
        $jours = (int)$diff->format('%R%a');
        if ($jours < 0) { $badge_class = 'badge-expired'; $badge_text = 'Expiré'; }
        elseif ($jours <= 7) { $badge_class = 'badge-warn'; $badge_text = 'Expire bientôt'; }
        else { $badge_class = 'badge-ok'; $badge_text = 'Valide'; }
      ?>
        <span class="badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
      <?php endif; ?>
    </div>
    
    <div class="info-grid" style="margin-bottom:1rem;">
      <div>
        <div style="font-size:.8rem; color:var(--text-3);">Catégorie</div>
        <div><?php echo htmlspecialchars($produit['categorie'] ?? '—') . ' — ' . htmlspecialchars($produit['type']); ?></div>
      </div>
      <div>
        <div style="font-size:.8rem; color:var(--text-3);">Quantité</div>
        <div><?php echo htmlspecialchars($produit['quantite'] ?? '—'); ?></div>
      </div>
      <div>
        <div style="font-size:.8rem; color:var(--text-3);">Producteur</div>
        <div><?php echo htmlspecialchars($produit['producteur_nom']); ?></div>
      </div>
      <div>
        <div style="font-size:.8rem; color:var(--text-3);">Origine</div>
        <div><?php echo htmlspecialchars($produit['origine']); ?></div>
      </div>
      <div>
        <div style="font-size:.8rem; color:var(--text-3);">Étapes enregistrées</div>
        <div><strong><?php echo count($etapes_existantes); ?></strong></div>
      </div>
      <?php if (!empty($produit['date_peremption'])): ?>
      <div>
        <div style="font-size:.8rem; color:var(--text-3);">Péremption</div>
        <div><?php echo date('d/m/Y', strtotime($produit['date_peremption'])); ?></div>
      </div>
      <?php endif; ?>
    </div>

    <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
      <a href="scanner.php" class="btn btn-secondary btn-sm"><?php echo get_icon('camera', '1em'); ?> Scanner un autre</a>
      <a href="consulter_produit.php?code=<?php echo urlencode($produit['code_unique']); ?>" class="btn btn-secondary btn-sm" target="_blank"><?php echo get_icon('search', '1em'); ?> Page publique</a>
    </div>
  </div>

  <!-- Formulaire d'ajout d'étape -->
  <div class="card anim stagger" style="border-color:var(--caribbean); margin-bottom:2rem;">
    <h3 style="margin-bottom:.5rem; color:var(--caribbean); display:flex; align-items:center; gap:.5rem;">
      <?php echo get_icon('plus', '1.2em', 'var(--caribbean)'); ?> Enregistrer votre action
    </h3>
    <p style="font-size:.85rem; color:var(--text-2); margin-bottom:1.5rem;">
      En tant que <strong><?php echo label_role($role); ?></strong>, vous pouvez enregistrer :
      <?php foreach (etapes_par_role($role) as $e): ?>
        <span style="background:hsla(155,80%,41%,.1); color:var(--caribbean); border-radius:12px; padding:2px 10px; font-size:.8rem; margin-left:3px; display:inline-flex; align-items:center; gap:3px;">
          <?php echo icone_etape($e) . ' ' . label_etape($e); ?>
        </span>
      <?php endforeach; ?>
    </p>

    <form method="POST" action="?code=<?php echo urlencode($produit['code_unique']); ?>">
      
      <!-- Sélection du type d'étape -->
      <div class="form-group">
        <label>Type d'action <span style="color:var(--danger);">*</span></label>
        <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:.3rem;" id="etape-buttons">
          <?php foreach (etapes_par_role($role) as $e): ?>
            <button type="button" class="btn btn-secondary btn-sm etape-select-btn"
                    data-etape="<?php echo $e; ?>"
                    onclick="selectEtape('<?php echo $e; ?>', this)"
                    style="transition: all .2s;">
              <?php echo icone_etape($e) . ' ' . label_etape($e); ?>
            </button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="etape" id="etape-input" value="" required>
        <small id="etape-hint" style="color:var(--text-3); font-size:.8rem;">← Sélectionnez un type d'action</small>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
          <label>Date et heure <span style="color:var(--danger);">*</span></label>
          <input type="datetime-local" name="date_etape" class="form-input" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
        </div>
        <div class="form-group">
          <label>Lieu <span style="color:var(--danger);">*</span></label>
          <input type="text" name="lieu" class="form-input" placeholder="Entrepôt, port, usine…" required>
        </div>
      </div>

      <div class="form-group">
        <label>Description / Observations</label>
        <textarea name="description" class="form-textarea" placeholder="Conditions, quantité reçue, remarques…"></textarea>
      </div>

      <?php if ($role === 'transformateur'): ?>
        <div class="form-group" style="padding:1rem; background:hsla(40,90%,55%,.1); border-radius:var(--radius-sm); border:1px solid hsla(40,90%,55%,.2);">
          <label style="color:var(--warning); display:flex; align-items:center; gap:.5rem;"><?php echo get_icon('warn', '1.2em', 'var(--warning)'); ?> Nouvelle date de péremption (Suite à la transformation)</label>
          <input type="date" name="date_peremption" class="form-input" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
      <?php endif; ?>

      <button type="submit" id="submit-btn" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:.5rem;" disabled>
        <?php echo get_icon('check', '1.2em'); ?> Enregistrer l'étape
      </button>
    </form>
  </div>

  <!-- Timeline des étapes existantes -->
  <?php if (!empty($etapes_existantes)): ?>
  <div class="card anim stagger">
    <h3 style="margin-bottom:1.5rem; border-bottom:1px solid hsla(160,50%,50%,.1); padding-bottom:.5rem;">
      Historique du lot (<?php echo count($etapes_existantes); ?> étape<?php echo count($etapes_existantes) > 1 ? 's' : ''; ?>)
    </h3>
    <div class="timeline">
      <?php foreach ($etapes_existantes as $index => $etape): ?>
        <div class="timeline-item">
          <div class="timeline-dot <?php echo $index === count($etapes_existantes) - 1 ? 'active' : ''; ?>">
            <?php
              $icones_svg = [
                'recolte' => get_icon('plante', '1.2em'), 'transport' => get_icon('truck', '1.2em'),
                'stockage' => get_icon('box', '1.2em'), 'transformation' => get_icon('factory', '1.2em'),
                'distribution' => get_icon('cart', '1.2em'), 'vente' => get_icon('bag', '1.2em')
              ];
              echo $icones_svg[$etape['etape']] ?? get_icon('pin', '1.2em');
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
  </div>
  <?php endif; ?>

  <?php endif; // fin $produit ?>

</main>

<!-- Librairie html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// Sélection visuelle du type d'étape
function selectEtape(val, btn) {
  document.getElementById('etape-input').value = val;
  document.querySelectorAll('.etape-select-btn').forEach(b => {
    b.style.background = '';
    b.style.color = '';
  });
  btn.style.background = 'var(--caribbean)';
  btn.style.color = 'white';
  document.getElementById('submit-btn').disabled = false;
  document.getElementById('etape-hint').style.display = 'none';
}

<?php if (!$produit): ?>
// Démarrage du scanner QR (caméra arrière)
var scanner = null;

document.addEventListener('DOMContentLoaded', function() {
  var readerDiv = document.getElementById('qr-reader');
  if (!readerDiv || typeof Html5Qrcode === 'undefined') return;

  scanner = new Html5Qrcode("qr-reader");

  Html5Qrcode.getCameras().then(function(cameras) {
    if (!cameras || cameras.length === 0) {
      readerDiv.innerHTML = '<p style="text-align:center; color:var(--text-3); padding:2rem;">Aucune caméra détectée.</p>';
      return;
    }

    scanner.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      function(decodedText) {
        var code = decodedText;
        try {
          var url = new URL(decodedText);
          var p = url.searchParams.get('code');
          if (p) code = p;
        } catch(e) {}

        document.getElementById('scan-status').innerHTML =
          '<span style="color:var(--caribbean); font-weight:600;">✅ QR code détecté ! Chargement...</span>';
        document.getElementById('btn-stop-camera').style.display = 'none';

        scanner.stop().then(function() {
          window.location.href = 'scanner.php?code=' + encodeURIComponent(code);
        });
      },
      function() {}
    ).then(function() {
      // Caméra démarrée → afficher le bouton fermer
      document.getElementById('btn-stop-camera').style.display = 'inline-flex';
    }).catch(function() {
      readerDiv.innerHTML = '<div class="alert alert-error" style="margin:0;">Impossible d\'accéder à la caméra. Utilisez la saisie manuelle ci-dessous.</div>';
    });
  }).catch(function() {
    readerDiv.innerHTML = '<div class="alert alert-error" style="margin:0;">Impossible d\'accéder à la caméra. Utilisez la saisie manuelle ci-dessous.</div>';
  });
});

// Arrêter la caméra proprement
function arreterCamera() {
  if (scanner && scanner.isScanning) {
    scanner.stop().then(function() {
      document.getElementById('qr-reader').innerHTML = '<p style="text-align:center; color:var(--text-3); padding:2rem;">📷 Caméra désactivée.</p>';
      document.getElementById('scan-status').innerHTML = 'Utilisez la saisie manuelle ci-dessous pour chercher un produit.';
      document.getElementById('btn-stop-camera').style.display = 'none';
    }).catch(function() {
      document.getElementById('btn-stop-camera').style.display = 'none';
    });
  } else {
    document.getElementById('btn-stop-camera').style.display = 'none';
  }
}
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
