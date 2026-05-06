<?php
// Démarre la session pour récupérer l'utilisateur connecté
session_start();

// Inclut la connexion à la base de données ($conn)
require_once '../config/db.php';

// Inclut les fonctions utilitaires
require_once '../includes/functions.php';

// Vérifie que l'utilisateur est bien connecté
verifier_connexion();

// Vérifie que la requête HTTP est bien de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère l'identifiant du produit concerné depuis le formulaire
    $produit_id = $_POST['produit_id'];

    // Récupère le nom de l'étape de traçabilité (valeurs possibles : recolte, transport, stockage, transformation, distribution)
    $etape = $_POST['etape'];

    // Récupère la description détaillée de l'étape
    $description = $_POST['description'];

    // Récupère le lieu où se déroule cette étape
    $lieu = $_POST['lieu'];

    // Récupère l'ID de l'utilisateur connecté (l'acteur qui enregistre cette étape)
    $acteur_id = get_user_id();

    // Préparation d'une requête SQL sécurisée pour insérer une nouvelle étape
    // NOW() génère automatiquement la date et l'heure actuelles pour le champ "date_etape"
    $stmt = mysqli_prepare($conn, "INSERT INTO etapes_tracabilite (produit_id, acteur_id, etape, description, lieu, date_etape) VALUES (?, ?, ?, ?, ?, NOW())");

    // Lie les variables aux paramètres ("iisss" = 2 integers + 3 strings)
    mysqli_stmt_bind_param($stmt, "iisss", $produit_id, $acteur_id, $etape, $description, $lieu);

    // Exécute la requête d'insertion
    if (mysqli_stmt_execute($stmt)) {
        // Redirige vers la page de détail du produit concerné
        header("Location: ../pages/produit.php?id=" . $produit_id);
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'étape: " . mysqli_stmt_error($stmt);
    }

    // Ferme la requête préparée
    mysqli_stmt_close($stmt);

}
?>
