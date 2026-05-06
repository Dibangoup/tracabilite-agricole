<?php
// =============================================
// Génération de QR Code pour un produit
// =============================================
// Ce script génère un QR Code contenant le lien de consultation
// d'un produit via son code unique. Le consommateur peut scanner
// le QR Code pour accéder directement à la page de traçabilité.

// Inclut la connexion à la base de données ($conn)
require_once '../config/db.php';

// Vérifie que le code unique du produit est bien passé en paramètre GET
if (!isset($_GET['code']) || empty($_GET['code'])) {
    die("❌ Erreur : aucun code produit spécifié.");
}

// Récupère le code unique du produit depuis l'URL
$code_unique = $_GET['code'];

// Vérifie que le produit existe dans la base de données (requête préparée)
$stmt = mysqli_prepare($conn, "SELECT id, nom, code_unique FROM produits WHERE code_unique = ?");
mysqli_stmt_bind_param($stmt, "s", $code_unique);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produit = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Si le produit n'existe pas, afficher une erreur
if (!$produit) {
    die("❌ Erreur : aucun produit trouvé avec le code « $code_unique ».");
}

// =============================================
// Construction de l'URL de consultation du produit
// =============================================
// Détecte automatiquement le protocole (http ou https) et le nom du serveur
$protocole = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$hote = $_SERVER['HTTP_HOST'];

// URL complète vers la page de consultation publique du produit
$url_consultation = $protocole . "://" . $hote . "/Projet_web/tracabilite-agricole/pages/consulter_produit.php?code=" . urlencode($produit['code_unique']);

// =============================================
// Génération du QR Code via l'API gratuite goqr.me
// =============================================
// Taille du QR Code en pixels (largeur x hauteur)
$taille = isset($_GET['taille']) ? intval($_GET['taille']) : 300;

// URL de l'API de génération de QR Code (service gratuit, sans clé API)
$url_qrcode = "https://api.qrserver.com/v1/create-qr-code/?"
    . "size=" . $taille . "x" . $taille
    . "&data=" . urlencode($url_consultation)
    . "&format=png"
    . "&margin=10";

// =============================================
// Mode d'affichage : image seule ou page HTML complète
// =============================================
if (isset($_GET['format']) && $_GET['format'] === 'image') {
    // Mode image : redirige directement vers l'image QR Code (pour intégration <img>)
    header("Location: " . $url_qrcode);
    exit();
}

// Mode page HTML : affiche le QR Code dans une page avec les infos du produit
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code — <?php echo htmlspecialchars($produit['nom']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .qr-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }
        .qr-container h2 {
            color: #2d6a4f;
            margin-bottom: 5px;
        }
        .qr-container .code {
            color: #888;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .qr-container img {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .qr-container p {
            color: #555;
            font-size: 14px;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 24px;
            background-color: #2d6a4f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.3s;
        }
        .btn:hover {
            background-color: #1b4332;
        }
        .btn-secondary {
            background-color: #6c757d;
            margin-left: 8px;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <h2>🌾 <?php echo htmlspecialchars($produit['nom']); ?></h2>
        <p class="code">Code : <strong><?php echo htmlspecialchars($produit['code_unique']); ?></strong></p>

        <!-- Image du QR Code générée par l'API -->
        <img src="<?php echo $url_qrcode; ?>" 
             alt="QR Code du produit <?php echo htmlspecialchars($produit['nom']); ?>"
             width="<?php echo $taille; ?>" 
             height="<?php echo $taille; ?>">

        <p>📱 Scannez ce QR Code pour consulter<br>la traçabilité complète de ce produit.</p>

        <!-- Bouton pour télécharger le QR Code -->
        <a href="<?php echo $url_qrcode . '&download=1'; ?>" class="btn" download="qrcode_<?php echo $produit['code_unique']; ?>.png">
            📥 Télécharger le QR Code
        </a>
        <!-- Bouton retour -->
        <a href="../pages/consulter_produit.php?code=<?php echo urlencode($produit['code_unique']); ?>" class="btn btn-secondary">
            🔙 Voir le produit
        </a>
    </div>
</body>
</html>
