<?php
require_once '../includes/db.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';



if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('ID étudiant invalide.');
}

$id_etudiant = (int) $_GET['id'];

$sql = 'SELECT 
            d.nom, d.prenom, d.nom_arabe , d.prenom_arabe , d.email, d.telephone,
            d.date_naissance, d.sexe, d.adresse,
            d.annee_obtention_bac, d.serie_bac, d.mention_bac,
            d.lieu_obtention_bac, d.nationalite, d.handicape,
            d.type_handicap, d.fonctionnaire, d.type_fonctionnaire,
            f.nom_formation, fi.nom_filiere, m.nom_mode,
            d.photo, d."CIN" AS cin_doc, d."CIN_verso" AS cin_verso,
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

$pdf = new TCPDF();
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);


if (!empty($etudiant['photo'])) {
    $photoData = is_resource($etudiant['photo']) 
        ? stream_get_contents($etudiant['photo']) 
        : pg_unescape_bytea($etudiant['photo']);

    $photoPath = tempnam(sys_get_temp_dir(), 'photo_') . '.jpg';
    file_put_contents($photoPath, $photoData);
    $pdf->Image($photoPath, 150, 10, 40, 40);
    unlink($photoPath);
}


$html = '<h2>Dossier étudiant</h2>
<table cellspacing="5" cellpadding="3">
<tr><td><strong>Nom :</strong></td><td>' . htmlspecialchars($etudiant['nom']) . '</td></tr>
<tr><td><strong>Prénom :</strong></td><td>' . htmlspecialchars($etudiant['prenom']) . '</td></tr>
<tr><td><strong>Nom Arabe :</strong></td><td>' . htmlspecialchars($etudiant['nom_arabe']) . '</td></tr>
<tr><td><strong>Prénom Arabe:</strong></td><td>' . htmlspecialchars($etudiant['prenom_arabe']) . '</td></tr>
<tr><td><strong>Email :</strong></td><td>' . htmlspecialchars($etudiant['email']) . '</td></tr>
<tr><td><strong>Téléphone :</strong></td><td>' . htmlspecialchars($etudiant['telephone']) . '</td></tr>
<tr><td><strong>Date naissance :</strong></td><td>' . htmlspecialchars($etudiant['date_naissance']) . '</td></tr>
<tr><td><strong>Sexe :</strong></td><td>' . htmlspecialchars($etudiant['sexe']) . '</td></tr>
<tr><td><strong>Adresse :</strong></td><td>' . htmlspecialchars($etudiant['adresse']) . '</td></tr>
<tr><td><strong>BAC :</strong></td><td>' . htmlspecialchars($etudiant['annee_obtention_bac']) . ' - ' . htmlspecialchars($etudiant['serie_bac']) . ' - ' . htmlspecialchars($etudiant['mention_bac']) . '</td></tr>
<tr><td><strong>Nationalité :</strong></td><td>' . htmlspecialchars($etudiant['nationalite']) . '</td></tr>
<tr><td><strong>Handicap :</strong></td><td>' . htmlspecialchars($etudiant['handicape']) . '</td></tr>';

if (strtolower($etudiant['handicape']) === 'oui') {
    $html .= '<tr><td><strong>Type handicap :</strong></td><td>' . htmlspecialchars($etudiant['type_handicap']) . '</td></tr>';
}

$html .= '<tr><td><strong>Fonctionnaire :</strong></td><td>' . htmlspecialchars($etudiant['fonctionnaire']) . '</td></tr>';

if (strtolower($etudiant['fonctionnaire']) === 'oui') {
    $html .= '<tr><td><strong>Type fonctionnaire :</strong></td><td>' . htmlspecialchars($etudiant['type_fonctionnaire']) . '</td></tr>';
}

$html .= '<tr><td><strong>Formation :</strong></td><td>' . htmlspecialchars($etudiant['nom_formation']) . '</td></tr>
<tr><td><strong>Filière :</strong></td><td>' . htmlspecialchars($etudiant['nom_filiere']) . '</td></tr>
<tr><td><strong>Mode :</strong></td><td>' . htmlspecialchars($etudiant['nom_mode']) . '</td></tr>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Documents scannés", 0, 1, 'C');
$pdf->Ln(5);


function insertImage($pdf, $title, $data, $x, $y) {
    if (!empty($data)) {
        $img = is_resource($data) ? stream_get_contents($data) : pg_unescape_bytea($data);
        $path = tempnam(sys_get_temp_dir(), 'img_') . '.jpg';
        file_put_contents($path, $img);

        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetXY($x, $y);
        $pdf->Cell(0, 5, $title, 0, 1);

        $pdf->Image($path, $x, $y + 5, 60, 40);
        unlink($path);
    }
}


$startY = 30;
insertImage($pdf, 'CIN - Recto', $etudiant['cin_doc'], 15, $startY);
insertImage($pdf, 'CIN - Verso', $etudiant['cin_verso'], 110, $startY + 0);
insertImage($pdf, 'Relevé BAC', $etudiant['bac_doc'], 15, $startY + 55);

$pdf->Output('dossier_etudiant_' . $id_etudiant . '.pdf', 'I');