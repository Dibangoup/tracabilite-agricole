<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Vérifier email unique
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    if (mysqli_stmt_num_rows($check) > 0) {
        mysqli_stmt_close($check);
        header("Location: ../auth/register.php?erreur=email_existe");
        exit();
    }
    mysqli_stmt_close($check);
    
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nom, $email, $hash, $role);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../auth/login.php?inscription=succes");
    } else {
        echo "Erreur d'inscription : " . mysqli_stmt_error($stmt);
    }
}
?>
