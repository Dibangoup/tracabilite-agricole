<main>
  <!-- HERO SECTION -->
  <section class="hero">
    <div class="hero-content anim">
      <div class="hero-badge"><?php echo get_icon('leaf', '1.2em'); ?> Traçabilité Agricole de Côte d'Ivoire</div>
      <h1>Savez-vous d'où vient ce que <span class="highlight">vous consommez</span> ?</h1>
      <p>Suivez le parcours complet de vos produits agricoles, depuis la plantation jusqu'à votre table. Transparence, qualité et confiance à chaque étape.</p>
      <div class="hero-actions">
        <a href="<?php echo $base_url ?? ''; ?>pages/consulter_produit.php" class="btn btn-primary"><?php echo get_icon('search', '1.2em'); ?> Suivre un produit</a>
        <?php if (!isset($_SESSION['user'])): ?>
          <a href="<?php echo $base_url ?? ''; ?>auth/register.php" class="btn btn-secondary">Créer un compte</a>
        <?php endif; ?>
        <a href="#fonctionnement" class="btn btn-secondary" style="background:transparent; border-color:var(--text-1); color:var(--text-1);">Comment ça marche ?</a>
      </div>
    </div>
  </section>

  <!-- LE PROBLÈME / CONTEXTE -->
  <section class="container" id="contexte">
    <div class="text-center anim">
      
      <h2 class="section-title">Pourquoi la traçabilité ?</h2>
      <p class="section-desc mx-auto">En Côte d'Ivoire, l'agriculture est le pilier de l'économie. Mais entre le champ et le consommateur final, l'information se perd souvent ou manque d'authenticité. Notre système reconnecte chaque acteur de la chaîne de valeur.</p>
    </div>
    <div class="cards-grid">
      <div class="card anim stagger">
        <div class="card-icon"><?php echo get_icon('shield', '1.5em', 'var(--caribbean)'); ?></div>
        <h3>Garantie de qualité</h3>
        <p>Assurez-vous que les produits respectent les normes à chaque étape (récolte, transport, stockage).</p>
      </div>
      <div class="card anim stagger">
        <div class="card-icon"><?php echo get_icon('farmer', '1.5em', 'var(--caribbean)'); ?></div>
        <h3>Valorisation du producteur</h3>
        <p>Mettez un visage sur ce que vous mangez. Le travail du paysan est enfin reconnu et valorisé.</p>
      </div>
      <div class="card anim stagger">
        <div class="card-icon"><?php echo get_icon('clock', '1.5em', 'var(--caribbean)'); ?></div>
        <h3>Suivi en temps réel</h3>
        <p>De la date de récolte à la date de péremption, toutes les informations sont accessibles en un scan.</p>
      </div>
    </div>
  </section>

  <!-- COMMENT ÇA MARCHE -->
  <section class="container" id="fonctionnement">
    <div class="text-center anim">
      <span class="section-label">Fonctionnement</span>
      <h2 class="section-title">Le cycle de vie d'un produit</h2>
      <p class="section-desc mx-auto">Découvrez comment l'information s'enrichit à chaque passage de relais.</p>
    </div>
    
    <div class="timeline mx-auto" style="max-width: 600px;">
      <div class="timeline-item anim stagger">
        <div class="timeline-dot active">1</div>
        <div class="timeline-content">
          <h4 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('plante', '2.5em', 'var(--caribbean)'); ?> Production & Récolte</h4>
          <div class="meta">Par le Producteur</div>
          <p>Le produit naît. Il reçoit son identifiant unique (QR code) et ses premières informations (origine, date, type).</p>
        </div>
      </div>
      <div class="timeline-item anim stagger">
        <div class="timeline-dot">2</div>
        <div class="timeline-content">
          <h4 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('truck', '2.5em', 'var(--caribbean)'); ?> Transport & Stockage</h4>
          <div class="meta">Par les Transporteurs & Coopératives</div>
          <p>Le produit voyage. Les conditions de transport et les lieux de stockage temporaire sont enregistrés.</p>
        </div>
      </div>
      <div class="timeline-item anim stagger">
        <div class="timeline-dot">3</div>
        <div class="timeline-content">
          <h4 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('factory', '2.5em', 'var(--caribbean)'); ?> Transformation</h4>
          <div class="meta">Par les Transformateurs</div>
          <p>Le produit brut devient fini (ex: Cacao → Chocolat). Une nouvelle date de péremption est définie.</p>
        </div>
      </div>
      <div class="timeline-item anim stagger">
        <div class="timeline-dot">4</div>
        <div class="timeline-content">
          <h4 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('cart', '2.5em', 'var(--caribbean)'); ?> Distribution</h4>
          <div class="meta">Par les Distributeurs</div>
          <p>Le produit arrive dans les rayons. Sa fraîcheur est garantie par l'historique complet.</p>
        </div>
      </div>
      <div class="timeline-item anim stagger">
        <div class="timeline-dot">5</div>
        <div class="timeline-content">
          <h4 style="display:flex;align-items:center;gap:.5rem;"><?php echo get_icon('consumer', '2.5em', 'var(--caribbean)'); ?> Consommation</h4>
          <div class="meta">Par Vous</div>
          <p>Vous scannez, vous savez tout. Vous pouvez même laisser un avis qui remontera jusqu'au producteur !</p>
        </div>
      </div>
    </div>
  </section>

  <!-- LES ACTEURS (STICKMAN SVG INLINE) -->
  <section class="container" id="acteurs">
    <div class="text-center anim">
      <span class="section-label">Écosystème</span>
      <h2 class="section-title">Les acteurs de la chaîne</h2>
      <p class="section-desc mx-auto">Chacun a un rôle clé dans la transparence du système.</p>
    </div>
    
    <div class="cards-grid">
      <!-- Producteur -->
      <div class="card actor-card anim stagger">
        <div class="stickman">
          <svg viewBox="0 0 100 120" stroke="var(--caribbean)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <!-- Chapeau -->
            <path d="M20 30 Q50 10 80 30 L90 35 L10 35 Z" fill="var(--caribbean)" opacity="0.2" stroke="none"/>
            <path d="M20 30 Q50 10 80 30" />
            <line x1="10" y1="35" x2="90" y2="35" />
            <!-- Tête -->
            <circle cx="50" cy="50" r="15" />
            <!-- Corps -->
            <line x1="50" y1="65" x2="50" y2="95" />
            <!-- Bras -->
            <line x1="50" y1="75" x2="30" y2="85" />
            <line x1="50" y1="75" x2="70" y2="85" />
            <!-- Jambes -->
            <line x1="50" y1="95" x2="35" y2="115" />
            <line x1="50" y1="95" x2="65" y2="115" />
            <!-- Plante -->
            <path d="M70 85 Q80 70 90 60 M70 85 Q85 85 95 80" stroke="var(--mountain)" />
          </svg>
        </div>
        <h3>Le Producteur</h3>
        <p>Inscrit le produit et génère le code unique initial.</p>
      </div>

      <!-- Coopérative -->
      <div class="card actor-card anim stagger">
        <div class="stickman">
          <svg viewBox="0 0 100 120" stroke="var(--caribbean)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <!-- Tête -->
            <circle cx="50" cy="30" r="15" />
            <!-- Corps -->
            <line x1="50" y1="45" x2="50" y2="75" />
            <!-- Bras tenant la boite -->
            <line x1="50" y1="55" x2="30" y2="65" />
            <line x1="50" y1="55" x2="70" y2="65" />
            <!-- Jambes -->
            <line x1="50" y1="75" x2="35" y2="95" />
            <line x1="50" y1="75" x2="65" y2="95" />
            <!-- Boite / Batiment -->
            <rect x="25" y="65" width="50" height="40" rx="4" fill="var(--caribbean)" opacity="0.1" />
            <rect x="25" y="65" width="50" height="40" rx="4" />
            <line x1="40" y1="80" x2="60" y2="80" />
            <line x1="50" y1="80" x2="50" y2="105" />
          </svg>
        </div>
        <h3>La Coopérative</h3>
        <p>Regroupe et stocke les récoltes des différents producteurs.</p>
      </div>

      <!-- Transporteur -->
      <div class="card actor-card anim stagger">
        <div class="stickman">
          <svg viewBox="0 0 100 120" stroke="var(--caribbean)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <!-- Tête -->
            <circle cx="30" cy="40" r="12" />
            <!-- Corps (assis) -->
            <line x1="30" y1="52" x2="30" y2="80" />
            <!-- Bras sur le volant -->
            <line x1="30" y1="65" x2="55" y2="60" />
            <!-- Jambes -->
            <line x1="30" y1="80" x2="50" y2="80" />
            <line x1="50" y1="80" x2="50" y2="95" />
            <!-- Camion -->
            <path d="M10 95 L80 95 L90 70 L60 70 L60 30 L10 30 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
            <path d="M10 95 L80 95 L90 70 L60 70 L60 30 L10 30 Z" />
            <!-- Volant -->
            <circle cx="60" cy="65" r="8" />
            <!-- Roues -->
            <circle cx="25" cy="95" r="10" fill="var(--surface)" />
            <circle cx="75" cy="95" r="10" fill="var(--surface)" />
          </svg>
        </div>
        <h3>Le Transporteur</h3>
        <p>Achemine les produits et valide les conditions de transit.</p>
      </div>

      <!-- Transformateur -->
      <div class="card actor-card anim stagger">
        <div class="stickman">
          <svg viewBox="0 0 100 120" stroke="var(--caribbean)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <!-- Tête -->
            <circle cx="70" cy="40" r="12" />
            <!-- Corps -->
            <line x1="70" y1="52" x2="70" y2="85" />
            <!-- Bras vers machine -->
            <line x1="70" y1="65" x2="45" y2="65" />
            <line x1="70" y1="65" x2="85" y2="75" />
            <!-- Jambes -->
            <line x1="70" y1="85" x2="60" y2="105" />
            <line x1="70" y1="85" x2="80" y2="105" />
            <!-- Usine / Machine -->
            <path d="M10 105 L45 105 L45 50 L30 40 L30 50 L15 40 L15 50 L10 50 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
            <path d="M10 105 L45 105 L45 50 L30 40 L30 50 L15 40 L15 50 L10 50 Z" />
            <!-- Fumée -->
            <path d="M15 30 Q20 20 15 10" stroke="var(--mountain)" stroke-dasharray="2 4" />
            <path d="M30 30 Q35 20 30 10" stroke="var(--mountain)" stroke-dasharray="2 4" />
          </svg>
        </div>
        <h3>Le Transformateur</h3>
        <p>Met à jour la nature du produit et sa date de péremption.</p>
      </div>

      <!-- Distributeur -->
      <div class="card actor-card anim stagger">
        <div class="stickman">
          <svg viewBox="0 0 100 120" stroke="var(--caribbean)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <!-- Tête -->
            <circle cx="40" cy="30" r="12" />
            <!-- Corps -->
            <line x1="40" y1="42" x2="40" y2="75" />
            <!-- Bras poussant caddie -->
            <line x1="40" y1="55" x2="65" y2="60" />
            <line x1="40" y1="55" x2="30" y2="65" />
            <!-- Jambes marchant -->
            <line x1="40" y1="75" x2="25" y2="95" />
            <line x1="40" y1="75" x2="50" y2="95" />
            <!-- Caddie / Rayon -->
            <path d="M60 55 L75 55 L85 85 L55 85 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
            <path d="M60 55 L75 55 L85 85 L55 85 Z" />
            <line x1="55" y1="85" x2="50" y2="95" />
            <line x1="85" y1="85" x2="80" y2="95" />
            <circle cx="50" cy="100" r="5" />
            <circle cx="80" cy="100" r="5" />
          </svg>
        </div>
        <h3>Le Distributeur</h3>
        <p>Réceptionne et met en rayon avec garantie de fraîcheur.</p>
      </div>

      <!-- Consommateur -->
      <div class="card actor-card anim stagger">
        <div class="stickman">
          <svg viewBox="0 0 100 120" stroke="var(--caribbean)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <!-- Tête -->
            <circle cx="50" cy="30" r="12" />
            <!-- Corps -->
            <line x1="50" y1="42" x2="50" y2="75" />
            <!-- Bras tenant le tel -->
            <line x1="50" y1="55" x2="65" y2="55" />
            <line x1="65" y1="55" x2="65" y2="45" />
            <!-- Autre bras -->
            <line x1="50" y1="55" x2="35" y2="65" />
            <!-- Jambes -->
            <line x1="50" y1="75" x2="40" y2="105" />
            <line x1="50" y1="75" x2="60" y2="105" />
            <!-- Téléphone -->
            <rect x="60" y="30" width="10" height="15" rx="2" fill="var(--surface-el)" />
            <!-- Rayon de scan -->
            <line x1="75" y1="35" x2="90" y2="30" stroke="var(--mountain)" stroke-dasharray="2 2" />
            <line x1="75" y1="40" x2="90" y2="50" stroke="var(--mountain)" stroke-dasharray="2 2" />
          </svg>
        </div>
        <h3>Le Consommateur</h3>
        <p>Scanne le code QR, consulte le parcours et donne son avis.</p>
      </div>
    </div>
  </section>


</main>
