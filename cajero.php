<?php
// Incluimos el archivo con las funciones
include('functions.php');

// Comprobar si el usuario ha iniciado sesión
session_start();
if (!isset($_SESSION['correo'])) {
    header("Location: login.php");
    exit();
}
$correo = $_SESSION['correo'];
// Verificamos si se ha enviado el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtenemos la operación bancaria seleccionada
  $operacion = $_POST['operacion'];
  
  // Obtenemos la cantidad ingresada si la operación lo requiere
  $cantidad = null;
  if(isset($_POST['cantidad'])) {
    $cantidad = $_POST['cantidad'];
  }
  $descripcion = null;
  if (isset($_POST['descripcion'])) {
    $descripcion = $_POST['descripcion'];
  }

  // Dependiendo de la operación bancaria, llamamos a la función correspondiente
  switch($operacion) {
    case 'retiro':
      $resultado = retirar_saldo(obtener_id_usuario($correo),$cantidad);
      break;
    case 'consulta':
      $resultado = consultar_saldo(obtener_id_usuario($correo));
      break;
    case 'consignacion':
      $resultado = consignar_saldo(obtener_id_usuario($correo),$cantidad);
      break;
      case 'pago':
        $resultado = pagar_servicio(obtener_id_usuario($correo), $cantidad, $descripcion);
        break;
    default:
      $resultado = "Operación no válida";
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Cajero electrónico</title>
  <script>
    function mostrarCantidad() {
      var operacion = document.getElementById("operacion").value;
      var cantidad = document.getElementById("cantidad-container");
      var descripcion = document.getElementById("descripcion-container");

      if(operacion === "consulta") {
        cantidad.style.display = "none";
        descripcion.style.display = "none";
      } else if (operacion === "pago") {
        cantidad.style.display = "block";
        descripcion.style.display = "block";
      } else {
        cantidad.style.display = "block";
        descripcion.style.display = "none";
      }
    }
  </script>
</head>
<body>
  <h1>Cajero electrónico</h1>

  <?php
  // Si se ha procesado alguna operación bancaria, mostramos el resultado
  if(isset($resultado)) {
    echo "<p>$resultado</p>";
  }
  ?>

  <form method="POST" action="cajero.php">
    <label for="operacion">Seleccione una operación:</label>
    <select name="operacion" id="operacion" onchange="mostrarCantidad()">
      <option value="retiro">Retiro de dinero</option>
      <option value="consulta">Consulta de saldo</option>
      <option value="consignacion">Consignación</option>
      <option value="pago">Pago en línea</option>
    </select>
    <br>
    <div id="cantidad-container">
      <label for="cantidad">Ingrese la cantidad a operar:</label>
      <input type="number" name="cantidad" id="cantidad">
      <br>
    </div>
    <div id="descripcion-container" style="display:none;">
      <label for="descripcion">Descripción de la transacción:</label>
      <input type="text" name="descripcion" id="descripcion">
      <br>
    </div>
    <input type="submit" value="Realizar operación">
  </form>
</body>
</html>
