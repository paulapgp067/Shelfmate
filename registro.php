<?php
require_once 'config/conexion.php';

session_start();

$errores = [];
$usuario = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = filtrado($_POST['usuario'] ?? '');
    $pass = $_POST['pass'];
    $claveShelf = filtrado($_POST['claveShelf'] ?? '');

    if (empty($usuario) || empty($pass) || empty($claveShelf)) {
        $errores[] = "Todos los campos son obligatorios.";
    } 

    if(strlen($pass) < 8){
        $errores[] = "La contraseña debe tener mínimo 8 carácteres.";
    }

    if(empty($errores)){
        $sql = "SELECT id_usuario FROM usuarios WHERE alias = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores[] = "Ese nombre de usuario ya existe.";
        }
        $stmt->close();
    }

        // Intentamos insertar en la base de datos
        if(empty($errores)){
            $id = insertarUsuario($conn,$usuario,$pass,$claveShelf);

            if($id){
                $_SESSION['usuario'] = $usuario;
                $_SESSION['id_usuario'] = $id;
                $_SESSION['rol'] = 'usuario';

                header('Location: ' . BASE_URL . 'Usuario/shelf.php');
                exit;
            } else {
                $errores[] = "Código de acceso inválido o error al registrar.";
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
    <title>Registro - ShelfMate</title>

    <link rel="icon" type="image/png" sizes="64x64" href="recursos/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/login.css">
</head>

<body>
    <main class="columnas">
        <!--LADO TITULO:IZQ-->
        <div class="izquierda">
            <h1 class="title">Únete a <span>ShelfMate</span> </h1>
            <img src="recursos/img/logo.png" alt="logo">
        </div>

        <!--LADO formulario:DERECHA-->
        <div class="derecha">

            <!-- Mostrar errores si existen (usando el estilo de login) -->
            <?php if (!empty($errores)): ?>
                <div class="error-box">
                    <?php foreach ($errores as $error): ?>
                        <p style="margin:0;"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="formulario">
                <!-- FORMULARIO DE REGISTRO -->
                <form action='' method='post'>
                    <div class="form-group">
                        <label class="shelf-label" for="usuario">Elige tu nombre:</label>
                        <input type='text' id="usuario" name='usuario' value="<?= htmlspecialchars($usuario) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="shelf-label" for="pass">Crea tu código secreto:</label>
                        <input type='password' id="pass" name='pass' required>
                    </div>

                    <div class="form-group">
                        <label class="shelf-label" for="claveShelf">Código de Acceso:</label>
                        <input type="text" id="claveShelf" name="claveShelf" class="input-magic-code" maxlength="4" placeholder="H0L3" required>
                        <small class="helper-text">* Solicita este código a tu administrador/a</small>
                    </div>

                    <div class="form-group">
                        <button type='submit' name="btn_registro"> Registrarme </button>
                    </div>
                </form>
            </div>

            <div class="formulario" style="text-align: center;">
                <label class="form-group" style="color: #5c3765; display: block; margin-bottom: 10px;">
                    ¿Ya tienes una cuenta?
                </label>
                <div class="form-group">
                    <!-- Botón para volver al login con estilo similar -->
                    <a href="login.php" style="text-decoration: none;">
                        <button type="button" style="background-color: transparent; color: #5c3765; border: 2px solid #5c3765;"> 
                            Volver al acceso 
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>