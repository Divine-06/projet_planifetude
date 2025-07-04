<?php
session_start();
date_default_timezone_set('Africa/Kinshasa'); // Pour éviter les problèmes de fuseau horaire

try {
    $connexion = new PDO("mysql:host=localhost;dbname=planifetude;charset=utf8", "root", "");
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier que l'utilisateur est connecté
$id = $_SESSION['id'] ?? null;
if (!$id) {
    die("Utilisateur non connecté.");
}

// Suppression si 'supp' est présent dans l'URL
if (isset($_GET['supp'])) {
    $supp = $_GET['supp'];
    $rec = $connexion->prepare("DELETE FROM taches WHERE id = ?");
    $rec->execute([$supp]);
}

// Récupération des tâches de l'utilisateur
$rec = $connexion->prepare("SELECT * FROM taches WHERE id_utilisateur = ? ORDER BY id DESC");
$rec->execute([$id]);
$ligne = $rec->fetchAll();

// Gestion des notifications
// Gestion des notifications
$notifications = [];
$now = new DateTime();

foreach ($ligne as $tache) {
    $datetimeTache = DateTime::createFromFormat('Y-m-d H:i:s', $tache['dates'] . ' ' . $tache['heure']);

    if ($datetimeTache !== false) {
        $interval = $now->diff($datetimeTache);
        $minutes = ($datetimeTache > $now) ? ($interval->days * 24 * 60 + $interval->h * 60 + $interval->i) : -1;

        if ($minutes >= 0 && $minutes <= 60) {
            $notifications[] = [
                'titre' => $tache['titre'],
                'message' => "⏰ La tâche « <strong>" . htmlspecialchars($tache['titre']) . "</strong> » commence à " . $datetimeTache->format('H:i') . "."
            ];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="tableau.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        .notification-bell {
            position: relative;
            display: inline-block;
            cursor: pointer;
            font-size: 22px;
            margin-left: 20px;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -10px;
            background-color: red;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .notification-popup {
            display: none;
            position: absolute;
            right: 0;
            top: 30px;
            background-color: #fff;
            border: 1px solid #ddd;
            width: 300px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 5px;
        }

        .notification-popup ul {
            list-style: none;
            margin: 0;
            padding: 10px;
        }

        .notification-popup li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .notification-popup li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h1>📋 Liste des Tâches</h1>
            <a href="ajout-tache.php" class="btn-ajouter">➕ Ajouter une tâche</a>
            <div class="notification-bell" onclick="toggleNotifications()">
                🔔
                <?php if (count($notifications) > 0): ?>
                    <span class="notification-badge"><?= count($notifications) ?></span>
                <?php endif; ?>

                <div id="notificationPopup" class="notification-popup">
                    <ul>
                        <?php foreach ($notifications as $notif): ?>
                            <li><?= $notif['message'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Heure</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ligne as $ligne): ?>
                        <tr>
                            <td><?= htmlspecialchars($ligne['titre']) ?></td>
                            <td><?= htmlspecialchars($ligne['descriptions']) ?></td>
                            <td><?= htmlspecialchars($ligne['dates']) ?></td>
                            <td><?= htmlspecialchars($ligne['statut']) ?></td>
                            <td><?= htmlspecialchars($ligne['heure']) ?></td>
                            <td>
                                <a href="modification.php?id=<?= $ligne['id'] ?>" class="btn-modifier">✏️ Modifier</a>
                            </td>
                            <td>
                                <a href="tableau.php?supp=<?= $ligne['id'] ?>" class="btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">🗑️ Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleNotifications() {
    const popup = document.getElementById("notificationPopup");
    popup.style.display = (popup.style.display === "block") ? "none" : "block";
}

document.addEventListener('click', function(event) {
    const bell = document.querySelector('.notification-bell');
    const popup = document.getElementById('notificationPopup');
    if (!bell.contains(event.target)) {
        popup.style.display = 'none';
    }
});
</script>
</body>
</html>
