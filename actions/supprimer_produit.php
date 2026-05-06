<?php
// Démarre la session pour vérifier l'utilisateur connecté
session_start();

// Inclut la connexion à la base de données ($conn)
require_once '../config/db.php';

// Inclut les fonctions utilitaires
require_once '../includes/functions.php';

// Vérifie que l'utilisateur est bien connecté
verifier_connexion();

// Vérifie que la requête HTTP est bien de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère l'ID du produit à supprimer depuis le formulaire
    $produit_id = intval($_POST['produit_id']); // Convertit en entier pour sécuriser

    // Récupère l'ID de l'utilisateur connecté
    $user_id = get_user_id();

    // Vérifie que le produit appartient bien à l'utilisateur connecté (sécurité)
    $check = mysqli_prepare($conn, "SELECT id FROM produits WHERE id = ? AND producteur_id = ?");
    mysqli_stmt_bind_param($check, "ii", $produit_id, $user_id);
    mysqli_stmt_execute($check);
    $check_result = mysqli_stmt_get_result($check);

    // Si le produit n'existe pas ou n'appartient pas à l'utilisateur, on refuse
    if (mysqli_num_rows($check_result) === 0) {
        mysqli_stmt_close($check);
        header("Location: ../pages/dashboard.php?erreur=non_autorise");
        exit();
    }
    mysqli_stmt_close($check);

    // Supprime d'abord les avis liés au produit (pour respecter les clés étrangères)
    $stmt1 = mysqli_prepare($conn, "DELETE FROM avis WHERE produit_id = ?");
    mysqli_stmt_bind_param($stmt1, "i", $produit_id);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // Supprime ensuite les étapes de traçabilité liées au produit
    $stmt2 = mysqli_prepare($conn, "DELETE FROM etapes_tracabilite WHERE produit_id = ?");
    mysqli_stmt_bind_param($stmt2, "i", $produit_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    // Enfin, supprime le produit lui-même
    $stmt3 = mysqli_prepare($conn, "DELETE FROM produits WHERE id = ? AND producteur_id = ?");
    mysqli_stmt_bind_param($stmt3, "ii", $produit_id, $user_id);

    if (mysqli_stmt_execute($stmt3)) {
        // Suppression réussie, redirige vers le tableau de bord
        header("Location: ../pages/dashboard.php?succes=produit_supprime");
        exit();
    } else {
        echo "Erreur lors de la suppression: " . mysqli_stmt_error($stmt3);
    }

    mysqli_stmt_close($stmt3);

}
?>
