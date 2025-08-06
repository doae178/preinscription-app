<?php 
require_once '../Includes/db.php';

$search = $_GET['search'] ?? '';
$sql = 'SELECT e.id_etudiant, e.nom, e.prenom
        FROM "Etudiant" e
        INNER JOIN "DossierInscription" d ON e.id_etudiant = d.id_etudiant';
$params = [];

if (!empty($search)) {
    $sql .= ' WHERE e.nom ILIKE :search OR e.prenom ILIKE :search';
    $params = [':search' => '%' . $search . '%'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Étudiants</title>
    <link rel="stylesheet" href="../public/ListeEtudiants.css">
</head>
<body>
    <p><a href="dashboard.php">← Retour </a></p>
    <h1>Liste des Étudiants</h1>

    <form method="GET">
        <input type="text" name="search" placeholder="Rechercher par nom ou prénom..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn">Rechercher</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($etudiants) > 0): ?>
                <?php foreach ($etudiants as $etudiant): ?>
                    <tr data-id="<?= htmlspecialchars($etudiant['id_etudiant']) ?>">
                        <td data-label="Nom"><?= htmlspecialchars($etudiant['nom']) ?></td>
                        <td data-label="Prénom"><?= htmlspecialchars($etudiant['prenom']) ?></td>
                        <td data-label="Action">
                            <a class="btn" href="dossier.php?id=<?= $etudiant['id_etudiant'] ?>">Voir Dossier</a>
                         <button type="button" style="background-color:#e74c3c; color:white; border:none; padding:8px 18px; border-radius:4px; cursor:pointer;" class="btn-supprimer" data-id="<?= $etudiant['id_etudiant'] ?>">Supprimer Dossier</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Aucun étudiant trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>


<script src="../Public/ListeEtudiants.js"></script>

</body>
</html>
