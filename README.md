# 🌾 Traçabilité Agricole

Plateforme web de traçabilité des produits agricoles permettant de suivre le parcours d'un produit de la récolte jusqu'à la vente, en passant par le transport, le stockage et la transformation.

## 📋 Description du projet

Ce projet permet :
- **Aux producteurs** : d'enregistrer leurs produits et de suivre chaque étape de la chaîne de traçabilité
- **Aux coopératives, transporteurs** : de documenter leurs interventions sur les produits
- **Aux consommateurs** : de consulter l'historique complet d'un produit via son code unique et de laisser des avis

Chaque produit dispose d'un **code unique** permettant de retracer l'ensemble de son parcours (récolte → transport → stockage → transformation → vente).

## 🛠️ Technologies utilisées

- **Backend** : PHP 8+ (procédural avec mysqli)
- **Base de données** : MySQL / MariaDB
- **Serveur local** : XAMPP (Apache + MySQL)
- **Sécurité** : Requêtes préparées (anti injection SQL), hashage bcrypt des mots de passe

## 📁 Structure du projet

```
tracabilite-agricole/
├── index.php                        # Point d'entrée du site
├── config/
│   └── db.php                       # Connexion à la base de données MySQL
├── includes/
│   ├── header.php                   # En-tête commun (navbar)
│   ├── footer.php                   # Pied de page commun
│   └── functions.php                # Fonctions utilitaires (vérification session, rôles)
├── auth/
│   └── login.php                    # Page de formulaire de connexion
├── actions/
│   ├── login.php                    # Traitement de la connexion utilisateur
│   ├── logout.php                   # Déconnexion (destruction de session)
│   ├── register.php                 # Inscription (hash bcrypt + email unique)
│   ├── ajouter_produit.php          # Ajout d'un nouveau produit
│   ├── modifier_produit.php         # Modification d'un produit existant
│   ├── supprimer_produit.php        # Suppression d'un produit (cascade)
│   ├── ajouter_etape.php            # Ajout d'une étape de traçabilité
│   └── ajouter_avis.php             # Ajout d'un avis consommateur
├── pages/
│   ├── accueil.php                  # Page d'accueil publique
│   ├── dashboard.php                # Tableau de bord du producteur
│   ├── ajouter_produit.php          # Formulaire d'ajout de produit
│   ├── produit.php                  # Détail d'un produit + étapes
│   └── consulter_produit.php        # Consultation publique d'un produit (par code)
└── assets/
    ├── css/                         # Feuilles de style
    ├── js/                          # Scripts JavaScript
    └── images/                      # Images du site
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
| `role` | ENUM | `producteur`, `cooperative`, `transporteur`, `consommateur` |
| `created_at` | TIMESTAMP | Date de création du compte |

### Table `produits`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT (PK, AI) | Identifiant unique |
| `nom` | VARCHAR(100) | Nom du produit |
| `type` | VARCHAR(100) | Type (fruit, légume, céréale...) |
| `origine` | VARCHAR(150) | Lieu d'origine |
| `code_unique` | VARCHAR(100) | Code de traçabilité unique (ex: PROD_6839a1b2...) |
| `producteur_id` | INT (FK) | Référence vers `users.id` |
| `created_at` | TIMESTAMP | Date d'ajout |

### Table `etapes_tracabilite`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT (PK, AI) | Identifiant unique |
| `produit_id` | INT (FK) | Référence vers `produits.id` |
| `acteur_id` | INT (FK) | Référence vers `users.id` (qui a effectué l'étape) |
| `etape` | ENUM | `recolte`, `transport`, `stockage`, `transformation`, `vente` |
| `description` | TEXT | Description détaillée |
| `lieu` | VARCHAR(150) | Lieu de l'étape |
| `date_etape` | DATETIME | Date de l'étape |
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

## ⚙️ Installation

### Prérequis
- [XAMPP](https://www.apachefriends.org/) installé (Apache + MySQL)

### Étapes

1. **Cloner le projet** dans le dossier `htdocs` de XAMPP :
   ```bash
   cd C:\xampp\htdocs\Projet_web
   git clone https://github.com/Dibangoup/tracabilite-agricole.git
   ```

2. **Créer la base de données** via phpMyAdmin :
   - Accéder à `http://localhost/phpmyadmin`
   - Créer une base nommée `tracabilite-agricole-db`
   - Créer les 4 tables (`users`, `produits`, `etapes_tracabilite`, `avis`) selon la structure ci-dessus

3. **Démarrer les services** XAMPP :
   - Lancer Apache
   - Lancer MySQL

4. **Accéder au site** :
   ```
   http://localhost/Projet_web/tracabilite-agricole/
   ```

## 🔐 Sécurité

- ✅ **Requêtes préparées** (mysqli) sur toutes les actions — protection contre les injections SQL
- ✅ **Hashage bcrypt** des mots de passe (`password_hash` + `password_verify`)
- ✅ **Vérification de session** sur les pages protégées (`verifier_connexion()`)
- ✅ **Vérification de propriété** — un producteur ne peut modifier/supprimer que ses propres produits
- ✅ **Vérification POST** — toutes les actions vérifient la méthode HTTP avant de traiter

## 👥 Équipe

Projet réalisé dans le cadre d'un projet web universitaire.

## 📄 Licence

Ce projet est à usage éducatif.
