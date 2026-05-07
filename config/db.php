<?php 
  // Connexion à la base de données MySQL
  // Paramètres : hôte, utilisateur, mot de passe, nom de la base
  $conn = mysqli_connect("localhost", "root", "", "tracabilite-agricole-db");

  // Définir l'encodage UTF-8 pour supporter les caractères spéciaux (accents, etc.)
  mysqli_set_charset($conn, "utf8mb4");

  // URL de base du site (utilisée pour les QR codes)
  // Détection automatique : fonctionne en local (XAMPP) ET en production sans modification
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  // Calcule le chemin web à partir de l'emplacement physique de ce fichier
  // __DIR__ = .../tracabilite-agricole/config → on remonte d'1 niveau → .../tracabilite-agricole
  $doc_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
  $project_root = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
  $base_path = str_replace($doc_root, '', $project_root);
  define('SITE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_path);

  // Vérification si la connexion a échoué
  if(!$conn){
    // Arrêter le script et afficher l'erreur de connexion
    die("❌ Erreur de connexion : ". mysqli_connect_error());
  }
?>
