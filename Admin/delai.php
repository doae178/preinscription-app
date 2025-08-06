<?php
require_once '../includes/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $response = ['success' => false, 'message' => ''];

    if ($_POST['action'] === 'add') {
      
        $id_formation = $_POST['id_formation'] ?? null;
        $id_annee = $_POST['id_annee'] ?? null;
        $date_debut = $_POST['date_debut'] ?? null;
        $date_fin = $_POST['date_fin'] ?? null;

        if ($id_formation && $id_annee && $date_debut && $date_fin) {
            $stmt = $pdo->prepare('INSERT INTO "DelaiFormulaire" (id_formation, id_annee, date_debut, date_fin) 
                                   VALUES (:id_formation, :id_annee, :date_debut, :date_fin)');
            if ($stmt->execute([
                ':id_formation' => $id_formation,
                ':id_annee' => $id_annee,
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin,
            ])) {
                $response['success'] = true;
                $response['message'] = "Délai ajouté avec succès.";
            } else {
                $response['message'] = "Erreur lors de l'ajout.";
            }
        } else {
            $response['message'] = "Tous les champs sont requis.";
        }
        echo json_encode($response);
        exit;
    }

    if ($_POST['action'] === 'update') {
       
        $id_delai = $_POST['id_delai'] ?? null;
        $field = $_POST['field'] ?? null; 
        $value = $_POST['value'] ?? null;

        if ($id_delai && in_array($field, ['date_debut', 'date_fin']) && $value) {
            $stmt = $pdo->prepare("UPDATE \"DelaiFormulaire\" SET $field = :value WHERE id_delai = :id_delai");
            if ($stmt->execute([':value' => $value, ':id_delai' => $id_delai])) {
                $response['success'] = true;
                $response['message'] = "Date mise à jour.";
            } else {
                $response['message'] = "Erreur lors de la mise à jour.";
            }
        } else {
            $response['message'] = "Données invalides.";
        }
        echo json_encode($response);
        exit;
    }
}


if (isset($_GET['delete'])) {
    $id_delai = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM "DelaiFormulaire" WHERE id_delai = ?');
    $stmt->execute([$id_delai]);
    header("Location: delai.php");
    exit;
}


$sql = 'SELECT d.id_delai, d.date_debut, d.date_fin, d.id_annee, d.id_formation,
        f.nom_formation, a.libelle as libelle_annee
        FROM "DelaiFormulaire" d
        JOIN "Formation" f ON d.id_formation = f.id_formation
        JOIN "AnneeUniversitaire" a ON d.id_annee = a.id_annee
        ORDER BY d.date_debut DESC';
$stmt = $pdo->query($sql);
$delais = $stmt->fetchAll();


$formations = $pdo->query('SELECT id_formation, nom_formation FROM "Formation" ORDER BY nom_formation')->fetchAll();


$annees = $pdo->query('SELECT id_annee, libelle FROM "AnneeUniversitaire" ORDER BY libelle DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />

    <title>Gestion des Délais des Formulaires</title>
    <link rel="stylesheet" href="../Public/delai.css" />
</head>
<body>
     <p><a href="dashboard.php">← Retour </a></p>
    <h1>Gestion des Délais des Formulaires</h1>

    <table>
        <thead>
            <tr>
                <th>Formation</th>
                <th>Année scolaire</th>
                <th>Date début (clic pour modifier)</th>
                <th>Date fin (clic pour modifier)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="delai-table-body">
            <?php foreach ($delais as $delai): ?>
            <tr data-id="<?= $delai['id_delai'] ?>">
                <td><?= htmlspecialchars($delai['nom_formation']) ?></td>
                <td><?= htmlspecialchars($delai['libelle_annee']) ?></td>
                <td class="editable" data-field="date_debut"><?= htmlspecialchars($delai['date_debut']) ?></td>
                <td class="editable" data-field="date_fin"><?= htmlspecialchars($delai['date_fin']) ?></td>
                <td>
                    <a href="?delete=<?= (int)$delai['id_delai'] ?>" class="delete-btn" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($delais)): ?>
            <tr><td colspan="5">Aucun délai configuré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <form id="add-form">
        <h2>Ajouter un nouveau délai</h2>
        <input type="hidden" name="action" value="add" />

        <label for="id_formation">Formation :</label>
        <select name="id_formation" id="id_formation" required>
            <option value="">-- Sélectionnez une formation --</option>
            <?php foreach ($formations as $formation): ?>
                <option value="<?= $formation['id_formation'] ?>"><?= htmlspecialchars($formation['nom_formation']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="id_annee">Année scolaire :</label>
        <select name="id_annee" id="id_annee" required>
            <option value="">-- Sélectionnez une année --</option>
            <?php foreach ($annees as $annee): ?>
                <option value="<?= $annee['id_annee'] ?>"><?= htmlspecialchars($annee['libelle']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="date_debut">Date d'ouverture :</label>
        <input type="date" name="date_debut" id="date_debut" required />

        <label for="date_fin">Date de fermeture :</label>
        <input type="date" name="date_fin" id="date_fin" required />

        <button type="submit">Ajouter</button>
    </form>

    <div id="message"></div>
<script src="../Public/delai.js"></script>

</body>
</html>
