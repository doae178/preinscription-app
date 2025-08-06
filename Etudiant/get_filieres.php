<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

if (isset($_GET['formation_id']) && is_numeric($_GET['formation_id'])) {
    $id = (int) $_GET['formation_id'];

    $stmt = $pdo->prepare("
        SELECT f.id_filiere, f.nom_filiere, m.nom_mode
        FROM \"Filiere\" f
        JOIN \"mode\" m ON f.id_mode = m.id_mode
        WHERE f.id_formation = :id
        ORDER BY f.nom_filiere
    ");
    $stmt->execute(['id' => $id]);
    $filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($filieres);
} else {
    echo json_encode([]);
}
