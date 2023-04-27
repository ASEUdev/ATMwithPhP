<?php
require_once 'functions.php';

// Verificamos si se ha enviado el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenemos los datos ingresados por el usuario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $saldo = $_POST['saldo'];

    try {
        // Conectamos a la base de datos
        $mysqli = conectar_db();

        // Verificamos si el usuario ya existe en la base de datos
        $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        if($stmt->fetch()) {
            // El usuario ya existe en la base de datos
            echo "<p>El usuario ya existe en la base de datos.</p>";
        } else {
            // Insertamos el usuario en la base de datos
            $stmt = $mysqli->prepare("INSERT INTO usuarios (nombre, email, contrasena, saldo) VALUES (?, ?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('sssi', $nombre, $email, $hashed_password, $saldo);
            $stmt->execute();

            // El usuario fue insertado correctamente en la base de datos
            echo "<p>El usuario fue registrado correctamente.</p>";
            header("Location: index.php");
            exit();
        }

        // Cerramos la conexión a la base de datos
        $mysqli->close();
    } catch(Exception $e) {
        // Hubo un error al conectarse a la base de datos o al realizar la consulta
        echo "<p>Hubo un error al registrar el usuario: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registro de usuarios</title>
</head>
<body>
  <h1>Registro de usuarios</h1>

  <form method="POST" action="registro.php">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required>
    <br>
    <label for="email">Correo electrónico:</label>
    <input type="email" name="email" required>
    <br>
    <label for="password">Contraseña:</label>
    <input type="password" name="password" required>
    <br>
    <label for="saldo">Saldo inicial:</label>
    <input type="number" name="saldo" required>
    <br>
    <input type="submit" value="Registrar usuario">
  </form>
</body>
</html>
