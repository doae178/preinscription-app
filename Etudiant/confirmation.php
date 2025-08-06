<?php
if (!isset($_GET['success'])) {
    header('Location: home.php'); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Confirmation d'inscription</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .message { font-size: 1.2rem; color: green; }
        a { display: inline-block; margin-top: 20px; text-decoration: none; color: #0056b3; }
    </style>
</head>
<body>
    <div class="message">
        ✅ Votre préinscription a bien été enregistrée !
    </div>
    <a href="home.php">Retour à la page d’accueil</a>
</body>
</html>
