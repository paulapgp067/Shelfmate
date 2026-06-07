<?php
require_once 'config/conexion.php';
require_once 'config/booksApi.php';
require 'config/errores.php';


//VALIDACIÓN.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$estado = $_GET['estado'] ?? 'quiero_leer';

// OBTENER DATOS USUARIO
$sql = " SELECT alias
    FROM usuarios
    WHERE id_usuario = ?
    LIMIT 1;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$alias = $usuario['alias'] ?? '';


//OBTENER LIBROS SEGÚN EL FILTRO SELECCIONADO.
if ($estado === 'todos') {

    $sqlLibros = "SELECT l.id_libro, l.titulo, l.autor, l.portada
                  FROM libros l
                  INNER JOIN usuario_libros ul
                      ON l.id_libro = ul.id_libro
                  WHERE ul.id_usuario = ?
                  ORDER BY l.titulo";

    $stmtLibros = $conn->prepare($sqlLibros);
    $stmtLibros->bind_param("i", $id_usuario);

} else {

    $sqlLibros = "SELECT l.id_libro, l.titulo, l.autor, l.portada
                  FROM libros l
                  INNER JOIN usuario_libros ul
                      ON l.id_libro = ul.id_libro
                  WHERE ul.id_usuario = ?
                  AND ul.estado = ?
                  ORDER BY l.titulo";

    $stmtLibros = $conn->prepare($sqlLibros);
    $stmtLibros->bind_param("is", $id_usuario, $estado);
}

$stmtLibros->execute();
$libros = $stmtLibros->get_result();

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Paulah067">

        <title>Panel de lecturas - <?php echo filtrado($alias); ?></title>
        <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">

        <!-- FUENTES -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/panelLecturas.css">
        <script src="<?= BASE_URL ?>recursos/js/panel-lecturas.js" defer></script>
    </head>

    <body>
            <?php include 'RECURSOS/nav.php';?>

            <main class="columnas">

                <section class="explorador">
                    <h1 class="title">📚 Explorar libros</h1>
                        <div class="buscador-box">
                            <input type="text" id="buscador" placeholder="Buscar libros...">
                        </div>

                        <div id="resultados" class="resultados"></div>
                </section>

                <section class="registros">
                    <div class="btn_select">

                        <a class="btn <?= $estado === 'quiero_leer' ? 'activo' : '' ?>"
                        href="?estado=quiero_leer">
                            TBR
                        </a>

                        <a class="btn <?= $estado === 'leyendo' ? 'activo' : '' ?>"
                        href="?estado=leyendo">
                            Leyendo
                        </a>

                        <a class="btn <?= $estado === 'leido' ? 'activo' : '' ?>"
                        href="?estado=leido">
                            Leído
                        </a>

                        <a class="btn <?= $estado === 'todos' ? 'activo' : '' ?>"
                        href="?estado=todos">
                            Todos
                        </a>

                    </div>

                    <div class="panel">

                        <?php if ($libros->num_rows > 0): ?>

                            <?php while ($libro = $libros->fetch_assoc()): ?>

                                <div class="registro">

                                    <img src="<?= htmlspecialchars($libro['portada']) ?>"
                                        alt="Portada de <?= htmlspecialchars($libro['titulo']) ?>">

                                    <h3><?= htmlspecialchars($libro['titulo']) ?></h3>

                                    <p> <?= htmlspecialchars($libro['autor']) ?></p>
                                </div>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <div class="sin-libros">
                            🤔No hay libros en esta categoría.
                            </div>

                        <?php endif; ?>

                    </div>
                </section>      
            </main>

        <?php include 'recursos/footer.php'; ?>       
    </body>
</html>