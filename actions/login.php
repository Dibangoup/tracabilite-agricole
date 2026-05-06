<?php
// Démarre la session pour accéder aux données de l'utilisateur connecté
session_start();

// Inclut la connexion à la base de données ($conn)
require_once '../config/db.php';

// Vérifie que la requête HTTP est bien de type POST (soumission de formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupère l'email envoyé par le formulaire
    $email = $_POST['email'];

    // Récupère le mot de passe envoyé par le formulaire
    $password = $_POST['password'];

    // Prépare une requête SQL sécurisée avec un paramètre "?" pour éviter les injections SQL
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");

    // Lie la variable $email au paramètre "?" de la requête ("s" = type string)
    mysqli_stmt_bind_param($stmt, "s", $email);

    // Exécute la requête préparée
    mysqli_stmt_execute($stmt);

    // Récupère le résultat de la requête
    $result = mysqli_stmt_get_result($stmt);

    // Extrait la première ligne du résultat sous forme de tableau associatif
    $user = mysqli_fetch_assoc($result);

    // Ferme la requête préparée pour libérer les ressources
    mysqli_stmt_close($stmt);

    // Vérifie si un utilisateur a été trouvé ET si le mot de passe correspond au hash en base
    if ($user && password_verify($password, $user['mot_de_passe'])) {

        // Stocke les informations de l'utilisateur dans la session
        $_SESSION['user'] = $user;

        // Redirige vers le tableau de bord
        header("Location: ../pages/dashboard.php");
        exit(); // Arrête le script après la redirection

    } else {
        // Si les identifiants sont incorrects, redirige vers le login avec un message d'erreur
        header("Location: ../auth/login.php?erreur=1");
        exit();
    }

} else {
    // Si quelqu'un accède directement à ce fichier sans soumettre de formulaire, rediriger
    header("Location: ../auth/login.php");
    exit();
}
?>
