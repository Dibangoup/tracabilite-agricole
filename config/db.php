<?php 
  // Connexion à la base de données MySQL
  // Paramètres : hôte, utilisateur, mot de passe, nom de la base
  $conn = mysqli_connect("localhost", "root", "", "tracabilite-agricole-db");

  // Définir l'encodage UTF-8 pour supporter les caractères spéciaux (accents, etc.)
  mysqli_set_charset($conn, "utf8mb4");

  // URL de base du site (utilisée pour les QR codes)
  // À modifier en production avec votre nom de domaine réel (ex: https://tracabilite.votredomaine.com)
  define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/Projet_web/tracabilite-agricole');

  // Vérification si la connexion a échoué
  if(!$conn){
    // Arrêter le script et afficher l'erreur de connexion
    die("❌ Erreur de connexion : ". mysqli_connect_error());
  }
?>
