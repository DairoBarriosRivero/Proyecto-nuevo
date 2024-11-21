<?php
session_start();
include 'conexion.php'; // Conectar a la base de datos

// Obtener el carrito de la sesión
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Obtener el ID del usuario
$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : null;

if (!$id_usuario) {
    die("Error: No se ha identificado al usuario.");
}

// Manejar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];

        // Actualizar cantidades de productos
        if ($accion === 'actualizar') {
            foreach ($_POST['cantidad'] as $idProducto => $nuevaCantidad) {
                $idProducto = intval($idProducto); // Asegurar que el ID es entero
                $nuevaCantidad = intval($nuevaCantidad); // Asegurar que la cantidad es un entero
                if ($nuevaCantidad > 0 && isset($carrito[$idProducto])) {
                    $carrito[$idProducto]['cantidad'] = $nuevaCantidad;
                } elseif ($nuevaCantidad == 0) {
                    unset($carrito[$idProducto]); // Eliminar el producto si la cantidad es 0
                }
            }
        }

        // Eliminar un producto del carrito
        if ($accion === 'eliminar' && isset($_POST['id'])) {
            $idProducto = intval($_POST['id']); // Asegurar que el ID es entero
            unset($carrito[$idProducto]);
        }

        // Registrar venta
        if ($accion === 'pagar' && !empty($carrito)) {
            try {
                // Iniciar transacción
                $conexion->beginTransaction();

                // Registrar cada producto del carrito en la tabla Ventas
                foreach ($carrito as $idProducto => $item) {
                    $stmtVenta = $conexion->prepare(
                        "INSERT INTO Ventas (id_usuario, id_producto, cantidad, precio_total) 
                        VALUES (?, ?, ?, ?)"
                    );
                    $precioTotal = $item['precio'] * $item['cantidad'];
                    $stmtVenta->execute([$id_usuario, $idProducto, $item['cantidad'], $precioTotal]);
                }

                // Confirmar transacción
                $conexion->commit();

                // Vaciar el carrito
                unset($_SESSION['carrito']);

                // Redirigir con mensaje de éxito
                header('Location: carrito.php?mensaje=compra_realizada');
                exit();
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $conexion->rollBack();
                die("Error al procesar la compra: " . $e->getMessage());
            }
        }

        // Guardar los cambios en la sesión
        $_SESSION['carrito'] = $carrito;

        // Redirigir para evitar reenvíos
        header('Location: carrito.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito</title>
</head>
<body>
    <h2>Mi Carrito</h2>
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'compra_realizada'): ?>
        <p style="color: green;">¡Compra realizada con éxito!</p>
    <?php endif; ?>

    <?php if (empty($carrito)): ?>
        <p>No hay productos en el carrito.</p>
    <?php else: ?>
        <form method="post" action="carrito.php">
            <table border="1">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrito as $id => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td>$<?= number_format($item['precio'], 2) ?></td>
                        <td>
                            <!-- Campo para actualizar cantidad -->
                            <input type="number" name="cantidad[<?= $id ?>]" value="<?= $item['cantidad'] ?>" min="0">
                        </td>
                        <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                        <td>
                            <!-- Botón para eliminar producto -->
                            <button type="submit" name="accion" value="eliminar">Eliminar</button>
                            <input type="hidden" name="id" value="<?= $id ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Botones de acción -->
            <button type="submit" name="accion" value="actualizar">Actualizar Carrito</button>
            <button type="submit" name="accion" value="pagar">Pagar</button>
        </form>
        <h3>Total a pagar: $<?= number_format(array_sum(array_map(fn($item) => $item['precio'] * $item['cantidad'], $carrito)), 2) ?></h3>
    <?php endif; ?>
    <a href="productos.php">Volver a productos</a>
</body>
</html>
