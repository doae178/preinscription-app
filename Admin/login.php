<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM "Admin" WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_nom'] = $admin['prenom'] . ' ' . $admin['nom'];

        
        if (!empty($admin['doit_changer_mdp']) && $admin['doit_changer_mdp'] == true) {
            header('Location: changer_motdepasse.php');
            exit;
        } else {
            header('Location: dashboard.php');
            exit;
        }
    } else {
        $error = "Email ou mot de passe incorrect";
        header("Location: login.html?error=" . urlencode($error));
        exit;
    }
} else {
    header('Location: login.html');
    exit;
}
