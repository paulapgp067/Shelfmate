<?php
require_once '../config/conexion.php';
session_start();

// SOLO ADMIN
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$id_admin = $_SESSION['id_usuario'];


//MARCAR MSG COMO LEÍDO
if (isset($_GET['leer'])) {
    $id_mensaje = (int) $_GET['leer'];

    $stmt = $conn->prepare("
        UPDATE mensajes
        SET leido = 1
        WHERE id_mensaje = ? AND id_admin = ?
    ");
    $stmt->bind_param("ii", $id_mensaje, $id_admin);
    $stmt->execute();

    header("Location: mensajes.php");
    exit;
}


// OBTENER MENSAJES
$sql = " SELECT m.id_mensaje, m.mensaje, m.fecha, m.leido, u.alias 
    AS usuario
    FROM mensajes m
    INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
    WHERE m.id_admin = ?
    ORDER BY m.fecha DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_admin);
$stmt->execute();

$mensajes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Paulah067">

        <title>Panel de mensajes</title>
        <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">
        <!-- FUENTES -->
        <link href="https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400&family=MedievalSharp&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
        <style>
        .contenedor {
            max-width: 800px;
            margin: auto;
        }

        .mensaje {
            background: white;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 6px solid transparent;
        }

        .mensaje.no-leido {
            border-left: 6px solid #e74c3c;
        }

        .usuario {
            font-weight: bold;
        }

        .fecha {
            font-size: 12px;
            color: #888;
        }

        .texto {
            margin-top: 10px;
        }

        .acciones {
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            padding: 6px 10px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
        }

        .btn:hover {
            background: #2980b9;
        }

        .leido {
            font-size: 12px;
            color: green;
            margin-left: 10px;
    }
    </style>
    </head>

    <body>

        <?php include '../recursos/nav.php'; ?>

        <header>
            <h1 class="title">📩 Mensajes recibidos</h1>   
        </header>


        <main class="contenedor">

            <?php if (empty($mensajes)): ?>
                <p style="text-align:center;">No tienes mensajes todavía.</p>
            <?php endif; ?>

            <?php foreach ($mensajes as $m): ?>

                <div class="mensaje <?= $m['leido'] ? '' : 'no-leido' ?>">

                    <div class="usuario">
                        <?= htmlspecialchars($m['usuario']) ?>

                        <?php if ($m['leido']): ?>
                            <span class="leido">✔ Leído</span>
                        <?php endif; ?>
                    </div>

                    <div class="fecha">
                        <?= $m['fecha'] ?>
                    </div>

                    <div class="texto">
                        <?= nl2br(htmlspecialchars($m['mensaje'])) ?>
                    </div>

                    <div class="acciones">

                        <?php if (!$m['leido']): ?>
                            <a class="btn" href="?leer=<?= $m['id_mensaje'] ?>">
                                Marcar como leído
                            </a>
                        <?php else: ?>
                            <span style="color:green;font-size:13px;">Mensaje leído</span>
                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </main>
        
            <?php require_once '../recursos/footer.php'; ?>
        
    </body>
</html>