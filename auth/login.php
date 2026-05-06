<?php
$base_url = '../';
$page_title = 'Connexion';
include '../includes/header.php';

// Si déjà connecté, rediriger
if (isset($_SESSION['user'])) {
    header("Location: ../pages/dashboard.php");
    exit();
}
?>

<main class="auth-page">
  <div class="auth-card anim">
    <h2>Connexion</h2>
    <p class="subtitle">Accédez à votre espace Du Sol à l'Assiette</p>

    <?php if (isset($_GET['erreur'])): ?>
      <div class="alert alert-error">
        <?php 
          if ($_GET['erreur'] == '1') echo "Email ou mot de passe incorrect.";
          elseif ($_GET['erreur'] == 'email_existe') echo "Cet email est déjà utilisé.";
          else echo "Une erreur est survenue.";
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['inscription'])): ?>
      <div class="alert alert-success">
        Inscription réussie ! Vous pouvez maintenant vous connecter.
      </div>
    <?php endif; ?>

    <form action="../actions/login.php" method="POST">
      <div class="form-group">
        <label>Adresse Email</label>
        <input type="email" name="email" class="form-input" required>
      </div>
      <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" name="password" class="form-input" required>
      </div>
      <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <div class="auth-footer">
      Nouveau sur Du Sol à l'Assiette ? <a href="register.php">Créer un compte</a>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
