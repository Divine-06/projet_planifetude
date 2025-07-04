
<?php
session_start();

// Connexion à la BDD
try {
    $connexion = new PDO("mysql:host=localhost;dbname=planifetude;charset=utf8", "root", "");
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier que l'utilisateur est connecté
$id_utilisateur = $_SESSION['id'] ?? null;
if (!$id_utilisateur) {
    die("Utilisateur non connecté.");
}

// Récupération de l'ID de la tâche à modifier
$id_tache = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$id_tache) {
    die("ID de tâche manquant.");
}

// Récupérer les données de la tâche
$stmt = $connexion->prepare("SELECT * FROM taches WHERE id = ? AND id_utilisateur = ?");
$stmt->execute([$id_tache, $id_utilisateur]);
$tache = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tache) {
    die("Tâche introuvable.");
}

// Mise à jour si formulaire soumis
if (isset($_POST['valider'])) {
    $titre = $_POST['titre'];
    $descriptions = $_POST['descriptions'];
    $dates = $_POST['dates'];
    $statut = $_POST['statut'];
    $heure = $_POST['heure'];


    $update = $connexion->prepare("UPDATE taches SET titre = ?, descriptions = ?, dates = ?,heure=?, statut = ? WHERE id = ? AND id_utilisateur = ?");
    $update->execute([$titre, $descriptions, $dates,$heure ,$statut, $id_tache, $id_utilisateur]);

    echo "<script>alert('Tâche modifiée avec succès.'); window.location.href='tableau.php';</script>";
    exit;
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une tâche</title>
</head>
<body>
  <link rel="stylesheet" href="modifier.css">
    <div class="container">
        <div class="card">
            <h2>✏️ Modifier la tâche</h2>
            <form method="POST">
                <label for="titre">Titre :</label><br>
                <input type="text" name="titre" value="<?= htmlspecialchars($tache['titre']) ?>" required><br><br>

                <label for="descriptions">Description :</label><br>
                <textarea name="descriptions" required><?= htmlspecialchars($tache['descriptions']) ?></textarea><br><br>

                <label for="dates">Date :</label><br>
                <input type="date" name="dates" value="<?= htmlspecialchars($tache['dates']) ?>" required><br><br>

                <label for="statut">Statut :</label><br>
                <select name="statut" required>
                    <option value="en cours" <?= $tache['statut'] == 'en cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="terminée" <?= $tache['statut'] == 'terminée' ? 'selected' : '' ?>>Terminée</option>
                    <option value="en attente" <?= $tache['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                </select><br><br>
                 <label for="heure">Heure :</label><br>
                <input type="time" name="heure" value="<?= htmlspecialchars($tache['heure']) ?>" required><br><br>


                <button type="submit" name="valider">✅ Enregistrer les modifications</button>
                <a href="tableau.php">↩️ Retour</a>
            </form>
        </div>
    </div>
</body>
</html>































