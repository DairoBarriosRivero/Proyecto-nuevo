<?php
session_start(); // Iniciar sesión para verificar si el usuario está logueado

if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está logueado, redirigir al login
    header("Location: login.php");
    exit();
}

// Obtener los datos del usuario desde la sesión
$username = $_SESSION['username'];
$tipo = $_SESSION['tipo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2>Bienvenido, <?= htmlspecialchars($username) ?></h2>
    
    <p>Tipo de usuario: <?= htmlspecialchars($tipo) ?></p>

    <?php if ($tipo == 'admin'): ?>
        <p><a href="agregar_producto.php">Agregar un nuevo producto</a></p>
        <p><a href="productos.php">Ver productos</a></p>
    <?php else: ?>
        <p><a href="productos.php">Ver productos disponibles</a></p>
    <?php endif; ?>
    
    <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>
