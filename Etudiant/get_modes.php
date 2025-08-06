<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$formation_id = isset($_GET['formation_id']) ? intval($_GET['formation_id']) : 0;

if ($formation_id > 0) {
    $stmt = $pdo->prepare('
        SELECT DISTINCT m.id_mode, m.nom_mode
        FROM "Formation_Mode" fm
        JOIN "mode" m ON fm.id_mode = m.id_mode
        WHERE fm.id_formation = :formation_id
        ORDER BY m.nom_mode
    ');
    $stmt->execute(['formation_id' => $formation_id]);
    $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($modes);
} else {
    echo json_encode([]);
}
