<?php
session_start();
require '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM \"Admin\" WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $newPassword = bin2hex(random_bytes(4));
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

       $update = $pdo->prepare("UPDATE \"Admin\" SET password = :password, doit_changer_mdp = TRUE WHERE email = :email");
$update->execute([
    'password' => $hashedPassword,
    'email' => $email
]);


        require '../send_mail.php';

        try {
            $mail->clearAllRecipients();
            $mail->addAddress($email);
            $mail->Subject = "🔐 Nouveau mot de passe temporaire";
            $mail->Body = "Bonjour,<br><br>Voici votre nouveau mot de passe temporaire : <b>$newPassword</b><br><br>Veuillez vous connecter rapidement et changer ce mot de passe.<br><br>Cordialement.";

            if ($mail->send()) {
                $message = "✅ Nouveau mot de passe envoyé à votre adresse e-mail.";
            } else {
                $message = "❌ Erreur lors de l'envoi du mail : " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            $message = "❌ Exception lors de l'envoi du mail : " . $e->getMessage();
        }
    } else {
        $message = "❌ Cet e-mail n'existe pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Réinitialisation du mot de passe</title>
  <link rel="stylesheet" href="../Public/forgot_password.css" />
</head>
<body>
  <div class="reset-card">
     <p><a href="login.php">← Retour </a></p>
    <h2>Réinitialiser le mot de passe</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="input-group">
        <label for="email">Votre adresse e-mail :</label><br>
        <input type="email" name="email" id="email" required />
      </div>
      <button type="submit">Envoyer le nouveau mot de passe</button>
    </form>
  </div>
</body>

</html>
