<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php
require_once __DIR__ . '/icons.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Système de traçabilité des produits agricoles en Côte d'Ivoire — suivez vos produits de la plantation au consommateur.">
  <title><?php echo isset($page_title) ? $page_title . ' — ' : ''; ?>Du Sol à l'Assiette</title>
  <link rel="stylesheet" href="<?php echo $base_url ?? ''; ?>assets/css/style.css">
  <!-- Favicon avec émoji feuille -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
</head>
<body>

<header class="site-header" id="site-header">
  <div class="header-inner">
    <a href="<?php echo $base_url ?? ''; ?>index.php" class="logo">
      <div class="logo-icon"><?php echo get_icon('leaf', '20px', 'var(--rich-black)'); ?></div>
      <span class="logo-text">Du Sol à l'Assiette</span>
    </a>

    <nav class="nav-links" id="nav-links">
      <?php if (isset($page_title) && $page_title !== 'Accueil'): ?>
        <a href="<?php echo $base_url ?? ''; ?>index.php" class="nav-btn">Accueil</a>
      <?php endif; ?>
      <a href="<?php echo $base_url ?? ''; ?>pages/consulter_produit.php" class="nav-btn accent-btn" style="display:inline-flex;align-items:center;gap:.3rem;"><?php echo get_icon('search', '1.2em'); ?> Suivre un produit</a>

      <?php if (isset($_SESSION['user'])): ?>
        <a href="<?php echo $base_url ?? ''; ?>pages/dashboard.php" class="nav-btn login-btn">
          📊 Dashboard
        </a>
        <a href="<?php echo $base_url ?? ''; ?>actions/logout.php" class="nav-btn">Déconnexion</a>
      <?php else: ?>
        <a href="<?php echo $base_url ?? ''; ?>auth/login.php" class="nav-btn login-btn">Se connecter</a>
      <?php endif; ?>
    </nav>

    <button class="menu-toggle" id="menu-toggle" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
