<?php
include 'conexion.php'; // Conectar con la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);  // Encriptar la contraseña
    $tipo = $_POST['tipo'];

    // Depuración: Verificar los valores recibidos del formulario
    var_dump($_POST);  // Esto imprimirá los valores del formulario
    
    try {
        // Preparar la consulta de inserción
        $sql = "INSERT INTO Usuarios (nombre, email, username, clave, tipo) VALUES (:nombre, :email, :username, :clave, :tipo)";
        $stmt = $conexion->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':username' => $username,
            ':clave' => $clave,
            ':tipo' => $tipo
        ]);

        // Verificar si la inserción fue exitosa
        if ($stmt->rowCount() > 0) {
            echo "Usuario registrado con éxito.";
            // Redirigir a la página de login después de registrar
            header("Location: login.php");
            exit();
        } else {
            echo "No se pudo registrar el usuario.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .registro-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        h2 {
            margin-bottom: 20px;
            color: #007BFF;
        }

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            margin-top: 15px;
            color: #555;
        }

        a {
            text-decoration: none;
            color: #007BFF;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="registro-container">
        <h2>Registro de Usuario</h2>
        <form method="POST" action="registro.php">
            <input type="text" name="nombre" placeholder="Nombre Completo" required>
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="text" name="username" placeholder="Nombre de Usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <select name="tipo">
                <option value="cliente">Cliente</option>
                <option value="admin">Administrador</option>
            </select>
            <button type="submit">Registrar</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>
