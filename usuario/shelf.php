<?php
session_start();
require '../config/conexion.php';
require_once '../config/errores.php';

//VALIDACIÓN DE SESIÓN
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'usuario') {
    header('Location: ' . BASE_URL . 'error.php?code=401');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];

// DATOS DEL PERFIL
$sql = "SELECT nombre, alias, email, fecha_nac, genero_principal, tropes
        FROM usuarios
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta " . $conn->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// DATOS POR DEFECTO
$alias = !empty($user['alias']) ? $user['alias'] : 'usuario';

$tropes_fav = !empty($user['tropes'])
    ? explode(',', $user['tropes'])
    : [];

$generos_fav = !empty($user['genero_principal'])
    ? $user['genero_principal']
    : 'Todos';

$fecha_nac = !empty($user['fecha_nac'])
    ? $user['fecha_nac']
    : 'Sospecha de vampiro';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Paulah067">

    <title> Mi Shelf: Estantería personal de <?php echo filtrado($alias); ?></title>
    <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">
    <!-- FUENTES -->
    <link href="https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400&family=MedievalSharp&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&display=swap" rel="stylesheet">
   
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/shelf.css">
</head>

<body>

    <?php include '../recursos/nav.php'; ?>

    <div class="columnas">
            <!-- LADO IZQUIERDA -->
        <section class="izquierda">

            <h1 class="shelf-title">Érase una vez,<span><?php echo filtrado($usuario); ?></span></h1>
            <div class="caja-perfil">
                <img src="<?= BASE_URL ?>recursos/img/user.jpg" alt="Imagen de perfil del usuario">
                <p>
                    <strong>Usuario:</strong>
                    <?php echo filtrado($usuario); ?>
                </p>

                <p>
                    <strong>Fecha de nacimiento:</strong>
                    <?php echo date('d/m/Y', strtotime($fecha_nac)); ?> 📅
                </p>

                <p><strong>Tropes favoritos:</strong></p>

                <ul class="tropes-list">
                    <?php foreach ($tropes_fav as $trope): ?>
                        <li><?php echo htmlspecialchars($trope); ?></li>
                    <?php endforeach; ?>
                </ul>

                <p>
                    <strong>Géneros favoritos:</strong><br>
                    <?php echo filtrado($generos_fav); ?>
                </p>

            </div>

            <!-- BOTÓN EDITAR -->
        <a href="<?= BASE_URL ?>usuario/formulario_user.php" class="button-edit">Editar perfil</a>

        </section>
             <!-- LADO DERECHO -->
        <section class="derecha">
            <div class="tablon-label">
                <a href="<?= BASE_URL ?>panelLecturas.php?id_usuario=<?= $id_usuario ?>">Panel de lecturas</a>
            </div>

            <div class="tablon-label">
                <a href="<?= BASE_URL ?>error.php?code=204"">Clubes</a>
            </div>

            <div class="tablon-label">
                <a href="<?= BASE_URL ?>contacto.php?id_usuario=<?= $id_usuario ?>">Contactar a un admin</a>
            </div>

        </section>
    </div>

    <?php require_once '../recursos/footer.php'; ?>
</body>
</html>