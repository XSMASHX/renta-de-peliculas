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

// Consulta para obtener el id_cliente del usuario actual
$query_cliente = "SELECT id_cliente FROM cliente WHERE correo_electronico = ?";
$stmt_cliente = $conexion->prepare($query_cliente);

if (!$stmt_cliente) {
    $response = ['status' => 'error', 'message' => 'Error en la preparación de la consulta del cliente'];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

$stmt_cliente->bind_param("s", $correo_electronico_actual);
$stmt_cliente->execute();
$stmt_cliente->bind_result($id_cliente);
$stmt_cliente->fetch();
$stmt_cliente->close();

// Consulta para obtener las películas rentadas por el usuario actual
$query = "SELECT p.titulo, p.genero, p.precio, a.fecha_alquiler, a.fecha_devolucion, a.pago_alquiler, a.multa
          FROM alquiler a
          JOIN pelicula p ON a.no_ejemplar = p.no_ejemplar
          WHERE a.id_cliente = ?";

$stmt = $conexion->prepare($query);

if (!$stmt) {
    $response = ['status' => 'error', 'message' => 'Error en la preparación de la consulta'];
    http_response_code(500);
    echo json_encode($response);
    exit();
}

$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $response = ['status' => 'success', 'message' => 'No hay películas rentadas por el usuario actual'];
    echo json_encode($response);
    exit();
}

$peliculas_rentadas = [];

while ($fila = $resultado->fetch_assoc()) {
    $peliculas_rentadas[] = $fila;
}

$stmt->close();
$conexion->close();

$response = ['status' => 'success', 'peliculas_rentadas' => $peliculas_rentadas];
echo json_encode($response);
?>
