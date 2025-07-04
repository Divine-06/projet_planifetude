<?php

try{
    $connexion=new PDO("mysql:host=localhost;dbname=planifetude;charset=utf8","root","");
    $connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch (PDOException$e){
    die("Erreur de connexion :".$e->getMessage());
}
 session_start();
 session_destroy();
 session_start();
 
if (isset($_POST['valider'])) {

    $email=$_POST['email'];
    $motdepasse=$_POST['motdepasse'];

    $rec=$connexion->prepare ("SELECT * FROM utulisateurs WHERE email=? AND motdepasse=?");
    $rec->execute(array($email,$motdepasse));
    $cpt=$rec->rowCount();

    if ($cpt==1){

        $info=$rec->fetch();

        if ($info){

            $_SESSION['id']=$info['id'];
            $_SESSION['email']=$info['email'];
            $_SESSION['motdepasse']=$info['motdepasse'];

            header("Location:tableau.php");
            exit();
        }

      

    }
      else{

        
            
            {?>
            
    <script> alert ("le mot de passe ou l'email est invalide") </script>

    

    <?php }
            



        }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=z, initial-scale=1.0">
    <title>conxion-planifEtude</title>
    <link rel="stylesheet" href="inscription.css">
</head>

<body>
    <form method="post">
    <div class="container">
        <h2>CONNEXION</h2>
            <label for="email">EMAIL:</label>

            <input type="email" id="email" name="email"><br>

            <label for="password">MOT DE PASSE:</label>

            <input type="password" id="password" name="motdepasse"><br> <br>

            <button type="submit" name="valider">se connecter</button>
            
            <p>pas encore de compte ? <a href="register.php">inscrivez-vous ici </a></p>
            <p>retour à la page <a href="index.php"> D'ACCUIEL</a></p>
        </form>
    </div>
</body>

</html>