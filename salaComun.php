<?php
require 'config/conexion.php';
require_once 'config/errores.php';

session_start();

// 1. VALIDACIÓN DE SESIÓN
if(!isset($_SESSION['id_usuario'])){
    header("Location: " . BASE_URL . "error.php?code=401");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];

$id_club = $_GET['id_club'] ?? null;

if (!$id_club) {
    if($rol != 'admin'){
        header("Location: " . BASE_URL . "Usuario/shelf.php");
         exit;
         } else {
            header("Location: " . BASE_URL . "Admin/panelAdmin.php");
            exit;
         }
    
}

// 2. DATOS DEL CLUB
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

$nombreClub = $club['nombre'];

// 5. OBTENER PRÓXIMAS REUNIONES
$sql_fechas = "
    SELECT  r.id_reunion, r.fecha_reunion, l.titulo, l.autor
    FROM reuniones r
    INNER JOIN libros l ON r.id_libro = l.id_libro
    WHERE r.id_club = ? AND r.fecha_reunion >= NOW()
    ORDER BY r.fecha_reunion ASC
    LIMIT 12
";

// LÍMITE 12 PARA QUE SEA A UN AÑO VISTA

$stmt_fechas = $conn->prepare($sql_fechas);

if (!$stmt_fechas) {
    die("Error SQL reuniones: " . $conn->error);
}

$stmt_fechas->bind_param("i", $id_club);
$stmt_fechas->execute();

$result_fechas = $stmt_fechas->get_result();



// ASISTENCIA
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $respuesta = (int) $_POST['respuesta'];
    $id_reunion = (int) $_POST['id_reunion'];

    // 1.MIRO QUE NO ESTÉ YA CONTESTADA LA PREGUNTA POR EL USUARIO
    $sql_check = "SELECT id_asistencia 
                  FROM asistencias 
                  WHERE id_usuario = ? AND id_reunion = ?";
    $stmt_check = $conn->prepare($sql_check);

    if(!$stmt_check){
        die("Error SQL check: " . $conn->error);
    }

    $stmt_check->bind_param("ii", $id_usuario, $id_reunion);

    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    // 2. SI YA EXISTE → UPDATE
    if($result_check->num_rows > 0){
        $sql = "UPDATE asistencias 
                SET respuesta = ? 
                WHERE id_usuario = ? AND id_reunion = ?";
        $stmt = $conn->prepare($sql);

        if(!$stmt){
            die("Error SQL update: " . $conn->error);
        }

        $stmt->bind_param("iii", $respuesta, $id_usuario, $id_reunion);

        if(!$stmt->execute()){
            die("Error UPDATE: " . $stmt->error);
        }

        $stmt->close();

        // 3. NO EXISTE LA RESPUESTA -> INSERT

    } else {
        $sql = "INSERT INTO asistencias (id_usuario, id_reunion, respuesta) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);

        if(!$stmt){
            die("Error SQL asistencia: " . $conn->error);
        }

        $stmt->bind_param("iii", $id_usuario, $id_reunion, $respuesta);

        if(!$stmt->execute()){
            die("Error INSERT: " . $stmt->error);
        }

        $stmt->close();
        }

        $stmt_check->close();
    }
 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Paulah067">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala común de <?php echo filtrado($nombreClub); ?></title>

    <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400&family=MedievalSharp&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/salaComun.css">
</head>

<body>

        <?php include 'RECURSOS/nav.php'; ?>

    <main class="sala-container">

        <h1 class="titulo-sala">
            📖 Sala común de <span><?php echo filtrado($nombreClub); ?></span>
        </h1>

        <!-- FECHAS DE REUNIONES -->
        <div class="cajon">

            <?php if ($result_fechas->num_rows > 0): ?>

                <?php while($reunion = $result_fechas->fetch_assoc()): ?>
                    <div class="mensaje" onclick="abrirModal(<?= $reunion['id_reunion'] ?>)">
                    <strong>📚 <?php echo filtrado($reunion['titulo']); ?></strong><br>
                    <small>✍️ <?php echo filtrado($reunion['autor']); ?></small><br>
                    <small>📅 <?php echo date('d/m/Y H:i', strtotime($reunion['fecha_reunion'])); ?></small>
                </div>
                <?php endwhile; ?>

                <?php else: ?>
                <p class="mensaje-vacio">No hay fechas nuevas</p>

            <?php endif; ?>

        </div>

    </main>
 <!-- FORMULARIO DE ASISTENCIA -->
    <div id="modal" class="modal hidden">

        <div class="modal-content">

            <h2>¿Asistirás a la reunión?</h2>

            <form method="POST">
                <input type="hidden" name="id_reunion" id="msg_id" required>

                <button type="submit" name="respuesta" value="1">Sí✨</button>
                <button type="submit" name="respuesta" value="0">No🥲</button>
            </form>

            <button onclick="cerrarModal()">Cerrar</button>
        </div>

    </div>

            <?php include 'RECURSOS/footer.php'; ?> 

<!-- JS -->
<script>
    function abrirModal(msgId){
        document.getElementById('modal').classList.remove("hidden");
        document.getElementById('msg_id').value = msgId;
    }

    function cerrarModal(){
        document.getElementById('modal').classList.add("hidden");
    }
</script>
</body>
</html>