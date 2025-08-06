<?php
require_once '../includes/db.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('ID étudiant invalide.');
}

$id_etudiant = (int) $_GET['id'];

function pgUnescapeBytea($bytea) {
    if (substr($bytea, 0, 2) === '\\x') {
        return hex2bin(substr($bytea, 2));
    }
    return $bytea;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);

$sql = 'SELECT 
            d.nom,
            d.prenom,
            d.nom_arabe,
            d.prenom_arabe,
            d.email,
            d.sexe,
            d.date_naissance,
            d.telephone,
            d.adresse,
            d.annee_obtention_bac,
            d.serie_bac,
            d.mention_bac,
            d.lieu_obtention_bac,
            d.nationalite,
            d.handicape,
            d.type_handicap,
            d.fonctionnaire,
            d.type_fonctionnaire,
            f.nom_formation,
            fi.nom_filiere,
            m.nom_mode,
            d.photo,
            d."CIN" AS cin_doc,
            d."CIN_verso" AS cin_docc,
            d."BAC" AS bac_doc
        FROM "DossierInscription" d
        JOIN "Formation" f ON d.id_formation = f.id_formation
        JOIN "Filiere" fi ON d.id_filiere = fi.id_filiere
        JOIN "mode" m ON d.id_mode = m.id_mode
        WHERE d.id_etudiant = :id_etudiant';

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_etudiant' => $id_etudiant]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    die('Étudiant non trouvé.');
}

if (is_resource($etudiant['photo'])) {
    $etudiant['photo'] = stream_get_contents($etudiant['photo']);
}
if (is_resource($etudiant['cin_doc'])) {
    $etudiant['cin_doc'] = stream_get_contents($etudiant['cin_doc']);
}
if (is_resource($etudiant['cin_docc'])) {
    $etudiant['cin_docc'] = stream_get_contents($etudiant['cin_docc']);
}
if (is_resource($etudiant['bac_doc'])) {
    $etudiant['bac_doc'] = stream_get_contents($etudiant['bac_doc']);
}

$fonctionnaire = trim($etudiant['fonctionnaire'] ?? '');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Dossier de <?= htmlspecialchars($etudiant['prenom']) ?> <?= htmlspecialchars($etudiant['nom']) ?></title>
    <link rel="stylesheet" href="../Public/dossier.css" />
 
</head>
<style>

.btn-download {
  background-color: #084298;
  color: white;
  border: none;
  padding: 12px 25px;
  font-size: 16px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  font-weight: 600;
  display: inline-block;
  margin-top: 20px;
}

.btn-download:hover {
  background-color: #003d99;
}


#downloadModal {
  display: none;
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
  z-index: 1000;
  display: flex;
}


#downloadModal > div {
  background: #fff;
  padding: 30px 25px;
  border-radius: 10px;
  min-width: 280px;
  max-width: 90%;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  text-align: center;
}


#downloadModal button {
  margin: 8px 10px 0 10px;
  padding: 10px 20px;
  font-size: 15px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

#downloadModal button:nth-child(2) {
  background-color: #007bff;
  color: white;
}

#downloadModal button:nth-child(2):hover {
  background-color: #0056b3;
}

#downloadModal button:nth-child(3) {
  background-color: #28a745;
  color: white;
}

#downloadModal button:nth-child(3):hover {
  background-color: #1e7e34;
}

#downloadModal button:last-child {
  background-color: #150346ff;
  color: white;
  margin-top: 15px;
}

#downloadModal button:last-child:hover {
  background-color: #04026cff;
}

@media (max-width: 768px) {
  h1 {
    font-size: 1.6rem;
  }

  ul li strong {
    display: block;
    width: auto;
    margin-bottom: 5px;
  }

  .container {
    padding: 20px;
  }
}
</style>
<body>
    <div class="container">
        <h1>Dossier de <?= htmlspecialchars($etudiant['prenom']) ?> <?= htmlspecialchars($etudiant['nom']) ?></h1>
        <p><a href="ListeEtudiants.php">← Retour à la liste</a></p>

        <div>
            <?php if ($etudiant['photo']): 
                $photoData = pgUnescapeBytea($etudiant['photo']);
                $photoMime = $finfo->buffer($photoData) ?: 'image/jpeg';
            ?>
                <img src="data:<?= htmlspecialchars($photoMime) ?>;base64,<?= base64_encode($photoData) ?>" alt="Photo de l'étudiant" style="max-height:200px;" />
            <?php else: ?>
                <p>Aucune photo disponible</p>
            <?php endif; ?>
        </div>

        <ul>
            <li><strong>Nom :</strong> <?= htmlspecialchars($etudiant['nom']) ?></li>
            <li><strong>Prénom :</strong> <?= htmlspecialchars($etudiant['prenom']) ?></li>
            <li><strong>Nom Arabe :</strong> <?= htmlspecialchars($etudiant['nom_arabe']) ?></li>
            <li><strong>Prénom Arabe :</strong> <?= htmlspecialchars($etudiant['prenom_arabe']) ?></li>
            <li><strong>Email :</strong> <?= htmlspecialchars($etudiant['email']) ?></li>
            <li><strong>Téléphone :</strong> <?= htmlspecialchars($etudiant['telephone']) ?></li>
            <li><strong>Date de naissance :</strong> <?= htmlspecialchars($etudiant['date_naissance']) ?></li>
            <li><strong>Sexe :</strong> <?= htmlspecialchars($etudiant['sexe']) ?></li>
            <li><strong>Adresse :</strong> <?= htmlspecialchars($etudiant['adresse']) ?></li>
            <li><strong>Année BAC :</strong> <?= htmlspecialchars($etudiant['annee_obtention_bac']) ?></li>
            <li><strong>Série BAC :</strong> <?= htmlspecialchars($etudiant['serie_bac']) ?></li>
            <li><strong>Mention BAC :</strong> <?= htmlspecialchars($etudiant['mention_bac']) ?></li>
            <li><strong>Lieu BAC :</strong> <?= htmlspecialchars($etudiant['lieu_obtention_bac']) ?></li>
            <li><strong>Nationalité :</strong> <?= htmlspecialchars($etudiant['nationalite']) ?></li>

            <li><strong>En situation de handicap :</strong> <?= htmlspecialchars($etudiant['handicape'] ?? 'Non renseigné') ?></li>
            <?php if (!empty($etudiant['handicape']) && strtolower($etudiant['handicape']) === 'oui'): ?>
                <li><strong>Type de handicap :</strong> <?= htmlspecialchars($etudiant['type_handicap'] ?? 'Non renseigné') ?></li>
            <?php endif; ?>

            <?php if ($fonctionnaire !== ''): ?>
                <li><strong>Fonctionnaire :</strong> <?= htmlspecialchars($fonctionnaire) ?></li>
                <?php if (strtolower($fonctionnaire) === 'oui'): ?>
                    <li><strong>Type de fonctionnaire :</strong> <?= htmlspecialchars($etudiant['type_fonctionnaire'] ?? 'Non renseigné') ?></li>
                <?php endif; ?>
            <?php endif; ?>

            <li><strong>Formation :</strong> <?= htmlspecialchars($etudiant['nom_formation']) ?></li>
            <li><strong>Filière :</strong> <?= htmlspecialchars($etudiant['nom_filiere']) ?></li>
            <li><strong>Mode d'acceptation :</strong> <?= htmlspecialchars($etudiant['nom_mode']) ?></li>
        </ul>

        <h3>Documents</h3>
        <div>
            <strong>CIN/CNE Recto :</strong><br />
            <?php if ($etudiant['cin_doc']): 
                $cinData = pgUnescapeBytea($etudiant['cin_doc']);
                $cinMime = $finfo->buffer($cinData) ?: 'image/jpeg';
            ?>
                <img src="data:<?= htmlspecialchars($cinMime) ?>;base64,<?= base64_encode($cinData) ?>" alt="CIN" style="max-height:150px;" />
            <?php else: ?>
                <p>Non fourni</p>
            <?php endif; ?>
        </div>
        <div>
            <strong>CIN/CNE Verso :</strong><br />
            <?php if ($etudiant['cin_docc']): 
                $cinData = pgUnescapeBytea($etudiant['cin_docc']);
                $cinMime = $finfo->buffer($cinData) ?: 'image/jpeg';
            ?>
                <img src="data:<?= htmlspecialchars($cinMime) ?>;base64,<?= base64_encode($cinData) ?>" alt="CIN" style="max-height:150px;" />
            <?php else: ?>
                <p>Non fourni</p>
            <?php endif; ?>
        </div>

        <div>
            <strong>BAC :</strong><br />
            <?php if ($etudiant['bac_doc']): 
                $bacData = pgUnescapeBytea($etudiant['bac_doc']);
                $bacMime = $finfo->buffer($bacData) ?: 'image/jpeg';
            ?>
                <img src="data:<?= htmlspecialchars($bacMime) ?>;base64,<?= base64_encode($bacData) ?>" alt="BAC" style="max-height:150px;" />
            <?php else: ?>
                <p>Non fourni</p>
            <?php endif; ?>
            <p>

<button id="btnDownload" class="btn-download">Télécharger le dossier complet</button>


<div id="downloadModal" style="
    display:none; 
    position:fixed;
    top:0; left:0; right:0; bottom:0; 
    background:rgba(0, 0, 0, 0.5);
    justify-content:center; 
    align-items:center;
    z-index:1000;
">
  <div style="background:#fff; padding:20px; border-radius:8px; min-width:250px; text-align:center;">
    <p>Choisissez le format :</p>
    <button onclick="download('pdf')">PDF</button>
    <button onclick="download('excel')">Excel</button>
    <br/><br/>
    <button onclick="closeDownloadModal()">Annuler</button>
  </div>
</div>

<script>

  const idEtudiant = <?= json_encode($id_etudiant) ?>;

  const modal = document.getElementById('downloadModal');
  const btnDownload = document.getElementById('btnDownload');

  btnDownload.addEventListener('click', () => {
    modal.style.display = 'flex';
  });

  function closeDownloadModal() {
    modal.style.display = 'none';
  }

 function download(type) {
  if (!idEtudiant) {
    alert("ID étudiant introuvable !");
    closeDownloadModal();
    return;
  }
  let url;
  if (type === 'pdf') {
    url = `download_dossier.php?id=${idEtudiant}`;
  } else if (type === 'excel') {
    url = `download_dossier_excel.php?id=${idEtudiant}`;
  } else {
    alert("Format inconnu");
    closeDownloadModal();
    return;
  }
  window.open(url, '_blank');
  closeDownloadModal();
}


</script>


</body>
</html>
