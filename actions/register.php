<?php
// Inclut la connexion à la base de données ($conn)
require_once '../config/db.php';

// Vérifie que la requête HTTP est bien de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère les données du formulaire d'inscription
    $nom = $_POST['nom'];               // Nom complet de l'utilisateur
    $email = $_POST['email'];           // Adresse email (unique dans la table)
    $password = $_POST['password'];     // Mot de passe en clair (sera hashé)
    $role = $_POST['role'];             // Rôle : producteur, cooperative, transporteur, distributeur, consommateur

    // Vérifier d'abord si l'email existe déjà dans la base de données
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    $check_result = mysqli_stmt_get_result($check);

    // Si l'email est déjà utilisé, on redirige avec une erreur
    if (mysqli_num_rows($check_result) > 0) {
        mysqli_stmt_close($check);
        header("Location: ../auth/login.php?erreur=email_existe");
        exit();
    }
    mysqli_stmt_close($check);

    // Hache le mot de passe avec bcrypt pour le stocker de manière sécurisée
    // password_hash() génère un hash unique à chaque appel (grâce au sel aléatoire)
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Préparation de la requête SQL sécurisée pour insérer le nouvel utilisateur
    $stmt = mysqli_prepare($conn, "INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");

    // Lie les variables aux paramètres ("ssss" = 4 strings)
    mysqli_stmt_bind_param($stmt, "ssss", $nom, $email, $hash, $role);

    // Exécute la requête d'insertion
    if (mysqli_stmt_execute($stmt)) {

        // Inscription réussie, redirige vers la page de connexion avec un message de succès
        header("Location: ../auth/login.php?inscription=succes");
        exit();
    } else {
        echo "Erreur lors de l'inscription: " . mysqli_stmt_error($stmt);
    }

    // Ferme la requête préparée
    mysqli_stmt_close($stmt);

}
?>
