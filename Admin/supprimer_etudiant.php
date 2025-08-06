<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_etudiant'] ?? null;

    if ($id && is_numeric($id)) {
        try {
            $stmt = $pdo->prepare('DELETE FROM "Etudiant" WHERE id_etudiant = :id');
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID invalide']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}
