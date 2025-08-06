<?php 
require_once '../includes/db.php';
$formAccess = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cin'])) {
    $cin = trim($_POST['cin']);
    $stmt = $pdo->prepare('SELECT * FROM "ListeCINAutorises" WHERE cin_cne = :cin_cne');
    $stmt->execute(['cin_cne' => $cin]);
    $result = $stmt->fetch();

    if ($result) {
    
        header("Location: formulaire.php?cin=" . urlencode($cin));
        exit();
    } else {
      
        $message = "❌ CIN/CNE non autorisé. Veuillez contacter l'administration.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Accueil - Préinscription</title>
    <link rel="stylesheet" href="../Public/homee.css" />
  
</head>
<body>

    <div class="navbar">
        <div class="left">
            <img src="../assets/esi.png" alt="logo" />
        </div>
        <div class="links">
            <a href="#about-ecole">À propos de l’école</a>
            <a href="#instructions">Préinscription</a>
        </div>
    </div>

    <header>
        <h1>Bienvenue sur la plateforme de préinscription</h1>
        <p>École des Sciences de l’Information</p>
    </header>

    <main>
        <section id="about-ecole" class="about">
            <h2>À propos de l’école</h2>
            <p>
                Fondée en 1975, l’École des Sciences de l’Information (ESI) de Rabat est une grande école d’ingénieurs publiques sous la tutelle du Haut‑commissariat au Plan.
            </p>
            <p>
                Elle forme des ingénieurs dès Bac+5 jusqu’au doctorat (SIT, SIQ, SIL, SID). Admission via classes prépa, concours national, ou licence.
            </p>
            <a href="http://www.esi.ac.ma" target="_blank" class="btn-link">Voir plus</a>
        </section>

       <section id="instructions" class="instructions">
    <h3>Instructions pour remplir le formulaire de préinscription :</h3>
    <ul>
        <li>Préparez votre CIN/CNE et vos documents (PDF ou images).</li>
        <li>Choisissez la formation : <strong>Cycle d’Ingénieur</strong>, <strong>Master</strong>, <strong>Doctorat</strong>.</li>
        <li>Remplissez soigneusement toutes les informations demandées.</li>
        <li>Relisez avant de valider.</li>
        <li>Pour accéder au formulaire, cliquez <a href="#" id="open-modal" style="color: #7a5eea; font-weight: bold;">ici</a>.</li>

        <?php if ($formAccess): ?>
            <li class="success">✅ CIN/CNE autorisé. Vous pouvez continuer l'inscription.</li>
        <?php elseif (!empty($message)): ?>
            <li class="error"><?= htmlspecialchars($message); ?></li>
        <?php endif; ?>
    </ul>
</section>

<div id="modal-cin" <?= !empty($message) ? 'class="active"' : '' ?> style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div id="modal-content" style="background:#fff; padding:20px; border-radius:8px; max-width:400px; width:90%; box-shadow:0 0 15px rgba(0,0,0,0.3); position:relative;">
        <span id="modal-close" style="position:absolute; top:8px; right:12px; font-size:1.5rem; font-weight:bold; color:#333; cursor:pointer;">&times;</span>
        <h3>Vérifiez votre CIN/CNE</h3>
        <?php if (!empty($message)): ?>
            <div class="error-message" style="color:red; margin-bottom:10px; font-weight:bold;"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="cin">Entrez votre CIN/CNE :</label>
            <input type="text" name="cin" id="cin" required autofocus style="width:100%; padding:8px; margin-top:5px; margin-bottom:15px; font-size:1rem;">
            <button type="submit" style="padding:10px; background-color:#0057a7; color:#fff; border:none; cursor:pointer; font-weight:bold; width:100%;">Vérifier</button>
        </form>
    </div>
</div>

    </main>

    <script src="../Public/cin.js"></script>

</body>
</html>
