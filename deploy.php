<?php
// Optional: Ganti dengan secret yang kamu set di GitHub Webhook
$secret = 'your_webhook_secret';

// Validasi signature dari GitHub (jika secret digunakan)
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');

if ($secret) {
    $hash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    if (!hash_equals($hash, $signature)) {
        http_response_code(403);
        die('Signature tidak valid.');
    }
}

// Jalankan Git Pull
$dir = '/home/anoadevc/public_html';
$cmd = "cd {$dir} && git pull origin main 2>&1";

exec($cmd, $output, $status);

// Log hasil (opsional)
file_put_contents("deploy.log", date('Y-m-d H:i:s') . "\n" . implode("\n", $output) . "\n\n", FILE_APPEND);

if ($status !== 0) {
    http_response_code(500);
    echo "Gagal pull:\n" . implode("\n", $output);
    exit;
}

echo "Berhasil deploy:\n" . implode("\n", $output);
