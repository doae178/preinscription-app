<?php
require_once '../includes/db.php';

$stmt = $pdo->query('SELECT id_formation, nom_formation FROM "Formation" ORDER BY id_formation');
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-keyboard/build/css/index.css" />
<script src="https://cdn.jsdelivr.net/npm/simple-keyboard/build/index.js"></script>
    <title>Choix de la Formation</title>
    <link rel="stylesheet" href="../Public/formulaire.css" />
</head>
<body>
     <div class="container">
    <h2>Formulaire de Préinscription</h2>

    <form method="post" action="traitement.php" enctype="multipart/form-data">

        <label for="formation">Formation :</label>
        <select name="formation" id="formation" required>
            <option value="">-- Sélectionnez une formation --</option>
            <?php foreach ($formations as $formation): ?>
                <option 
                    value="<?= htmlspecialchars($formation['id_formation']) ?>" 
                    data-nom="<?= htmlspecialchars($formation['nom_formation']) ?>">
                    <?= htmlspecialchars($formation['nom_formation']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="filiere">Filière :</label>
        <select name="filiere" id="filiere" disabled required>
            <option value="">-- Sélectionnez une filière --</option>
        </select>

        <div class="form-group">
            <label for="mode">Mode d’acceptation :</label>
            <select id="mode" name="mode" required>
                <option value="">-- Sélectionnez un mode --</option>
            </select>
        </div>

        <button type="button" id="btn-remplir">Remplir le formulaire</button>

        <div id="dossier-fields" style="display: none; margin-top: 20px;">
<label for="cin">CIN/CNE :</label>
<input type="text" name="cin" id="cin" value="<?= htmlspecialchars($_GET['cin'] ?? '') ?>" readonly />

            <div class="champ">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom">
            </div>
            <div class="champ">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom">
            </div>
           <div class="champ">
    <label for="nom_arabe">Nom (Arabe) :</label>
    <input type="text" id="nom_arabe" name="nom_arabe" dir="rtl" autocomplete="off" />
    <div id="keyboard-container-nom_arabe"></div>
<label for="nom_arabe">Prénom (Arabe) :</label>
    <input type="text" id="prenom_arabe" name="prenom_arabe" dir="rtl" autocomplete="off" />
    <div id="keyboard-container-nom_arabe"></div>
            </div>
            <div class="champ">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="champ">
                <label for="sexe">Sexe :</label>
                <select id="sexe" name="sexe">
                    <option value="">-- Choisir --</option>
                    <option value="Homme">Homme</option>
                    <option value="Femme">Femme</option>
                </select>
            </div>
            <div class="champ">
                <label for="date_naissance">Date de naissance :</label>
                <input type="date" id="date_naissance" name="date_naissance">
            </div>
            <div class="champ">
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone">
            </div>  
            <div class="champ">
                <label for="adresse">Adresse :</label>
                <input type="text" id="adresse" name="adresse">
            </div>
            <div class="champ">
                <label for="cin_recto">Photo CIN/CNE Recto :</label>
                <input type="file" id="cin_recto" name="cin_recto" accept="image/*" required>
            </div>
            <div class="champ">
                <label for="cin_verso">Photo CIN/CNE Verso :</label>
                <input type="file" id="cin_verso" name="cin_verso" accept="image/*" required>
            </div>
            <div class="champ">
                <label for="serie_bac">Série Bac :</label>
                <input type="text" id="serie_bac" name="serie_bac">
            </div>
            <div class="champ">
                <label for="mention_bac">Mention Bac :</label>
                <input type="text" id="mention_bac" name="mention_bac">
            </div>
            <div class="champ">
                <label for="annee_obtention_bac">Année Bac :</label>
                <input type="text" id="annee_obtention_bac" name="annee_obtention_bac">
            </div>
            <div class="champ">
                <label for="lieu_obtention_bac">Lieu obtention Bac :</label>
                <input type="text" id="lieu_obtention_bac" name="lieu_obtention_bac">
            </div>
            <div class="champ">
                <label for="photo">Photo :</label>
                <input type="file" id="photo" name="photo">
            </div>
            
            <div class="champ">
                <label for="bac">Copie du Bac :</label>
                <input type="file" id="bac" name="BAC">
            </div>

            <label for="nationalite">Nationalité :</label>
            <input type="text" id="nationalite" name="nationalite">

            <label for="handicape">Êtes-vous en situation de handicap ?</label>
            <select name="handicape" id="handicape" onchange="toggleTypeHandicap()">
                <option value="">-- Sélectionnez --</option>
                <option value="oui">Oui</option>
                <option value="non">Non</option>
            </select>

            <div id="typeHandicapContainer" style="display: none;">
                <label for="type_handicap">Type de handicap :</label>
                <input type="text" name="type_handicap" id="type_handicap">
            </div>

            <div id="fonctionnaireContainer" style="display: none;">
                <label for="fonctionnaire">Êtes-vous fonctionnaire ?</label>
                <select name="fonctionnaire" id="fonctionnaire" onchange="toggleTypeFonctionnaire()">
                    <option value="">-- Sélectionnez --</option>
                    <option value="oui">Oui</option>
                    <option value="non">Non</option>
                </select>

                <div id="typeFonctionnaireContainer" style="display:none;">
                    <label for="type_fonctionnaire">Type de fonctionnaire :</label>
                    <select name="type_fonctionnaire" id="type_fonctionnaire">
                        <option value="">-- Sélectionnez --</option>
                        <option value="étatique">Étatique</option>
                        <option value="privée">Privée</option>
                    </select>
                </div>
            </div>

            <button type="submit">Soumettre</button>
            
        </div>
    </form>
    </div>
    <script src="../Public/formulaire.js"></script>
</body>
</html>
