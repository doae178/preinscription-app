<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_etudiant = $_POST['id_etudiant'] ?? null;
    if (!$id_etudiant || !ctype_digit($id_etudiant)) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM "DossierInscription" WHERE id_etudiant = :id');
    $stmt->execute([':id' => $id_etudiant]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Dossier non trouvé']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
