<?php
session_start(); // Iniciar sesión
include 'conexion.php'; // Conectar con la base de datos

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejar la acción de agregar al carrito
if (isset($_GET['accion']) && $_GET['accion'] === 'agregar' && isset($_GET['id'])) {
    $idProducto = intval($_GET['id']); // Asegurar que el ID sea un entero

    // Consultar la base de datos para obtener los detalles del producto
    $sql = "SELECT * FROM Productos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$idProducto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // Verificar si el producto ya está en el carrito
        if (isset($_SESSION['carrito'][$idProducto])) {
            // Incrementar la cantidad
            $_SESSION['carrito'][$idProducto]['cantidad']++;
        } else {
            // Agregar el producto al carrito
            $_SESSION['carrito'][$idProducto] = [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => 1
            ];
        }
    }

    // Redirigir para evitar recargar con acción
    header('Location: productos.php');
    exit();
}

// Obtener los productos desde la base de datos
$sql = "SELECT * FROM Productos";
$stmt = $conexion->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        h2, h3 {
            text-align: center;
            color: orangered;
        }
        .productos {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .producto {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 15px;
            text-align: center;
        }
        .producto h3 {
            margin: 0;
            color: #007bff;
        }
        .producto p {
            margin: 5px 0;
            color: #555;
        }
        .producto img {
            margin: 10px 0;
            max-width: 100%;
            height: auto;
        }
        .producto a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            text-decoration: none;
            background-color: #28a745;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .producto a:hover {
            background-color: #218838;
        }
        .carrito {
            text-align: center;
            margin: 20px 0;
        }
        .carrito a {
            text-decoration: none;
            color: #007bff;
            font-size: 18px;
        }
        .logout {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .logout button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h2>ORANGE TECNOLOGY</h2>

    <!-- Botón de cierre de sesión -->
    <div class="logout">
        <form action="logout.php" method="post">
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>

    <div class="productos">
        <?php foreach ($productos as $producto): ?>
        <div class="producto">
            <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
            <p><?= htmlspecialchars($producto['descripcion']) ?></p>
            <p>Precio: $<?= number_format($producto['precio'], 2) ?></p>
            <p>Cantidad disponible: <?= $producto['cantidad'] ?></p>
            <?php if (!empty($producto['imagen'])): ?>
                <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
            <?php endif; ?>
            <a href="productos.php?accion=agregar&id=<?= $producto['id'] ?>">Agregar al carrito</a>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="carrito">
        <h3><a href="carrito.php">Ver mi carrito</a></h3>
    </div>
</body>
</html>
