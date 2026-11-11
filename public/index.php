<?php
session_start();

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
