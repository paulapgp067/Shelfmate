<?php
require_once 'config/conexion.php';
require_once 'config/booksApi.php';
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Explorar libros - ShelfMate</title>
        <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">

        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/explorar.css">

        <script src="<?= BASE_URL ?>recursos/js/buscador-libros.js" defer></script>
    </head>

    <body>

        <?php include 'recursos/nav.php'; ?>

        <header>
            <h1 class="title">📚 Explorar libros</h1>
        </header>

        <main>
            <div class="buscador-box">
                <input type="text" id="buscador" placeholder="Buscar libros...">
            </div>

            <div id="loading" class="loading">
                <div class="libro">📚</div>
                <p></p>
            </div>

            <div id="resultados" class="resultados"></div>
        </main>
    </body>
</html>