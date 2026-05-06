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

    // Récupère les données du formulaire de modification
    $produit_id = intval($_POST['produit_id']); // ID du produit à modifier
    $nom = $_POST['nom'];                       // Nouveau nom du produit
    $type = $_POST['type'];                     // Nouveau type du produit
    $origine = $_POST['origine'];               // Nouvelle origine du produit

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

    // Préparation de la requête SQL de mise à jour (UPDATE)
    $stmt = mysqli_prepare($conn, "UPDATE produits SET nom = ?, type = ?, origine = ? WHERE id = ? AND producteur_id = ?");

    // Lie les variables aux paramètres ("sssii" = 3 strings + 2 integers)
    mysqli_stmt_bind_param($stmt, "sssii", $nom, $type, $origine, $produit_id, $user_id);

    // Exécute la requête de mise à jour
    if (mysqli_stmt_execute($stmt)) {
        // Modification réussie, redirige vers la page de détail du produit
        header("Location: ../pages/produit.php?id=" . $produit_id);
        exit();
    } else {
        echo "Erreur lors de la modification: " . mysqli_stmt_error($stmt);
    }

    // Ferme la requête préparée
    mysqli_stmt_close($stmt);

}
?>
