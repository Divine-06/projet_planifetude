<?php 

try{
    $connexion=new PDO("mysql:host=localhost;dbname=planifetude;charset=utf8","root","");
    $connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch (PDOException$e){
    die("Erreur de connexion :".$e->getMessage());
}

if (isset($_POST['submit'])){

extract($_POST);
$insert=$connexion->prepare("INSERT INTO utulisateurs(nom,email,motdepasse)VALUES (?,?,?)");
$insert->execute (array($nom,$email,$motdepasse));

    {?>

    <script> alert("erreur") </script>

    

    <?php
    
}
header(("location:login.php"));
exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INSCRIPTION-PlanifEtude</title>
    <link rel="stylesheet" href="formulaire.css">
</head>
<body > 
    <main class="form-container">
   <h2>créer un compte😊 </h2>
   <form  method="post">
    <div>   
    <label for="">VOTRE NOM COMPLET : </label>
    <input type="text" id="nom" name="nom" required>
   </div>
    <div>
        <label for="">Votre adress e-mail</label>
        <input type="email" id="email" name="email"required >
    </div>  
    <label for="password">Mot de passe:</label>
    <input type="password" id="password" name="motdepasse"> <br>
    <label for="confirm-password">confirmer le mot de passe:</label>
    <input type="password" id="confirm-password" name="confirm-password"><br><br>

    <button type="submit" name="submit">soumettre👍</button>

    <p>avez-vous déjà un compte ?
    <a href="login.php">connectez-vous ici </a></p>
    </form>
    
</body>
</html>    