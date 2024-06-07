<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['correo_electronico'])) {
    // Devolver un mensaje de error JSON si el usuario no está autenticado
    $response = ['status' => 'error', 'message' => 'Usuario no autenticado'];
    http_response_code(401);
    echo json_encode($response);
    exit();
}

// Obtener el correo electrónico del usuario actual
$correo_electronico_actual = $_SESSION['correo_electronico'];

// Conectar a la base de datos (asegúrate de configurar tu conexión a la base de datos)
$conexion = new mysqli('localhost', 'root', '', 'renta_peliculas');

// Verificar la conexión y mostrar errores detallados si falla
if ($conexion->connect_error) {
    $response = ['status' => 'error', 'message' => 'Error de conexión a la base de datos: ' . $conexion->connect_error];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Consulta para obtener todos los datos del cliente
$query = "SELECT nombre, apellido, telefono, correo_electronico, direccion FROM cliente WHERE correo_electronico = ?";

// Preparar la consulta y manejar errores de preparación
$stmt = $conexion->prepare($query);
if (!$stmt) {
    $response = ['status' => 'error', 'message' => 'Error en la preparación de la consulta: ' . $conexion->error];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Vincular parámetros y manejar errores de vinculación
$stmt->bind_param("s", $correo_electronico_actual);
if (!$stmt->bind_param("s", $correo_electronico_actual)) {
    $response = ['status' => 'error', 'message' => 'Error al vincular parámetros: ' . $stmt->error];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Ejecutar la consulta y manejar errores de ejecución
$stmt->execute();
if ($stmt->errno) {
    $response = ['status' => 'error', 'message' => 'Error al ejecutar la consulta: ' . $stmt->error];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Vincular variables de resultado y manejar errores de vinculación
$stmt->bind_result($nombre, $apellido, $telefono, $correo, $direccion);
if (!$stmt->bind_result($nombre, $apellido, $telefono, $correo, $direccion)) {
    $response = ['status' => 'error', 'message' => 'Error al vincular variables de resultado: ' . $stmt->error];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Obtener el resultado y manejar errores de obtención
$stmt->fetch();
if ($stmt->errno) {
    $response = ['status' => 'error', 'message' => 'Error al obtener el resultado: ' . $stmt->error];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Cerrar la consulta y la conexión
$stmt->close();
$conexion->close();

// Devolver todos los datos del usuario como un JSON
$response = [
    'status' => 'success',
    'nombre' => $nombre,
    'apellido' => $apellido,
    'telefono' => $telefono,
    'correo' => $correo,
    'direccion' => $direccion
];
echo json_encode($response);
?>
