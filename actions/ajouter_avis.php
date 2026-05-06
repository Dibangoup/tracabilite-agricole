<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = intval($_POST['produit_id']);
    $nom = $_POST['nom'];
    $note = intval($_POST['note']);
    $commentaire = $_POST['commentaire'];
    $code = $_POST['code'];

    $stmt = mysqli_prepare($conn, "INSERT INTO avis (produit_id, nom_consommateur, note, commentaire) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isis", $produit_id, $nom, $note, $commentaire);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../pages/consulter_produit.php?code=" . urlencode($code) . "&avis=succes");
    } else {
        echo "Erreur d'ajout d'avis : " . mysqli_stmt_error($stmt);
    }
}
?>
