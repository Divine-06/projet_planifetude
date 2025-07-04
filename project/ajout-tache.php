<?php
try {
    $connexion = new PDO("mysql:host=localhost;dbname=planifetude;charset=utf8", "root", "");
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();

$id = $_SESSION['id'] ?? null;

if (isset($_POST['valider'])) {
    // Utiliser extract pour extraire les valeurs du formulaire
    extract($_POST); // Crée les variables $titre, $dates, $descriptions, $statut

    // Vérifier que toutes les variables nécessaires sont définies et non vides
    if (!empty($titre) && !empty($dates) && !empty($descriptions) && !empty($statut)&&!empty($heure) && !empty($id)) {
        $stmt = $connexion->prepare("INSERT INTO taches (titre, dates, descriptions, id_utilisateur, statut,heure) VALUES (?, ?, ?, ?, ?,?)");

        if ($stmt->execute([$titre, $dates, $descriptions, $id, $statut,$heure])) {
            // Succès : message + redirection via JS
            echo '<script>
                alert("Opération réussie");
                window.location.href = "tableau.php";
            </script>';
            exit;
        } else {
            echo '<script>alert("Échec de l\'opération");</script>';
        }
    } else {
        echo '<script>alert("Tous les champs sont requis.");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ajouter une tâche</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f8;
      color: #333;
      padding: 20px;
    }

    header {
      background-color: #0a2a66;
      color: white;
      text-align: center;
      padding: 25px 10px;
      border-radius: 10px 10px 0 0;
    }

    header h1 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    nav button {
      background-color: white;
      color: #0a2a66;
      border: 2px solid #0a2a66;
      padding: 10px 20px;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    nav button:hover {
      background-color: #0a2a66;
      color: white;
    }

    main {
      max-width: 800px;
      margin: 40px auto;
      background-color: white;
      padding: 30px;
      border-radius: 0 0 10px 10px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px 30px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: 600;
      color: #0a2a66;
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="date"],
    textarea,
    select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      background-color: #f9f9f9;
      width: 100%;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    .full-width {
      grid-column: 1 / -1;
    }

    .submit-button {
      grid-column: 1 / -1;
      display: flex;
      justify-content: center;
    }

    button[type="submit"] {
      background-color: #0a2a66;
      color: white;
      padding: 12px 25px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #083a8c;
    }

    @media screen and (max-width: 768px) {
      form {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>Ajouter une nouvelle tâche</h1>
    <nav>
      <button type="button" onclick="window.location.href='tableau.php'">⬅️ Retour au tableau de bord</button>
    </nav>
  </header>

  <main>
    <form method="post">
      <div class="form-group">
        <label for="task-title">Titre de la tâche :</label>
        <input type="text" id="task-title" name="titre" placeholder="EX : devoir de maths" required />
      </div>

      <div class="form-group">
        <label for="task-date">Date limite :</label>
        <input type="date" id="task-date" name="dates" required />
      </div>

      <div class="form-group full-width">
        <label for="description">Instructions :</label>
        <textarea name="descriptions" id="description" placeholder="Instructions" required></textarea>
      </div>

      <div class="form-group full-width">
        <label for="statut">Statut :</label>
        <select name="statut" id="statut" required>
          <option value="en attend">🕒 En attente</option>
          <option value="urgent">⚠️ Urgent</option>
          <option value="terminer">✅ Terminé</option>
        </select>
      </div>
       <div class="form-group">
        <label for="heure">Heure:</label>
        <input type="time" id="task-date" name="heure" required />
      </div>

      <div class="submit-button">
        <button type="submit" name="valider">💾 Enregistrer la tâche</button>
      </div>
    </form>
  </main>

</body>
</html>
