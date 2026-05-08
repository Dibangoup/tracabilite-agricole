<?php 
  // ============================================================
  // CONFIGURATION DE LA BASE DE DONNÉES
  // ============================================================
  // Détection automatique de l'environnement :
  // - localhost / 127.0.0.1 → config XAMPP
  // - sinon (InfinityFree, etc.) → config production
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $production = (strpos($host, 'localhost') === false && strpos($host, '127.0.0.1') === false);

  if ($production) {
    // --- Coordonnées InfinityFree ---
    define('DB_HOST', 'sql113.infinityfree.com');
    define('DB_NAME', 'if0_41856546_tracabiliteagricoledb');
    define('DB_USER', 'if0_41856546');
    define('DB_PASS', 'nGw7XFHUMve5G1O');
  } else {
    // --- Coordonnées locales (XAMPP) ---
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'tracabilite-agricole-db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
  }

  // Définir le fuseau horaire par défaut (Côte d'Ivoire)
  date_default_timezone_set('Africa/Abidjan');

  // Connexion à la base de données MySQL
  $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  // Vérification si la connexion a échoué
  if (!$conn) {
    die("❌ Erreur de connexion : " . mysqli_connect_error());
  }

  // Définir l'encodage UTF-8 pour supporter les caractères spéciaux (accents, etc.)
  mysqli_set_charset($conn, "utf8mb4");

  // URL de base du site (utilisée pour les QR codes)
  // Détection automatique : fonctionne en local (XAMPP) ET en production sans modification
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $doc_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
  $project_root = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
  $base_path = str_replace($doc_root, '', $project_root);
  define('SITE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_path);
?>
