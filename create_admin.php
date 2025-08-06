<?php
require_once 'Includes/db.php';


$prenom = 'Douae';
$nom = 'Lamrani';
$email = 'douaelamrani3@gmail.com';
$password_plain = 'admin123'; 


$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

try {
    
    $stmt = $pdo->prepare('INSERT INTO "Admin" (prenom, nom, email, password) 
                           VALUES (:prenom, :nom, :email, :password)');
    
    $stmt->execute([
        ':prenom' => $prenom,
        ':nom' => $nom,
        ':email' => $email,
        ':password' => $password_hashed
    ]);

    echo "✅ Admin créé avec succès !";
} catch (PDOException $e) {
    die("❌ Erreur lors de la création de l'admin : " . $e->getMessage());
}
