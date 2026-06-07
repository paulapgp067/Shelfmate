<?php
require_once 'config/conexion.php';

session_start();

$errores = [];
$usuario = '';


if (isset($_SESSION['usuario']) && !isset($_POST['login'])) {
    $usuario = $_SESSION['usuario'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // LOGEO
    if (isset($_POST['btn_login'])) {
    $usuario = trim($_POST['usuario']);
    $pass = filtrado($_POST['pass']);

    if (empty($usuario) || empty($pass)) {
        $errores[] = "Por favor, rellena todos los campos para entrar.";
    } else { 
        $user = validarUsuario($conn, $usuario, $pass); 

        if ($user) { 
            $_SESSION['usuario'] = $user['alias'];      
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['rol'] = $user['rol'];
            

            if($_SESSION['rol'] === 'admin'){
                header('Location: Admin/panelAdmin.php');
                exit;
            } 
            header('Location: Usuario/shelf.php');
            exit;

        } else {
            $errores[] = "Usuario o código secreto incorrectos.";
        }
    }
}

    // REDIRECCIÓN A REGISTRO 
    if (isset($_POST['btn_ir_registro'])) {
        header('Location: registro.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Paulah067">
        <title>Zona de lectores: acceso restringido</title>

        <link rel="icon" type="image/png" sizes="64x64" href="recursos/img/logo.png">
        
        <!-- FUENTE -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/login.css">
    </head>

    <body>
        <div class="columnas">
            <!--LADO TITULO:IZQ-->
            <div class="izquierda">
                <h1 class="title">Bienvenida/o a <span>ShelfMate</span> </h1>
                <img src="recursos/img/logo.png" alt="logo">
            </div>

            <!--LADO formulario:DERECHA-->
            <div class="derecha">

                <!-- Mostrar errores si existen -->
                <?php if (!empty($errores)): ?>
                    <div class="error-box">
                        <?php foreach ($errores as $error): ?>
                            <p style="margin:0;"><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="formulario">
                    <!-- FORMULARIO DE LOGIN -->
                    <form action='' method='post'>
                        <div class="form-group">
                            <label class="shelf-label" for="alias">Tu alias:</label>
                            <input type='text' id="alias" name='usuario' value="<?= htmlspecialchars($usuario) ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="shelf-label" for="pass">Tu código secreto:</label>
                            <input type='password' id="pass" name='pass' required>
                        </div>

                        <div class="form-group">
                            <button type='submit' name="btn_login"> Empezar </button>
                        </div>
                    </form>
                </div>

                <div class="formulario">
                    <label class="form-group" style="color: #5c3765; text-align: center; display: block; margin-bottom: 10px;">
                        ¿Aún no formas parte de ninguna shelf?
                    </label>

                    <!-- FORMULARIO PARA REDIRIGIR A REGISTRO-->
                    <form action="" method="POST">
                        <div class="form-group">
                            <label class="shelf-label">Código de Acceso</label>
                            <input type="text" name="idShelf" class="input-magic-code" value="F001" maxlength="4" placeholder="ID" required>
                            <small class="helper-text">4 caracteres (proporcionado por Admin)</small>
                        </div>
                        <div class="form-group">
                            <button type='submit' name="btn_ir_registro"> Registrarme </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>

</html>