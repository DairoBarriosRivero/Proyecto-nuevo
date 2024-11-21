<?php
session_start(); // Iniciar sesión
include 'conexion.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'admin') {
    echo "Acceso no autorizado.";
    exit();
}

// Si el formulario se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $imagen = $_FILES['imagen']['name'];

    // Subir la imagen (si se ha cargado una)
    if (!empty($imagen)) {
        $ruta_imagen = 'imagenes/' . basename($imagen); // Guardar imagen en la carpeta 'imagenes'
        move_uploaded_file($_FILES['imagen']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/Proyecto Nuevo/' . $ruta_imagen);
    } else {
        $ruta_imagen = null; // Si no hay imagen, se deja como NULL
    }

    // Insertar los datos en la base de datos
    try {
        $sql = "INSERT INTO Productos (nombre, descripcion, precio, cantidad, imagen) 
                VALUES (:nombre, :descripcion, :precio, :cantidad, :imagen)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':imagen', $ruta_imagen);
        $stmt->execute();

        echo "Producto agregado con éxito.";
    } catch (PDOException $e) {
        echo "Error al agregar el producto: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 1.1em;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 150px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            text-align: center;
        }

        p a {
            color: #007BFF;
            text-decoration: none;
            font-size: 1.1em;
        }

        p a:hover {
            text-decoration: underline;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Agregar Nuevo Producto</h2>

        <!-- Formulario de productos -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required><br>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" required></textarea><br>
            </div>

            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" name="precio" id="precio" step="0.01" required><br>
            </div>

            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" required><br>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen (opcional):</label>
                <input type="file" name="imagen" id="imagen"><br><br>
            </div>

            <button type="submit">Agregar Producto</button>
        </form>

        <p><a href="dashboard.php">Volver al Dashboard</a></p>
    </div>
</body>
</html>

