<?php
$base_url = '../';
$page_title = 'Tableau de bord';
include '../includes/header.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();
$user_id = get_user_id();
$role = get_user_role();
$user_name = $_SESSION['user']['nom'];

// Variables selon le rôle
$stats = ['total' => 0, 'recents' => 0];
$produits = [];
$historique = [];

if ($role === 'producteur') {
    // Stats producteur
    $stmt = mysqli_query($conn, "SELECT COUNT(*) as c FROM produits WHERE producteur_id = $user_id");
    $stats['total'] = mysqli_fetch_assoc($stmt)['c'];
    
    // Ses produits
    $stmt = mysqli_query($conn, "SELECT * FROM produits WHERE producteur_id = $user_id ORDER BY created_at DESC LIMIT 10");
    while($row = mysqli_fetch_assoc($stmt)) $produits[] = $row;

} elseif ($role === 'consommateur') {
    // Récupérer l'historique des recherches pour les consommateurs
    $stmt = mysqli_query($conn, "SELECT h.date_recherche, p.nom, p.code_unique, p.categorie 
                                 FROM historique_recherche h 
                                 JOIN produits p ON h.produit_id = p.id 
                                 WHERE h.user_id = $user_id 
                                 ORDER BY h.date_recherche DESC LIMIT 10");
    while($row = mysqli_fetch_assoc($stmt)) $historique[] = $row;
    $stats['total'] = count($historique);

} else {
    // Autres acteurs (coopérative, transporteur, transformateur, distributeur)
    // Stats: nombre d'étapes enregistrées
    $stmt = mysqli_query($conn, "SELECT COUNT(*) as c FROM etapes_tracabilite WHERE acteur_id = $user_id");
    $stats['total'] = mysqli_fetch_assoc($stmt)['c'];
    
    // Historique des interventions
    $stmt = mysqli_query($conn, "SELECT e.date_etape, e.etape, p.nom, p.code_unique 
                                 FROM etapes_tracabilite e 
                                 JOIN produits p ON e.produit_id = p.id 
                                 WHERE e.acteur_id = $user_id 
                                 ORDER BY e.date_etape DESC LIMIT 10");
    while($row = mysqli_fetch_assoc($stmt)) $historique[] = $row;
}
?>

<main class="dashboard container">
  <div class="dash-header anim">
    <h1 style="display:flex;align-items:center;gap:.5rem;">Bonjour, <?php echo htmlspecialchars($user_name); ?> <?php echo get_icon('wave', '1em', 'var(--caribbean)'); ?></h1>
    <p>Espace <?php echo ucfirst($role); ?></p>
  </div>

  <?php if (isset($_GET['succes'])): ?>
    <div class="alert alert-success anim">Action effectuée avec succès !</div>
  <?php endif; ?>

  <div class="dash-stats anim stagger">
    <?php if ($role === 'producteur'): ?>
      <div class="dash-stat">
        <div class="num"><?php echo $stats['total']; ?></div>
        <div class="lbl">Produits enregistrés</div>
      </div>
      <div class="dash-stat">
        <div class="num">
          <?php 
            $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM produits WHERE producteur_id = $user_id AND MONTH(created_at) = MONTH(CURRENT_DATE())");
            echo mysqli_fetch_assoc($q)['c'];
          ?>
        </div>
        <div class="lbl">Ajoutés ce mois</div>
      </div>
    <?php elseif ($role === 'consommateur'): ?>
      <div class="dash-stat">
        <div class="num"><?php echo $stats['total']; ?></div>
        <div class="lbl">Produits consultés</div>
      </div>
    <?php else: ?>
      <div class="dash-stat">
        <div class="num"><?php echo $stats['total']; ?></div>
        <div class="lbl">Interventions enregistrées</div>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($role === 'producteur'): ?>
    <div class="dash-actions anim">
      <a href="ajouter_produit.php" class="btn btn-primary">+ Enregistrer un nouveau produit</a>
    </div>

    <div class="card anim" style="overflow-x:auto;">
      <h3 style="margin-bottom:1rem;">Vos produits récents</h3>
      <?php if (empty($produits)): ?>
        <p style="color:var(--text-3);">Vous n'avez pas encore enregistré de produit.</p>
      <?php else: ?>
        <table class="product-table">
          <thead>
            <tr>
              <th>Code Unique</th>
              <th>Nom du produit</th>
              <th>Catégorie</th>
              <th>Date d'ajout</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($produits as $p): ?>
              <tr>
                <td style="font-family:monospace; color:var(--caribbean);"><?php echo $p['code_unique']; ?></td>
                <td><strong><?php echo htmlspecialchars($p['nom']); ?></strong></td>
                <td><?php echo htmlspecialchars($p['categorie']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                <td><a href="produit.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary btn-sm">Gérer</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  <?php elseif ($role === 'consommateur'): ?>
    <div class="dash-actions anim">
      <a href="consulter_produit.php" class="btn btn-primary"><?php echo get_icon('search', '1.2em'); ?> Scanner un nouveau produit</a>
    </div>
    
    <div class="card anim">
      <h3 style="margin-bottom:1rem;">Votre historique de recherche</h3>
      <?php if (empty($historique)): ?>
        <p style="color:var(--text-3);">Vous n'avez encore consulté aucun produit.</p>
      <?php else: ?>
        <ul style="display:flex; flex-direction:column; gap:.8rem;">
          <?php foreach($historique as $h): ?>
            <li style="display:flex; justify-content:space-between; align-items:center; padding:.8rem; background:hsla(160,50%,50%,.05); border-radius:var(--radius-sm);">
              <div>
                <strong><?php echo htmlspecialchars($h['nom']); ?></strong> <span style="font-size:.8rem; color:var(--text-3);">(<?php echo $h['categorie']; ?>)</span><br>
                <span style="font-size:.8rem; color:var(--caribbean); font-family:monospace;"><?php echo $h['code_unique']; ?></span>
              </div>
              <div style="text-align:right;">
                <span style="font-size:.75rem; color:var(--text-3); display:block; margin-bottom:.3rem;"><?php echo date('d/m/Y H:i', strtotime($h['date_recherche'])); ?></span>
                <a href="consulter_produit.php?code=<?php echo $h['code_unique']; ?>" class="btn btn-secondary btn-sm" style="padding:.2rem .6rem; font-size:.7rem;">Revoir</a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <!-- Intermédiaires (Coopérative, Transporteur, Transformateur, Distributeur) -->
    <div class="card anim" style="margin-bottom:2rem;">
      <h3 style="margin-bottom:1rem;">Scanner un produit pour ajouter une étape</h3>
      <p style="font-size:.9rem; color:var(--text-2); margin-bottom:1rem;">Entrez le code unique du produit pour enregistrer votre intervention (<?php echo $role; ?>).</p>
      <form action="produit.php" method="GET" style="display:flex; gap:1rem; max-width:500px;">
        <input type="text" name="code_search" class="form-input" placeholder="Ex: PROD_..." required style="flex:1;">
        <button type="submit" class="btn btn-primary">Chercher</button>
      </form>
    </div>

    <div class="card anim" style="overflow-x:auto;">
      <h3 style="margin-bottom:1rem;">Vos dernières interventions</h3>
      <?php if (empty($historique)): ?>
        <p style="color:var(--text-3);">Aucune intervention enregistrée.</p>
      <?php else: ?>
        <table class="product-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Étape</th>
              <th>Produit</th>
              <th>Code</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($historique as $h): ?>
              <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($h['date_etape'])); ?></td>
                <td><span class="badge badge-ok"><?php echo ucfirst($h['etape']); ?></span></td>
                <td><strong><?php echo htmlspecialchars($h['nom']); ?></strong></td>
                <td style="font-family:monospace; color:var(--caribbean);"><?php echo $h['code_unique']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
