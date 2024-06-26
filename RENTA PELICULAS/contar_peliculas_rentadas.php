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

// Verificar la conexión
if ($conexion->connect_error) {
    // Devolver un mensaje de error JSON si hay un error de conexión
    $response = ['status' => 'error', 'message' => 'Error de conexión a la base de datos'];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Consulta para contar las películas rentadas por el cliente
$query = "SELECT COUNT(*) AS total_peliculas FROM alquiler a
          JOIN cliente c ON a.id_cliente = c.id_cliente
          WHERE c.correo_electronico = ?";

// Preparar la consulta
$stmt = $conexion->prepare($query);

// Verificar si la consulta se preparó correctamente
if (!$stmt) {
    // Devolver un mensaje de error JSON si hay un error en la consulta
    $response = ['status' => 'error', 'message' => 'Error en la preparación de la consulta'];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Vincular parámetros
$stmt->bind_param("s", $correo_electronico_actual);

// Ejecutar la consulta
$stmt->execute();

// Verificar si la consulta se ejecutó correctamente
if ($stmt->errno) {
    // Devolver un mensaje de error JSON si hay un error al ejecutar la consulta
    $response = ['status' => 'error', 'message' => 'Error al ejecutar la consulta'];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// Vincular variables de resultado
$stmt->bind_result($total_peliculas);

// Obtener el resultado
$stmt->fetch();

// Cerrar la consulta y la conexión
$stmt->close();
$conexion->close();

// Devolver el número de películas rentadas como un JSON
$response = ['status' => 'success', 'total_peliculas' => $total_peliculas];
echo json_encode($response);
?>
