<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $categorie = $_POST['categorie'];
    $type = $_POST['type'];
    $quantite = $_POST['quantite'];
    $origine = $_POST['origine'];
    $description = $_POST['description'] ?? null;
    $date_peremption = !empty($_POST['date_peremption']) ? $_POST['date_peremption'] : null;

    $producteur_id = get_user_id();
    $code = uniqid("PROD_");

    // 1. Insérer le produit
    $stmt = mysqli_prepare($conn, "INSERT INTO produits (nom, categorie, type, quantite, origine, description, date_peremption, code_unique, producteur_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Binding: 8 strings + 1 integer = ssssssssi
    mysqli_stmt_bind_param($stmt, "ssssssssi", $nom, $categorie, $type, $quantite, $origine, $description, $date_peremption, $code, $producteur_id);

    if (mysqli_stmt_execute($stmt)) {
        $produit_id = mysqli_insert_id($conn);
        
        // 2. Créer automatiquement la première étape (récolte)
        $etape = 'recolte';
        $desc_etape = "Enregistrement initial du produit. Quantité: " . $quantite;
        $date_recolte = date('Y-m-d H:i:s');
        
        $stmt_etape = mysqli_prepare($conn, "INSERT INTO etapes_tracabilite (produit_id, acteur_id, etape, description, lieu, date_etape) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_etape, "iissss", $produit_id, $producteur_id, $etape, $desc_etape, $origine, $date_recolte);
        mysqli_stmt_execute($stmt_etape);
        mysqli_stmt_close($stmt_etape);

        header("Location: ../pages/dashboard.php?succes=1");
        exit();
    } else {
        echo "Erreur lors de l'insertion: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}
?>
