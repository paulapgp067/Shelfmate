<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: error.php?code=401');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

/* GUARDAR ESTADO */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $api_id = $_POST['api_id'] ?? null;
    $titulo = $_POST['titulo'] ?? null;
    $autor = $_POST['autor'] ?? null;
    $portada = $_POST['portada'] ?? null;
    $estado = $_POST['estado'] ?? null;

    if (!empty($api_id) && !empty($estado)) {

        // buscar libro
        $stmt = $conn->prepare("SELECT id_libro FROM libros WHERE api_Book_id = ?");
        $stmt->bind_param("s", $api_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $libro = $res->fetch_assoc();
        $stmt->close();

        //Si no esta, lo inserto.
        if ($libro) {
            $id_libro = $libro['id_libro'];
        } else {

            $stmt = $conn->prepare("
                INSERT INTO libros (api_Book_id, titulo, autor, portada)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("ssss", $api_id, $titulo, $autor, $portada);
            $stmt->execute();
            $id_libro = $stmt->insert_id;
            $stmt->close();
        }

        // insertar o actualizar estado
    $stmt = $conn->prepare("
            INSERT INTO usuario_libros (id_usuario, id_libro, estado)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE estado = VALUES(estado)
        ");

        $stmt->bind_param("iis", $id_usuario, $id_libro, $estado);
        $stmt->execute();
        $stmt->close();
        header("Location: panelLecturas.php?success=1");
        exit;
    } else {
        header("Location: panelLecturas.php?error=empty");
        exit;
    }
}
?>

