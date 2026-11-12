<?php
// config/turnstile.php — Cloudflare Turnstile (anti-spam formulaires)

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

return $defaults;
