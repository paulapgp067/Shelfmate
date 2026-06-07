<?php
require_once 'config/errores.php';
require_once 'config/conexion.php';

session_start();

$errorNum = $_GET['code'] ?? 500;
$errorMsg = $errores[$errorNum] ?? 'Error desconocido';

$rol = $_SESSION['rol'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Paulah067">
    <title>Error <?= $errorNum ?></title>
    <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">


    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Georgia', serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f4efe9, #e6d8c7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4b3f35;
        }

        .error-card {
            background: #fffaf4;
            padding: 2.5rem;
            border-radius: 16px;
            width: 90%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .emoji {
            font-size: 3rem;
            display: block;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .message {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .extra {
            font-size: 0.95rem;
            opacity: 0.8;
            margin-bottom: 2rem;
        }

        .btn {
            text-decoration: none;
            background: #8b5e3c;
            color: white;
            padding: 0.7rem 1.4rem;
            border-radius: 25px;
            transition: background 0.3s ease;
            display: inline-block;
        }

        .btn:hover {
            background: #6f472b;
        }
    </style>
</head>

<body>

    <main class="error-card">
        <span class="emoji">📚</span>

        <h1>Error <?= $errorNum ?></h1>

        <p class="message"><?= htmlspecialchars($errorMsg) ?></p>

        <p class="extra">Parece que esta página se ha perdido entre las estanterías…</p>

        <?php if ($rol !== 'admin'): ?>
            <a href="<?= BASE_URL ?>Usuario/shelf.php" class="btn">Volver a la shelf</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>Admin/panelAdmin.php" class="btn">Volver al panel admin</a>
        <?php endif; ?>

    </main>
</body>
</html>