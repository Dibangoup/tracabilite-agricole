<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

verifier_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = get_user_id();

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Détruire la session et rediriger vers l'accueil
        session_destroy();
        header("Location: ../index.php");
        exit();
    } else {
        // En cas d'erreur de contrainte (si le ON DELETE CASCADE manque)
        die("❌ Erreur lors de la suppression : " . mysqli_stmt_error($stmt) . "<br><br><b>Astuce :</b> Si vous voyez une erreur 'foreign key constraint', cela signifie qu'il manque l'option 'ON DELETE CASCADE' sur les tables liées à l'utilisateur dans votre base de données locale.");
    }
    mysqli_stmt_close($stmt);
}
?>
