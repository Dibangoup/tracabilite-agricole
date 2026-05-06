<?php

/**
 * Vérifie si l'utilisateur est connecté.
 * Si non connecté, redirige vers la page de connexion et arrête le script.
 * À appeler au début de chaque page protégée (dashboard, ajouter_produit, etc.)
 */
function verifier_connexion() {
    // Démarre la session si elle n'est pas déjà active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérifie si la variable de session 'user' existe
    if (!isset($_SESSION['user'])) {
        // Si l'utilisateur n'est pas connecté, rediriger vers le formulaire de connexion
        header("Location: ../auth/login.php");
        exit(); // Arrêter l'exécution du script après la redirection
    }
}

/**
 * Récupère l'ID de l'utilisateur actuellement connecté.
 * @return int|null L'ID de l'utilisateur ou null si non connecté
 */
function get_user_id() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
}

/**
 * Récupère le rôle de l'utilisateur actuellement connecté.
 * @return string|null Le rôle (producteur, cooperative, transporteur, distributeur, consommateur)
 */
function get_user_role() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;
}

?>
