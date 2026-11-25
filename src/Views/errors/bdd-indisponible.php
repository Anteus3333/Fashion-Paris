<?php
/** Page d’indisponibilité BDD — sans layout (le footer charge aussi MySQL). */
http_response_code(503);
header('Content-Type: text/html; charset=UTF-8');
header('Retry-After: 120');
$base = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service temporairement indisponible — Fashion Paris</title>
    <style>
        :root {
            --ink: #1a1520;
            --paper: #f7f2ec;
            --accent: #6b1428;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            font-family: "Segoe UI", system-ui, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, var(--paper) 0%, #efe6dc 100%);
        }
        main {
            max-width: 28rem;
            text-align: center;
        }
        .brand {
            margin: 0 0 .75rem;
            font-size: .8rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--accent);
        }
        h1 {
            margin: 0 0 .75rem;
            font-size: 1.55rem;
            line-height: 1.25;
        }
        p {
            margin: 0 0 1.25rem;
            color: #5c5566;
            line-height: 1.5;
        }
        a {
            color: var(--accent);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <main role="main">
        <p class="brand">Fashion Paris</p>
        <h1>Service temporairement indisponible</h1>
        <p>
            La boutique ne peut pas accéder à ses données pour le moment.
            Aucun détail technique n’est affiché ici. Merci de réessayer dans quelques instants.
        </p>
        <p><a href="<?= htmlspecialchars($base !== '' ? $base . '/' : '/') ?>">Réessayer</a></p>
    </main>
</body>
</html>
