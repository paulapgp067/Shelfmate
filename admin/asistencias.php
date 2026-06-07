<?php
require_once '../config/conexion.php';
session_start();

// SOLO ADMIN
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: " . BASE_URL ."login.php");
    exit;
}

$id_club = $_GET['id_club'] ?? null;

if (!$id_club) {
    die("Club no especificado");
}

// OBTENER REUNIONES DEL CLUB
$sqlReuniones = "
SELECT r.id_reunion, r.fecha_reunion, l.titulo
FROM reuniones r
JOIN libros l ON r.id_libro = l.id_libro
WHERE r.id_club = ?
ORDER BY r.fecha_reunion ASC
";

$stmt = $conn->prepare($sqlReuniones);
$stmt->bind_param("i", $id_club);
$stmt->execute();
$reuniones = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="icon" href="<?= BASE_URL ?>recursos/img/logo.png">
        <title>Asistencias del Club</title>

        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">

        <style>
            .reunion {
                background: white;
                margin-bottom: 25px;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(0,0,0,0.1);
                font-family: 'Playfair Display', serif;
            }

            .stats {
                display: flex;
                gap: 10px;
                margin: 10px 0;
            }

            .box {
                flex: 1;
                padding: 8px;
                border-radius: 8px;
                text-align: center;
                font-weight: bold;
            }

            .si { background: #d4f8d4; }
            .no { background: #ffd6d6; }
            .pend { background: #e0e0e0; }

            ul {
                padding-left: 20px;
            }

            li {
                padding: 6px 0;
                border-bottom: 1px solid #eee;
            }
        </style>
    </head>

    <body>

        <?php include '../recursos/nav.php'; ?>

        <header>
            <h1 class="title">📊 Asistencias del club</h1>
        </header>
        
        <?php while ($r = $reuniones->fetch_assoc()): ?>

        <?php
        $id_reunion = $r['id_reunion'];

        // TODOS LOS USUARIOS + RESPUESTA
        $sql = "
        SELECT u.alias, a.respuesta
        FROM usuarios u
        LEFT JOIN asistencias a 
            ON a.id_usuario = u.id_usuario 
            AND a.id_reunion = ?
        WHERE u.rol = 'usuario'
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_reunion);
        $stmt->execute();
        $res = $stmt->get_result();

        $si = 0;
        $no = 0;
        $pend = 0;

        $asistentes = [];

        while ($row = $res->fetch_assoc()) {

            if ($row['respuesta'] === null) {
                $pend++;
            } elseif ((int)$row['respuesta'] === 1) {
                $si++;
                $asistentes[] = $row['alias'];
            } else {
                $no++;
            }
        }

        $stmt->close();
        ?>

        <div class="reunion">

            <h2>📚 <?= htmlspecialchars($r['titulo']) ?></h2>
            <p>📅 <?= date('d/m/Y', strtotime($r['fecha_reunion'])) ?></p>

            <!-- STATS -->
            <div class="stats">
                <div class="box si">✔️ Van: <?= $si ?></div>
                <div class="box no">❌ No van: <?= $no ?></div>
                <div class="box pend">⏳ Sin responder: <?= $pend ?></div>
            </div>

            <!-- AHORA UNA LISTA DE SOLO LOS QUE VAN -->
            <h3>✔️ Asistentes</h3>

            <ul>
                <?php if (count($asistentes) > 0): ?>
                    <?php foreach ($asistentes as $alias): ?>
                        <li>✔️ <?= htmlspecialchars($alias) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>❌ Nadie ha confirmado asistencia todavía</li>
                <?php endif; ?>
            </ul>

        </div>

        <?php endwhile; ?>

        <?php require_once '../recursos/footer.php'; ?>

    </body>
</html>