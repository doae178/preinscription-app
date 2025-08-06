<?php
require_once '../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$formation_id = $_POST['formation_id'] ?? null;

$response = [
    'delai_en_cours' => false,
    'message' => 'Paramètre formation manquant ou invalide.'
];

if (!$formation_id) {
    echo json_encode($response);
    exit;
}

$aujourdhui = date('Y-m-d');

try {
    $sql = 'SELECT 1 FROM "DelaiFormulaire" 
            WHERE id_formation = :id_formation 
            AND :aujourdhui BETWEEN date_debut AND date_fin
            LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_formation' => $formation_id,
        ':aujourdhui' => $aujourdhui
    ]);

   $existe = $stmt->fetchColumn();

if ($existe) {
    $response['delai_en_cours'] = true;
     $response['message'] = '.';
} else {
    $response['delai_en_cours'] = false;
    $response['message'] = 'Le délai pour cette formation est terminé.';
}
} catch (Exception $e) {
    $response['delai_en_cours'] = false;
    $response['message'] = 'Erreur serveur : ' . $e->getMessage();
}

echo json_encode($response);
