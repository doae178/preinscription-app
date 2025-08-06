<?php
// Point d'entrée principal de l'application
// Rediriger vers la page d'accueil étudiant

// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Si admin connecté, rediriger vers le dashboard admin
    header('Location: Admin/dashboard.php');
    exit();
} else {
    // Sinon, rediriger vers la page d'accueil étudiant
    header('Location: Etudiant/home.php');
    exit();
}
?> 