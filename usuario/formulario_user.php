<?php
require_once '../config/conexion.php';
require_once '../config/errores.php';
session_start();

//PROTECCIÓN de acceso.
if(!isset($_SESSION['id_usuario'])){
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// SESIÓN
// $usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['id_usuario'];
$errores = [];

$rol = $_SESSION['rol'];

// PRECARGA DATOS
$sql = "SELECT nombre, alias, email, pass, fecha_nac, genero_principal, tropes 
        FROM usuarios 
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!is_array($user)) {
    $errores[] = "No se pudieron cargar los datos del usuario";
    $user = [
        'nombre' => '',
        'alias' => '',
        'email' => '',
        'fecha_nac' => '',
        'genero_principal' => '',
        'tropes' => ''
    ];
}

$tropes_usuario = !empty($user['tropes'])
    ? explode(',', $user['tropes'])
    : [];

//FORM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_editar'])) {

    $datos_limpios = array_map('filtrado', $_POST);

    $nombre = $datos_limpios['nombre'] ?? '';
    $alias = $datos_limpios['alias'] ?? '';
    $email = $datos_limpios['email'] ?? '';
    $pass = $datos_limpios['pass'] ?? '';
    $fecha_nac = $datos_limpios['fechaNac'] ?? '';
    $genero = $datos_limpios['genero_principal'] ?? '';
    $tropes = $datos_limpios['tropes'] ?? [];

    if(!is_array($tropes)){
        $tropes = [];
    }

    $tropes = implode(',', $tropes);

    // VALIDACIONES
    if (empty($alias)) $errores[] = "El alias es obligatorio";

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email no válido";
    }


    // ACTUALIZACIÓN
    if(empty($errores)){
        try{

            if (!empty($pass)) {

                $sql = "UPDATE usuarios 
                        SET nombre=?, email=?, alias=? pass=?, fecha_nac=?, genero_principal=?, tropes=? 
                        WHERE id_usuario=?";

                $stmt = $conn->prepare($sql);

                $passHash = password_hash($pass, PASSWORD_DEFAULT);

                $stmt->bind_param(
                    "sssssssi",
                    $nombre,
                    $alias,
                    $email,
                    $passHash,
                    $fecha_nac,
                    $genero,
                    $tropes,
                    $id_usuario
                );

            } else {

                $sql = "UPDATE usuarios 
                        SET nombre=?, alias=?, email=?, fecha_nac=?, genero_principal=?, tropes=? 
                        WHERE id_usuario=?";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "ssssssi",
                    $nombre,
                    $alias,
                    $email,
                    $fecha_nac,
                    $genero,
                    $tropes,
                    $id_usuario
                );
            }

            $stmt->execute();

            $_SESSION['usuario'] = $alias;

            if($_SESSION['rol'] === 'admin'){
                echo "<script>
                    alert('Perfil actualizado correctamente');
                    window.location.href = '../admin/panelAdmin.php';
                  </script>";
            exit;
            } else {
                echo "<script>
                    alert('Perfil actualizado correctamente');
                    window.location.href = 'shelf.php';
                  </script>";
            exit;
            }

        } catch(Exception $e){
            $errores[] = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Paulah067">

        <title>Mi Shelf: Edición</title>
        <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
         <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/login.css">
        <script src="<?= BASE_URL ?>recursos/js/generos-tropes.js" defer></script>
    </head>

    <body>  

        <div class="formulario">
            <h1 class="login-title">Editar Perfil &#9997; </h1>
            <!--BLOQUE DE ERRORES -->
            <?php if (!empty($errores)): ?>
            <div class="error-box">
                <?php foreach ($errores as $error): echo "<p>$error</p>"; endforeach; ?>
            </div>
        <?php endif; ?>
        <!--FIN BLOQUE DE ERRORES -->

           <form action="" method="post">
            <div class="form-group">
                <label class="shelf-label" for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>">
            </div>
            <div class="form-group">
                <label class="shelf-label" for="alias">Alias:</label>
                <input type="text" id="alias" name="alias" value="<?= htmlspecialchars($user['alias']) ?>" required>
            </div>

            <div class="form-group">
                        <label class="shelf-label" for="pass">Código secreto:</label>
                        <input type='password' id="pass" name='pass' 
                            placeholder="...">
            </div>

            <div class="form-group">
                        <label class="shelf-label" for="email">Email:</label>
                        <input type='email' id="email" name='email' value="<?= htmlspecialchars($user['email']) ?>" placeholder="yo@example.com" required>
            </div>

            <div class="form-group">
                        <label class="shelf-label" for="fechaNac">Cumple &#127775; :</label>
                        <input type='date' id="fechaNac" name='fechaNac' value="<?= $user['fecha_nac'] ?>">
            </div>
            
            <div class="form-group"> 
                        <label class="shelf-label" for="generos">Género principal: (elige uno para ver tropes):</label>
                        <select name="genero_principal" id="genero_principal" required>
                    <option value="">Selecciona un género...</option>
                    <option value="romance" <?= $user['genero_principal'] === 'romance' ? 'selected' : '' ?>>Romance / Romantasy</option>
                    <option value="thriller" <?= $user['genero_principal'] === 'thriller' ? 'selected' : '' ?>>Thriller / Policiaca</option>
                    <option value="fantasia" <?= $user['genero_principal'] === 'fantasia' ? 'selected' : '' ?>>Fantasía / Ciencia Ficción</option>
                    <option value="otros" <?= $user['genero_principal'] === 'otros' ? 'selected' : '' ?>>Otros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tropes">Tropes sugeridos:</label>
                <select multiple name="tropes[]" id="tropes">
                    <!-- Esto se RELLENA con JavaScript -->
                    <option value="">Primero elige un género...</option>
                </select>
                <small class="helper-text">*Mantén Ctrl para elegir varios</small>
            </div>

            <button type="submit" name="btn_editar" class="btn-save">Guardar Cambios</button>
        </form>           
    </div>
</body>
</html>