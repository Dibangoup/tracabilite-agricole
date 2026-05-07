<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = intval($_POST['produit_id']);
    $user_id = get_user_id();

    // Verify ownership
    $check = mysqli_prepare($conn, "SELECT id FROM produits WHERE id = ? AND producteur_id = ?");
    mysqli_stmt_bind_param($check, "ii", $produit_id, $user_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    if (mysqli_stmt_num_rows($check) === 0) {
        mysqli_stmt_close($check);
        header("Location: ../pages/dashboard.php?erreur=non_autorise");
        exit();
    }
    mysqli_stmt_close($check);
    
    // ON DELETE CASCADE takes care of etapes and avis
    $stmt = mysqli_prepare($conn, "DELETE FROM produits WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $produit_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../pages/dashboard.php?succes=supprime");
    } else {
        echo "Erreur : " . mysqli_stmt_error($stmt);
    }
}
?>
