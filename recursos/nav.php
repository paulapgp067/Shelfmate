<?php
$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT id_club 
        FROM clubes_usuarios 
        WHERE id_usuario = ? 
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en prepare nav: " . $conn->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$result = $stmt->get_result();

if ($result && $fila = $result->fetch_assoc()) {
    $id_club = $fila['id_club'];
}
?>

<nav class="main-navbar">

<!---LOGO-->
    <div class="nav-logo">
        <a href="" class="logo-link">
            <img src="<?= BASE_URL ?>recursos/img/logo.png" alt="Shelfmate logo"></a>
            <br><span style="margin-left: 15px;">ShelfMate</span>
        </a>
    </div>
    
    <!--LINKS-->
    <ul class="nav-links"> 
        <li><a href="<?= BASE_URL ?>explorar.php?id_usuario=<?= urlencode($id_usuario) ?>">Explorar</a></li> 
        <li><a href="<?= BASE_URL ?>salaComun.php?id_club=<?= urlencode($id_club) ?>">Sala común</a></li>
    </ul>
    <!--MÁS LINKS-->
    <div class="nav-actions">
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <a href="<?= BASE_URL ?>admin/panelAdmin.php" class="nav-btn">PanelAdmin</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>usuario/shelf.php" class="nav-btn">Shelf</a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>logout.php" class="nav-btn logout">Salir</a>
    </div>

</nav>