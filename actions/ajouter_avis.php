<?php
// Inclut la connexion à la base de données ($conn)
require_once '../config/db.php';

// Vérifie que la requête HTTP est bien de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère l'identifiant du produit sur lequel porte l'avis
    $produit_id = $_POST['produit_id'];

    // Récupère le nom du consommateur qui laisse l'avis
    $nom = $_POST['nom'];

    // Récupère la note attribuée au produit (ex: de 1 à 5)
    $note = intval($_POST['note']); // Convertit en entier pour sécuriser

    // Récupère le commentaire/texte de l'avis du consommateur
    $commentaire = $_POST['commentaire'];

    // Récupère le code unique du produit pour la redirection
    $code = $_POST['code'];

    // Préparation d'une requête SQL sécurisée pour insérer un nouvel avis
    $stmt = mysqli_prepare($conn, "INSERT INTO avis (produit_id, nom_consommateur, note, commentaire) VALUES (?, ?, ?, ?)");

    // Lie les variables aux paramètres ("isis" = int, string, int, string)
    mysqli_stmt_bind_param($stmt, "isis", $produit_id, $nom, $note, $commentaire);

    // Exécute la requête d'insertion
    if (mysqli_stmt_execute($stmt)) {
        // Redirige vers la page de consultation du produit avec son code unique
        header("Location: ../pages/consulter_produit.php?code=" . $code);
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'avis: " . mysqli_stmt_error($stmt);
    }

    // Ferme la requête préparée
    mysqli_stmt_close($stmt);

}
?>