<?php
session_start();
require '../includes/db.php';

// Vérifier que l'admin est connecté, sinon rediriger vers login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirm_mdp = $_POST['confirm_mdp'] ?? '';

    if (!$ancien_mdp || !$nouveau_mdp || !$confirm_mdp) {
        $message = "Tous les champs sont obligatoires.";
    } elseif ($nouveau_mdp !== $confirm_mdp) {
        $message = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
    } else {
       
        $stmt = $pdo->prepare('SELECT * FROM "Admin" WHERE id = :id');
        $stmt->execute(['id' => $_SESSION['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            die('Admin non trouvé.');
        }

       
        if (!password_verify($ancien_mdp, $admin['password'])) {
            $message = "L'ancien mot de passe est incorrect.";
        } else {
          
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE "Admin" SET password = :password, doit_changer_mdp = FALSE WHERE id = :id');
            $update->execute([
                'password' => $hash,
                'id' => $_SESSION['admin_id']
            ]);

            $message = "✅ Mot de passe modifié avec succès ! Vous pouvez maintenant vous connecter.";
        
            session_destroy();
            header('Refresh:2; url=login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Changer le mot de passe</title> 
  <link rel="stylesheet" href="../Public/changer_motdepasse.css" />
</head>
<body>
  <div class="reset-card">
    <h2>Changer le mot de passe</h2>
    <?php if ($message): ?>
      <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="input-group">
        <label>Ancien mot de passe :</label>
        <input type="password" name="ancien_mdp" required />
      </div>

      <div class="input-group">
        <label>Nouveau mot de passe :</label>
        <input type="password" name="nouveau_mdp" required />
      </div>

      <div class="input-group">
        <label>Confirmer le nouveau mot de passe :</label>
        <input type="password" name="confirm_mdp" required />
      </div>

      <button type="submit">Modifier le mot de passe</button>
    </form>
  </div>
</body>

</html>
