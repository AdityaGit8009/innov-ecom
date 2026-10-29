<?php

class SipMailer
{
    private array $config;
    private string $lastError = '';

    public function __construct()
    {
        $cfgPath = __DIR__ . '/../config/mail.php';
        $this->config = is_readable($cfgPath) ? require $cfgPath : [];
    }

    public function adminEmail(): string
    {
        return $this->config['admin_email'] ?? 'webdeveloper8009@gmail.com';
    }

    public function lastError(): string
    {
        return $this->lastError;
    }

    /**
     * Send HTML email using Gmail SMTP.
     *
     * @param array<int,array{path:string,name?:string,type?:string}> $attachments
     */
    public function send(string $to, string $subject, string $html, string $replyTo = '', array $attachments = []): bool
    {
        $to = trim($to);

        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->lastError = 'Invalid recipient email.';
            return false;
        }

        $fromEmail = trim($this->config['from_email'] ?? '');
        $fromName  = trim($this->config['from_name'] ?? 'Softpro Innovations');
        $smtpUser  = trim($this->config['smtp_user'] ?? $fromEmail);
        $smtpPass  = trim($this->config['smtp_pass'] ?? '');

        if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $this->lastError = 'Invalid from email in config/mail.php.';
            return false;
        }

        if ($smtpUser === '' || $smtpPass === '' || $smtpPass === 'PASTE_GMAIL_APP_PASSWORD_HERE') {
            $this->lastError = 'SMTP user/app password missing in config/mail.php.';
            error_log('SipMailer Error: ' . $this->lastError);
            return false;
        }

        $body = $this->wrapTemplate($subject, $html);
        $message = $this->buildMessage($fromEmail, $fromName, $replyTo, $body, $attachments);

        return $this->sendSmtp($to, $subject, $message, $fromEmail, $smtpUser, $smtpPass);
    }

    private function buildMessage(string $fromEmail, string $fromName, string $replyTo, string $htmlBody, array $attachments = []): string
    {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'From: ' . $this->encodeHeader($fromName) . ' <' . $fromEmail . '>';

        if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers[] = 'Reply-To: ' . $replyTo;
        }

        $headers[] = 'Date: ' . date('r');
        $headers[] = 'X-Mailer: SIP Innovation SMTP Mailer';

        $validAttachments = [];
        foreach ($attachments as $attachment) {
            $path = (string)($attachment['path'] ?? '');
            if ($path !== '' && is_readable($path) && is_file($path)) {
                $validAttachments[] = $attachment;
            }
        }

        if (empty($validAttachments)) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            return implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody;
        }

        $boundary = 'sip_mixed_' . md5(uniqid((string)mt_rand(), true));
        $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

        $message = implode("\r\n", $headers) . "\r\n\r\n";
        $message .= '--' . $boundary . "\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $message .= $htmlBody . "\r\n\r\n";

        foreach ($validAttachments as $attachment) {
            $path = (string)$attachment['path'];
            $name = (string)($attachment['name'] ?? basename($path));
            $type = (string)($attachment['type'] ?? 'application/octet-stream');
            $safeName = str_replace(['"', "\r", "\n"], '', $name);

            $message .= '--' . $boundary . "\r\n";
            $message .= 'Content-Type: ' . $type . '; name="' . $safeName . '"' . "\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n";
            $message .= 'Content-Disposition: attachment; filename="' . $safeName . '"' . "\r\n\r\n";
            $message .= chunk_split(base64_encode((string)file_get_contents($path))) . "\r\n";
        }

        $message .= '--' . $boundary . "--";
        return $message;
    }

    private function sendSmtp(string $to, string $subject, string $message, string $fromEmail, string $smtpUser, string $smtpPass): bool
    {
        $host = $this->config['smtp_host'] ?? 'smtp.gmail.com';
        $port = (int)($this->config['smtp_port'] ?? 587);
        $timeout = 30;
        $errno = 0;
        $errstr = '';

        $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $timeout);

        if (!$socket) {
            $this->lastError = "SMTP connection failed: {$errstr} ({$errno})";
            error_log('SipMailer Error: ' . $this->lastError);
            return false;
        }

        stream_set_timeout($socket, $timeout);

        try {
            $this->expect($socket, [220]);
            $this->command($socket, 'EHLO localhost', [250]);
            $this->command($socket, 'STARTTLS', [220]);

            if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception('Unable to start TLS encryption. Enable OpenSSL in PHP.');
            }

            $this->command($socket, 'EHLO localhost', [250]);
            $this->command($socket, 'AUTH LOGIN', [334]);
            $this->command($socket, base64_encode($smtpUser), [334]);
            $this->command($socket, base64_encode($smtpPass), [235]);
            $this->command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
            $this->command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            $this->command($socket, 'DATA', [354]);

            $encodedSubject = $this->encodeHeader($subject);
            $data = 'Subject: ' . $encodedSubject . "\r\n" . $message . "\r\n.";
            $this->command($socket, $data, [250]);
            $this->command($socket, 'QUIT', [221]);

            fclose($socket);
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log('SipMailer Error: ' . $this->lastError);
            if (is_resource($socket)) {
                @fclose($socket);
            }
            return false;
        }
    }

    private function command($socket, string $command, array $expectedCodes): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->expect($socket, $expectedCodes);
    }

    private function expect($socket, array $expectedCodes): string
    {
        $response = '';

        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;

            if (preg_match('/^(\d{3})\s/', $line, $m)) {
                $code = (int)$m[1];

                if (!in_array($code, $expectedCodes, true)) {
                    throw new Exception('SMTP error: ' . trim($response));
                }

                return $response;
            }
        }

        throw new Exception('SMTP server did not respond.');
    }

    private function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function wrapTemplate(string $title, string $content): string
    {
        $brand = htmlspecialchars($this->config['from_name'] ?? 'Softpro Innovations', ENT_QUOTES, 'UTF-8');
        return '<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
            . '<body style="margin:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111827;">'
            . '<div style="max-width:680px;margin:0 auto;padding:28px 14px;">'
            . '<div style="background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e8edf2;box-shadow:0 14px 40px rgba(17,24,39,.08);">'
            . '<div style="background:linear-gradient(135deg,#111827,#fb5b21);color:#ffffff;padding:24px;">'
            . '<div style="font-size:13px;letter-spacing:.08em;text-transform:uppercase;opacity:.9;">' . $brand . '</div>'
            . '<h2 style="margin:8px 0 0;font-size:22px;line-height:1.35;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2></div>'
            . '<div style="padding:26px 24px;font-size:15px;line-height:1.7;">' . $content . '</div>'
            . '<div style="padding:16px 24px;background:#fff7ed;color:#6b7280;font-size:13px;line-height:1.5;">'
            . '<b>' . $brand . '</b><br>This is an automated email. Please do not reply unless required.</div>'
            . '</div></div></body></html>';
    }
}
