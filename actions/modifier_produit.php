<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = intval($_POST['produit_id']);
    $nom = $_POST['nom'];
    $type = $_POST['type'];
    $description = $_POST['description'];

    $user_id = get_user_id();

    // Verify ownership
    $check = mysqli_prepare($conn, "SELECT id FROM produits WHERE id = ? AND producteur_id = ?");
    mysqli_stmt_bind_param($check, "ii", $produit_id, $user_id);
    mysqli_stmt_execute($check);
    $res = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($res) === 0) {
        header("Location: ../pages/dashboard.php?erreur=non_autorise");
        exit();
    }
    mysqli_stmt_close($check);

    // Update
    $stmt = mysqli_prepare($conn, "UPDATE produits SET nom = ?, type = ?, description = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $nom, $type, $description, $produit_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../pages/produit.php?id=" . $produit_id);
    } else {
        echo "Erreur : " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>
