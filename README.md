# 🌾 Traçabilité Agricole

Plateforme web de traçabilité des produits agricoles en Côte d'Ivoire, permettant de suivre le parcours complet d'un produit — de la récolte jusqu'à la vente — via un système de **QR codes scannables**.

## 📋 Description du projet

Ce projet permet :
- **Aux producteurs** : d'enregistrer leurs produits, générer un QR code unique et suivre chaque étape de la chaîne
- **Aux coopératives, transporteurs, transformateurs, distributeurs** : de documenter leurs interventions sur les produits en scannant le QR code
- **Aux consommateurs** : de scanner le QR code d'un produit pour consulter son historique complet et laisser des avis

Chaque produit dispose d'un **QR code unique** qui, une fois scanné, ouvre directement la page de traçabilité dans le navigateur (récolte → transport → stockage → transformation → distribution → vente).

## ✨ Fonctionnalités principales

- 🔐 Authentification sécurisée (inscription / connexion) avec hashage bcrypt
- 👥 6 rôles distincts : producteur, coopérative, transporteur, transformateur, distributeur, consommateur
- 📦 CRUD complet des produits (créer, modifier, supprimer / marquer détruit)
- 📊 Tableau de bord adapté selon le rôle de l'utilisateur
- 📱 **QR codes scannables** — encodent une URL complète pour ouverture directe dans le navigateur
- 🔄 Suivi des étapes de traçabilité avec timeline visuelle
- ⏳ Gestion des dates de péremption (mise à jour lors de la transformation)
- ⭐ Système d'avis consommateurs (notes 1-5 + commentaires)
- 🔍 Recherche de produits par code unique ou scan QR
- 📜 Historique des recherches pour les consommateurs
- 🌐 Détection automatique de l'URL du site (compatible local et production)
- 🌍 **Gestion du fuseau horaire** — configuré par défaut pour la Côte d'Ivoire (Africa/Abidjan)
- 🕒 **Précision temporelle** — sélection de l'heure exacte lors de l'enregistrement d'une étape (rétroactive)
- 📷 **Scan QR optimisé** — utilisation de la caméra arrière sur mobile et intégration directe pour l'ajout d'étapes depuis le tableau de bord

## 🛠️ Technologies utilisées

- **Backend** : PHP 8+ (procédural avec mysqli)
- **Base de données** : MySQL / MariaDB
- **Serveur local** : XAMPP (Apache + MySQL)
- **QR Codes** : API externe [goqr.me](https://goqr.me/api/) pour la génération
- **Scanner QR** : Bibliothèque [html5-qrcode](https://github.com/mebjas/html5-qrcode) (côté client)
- **Sécurité** : Requêtes préparées (anti injection SQL), hashage bcrypt des mots de passe
- **Frontend** : HTML5, CSS3 (design nature — vert & brun), SVG inline, animations CSS

## 📁 Structure du projet

```
tracabilite-agricole/
├── index.php                        # Point d'entrée (redirige vers l'accueil)
├── README.md                        # Documentation du projet
├── config/
│   └── db.php                       # Connexion MySQL + constante SITE_URL (auto-détection)
├── includes/
│   ├── header.php                   # En-tête commun (navbar responsive)
│   ├── footer.php                   # Pied de page commun
│   ├── functions.php                # Fonctions utilitaires (session, rôles)
│   └── icons.php                    # Bibliothèque d'icônes SVG inline
├── auth/
│   ├── login.php                    # Formulaire de connexion
│   └── register.php                 # Formulaire d'inscription (choix du rôle)
├── actions/
│   ├── login.php                    # Traitement connexion (password_verify)
│   ├── logout.php                   # Déconnexion (destruction de session)
│   ├── register.php                 # Inscription (hash bcrypt + email unique)
│   ├── ajouter_produit.php          # Ajout produit + première étape (récolte)
│   ├── modifier_produit.php         # Modification d'un produit existant
│   ├── supprimer_produit.php        # Suppression d'un produit (cascade)
│   ├── ajouter_etape.php            # Ajout d'une étape de traçabilité
│   └── ajouter_avis.php            # Ajout d'un avis consommateur
├── pages/
│   ├── accueil.php                  # Page d'accueil publique (hero, timeline, acteurs)
│   ├── dashboard.php                # Tableau de bord adapté selon le rôle
│   ├── ajouter_produit.php          # Formulaire d'ajout de produit
│   ├── produit.php                  # Détail produit + QR code + gestion étapes
│   └── consulter_produit.php        # Consultation publique par code/scan QR
├── assets/
│   ├── css/                         # Feuilles de style
│   ├── js/                          # Scripts JavaScript
│   └── images/                      # Images du site
└── sql/
    ├── schema.sql                   # Création initiale des tables
    └── schema_update.sql            # Migrations (nouveaux rôles, colonnes, tables)
```

## 🗄️ Base de données

**Nom** : `tracabilite-agricole-db`

### Table `users`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT (PK, AI) | Identifiant unique |
| `nom` | VARCHAR(100) | Nom complet |
| `email` | VARCHAR(100) | Email (unique) |
| `mot_de_passe` | VARCHAR(255) | Mot de passe hashé (bcrypt) |
| `role` | ENUM | `producteur`, `cooperative`, `transporteur`, `transformateur`, `distributeur`, `consommateur` |
| `created_at` | TIMESTAMP | Date de création du compte |

### Table `produits`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT (PK, AI) | Identifiant unique |
| `nom` | VARCHAR(100) | Nom du produit |
| `type` | VARCHAR(100) | Type / Variété |
| `categorie` | VARCHAR(100) | Catégorie (fruit, légume, céréale...) |
| `quantite` | VARCHAR(50) | Quantité initiale |
| `description` | TEXT | Description / Notes |
| `origine` | VARCHAR(150) | Lieu d'origine |
| `code_unique` | VARCHAR(100) | Code de traçabilité unique (ex: `PROD_6839a1b2...`) |
| `date_peremption` | DATE | Date de péremption (nullable) |
| `producteur_id` | INT (FK) | Référence vers `users.id` |
| `created_at` | TIMESTAMP | Date d'ajout |

### Table `etapes_tracabilite`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT (PK, AI) | Identifiant unique |
| `produit_id` | INT (FK) | Référence vers `produits.id` |
| `acteur_id` | INT (FK) | Référence vers `users.id` (qui a effectué l'étape) |
| `etape` | ENUM | `recolte`, `transport`, `stockage`, `transformation`, `distribution`, `vente` |
| `description` | TEXT | Description détaillée |
| `lieu` | VARCHAR(150) | Lieu de l'étape |
| `date_etape` | DATETIME | Date de l'étape |
| `date_peremption` | DATE | Nouvelle date de péremption (si transformation) |
| `created_at` | TIMESTAMP | Date d'enregistrement |

### Table `avis`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT (PK, AI) | Identifiant unique |
| `produit_id` | INT (FK) | Référence vers `produits.id` |
| `nom_consommateur` | VARCHAR(100) | Nom du consommateur |
| `commentaire` | TEXT | Contenu de l'avis |
| `note` | INT | Note (1 à 5) |
| `date_avis` | TIMESTAMP | Date de l'avis |

### Table `historique_recherche`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT (PK, AI) | Identifiant unique |
| `user_id` | INT (FK) | Référence vers `users.id` |
| `produit_id` | INT (FK) | Référence vers `produits.id` |
| `code_recherche` | VARCHAR(100) | Code recherché |
| `date_recherche` | TIMESTAMP | Date de la recherche |

## ⚙️ Installation

### Prérequis
- [XAMPP](https://www.apachefriends.org/) installé (Apache + MySQL)

### Étapes

1. **Cloner le projet** dans le dossier `htdocs` de XAMPP :
   ```bash
   cd C:\xampp\htdocs\Projet_web
   git clone https://github.com/Dibangoup/tracabilite-agricole.git
   ```

2. **Créer la base de données** via phpMyAdmin ou en ligne de commande :
   ```bash
   # Option 1 : Importer le schéma initial
   mysql -u root < sql/schema.sql

   # Option 2 : Si la base existe déjà, appliquer les mises à jour
   mysql -u root < sql/schema_update.sql
   ```
   Ou via phpMyAdmin (`http://localhost/phpmyadmin`) → onglet **Importer** → sélectionner `sql/schema.sql`

3. **Démarrer les services** XAMPP :
   - Lancer Apache
   - Lancer MySQL

4. **Accéder au site** :
   ```
   http://localhost/Projet_web/tracabilite-agricole/
   ```

## 🚀 Déploiement en production

La constante `SITE_URL` dans `config/db.php` détecte automatiquement :
- Le **protocole** (`http` ou `https`)
- Le **domaine** du serveur
- Le **chemin** de l'application

**Aucune modification de code n'est nécessaire lors du déploiement.** Il suffit de :

1. Transférer les fichiers sur le serveur (FTP, SSH, Git)
2. Créer la base de données et importer `sql/schema.sql` + `sql/schema_update.sql`
3. Modifier les identifiants de connexion dans `config/db.php` :
   ```php
   $conn = mysqli_connect("votre_hote", "votre_user", "votre_mdp", "tracabilite-agricole-db");
   ```

Les QR codes générés encoderont automatiquement la bonne URL (ex: `https://votredomaine.com/pages/consulter_produit.php?code=PROD_xxx`).

## 📱 QR Codes

Chaque produit possède un QR code qui encode une **URL complète** pointant vers sa page de traçabilité. Quand un consommateur scanne ce QR code avec son téléphone :

1. Le navigateur s'ouvre automatiquement
2. La page affiche toutes les informations du produit :
   - Fiche d'identité (nom, origine, producteur, catégorie, péremption)
   - Timeline complète du parcours (récolte → transport → ... → vente)
   - Avis des consommateurs
   - Formulaire pour laisser un avis

## 🔐 Sécurité

- ✅ **Requêtes préparées** (mysqli) sur toutes les actions — protection contre les injections SQL
- ✅ **Hashage bcrypt** des mots de passe (`password_hash` + `password_verify`)
- ✅ **Vérification de session** sur les pages protégées (`verifier_connexion()`)
- ✅ **Vérification de propriété** — un producteur ne peut modifier/supprimer que ses propres produits
- ✅ **Vérification POST** — toutes les actions vérifient la méthode HTTP avant de traiter
- ✅ **Échappement HTML** — `htmlspecialchars()` sur tous les affichages pour éviter le XSS

## ⚠️ Problèmes fréquents et solutions

### 1. Erreur lors de la suppression d'un produit (Foreign Key Constraint)
**Symptôme :** Erreur `Cannot delete or update a parent row: a foreign key constraint fails` lors de la tentative de suppression d'un produit.
**Cause :** La base de données a été créée sans l'option `ON DELETE CASCADE` pour les tables liées (`avis` et `etapes_tracabilite`).
**Solution :** Exécuter ces requêtes SQL dans phpMyAdmin pour recréer les liaisons correctement :
```sql
ALTER TABLE `avis` DROP FOREIGN KEY `avis_ibfk_1`;
ALTER TABLE `avis` ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits`(`id`) ON DELETE CASCADE;

ALTER TABLE `etapes_tracabilite` DROP FOREIGN KEY `etapes_tracabilite_ibfk_1`;
ALTER TABLE `etapes_tracabilite` ADD CONSTRAINT `etapes_tracabilite_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits`(`id`) ON DELETE CASCADE;
```

### 2. Erreur 500 sur InfinityFree (mysqlnd manquant)
**Symptôme :** Erreur fatale (souvent 500) sur certaines pages (notamment la suppression de produit ou la connexion) lors de l'hébergement sur InfinityFree.
**Cause :** La fonction `mysqli_stmt_get_result()` nécessite le driver PHP `mysqlnd` qui n'est pas activé par défaut sur certains hébergements gratuits comme InfinityFree.
**Solution :** Les requêtes ont été réécrites pour utiliser `mysqli_stmt_store_result()` et `mysqli_stmt_bind_result()` ou `mysqli_stmt_num_rows()`, qui sont compatibles nativement partout.

## 👥 Équipe
Projet réalisé dans le cadre d'un projet web universitaire.

- **Repository** : [github.com/Dibangoup/tracabilite-agricole](https://github.com/Dibangoup/tracabilite-agricole)

## 📄 Licence

Ce projet est à usage éducatif.
