<?php
// ============================================================
// mailer.php — FLIMFLIX Movie Website
// Sends HTML emails using PHPMailer + Gmail SMTP
// ============================================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Path check karjo ke PHPMailer folder tamara project root ma hoy
require('PHPMailer/PHPMailer.php');
require('PHPMailer/SMTP.php');
require('PHPMailer/Exception.php');

/**
 * Send an HTML email via Gmail SMTP.
 */
function sendEmail($to, $subject, $body, $file = null)
{
    $mail = new PHPMailer(true);

    try {
        // ---- SMTP Configuration ----
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Host       = 'smtp.gmail.com';
        $mail->Port       = 465;

        // --- SMTP Credentials ---
        $mail->Username   = 'nmrajput11@gmail.com';
        $mail->Password   = 'esux gtsk fnqz abek'; // 16-digit App Password

        // Localhost par SSL peer verification error avoid karva mate
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ];

        // ---- Sender & Recipient ----
        $mail->setFrom('nmrajput11@gmail.com', 'FLIMFLIX Movies');
        $mail->addReplyTo('nmrajput11@gmail.com', 'FLIMFLIX Support');
        $mail->addAddress($to);

        // ---- Content ----
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; // Gujarati ke special chars mate
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        // ---- Attachment ----
        if ($file && file_exists($file)) {
            $mail->addAttachment($file);
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error for developer but return false for logic
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
