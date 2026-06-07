<?php
require '../config/conexion.php';
require_once '../config/errores.php';

session_start();

// VALIDACIÓN DE SESIÓN
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header('Location: ' . BASE_URL . 'RECURSOS/error.php?code=401');
    exit;
}

$id_club = $_GET['id_club'] ?? null;

// OBTENER CLUB
$sql = "SELECT nombre FROM clubes WHERE id_club = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error SQL club: " . $conn->error);
}

$stmt->bind_param("i", $id_club);
$stmt->execute();

$result = $stmt->get_result();
$club = $result->fetch_assoc();

$stmt->close();

if (!$club) {
    header("Location: " . BASE_URL . "admin/panelAdmin.php");
    exit;
}

$nombreClub = $club['nombre'];


// PROCESAR FORMULARIO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $api_Book_id = $_POST['api_Book_id'] ?? null;
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $anioPublicacion = $_POST['anioPublicacion'] ?? 0;
    $portada = $_POST['portada'] ?? '';
    $fecha_reunion = $_POST['fecha_reunion'] ?? '';

    if (!$api_Book_id || !$titulo || !$fecha_reunion) {
        header('Location: ' . BASE_URL . 'error.php?code=321');
        exit;
    }


    // 1. BUSCAR LIBRO
    $sqlLibro = "SELECT id_libro FROM libros WHERE api_Book_id = ?";
    $stmt = $conn->prepare($sqlLibro);
    $stmt->bind_param("s", $api_Book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $libro = $result->fetch_assoc();
    $stmt->close();

    if ($libro) {
        $id_libro = $libro['id_libro'];
    } else {

        // INSERTAR LIBRO
        $sqlInsert = "INSERT INTO libros 
            (api_Book_id, titulo, autor, anioPublicacion, portada)
            VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sqlInsert);

        $stmt->bind_param(
            "sssis",
            $api_Book_id,
            $titulo,
            $autor,
            $anioPublicacion,
            $portada
        );

        $stmt->execute();

        $id_libro = $stmt->insert_id;

        $stmt->close();
    }

    // 2. INSERTAR LA REUNIÓN
    $sql = "INSERT INTO reuniones (id_club, id_libro, fecha_reunion)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iis",
        $id_club,
        $id_libro,
        $fecha_reunion
    );

    if ($stmt->execute()) {
        echo "<script>
                    alert('📍Reunión programada. ¡A leer!📖⭐');
                    window.location.href = 'programarReunion.php?id_club=$id_club';
                  </script>";
        exit;
    } else {
        die("Error al crear reunión: " . $stmt->error);
    }
}

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Paulah067">

        <title>Programar reunión - <?php echo filtrado($nombreClub); ?></title>

        <link rel="icon" href="<?= BASE_URL ?>recursos/img/logo.png">
        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
        
        <script src="<?= BASE_URL ?>recursos/js/autocompletado-libros.js"></script>
        <style>
        .buscador {
        position: relative;
        width: 100%;
        }

        #resultadosLibros {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;

            background: white;
            border: 1px solid #ddd;
            border-top: none;

            max-height: 300px;
            overflow-y: auto;

            z-index: 9999;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        #resultadosLibros div {
            display: flex;
            gap: 10px;
            padding: 10px;
            cursor: pointer;
            align-items: center;
        }

        #resultadosLibros div:hover {
            background: #f2f2f2;
        }

        #resultadosLibros img {
            width: 40px;
            height: 60px;
            object-fit: cover;
        }
        </style>
    </head>

    <body>

        <?php include '../recursos/nav.php'; ?>

        <header style="display: grid; justify-content: center;">
        <h1 class="title">Programar reunión - <span><?php echo filtrado($nombreClub); ?></span> </h1>  
        </header>

        <!-- MI FORMULARIO -->
        <main class="formulario">
            <form method="POST">

                <!-- BUSCADOR -->
                <div class="buscador">
                    <label>Buscar libro:</label>
                    <input type="text" id="busquedaLibro" autocomplete="off">
                    <div id="resultadosLibros"></div>
                </div>

                <!-- OCULTOS -->
                    <input type="hidden" id="api_Book_id" name="api_Book_id">
                    <input type="hidden" id="titulo" name="titulo">
                    <input type="hidden" id="autor" name="autor">
                    <input type="hidden" id="anioPublicacion" name="anioPublicacion">
                    <input type="hidden" id="portada" name="portada">

                <!-- fecha_reunion -->
                <div class="form-group">
                    <label>fecha_reunion:</label>
                    <input type="datetime-local" name="fecha_reunion" required>
                </div>

                <button type="submit">Programar reunión</button>

            </form>
        </main>

            <?php include '../recursos/footer.php'; ?>
        
    </body>
</html>