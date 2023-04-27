<?php
require_once 'config.php';

function conectar_db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    return $conn;
}

function consultar_saldo($id_usuario) {
    $conn = conectar_db();
    $sql = "SELECT saldo FROM usuarios WHERE id = $id_usuario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["saldo"];
    } else {
        return "Usuario no encontrado.";
    }
}

function retirar_saldo($id_usuario, $monto) {
    $conn = conectar_db();
    $saldo_actual = consultar_saldo($id_usuario);

    if ($monto > $saldo_actual) {
        return "No tiene suficiente saldo para realizar esta operación.";
    }

    $nuevo_saldo = $saldo_actual - $monto;
    $sql = "UPDATE usuarios SET saldo = $nuevo_saldo WHERE id = $id_usuario";

    if ($conn->query($sql) === TRUE) {
        return "Retiro exitoso. Su nuevo saldo es de $nuevo_saldo.";
    } else {
        return "Error al retirar el saldo: " . $conn->error;
    }
}

function consignar_saldo($cuenta_id, $monto) {
    $conn = conectar_db();
    // actualizar el saldo de la cuenta
    $query = "UPDATE usuarios SET saldo = saldo + $monto WHERE id = $cuenta_id";
    $result = mysqli_query($conn, $query);

    // verificar si se actualizó el saldo correctamente
    if ($result) {
        return "Depósito exitoso. Su nuevo saldo es de " . consultar_saldo($cuenta_id);
    } else {
        return "Error al depositar el saldo: " . $conn->error;
    }
}

function pagar_servicio($cuenta_id, $monto, $servicio_id) {
    $conn = conectar_db();

    // actualizar el saldo de la cuenta
    $query = "UPDATE cuentas SET saldo = saldo - $monto WHERE id = '$cuenta_id'";
    $result = mysqli_query($conn, $query);

    // verificar si se actualizó el saldo correctamente
    if (!$result) {
        return false;
    }
      

    // registrar la transacción en la tabla de transacciones
    $query2 = "INSERT INTO transacciones (cuenta_id, tipo, monto, descripcion) VALUES ('$cuenta_id', 'Pago Servicio', $monto, '$servicio_id')";
    $result2 = mysqli_query($conn, $query2);

    // verificar si se registró la transacción correctamente
    if ($result2) {
        return "Pago de servicio $servicio_id exitoso. Su nuevo saldo es de " . consultar_saldo($cuenta_id);
    } else {
        return "Error al realizar el pago: " . $conn->error;
    }
}

// Función para verificar si un correo electrónico ya está registrado en la base de datos
function checkEmail($email) {
    $conn = conectar_db();
  
    // Escapamos los caracteres especiales en el correo electrónico
    $email = mysqli_real_escape_string($conn, $email);
  
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
  
    if(mysqli_num_rows($result) > 0) {
      // El correo electrónico ya está registrado
      $conn->close();
      return true;
    } else {
      // El correo electrónico no está registrado
      $conn->close();
      return false;
    }
  }

function obtener_id_usuario($correo) {
    // Conectarse a la base de datos
    $conn = conectar_db();

    // Escapar el correo electrónico para evitar inyección de SQL
    $correo = mysqli_real_escape_string($conn, $correo);

    // Construir la consulta SQL para buscar el usuario
    $sql = "SELECT id FROM usuarios WHERE email='$correo' LIMIT 1";

    // Ejecutar la consulta SQL y obtener el resultado
    $resultado = $conn->query($sql);

    // Verificar si se encontró el usuario
    if ($resultado && $resultado->num_rows == 1) {
        // Obtener el registro del usuario
        $registro = $resultado->fetch_assoc();

        // Obtener el valor del campo 'id'
        $id_usuario = $registro['id'];

        // Cerrar la conexión a la base de datos
        $conn->close();

        // Devolver el valor del campo 'id'
        return $id_usuario;
    } else {
        // Cerrar la conexión a la base de datos
        $conn->close();

        // Devolver 'false' si el usuario no se encontró
        return false;
    }
}

?>
