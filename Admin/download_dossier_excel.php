<?php
require_once '../includes/db.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('ID étudiant invalide.');
}
$id_etudiant = (int) $_GET['id'];

$sql = 'SELECT 
            d.nom, d.prenom, d.nom_arabe, d.prenom_arabe, d.email, d.telephone,
            d.date_naissance, d.sexe, d.adresse,
            d.annee_obtention_bac, d.serie_bac, d.mention_bac,
            d.lieu_obtention_bac, d.nationalite, d.handicape,
            d.type_handicap, d.fonctionnaire, d.type_fonctionnaire,
            f.nom_formation, fi.nom_filiere, m.nom_mode
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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Champ');
$sheet->setCellValue('B1', 'Valeur');

$row = 2;

$dataMap = [
    'Nom' => $etudiant['nom'],
    'Prénom' => $etudiant['prenom'],
    'Nom Arabe' => $etudiant['nom_arabe'],
    'Prénom Arabe' => $etudiant['prenom_arabe'],
    'Email' => $etudiant['email'],
    'Téléphone' => $etudiant['telephone'],
    'Date de naissance' => $etudiant['date_naissance'],
    'Sexe' => $etudiant['sexe'],
    'Adresse' => $etudiant['adresse'],
    'Année Bac' => $etudiant['annee_obtention_bac'],
    'Série Bac' => $etudiant['serie_bac'],
    'Mention Bac' => $etudiant['mention_bac'],
    'Lieu obtention Bac' => $etudiant['lieu_obtention_bac'],
    'Nationalité' => $etudiant['nationalite'],
    'Handicap' => $etudiant['handicape'],
    'Type handicap' => strtolower($etudiant['handicape']) === 'oui' ? $etudiant['type_handicap'] : '',
    'Fonctionnaire' => $etudiant['fonctionnaire'],
    'Type fonctionnaire' => strtolower($etudiant['fonctionnaire']) === 'oui' ? $etudiant['type_fonctionnaire'] : '',
    'Formation' => $etudiant['nom_formation'],
    'Filière' => $etudiant['nom_filiere'],
    'Mode' => $etudiant['nom_mode'],
];

foreach ($dataMap as $field => $value) {
    $sheet->setCellValue('A' . $row, $field);
    $sheet->setCellValue('B' . $row, $value);
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="dossier_etudiant_' . $id_etudiant . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
