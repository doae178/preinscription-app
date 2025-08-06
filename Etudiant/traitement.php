<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function getPost($key, $default = null) {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    
    $nom = getPost('nom');
    $prenom = getPost('prenom');
    $date_naissance = getPost('date_naissance');
    $email = getPost('email');
    $nom_arabe = getPost('nom_arabe');
    $prenom_arabe = getPost('prenom_arabe');
    $sexe = getPost('sexe');
    $telephone = getPost('telephone');
    $adresse = getPost('adresse');
    $annee_bac = getPost('annee_obtention_bac');
    $serie_bac = getPost('serie_bac');
    $mention_bac = getPost('mention_bac');
    $lieu_bac = getPost('lieu_obtention_bac');
    $id_formation = getPost('formation');  
    $id_filiere = getPost('filiere');
    $nationalite = getPost('nationalite'); 

    
    $handicape = getPost('handicape');
    $type_handicap = getPost('type_handicap');
    $fonctionnaire = getPost('fonctionnaire');
    $type_fonctionnaire = getPost('type_fonctionnaire');

   $cin = getPost('cin');


    $obligatoires = [
        'nom' => $nom,
        'prenom' => $prenom,
        'date_naissance' => $date_naissance,
        'email' => $email,
        'id_formation' => $id_formation,
        'id_filiere' => $id_filiere,
        'annee_obtention_bac' => $annee_bac,
        'sexe' => $sexe,
        'nationalite' => $nationalite,
    ];

    foreach ($obligatoires as $key => $val) {
        if (empty($val)) {
            die("Erreur : Le champ obligatoire '$key' est manquant ou vide.");
        }
    }

    $stmtMode = $pdo->prepare('SELECT id_mode FROM "Filiere" WHERE id_filiere = ?');
    $stmtMode->execute([$id_filiere]);
    $id_mode = $stmtMode->fetchColumn();

    if (!$id_mode) {
        die("Erreur : Impossible de récupérer l'id_mode pour la filière sélectionnée.");
    }            

   
    $fichiers_attendus = ['photo', 'BAC', 'cin_recto', 'cin_verso'];
    foreach ($fichiers_attendus as $f) {
        if (!isset($_FILES[$f]) || $_FILES[$f]['error'] !== 0) {
            die("Erreur : Le fichier '$f' doit être uploadé correctement.");
        }
    }

  
    $photo_data = file_get_contents($_FILES['photo']['tmp_name']);
    $bac_data = file_get_contents($_FILES['BAC']['tmp_name']);
    $cin_recto_data = file_get_contents($_FILES['cin_recto']['tmp_name']);
    $cin_verso_data = file_get_contents($_FILES['cin_verso']['tmp_name']);

    try {
        $pdo->beginTransaction();

     

       
        $stmt1 = $pdo->prepare('INSERT INTO "Etudiant" (nom, prenom, date_naissance, cin, email) 
                               VALUES (:nom, :prenom, :date_naissance, :cin, :email) RETURNING id_etudiant');
        $stmt1->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':date_naissance' => $date_naissance,
            ':cin' => $cin,
            ':email' => $email,
        ]);
        $id_etudiant = $stmt1->fetchColumn();

       
        $stmt2 = $pdo->prepare('INSERT INTO "DossierInscription" (
            id_etudiant, nom, prenom, nom_arabe, prenom_arabe, email, sexe, date_naissance,
            telephone, adresse, annee_obtention_bac, serie_bac, mention_bac, lieu_obtention_bac,
            id_formation, id_filiere, id_mode, photo, "CIN", "CIN_verso", "BAC", nationalite,
            handicape, type_handicap, fonctionnaire, type_fonctionnaire
        ) VALUES (
            :id_etudiant, :nom, :prenom, :nom_arabe, :prenom_arabe, :email, :sexe, :date_naissance,
            :telephone, :adresse, :annee_bac, :serie_bac, :mention_bac, :lieu_bac,
            :id_formation, :id_filiere, :id_mode, :photo, :cin_recto, :cin_verso, :bac, :nationalite,
            :handicape, :type_handicap, :fonctionnaire, :type_fonctionnaire
        )');

        $stmt2->bindParam(':id_etudiant', $id_etudiant);
        $stmt2->bindParam(':nom', $nom);
        $stmt2->bindParam(':prenom', $prenom);
        $stmt2->bindParam(':nom_arabe', $nom_arabe);
        $stmt2->bindParam(':prenom_arabe', $prenom_arabe);
        $stmt2->bindParam(':email', $email);
        $stmt2->bindParam(':sexe', $sexe);
        $stmt2->bindParam(':date_naissance', $date_naissance);
        $stmt2->bindParam(':telephone', $telephone);
        $stmt2->bindParam(':adresse', $adresse);
        $stmt2->bindParam(':annee_bac', $annee_bac);
        $stmt2->bindParam(':serie_bac', $serie_bac);
        $stmt2->bindParam(':mention_bac', $mention_bac);
        $stmt2->bindParam(':lieu_bac', $lieu_bac);
        $stmt2->bindParam(':id_formation', $id_formation);
        $stmt2->bindParam(':id_filiere', $id_filiere);
        $stmt2->bindParam(':id_mode', $id_mode);
        $stmt2->bindParam(':photo', $photo_data, PDO::PARAM_LOB);
        $stmt2->bindParam(':cin_recto', $cin_recto_data, PDO::PARAM_LOB);
        $stmt2->bindParam(':cin_verso', $cin_verso_data, PDO::PARAM_LOB);
        $stmt2->bindParam(':bac', $bac_data, PDO::PARAM_LOB);
        $stmt2->bindParam(':nationalite', $nationalite);

        $stmt2->bindParam(':handicape', $handicape);
        $stmt2->bindParam(':type_handicap', $type_handicap);
        $stmt2->bindParam(':fonctionnaire', $fonctionnaire);
        $stmt2->bindParam(':type_fonctionnaire', $type_fonctionnaire);

        $stmt2->execute();

        $pdo->commit();

     header('Location: confirmation.php?success=1');
exit;


    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "❌ Erreur lors de l'insertion : " . $e->getMessage();
    }

} else {
    echo "Méthode non autorisée.";
}
