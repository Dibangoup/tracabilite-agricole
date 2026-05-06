<?php
// Démarre la session pour récupérer l'utilisateur connecté
session_start();

// Inclut la connexion à la base de données ($conn)
require_once '../config/db.php';

// Inclut les fonctions utilitaires (vérification de connexion, etc.)
require_once '../includes/functions.php';

// Vérifie que l'utilisateur est bien connecté, sinon redirige vers login
verifier_connexion();

// Vérifie que la requête HTTP est bien de type POST (soumission de formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des données envoyées par le formulaire
    $nom = $_POST['nom'];           // Nom du produit
    $type = $_POST['type'];         // Type du produit (fruit, légume, céréale...)
    $origine = $_POST['origine'];   // Lieu d'origine du produit

    // Récupère l'ID de l'utilisateur connecté (le producteur qui ajoute le produit)
    $producteur_id = get_user_id();

    // Génération d'un identifiant unique pour le produit avec le préfixe "PROD_"
    // Exemple de résultat : "PROD_6839a1b2c3d4e"
    $code = uniqid("PROD_");

    // Préparation d'une requête SQL sécurisée (requête préparée pour éviter les injections SQL)
    // On insère le nom, le type, l'origine, le code unique et l'ID du producteur
    $stmt = mysqli_prepare($conn, "INSERT INTO produits (nom, type, origine, code_unique, producteur_id) VALUES (?, ?, ?, ?, ?)");

    // Lie les variables aux paramètres "?" ("ssssi" = 4 strings + 1 integer)
    mysqli_stmt_bind_param($stmt, "ssssi", $nom, $type, $origine, $code, $producteur_id);

    // Exécute la requête et vérifie le succès
    if (mysqli_stmt_execute($stmt)) {

        // Si l'insertion a réussi, redirige vers le tableau de bord
        header("Location: ../pages/dashboard.php");
        exit();
    } else {
        // Si l'insertion a échoué, affiche le message d'erreur MySQL
        echo "Erreur lors de l'insertion: " . mysqli_stmt_error($stmt);
    }

    // Ferme la requête préparée
    mysqli_stmt_close($stmt);

}
?>