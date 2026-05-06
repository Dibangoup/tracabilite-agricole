<!-- Help floating button -->
<button class="help-fab" id="help-fab" aria-label="Aide">?</button>
<div class="help-panel" id="help-panel">
  <h3 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('info', '1.2em', 'var(--caribbean)'); ?> Besoin d'aide ?</h3>
  <p style="font-size:.85rem;color:var(--text-3);margin-bottom:1rem;">Envoyez-nous un message, nous vous répondrons rapidement.</p>
  <form id="help-form">
    <div class="form-group">
      <input type="email" name="email" class="form-input" placeholder="Votre email (optionnel)">
    </div>
    <div class="form-group">
      <textarea name="message" class="form-textarea" placeholder="Décrivez votre problème ou suggestion..." required></textarea>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Envoyer</button>
  </form>
</div>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <h4 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('leaf', '1.2em', 'var(--caribbean)'); ?> Du Sol à l'Assiette</h4>
        <p>Système de traçabilité des produits agricoles en Côte d'Ivoire. De la plantation au consommateur.</p>
      </div>

      <div class="footer-col">
        <h4>Contact</h4>
        <p>Abidjan, Côte d'Ivoire</p>
        <p>oliviern326@gmail.com</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?php echo date('Y'); ?> Du Sol à l'Assiette — Projet universitaire. Tous droits réservés.</p>
    </div>
  </div>
</footer>

<!-- EmailJS SDK -->
<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script>
  emailjs.init("B64G-TcFIXjplijR3");
</script>
<script src="<?php echo $base_url ?? ''; ?>assets/js/main.js"></script>
</body>
</html>
