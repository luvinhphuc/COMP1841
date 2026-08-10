<?php

namespace App\Services;

use InvalidArgumentException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

/**
 * Builds and sends SMTP messages from validated environment configuration.
 */
class MailService
{
    private array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? require ROOT_PATH . '/config/mail.php';
    }

    public function sendToAdmin(
        string $subject,
        string $htmlBody,
        string $plainBody = '',
        ?array $replyTo = null
    ) {
        $this->send(
            [
                'email' => $this->config['admin_email'] ?? '',
                'name' => $this->config['admin_name'] ?? '',
            ],
            $subject,
            $htmlBody,
            $plainBody,
            $replyTo
        );
    }

    public function send(
        array $recipient,
        string $subject,
        string $htmlBody,
        string $plainBody = '',
        ?array $replyTo = null
    ) {
        $host = trim((string) ($this->config['host'] ?? ''));
        $fromEmail = trim((string) ($this->config['from_email'] ?? ''));
        $recipientEmail = trim((string) ($recipient['email'] ?? ''));

        if ($host === '') {
            throw new RuntimeException('MAIL_HOST is not configured.');
        }

        $this->requireValidEmail($fromEmail, 'MAIL_FROM_ADDRESS');
        $this->requireValidEmail($recipientEmail, 'ADMIN_EMAIL');

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = max(1, (int) ($this->config['port'] ?? 587));
        $mail->Timeout = max(1, (int) ($this->config['timeout'] ?? 10));

        $username = trim((string) ($this->config['username'] ?? ''));
        $mail->SMTPAuth = $username !== '';

        if ($mail->SMTPAuth) {
            $password = (string) ($this->config['password'] ?? '');

            if ($password === '') {
                throw new RuntimeException('Mail password is not configured.');
            }

            $mail->Username = $username;
            $mail->Password = $password;
        }

        $this->configureEncryption($mail, (string) ($this->config['encryption'] ?? 'tls'));

        $mail->setFrom($fromEmail, trim((string) ($this->config['from_name'] ?? '')));
        $mail->addAddress($recipientEmail, trim((string) ($recipient['name'] ?? '')));

        if (is_array($replyTo)) {
            $replyToEmail = trim((string) ($replyTo['email'] ?? ''));

            if ($replyToEmail !== '') {
                $this->requireValidEmail($replyToEmail, 'Reply-To email');
                $mail->addReplyTo($replyToEmail, trim((string) ($replyTo['name'] ?? '')));
            }
        }

        $mail->isHTML(true);
        $mail->Subject = trim($subject);
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainBody !== ''
            ? $plainBody
            : trim(html_entity_decode(strip_tags($htmlBody), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $mail->send();
    }

    private function configureEncryption(PHPMailer $mail, string $encryption)
    {
        $encryption = strtolower(trim($encryption));

        if (in_array($encryption, ['tls', 'starttls'], true)) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            return;
        }

        if (in_array($encryption, ['ssl', 'smtps'], true)) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            return;
        }

        if ($encryption === '' || $encryption === 'none') {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
            return;
        }

        throw new RuntimeException('MAIL_ENCRYPTION must be tls, ssl, or none.');
    }

    private function requireValidEmail(string $email, string $setting)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException($setting . ' must contain a valid email address.');
        }
    }
}
