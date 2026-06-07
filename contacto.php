<?php
require_once 'config/conexion.php';
session_start();

//VALIDACIÓN
if(!isset($_SESSION['id_usuario'])){
    header("Location: " . BASE_URL . "error.php?code=401");
    exit;
}


$id_usuario = $_SESSION['id_usuario'];

// TRAER ADMINS
  $sql = "SELECT id_usuario, nombre, alias FROM usuarios WHERE rol = 'admin' ";
  $result = $conn->query($sql);

  $administradores = [];

  if($result) {
    $administradores = $result->fetch_all(MYSQLI_ASSOC);
  }

  /* ENVIAR MENSAJE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_contactar'])) {

    $id_admin = (int) $_POST['admin_contactar'];
    $mensaje = trim($_POST['comentarios']);

    if (!empty($id_admin) && !empty($mensaje)) {

        $stmt = $conn->prepare("
            INSERT INTO mensajes (id_usuario, id_admin, mensaje)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param("iis", $id_usuario, $id_admin, $mensaje);
        $stmt->execute();

        echo "Mensaje enviado correctamente";
    }
}
?>
<!DOCTYPE html>
<html lang= "es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Paulah067">
    <title>Sobre ShelfMate</title>
    <link rel="icon" type="image/png" sizes="64x64" href="<?= BASE_URL ?>recursos/img/logo.png">

    <!-- FUENTES -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/contacto.css">
</head>
<body>
    <header>
        <?php include 'recursos/nav.php'; ?>    
    </header>

    <main class="box">
        <h1 class="title">Contacto</h1>

            <form method="POST">
                <div class="form-group">
                    <label for="admin_contactar">Admin: (elige uno):</label>

                        <select name="admin_contactar" id="admin_contactar" required>
                            <option value="">Selecciona un admin...</option>

                                <?php foreach($administradores as $admin): ?>
                            <option value="<?= $admin['id_usuario'] ?>">
                                <?= htmlspecialchars($admin['alias']) ?>
                            </option> 
                                <?php endforeach; ?>
                        </select>
                </div>

                <div class="form-group">
                    <textarea id="comentarios" name="comentarios" rows="10" cols="60" placeholder="Cuéntanos..."></textarea>
                </div>

            <button type="submit" name="btn_contactar" class="btn-contact">Contactar</button>
            </form>
    </main>       
</body>
</html>