document.addEventListener('DOMContentLoaded', () => {
  // ===== ANIMATIONS AU DÉFILEMENT =====
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); }
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.anim').forEach(el => observer.observe(el));

  // ===== EN-TÊTE AU DÉFILEMENT =====
  const header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 50);
    });
  }

  // ===== MENU MOBILE =====
  const toggle = document.querySelector('.menu-toggle');
  const navLinks = document.querySelector('.nav-links');
  if (toggle && navLinks) {
    toggle.addEventListener('click', () => navLinks.classList.toggle('open'));
    navLinks.querySelectorAll('.nav-btn').forEach(btn => {
      btn.addEventListener('click', () => navLinks.classList.remove('open'));
    });
  }

  // ===== COMPTEURS ANIMÉS =====
  const counters = document.querySelectorAll('[data-count]');
  const countObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting && !e.target.dataset.counted) {
        e.target.dataset.counted = 'true';
        animateCounter(e.target, parseInt(e.target.dataset.count));
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(el => countObserver.observe(el));

  function animateCounter(el, target) {
    let start = 0;
    const duration = 2000;
    const step = (ts) => {
      if (!start) start = ts;
      const progress = Math.min((ts - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString('fr-FR');
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString('fr-FR') + '+';
    };
    requestAnimationFrame(step);
  }

  // ===== SÉLECTEUR DE RÔLE =====
  document.querySelectorAll('.role-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      const radio = card.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  // ===== PANNEAU D'AIDE =====
  const helpFab = document.querySelector('.help-fab');
  const helpPanel = document.querySelector('.help-panel');
  if (helpFab && helpPanel) {
    helpFab.addEventListener('click', () => helpPanel.classList.toggle('open'));
    document.addEventListener('click', (e) => {
      if (!helpPanel.contains(e.target) && !helpFab.contains(e.target)) {
        helpPanel.classList.remove('open');
      }
    });
  }

  // ===== FORMULAIRE D'AIDE (EmailJS - à configurer) =====
  const helpForm = document.getElementById('help-form');
  if (helpForm) {
    helpForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const msg = helpForm.querySelector('textarea').value.trim();
      const email = helpForm.querySelector('input[name="email"]');
      if (!msg) return;

      // Envoi EmailJS
      if (typeof emailjs !== 'undefined') {
        emailjs.send('service_g1cl5uk', 'template_idk7ppm', {
          message: msg,
          from_email: email ? email.value : 'visiteur',
          from_name: 'Contact Traçabilité'
        }).then(() => {
          showHelpSent();
        }).catch(() => {
          alert('Erreur lors de l\'envoi. Réessayez plus tard.');
        });
      } else {
        showHelpSent();
      }
    });

    function showHelpSent() {
      helpForm.innerHTML = '<div class="help-sent"><span style="font-size:2rem">✓</span><p>Message envoyé avec succès !</p></div>';
      setTimeout(() => {
        helpPanel.classList.remove('open');
        setTimeout(() => {
          helpForm.innerHTML = getHelpFormHTML();
        }, 300);
      }, 2000);
    }
  }

  function getHelpFormHTML() {
    return `
      <div class="form-group">
        <input type="email" name="email" class="form-input" placeholder="Votre email (optionnel)">
      </div>
      <div class="form-group">
        <textarea name="message" class="form-textarea" placeholder="Décrivez votre problème ou suggestion..." required></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Envoyer</button>
    `;
  }

  // ===== FENÊTRES MODALES =====
  window.openModal = function(id) {
    document.getElementById(id)?.classList.add('open');
  };
  window.closeModal = function(id) {
    document.getElementById(id)?.classList.remove('open');
  };
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) overlay.classList.remove('open');
    });
  });

  // ===== SCANNER QR =====
  window.startQRScanner = function() {
    const readerDiv = document.getElementById('qr-reader');
    if (!readerDiv || typeof Html5QrcodeScanner === 'undefined') return;
    
    const scanner = new Html5QrcodeScanner("qr-reader", {
      fps: 10, qrbox: { width: 250, height: 250 }
    });
    scanner.render((decodedText) => {
      scanner.clear();
      const input = document.getElementById('code-input');
      if (input) {
        input.value = decodedText;
        input.form.submit();
      }
    });
  };

  // ===== GESTION DES AVIS (EmailJS + PHP) =====
  const reviewForm = document.getElementById('review-form');
  if (reviewForm) {
    reviewForm.addEventListener('submit', function(e) {
      // On ne fait pas e.preventDefault() car on veut que le PHP enregistre aussi l'avis
      // Mais on lance l'email en parallèle
      const nom = reviewForm.querySelector('input[name="nom"]').value;
      const note = reviewForm.querySelector('select[name="note"]').value;
      const comm = reviewForm.querySelector('textarea[name="commentaire"]').value;
      
      if (typeof emailjs !== 'undefined') {
        emailjs.send('service_g1cl5uk', 'template_idk7ppm', {
          from_name: nom,
          message: `Nouvel avis (${note}/5) : ${comm}`,
          subject: "Nouvel avis consommateur - Du Sol à l'Assiette"
        });
      }
    });
  }

  // ===== DÉFILEMENT FLUIDE =====
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });
});
