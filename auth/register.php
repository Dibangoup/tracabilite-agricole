<?php
$base_url = '../';
$page_title = 'Inscription';
include '../includes/header.php';

if (isset($_SESSION['user'])) {
    header("Location: ../pages/dashboard.php");
    exit();
}
?>

<main class="auth-page" style="padding-top: 6rem; padding-bottom: 4rem;">
  <div class="auth-card anim" style="max-width: 600px;">
    <h2>Inscription</h2>
    <p class="subtitle">Rejoignez le réseau Du Sol à l'Assiette</p>

    <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'email_existe'): ?>
      <div class="alert alert-error" style="margin-bottom: 1.5rem; text-align: center;">
        Cet email est déjà utilisé par un autre compte. Veuillez en choisir un autre ou <a href="login.php" style="text-decoration:underline;">vous connecter</a>.
      </div>
    <?php endif; ?>

    <form action="../actions/register.php" method="POST">
      <div class="form-group">
        <label>Je suis un(e) :</label>
        <div class="role-grid">
          <label class="role-card selected">
            <input type="radio" name="role" value="producteur" checked>
            <div class="role-icon"><?php echo get_icon('farmer', '1.5em'); ?></div>
            <div class="role-name">Producteur</div>
          </label>
          <label class="role-card">
            <input type="radio" name="role" value="cooperative">
            <div class="role-icon"><?php echo get_icon('box', '1.5em'); ?></div>
            <div class="role-name">Coopérative</div>
          </label>
          <label class="role-card">
            <input type="radio" name="role" value="transporteur">
            <div class="role-icon"><?php echo get_icon('truck', '1.5em'); ?></div>
            <div class="role-name">Transporteur</div>
          </label>
          <label class="role-card">
            <input type="radio" name="role" value="transformateur">
            <div class="role-icon"><?php echo get_icon('factory', '1.5em'); ?></div>
            <div class="role-name">Transformateur</div>
          </label>
          <label class="role-card">
            <input type="radio" name="role" value="distributeur">
            <div class="role-icon"><?php echo get_icon('cart', '1.5em'); ?></div>
            <div class="role-name">Distributeur</div>
          </label>
          <label class="role-card">
            <input type="radio" name="role" value="consommateur">
            <div class="role-icon"><?php echo get_icon('consumer', '1.5em'); ?></div>
            <div class="role-name">Consommateur</div>
          </label>
        </div>
      </div>

      <div class="form-group">
        <label>Nom complet / Nom de la structure</label>
        <input type="text" name="nom" class="form-input" required>
      </div>
      <div class="form-group">
        <label>Adresse Email</label>
        <input type="email" name="email" class="form-input" required>
      </div>
      <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" name="password" class="form-input" required minlength="6">
      </div>
      
      <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">Créer mon compte</button>
    </form>

    <div class="auth-footer">
      Vous avez déjà un compte ? <a href="login.php">Connectez-vous</a>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
