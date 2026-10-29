<?php
require_once __DIR__ . '/includes/Mailer.php';

$mailer = new SipMailer();

$sent = $mailer->send(
    $mailer->adminEmail(),
    'Test Mail From SIP Innovation',
    '<p>Hello Admin, this is a test email from SIP Innovation website.</p>',
    'adityapratap7391@gmail.com'
);

echo $sent ? 'Mail sent successfully.' : 'Mail failed: ' . htmlspecialchars($mailer->lastError(), ENT_QUOTES, 'UTF-8');
