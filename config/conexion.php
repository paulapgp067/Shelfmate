<?php
// 1. Configuración

define('BASE_URL', 'http://localhost/Shelfmate/');

//FUNCIÓN DE FILTRADO QUE USO EN TODAS LAS PÁGINAS.
function filtrado($dato) {
    if (is_array($dato)) {
        return array_map('filtrado', $dato);
    }

    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

//2. Conexión.
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shelfmate";

$conn = new mysqli($host, $user, $pass, $dbname);

if($conn->connect_error){
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// 3. FUNCIONES 

// Para el LOGIN
function validarUsuario($conn, $usuario, $pass) {
    $sql = "SELECT id_usuario, alias, pass, rol FROM usuarios WHERE alias = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($fila = $result->fetch_assoc()) {
        if(password_verify($pass, $fila['pass'])){
            return $fila;
        }   
    }
    return false;
}

// Para el REGISTRO
function insertarUsuario($conn, $usuario, $pass, $claveShelf) {
    
//1.BUSCO CLUB POR LA CLAVE INTRODUCIDA
    $sql = "SELECT id_club FROM clubes WHERE clave = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $claveShelf);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false; // ESA CLAVE NO EXISTE
    }

    $club = $result->fetch_assoc();
    $id_club = $club['id_club'];

// 2. HAY CLAVE -> INTRODUZCO USUARIO
    $passSegura = password_hash($pass, PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (alias, pass) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $passSegura);

    if (!$stmt->execute()) {
        return false; // USUARIO DUPLICADO
    }

    $id_usuario = $conn->insert_id;

    // 3. METO LOS DATOS EN LA TABLA DE RELACIÓN
    $sql = "INSERT INTO clubes_usuarios (id_club, id_usuario) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_club, $id_usuario);
    $stmt->execute();

    return $id_usuario;
}

?>