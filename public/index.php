<?php
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

$autoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Services/Csrf.php';
require_once __DIR__ . '/../src/Services/AssetHelper.php';
require_once __DIR__ . '/../src/Services/ViewHelper.php';
require_once __DIR__ . '/../src/Services/UrlHelper.php';
require_once __DIR__ . '/../src/Services/PanierService.php';

define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

$router = new Router();

$url = trim($_GET['url'] ?? '', '/');
if ($url === '') {
    $url = '/';
}

$router->dispatch($url);
