<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $email_esc = mysqli_real_escape_string($conn, $email);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_esc'");
    $user = mysqli_fetch_assoc($result);
    
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user'] = $user;
        header("Location: ../pages/dashboard.php");
        exit();
    } else {
        header("Location: ../auth/login.php?erreur=1");
        exit();
    }
}
?>
