<?php
session_start();
require_once '../includes/db.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$adminNom = $_SESSION['admin_nom'];
$message = $_SESSION['message'] ?? "";
unset($_SESSION['message']);

if (!isset($_SESSION['uploaded_file_content'])) {
    $_SESSION['uploaded_file_content'] = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if (isset($_POST['delete_all'])) {
        try {
            $pdo->exec('DELETE FROM "ListeCINAutorises"');
            $pdo->exec('ALTER SEQUENCE "ListeCINAutorises_id_seq" RESTART WITH 1');

            $_SESSION['uploaded_file_content'] = null;
            $_SESSION['uploaded_file'] = null;
            $_SESSION['message'] = " La liste a été supprimée.";
        } catch (Exception $e) {
            $_SESSION['message'] = "Erreur lors de la suppression : " . $e->getMessage();
        }
        header("Location: dashboard.php");
        exit;
    }

   
    if (isset($_POST['save_to_db'])) {
        if (isset($_SESSION['uploaded_file_content']) && is_array($_SESSION['uploaded_file_content'])) {
            try {
                $pdo->exec('DELETE FROM "ListeCINAutorises"');
                foreach ($_SESSION['uploaded_file_content'] as $cin) {
                    $cin = trim($cin);
                    if (!empty($cin)) {
                        $stmt = $pdo->prepare('INSERT INTO "ListeCINAutorises" (cin_cne) VALUES (:cin_cne)');
                        $stmt->execute(['cin_cne' => $cin]);
                    }
                }
                $_SESSION['message'] = "Liste enregistrée .";
            } catch (Exception $e) {
                $_SESSION['message'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
            }
        } else {
            $_SESSION['message'] = "Aucun fichier chargé à enregistrer.";
        }
        header("Location: dashboard.php");
        exit;
    }

    if (isset($_FILES['cin_file']) && $_FILES['cin_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cin_file'];
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $_SESSION['uploaded_file'] = [
            'name' => $fileName,
            'size' => $file['size'],
            'type' => $file['type']
        ];

        $cins = [];

        try {
            if (in_array($fileExtension, ['xls', 'xlsx'])) {
                $spreadsheet = IOFactory::load($fileTmpPath);
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $val = trim($cell->getValue());
                        if (!empty($val)) {
                            $cins[] = $val;
                        }
                    }
                }
            } elseif (in_array($fileExtension, ['csv', 'txt'])) {
                $handle = fopen($fileTmpPath, 'r');
                while (($line = fgets($handle)) !== false) {
                    $val = trim($line);
                    if (!empty($val)) {
                        $cins[] = $val;
                    }
                }
                fclose($handle);
            } else {
                $_SESSION['message'] = "Format de fichier non pris en charge.";
                header("Location: dashboard.php");
                exit;
            }

            $_SESSION['uploaded_file_content'] = $cins;
            $_SESSION['message'] = "Fichier chargé avec succès, vous pouvez maintenant l'enregistrer .";

        } catch (Exception $e) {
            $_SESSION['message'] = "Erreur lors du chargement : " . $e->getMessage();
        }

        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../Public/dashboard.css" />
</head>
<body>
<div class="navbar">
    <div class="left">
        <img src="../assets/logo.png" alt="Logo" />
        <div class="greeting">Bienvenue, <?= htmlspecialchars($adminNom) ?> !</div>
    </div>
    <div class="links">
        <a href="dashboard.php">ListeCIN</a>
        <a href="ListeEtudiants.php">ListeEtudiants</a>
         <a href="delai.php">Gestion du délai</a>
        <a href="logout.php" class="logout">déconnexion</a>
    </div>
</div>

<div class="content">
    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="upload-widget">
        <h3>Téléverser un fichier CIN / CNE</h3>

        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="drop-zone" style="cursor:pointer;">
                <label for="cin_file" style="cursor:pointer; display:flex; align-items:center;">
                    <div class="icon" style="font-size:24px; user-select:none;">+</div>
                    <p style="margin-left: 8px;">Cliquez pour choisir un fichier</p>
                </label>
                <input
                    type="file"
                    name="cin_file"
                    id="cin_file"
                    accept=".csv,.txt,.xls,.xlsx"
                    style="display:none"
                    required
                    onchange="document.getElementById('uploadForm').submit();"
                />
            </div>
        </form>

        <?php if (isset($_SESSION['uploaded_file'])):
            $f = $_SESSION['uploaded_file'];
            $content = $_SESSION['uploaded_file_content'] ?? [];
        ?>
        <div class="file-preview" style="display: block; margin-top: 20px;">
            <div class="file-info" style="display:flex; align-items:center;">
                <img src="../assets/file.png" alt="ZIP Icon" style="width:32px; height:32px; margin-right: 10px;" />
                <div>
                    <div class="file-name"><?= htmlspecialchars($f['name']) ?></div>
                  <!--  <div class="file-meta"><?= htmlspecialchars($f['type']) ?> | <?= round($f['size'] / 1024, 1) ?> Ko</div>-->
                </div>
            </div>

            <div class="file-actions" style="margin-top:10px;">
               
                <form method="POST" style="display:inline;">
                    <button type="submit" name="delete_all" class="btn red">🗑 Supprimer Fichier</button>
                </form>

               
                <form method="POST" style="display:inline; margin-left: 10px;">
                    <button type="submit" name="save_to_db" class="btn">💾 Enregistrer Fichier</button>
                </form>

              
                <button class="btn" id="btnViewContent">👁️ Voir contenu</button>
            </div>
        </div>

    
        <div id="fileModal" class="modal">
            <div class="modal-content">
                <button class="remove-task" id="modalCloseBtn">&times;</button>
                <h3>CIN / CNE</h3>
                <pre id="fileTextContent">
<?php
if (!empty($content)) {
    foreach ($content as $line) {
        echo htmlspecialchars($line) . "\n";
    }
} else {
    echo "Aucun contenu disponible.";
}
?>
                </pre>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<script src="../Public/upload.js"></script>
</body>
</html>
