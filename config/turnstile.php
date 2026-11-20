<?php
// config/turnstile.php — Cloudflare Turnstile (anti-spam formulaires)
// Les clés réelles vivent dans turnstile.local.php (prod).
// En local, ce fichier les remplace : le widget Cloudflare n'autorise pas localhost.

$defaults = [
    'enabled'    => false,
    'site_key'   => '',
    'secret_key' => '',
    'widget'     => 'managed',
];

$local = __DIR__ . '/turnstile.local.php';
if (is_file($local)) {
    $defaults = array_replace_recursive($defaults, require $local);
}

$host = (string) ($_SERVER['HTTP_HOST'] ?? '');
$estLocal = $host === ''
    || str_starts_with($host, 'localhost')
    || str_starts_with($host, '127.0.0.1');

if ($estLocal) {
    $defaults['enabled'] = true;
    $defaults['site_key'] = '1x00000000000000000000AA';
    $defaults['secret_key'] = '1x0000000000000000000000000000000AA';
}

return $defaults;
