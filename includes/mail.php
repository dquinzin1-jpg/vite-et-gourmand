<?php
/**
 * Envoi de courriel — VERSION SIMULÉE (environnement de développement).
 *
 * En développement local, aucun serveur SMTP n'est configuré : les courriels
 * ne sont donc pas réellement expédiés mais journalisés dans le fichier
 * logs/mails.log. Cela permet de vérifier leur contenu et leur déclenchement
 * sans dépendre d'un service d'envoi externe.
 *
 * En production, il suffirait de remplacer le corps de simulation par un envoi
 * réel via la bibliothèque PHPMailer (SMTP authentifié) : les appels faits
 * depuis le reste du site (inscription, commande, etc.) resteraient identiques.
 *
 * @param string $destinataire Adresse e-mail du destinataire
 * @param string $sujet        Sujet du message
 * @param string $message      Corps du message (texte brut)
 * @return bool                true si la simulation a réussi à journaliser
 */
function envoyerMail(string $destinataire, string $sujet, string $message): bool
{
    $dossierLogs = __DIR__ . '/../logs';
    if (!is_dir($dossierLogs)) {
        @mkdir($dossierLogs, 0775, true);
    }

    $entree = sprintf(
        "[%s] À : %s | Sujet : %s\n%s\n%s\n",
        date('Y-m-d H:i:s'),
        $destinataire,
        $sujet,
        $message,
        str_repeat('-', 60)
    );

    // --- SIMULATION (développement) : on journalise le mail dans un fichier ---
    $ok = @file_put_contents(
        $dossierLogs . '/mails.log',
        $entree,
        FILE_APPEND | LOCK_EX
    );

    // --- PRODUCTION (exemple, à activer avec PHPMailer) ---------------------
    // require_once __DIR__ . '/../vendor/autoload.php';
    // $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    // $mail->isSMTP();
    // $mail->Host       = $_ENV['SMTP_HOST'];
    // $mail->SMTPAuth   = true;
    // $mail->Username   = $_ENV['SMTP_USER'];
    // $mail->Password   = $_ENV['SMTP_PASS'];
    // $mail->Port       = 587;
    // $mail->setFrom('contact@vite-et-gourmand.fr', 'Vite & Gourmand');
    // $mail->addAddress($destinataire);
    // $mail->Subject    = $sujet;
    // $mail->Body       = $message;
    // $mail->send();
    // ------------------------------------------------------------------------

    return $ok !== false;
}