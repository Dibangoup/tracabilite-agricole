<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = intval($_POST['produit_id']);
    $etape = $_POST['etape'];
    $lieu = $_POST['lieu'];
    $description = $_POST['description'];
    $acteur_id = get_user_id();
    $role = get_user_role();

    $date_peremption = null;
    if ($role === 'transformateur' && !empty($_POST['date_peremption'])) {
        $date_peremption = $_POST['date_peremption'];
    }

    $date_etape = !empty($_POST['date_etape']) ? $_POST['date_etape'] : date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($conn, "INSERT INTO etapes_tracabilite (produit_id, acteur_id, etape, description, lieu, date_etape, date_peremption) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisssss", $produit_id, $acteur_id, $etape, $description, $lieu, $date_etape, $date_peremption);

    if (mysqli_stmt_execute($stmt)) {
        // Mettre à jour la date de péremption du produit s'il y a lieu
        if ($date_peremption) {
            $update = mysqli_prepare($conn, "UPDATE produits SET date_peremption = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "si", $date_peremption, $produit_id);
            mysqli_stmt_execute($update);
        }
        
        header("Location: ../pages/produit.php?id=" . $produit_id . "&succes=1");
    } else {
        echo "Erreur : " . mysqli_stmt_error($stmt);
    }
}
?>
