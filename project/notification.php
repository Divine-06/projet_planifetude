<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

date_default_timezone_set('Europe/Paris');

$connexion = new PDO("mysql:host=localhost;dbname=planifetude;charset=utf8", "root", "");
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = new DateTime();
$nextHour = clone $now;
$nextHour->modify('+1 hour');

$nextHourDate = $nextHour->format('Y-m-d');
$nextHourTime = $nextHour->format('H:i');

$sql = "
    SELECT t.*, u.email, u.nom
    FROM taches t
    JOIN utilisateurs u ON t.id_utilisateur = u.id
    WHERE t.dates = :date
    AND t.heure = :heure
    AND t.notification_envoyee = 0
";
$stmt = $connexion->prepare($sql);
$stmt->execute([
    ':date' => $nextHourDate,
    ':heure' => $nextHourTime
]);
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($taches as $tache) {
    $mail = new PHPMailer(true);
    try {
        // Config Mailtrap
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = 'TON_USERNAME_MAILTRAP';
        $mail->Password = 'TON_PASSWORD_MAILTRAP';
        $mail->Port = 2525;

        $mail->setFrom('no-reply@planifetude.com', 'PlanifEtude');
        $mail->addAddress($tache['email'], $tache['nom']);

        $mail->Subject = "⏰ Rappel : tâche à faire bientôt !";
        $mail->Body = "Bonjour {$tache['nom']},\n\n"
                    . "Vous avez une tâche prévue à {$tache['heure']} aujourd’hui :\n"
                    . "Titre : {$tache['titre']}\n"
                    . "Description : {$tache['descriptions']}\n\n"
                    . "Bon courage !\n\n- PlanifEtude";

        $mail->send();

        // Marquer comme envoyée
        $update = $connexion->prepare("UPDATE taches SET notification_envoyee = 1 WHERE id = ?");
        $update->execute([$tache['id']]);

    } catch (Exception $e) {
        echo "Erreur lors de l'envoi du mail : {$mail->ErrorInfo}";
    }
}
