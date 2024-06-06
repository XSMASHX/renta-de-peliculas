<?php
session_start();
if (!isset($_SESSION['correo_electronico'])) {
    echo json_encode(['status' => 'error', 'message' => 'Debe iniciar sesión para rentar una película']);
    exit;
}

$correo_electronico = $_SESSION['correo_electronico'];

$conexion = new mysqli("localhost", "root", "", "renta_peliculas");

if ($conexion->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Error de conexión: ' . $conexion->connect_error]));
}

$sql_cliente = "SELECT id_cliente FROM cliente WHERE correo_electronico = ?";
$stmt_cliente = $conexion->prepare($sql_cliente);
$stmt_cliente->bind_param("s", $correo_electronico);
$stmt_cliente->execute();
$resultado_cliente = $stmt_cliente->get_result();

if ($resultado_cliente->num_rows > 0) {
    $cliente = $resultado_cliente->fetch_assoc();
    $id_cliente = $cliente['id_cliente'];

    // Consulta para obtener las películas rentadas por el usuario
    $sql_peliculas_rentadas = "SELECT p.titulo, p.genero, p.precio
                                FROM pelicula p
                                INNER JOIN alquiler a ON p.no_ejemplar = a.no_ejemplar
                                WHERE a.id_cliente = ?";
    $stmt_peliculas_rentadas = $conexion->prepare($sql_peliculas_rentadas);
    $stmt_peliculas_rentadas->bind_param("i", $id_cliente);
    $stmt_peliculas_rentadas->execute();
    $resultado_peliculas_rentadas = $stmt_peliculas_rentadas->get_result();

    // Verificar si hay películas rentadas por el usuario
    if ($resultado_peliculas_rentadas->num_rows > 0) {
        $peliculas_rentadas = $resultado_peliculas_rentadas->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'peliculas_rentadas' => $peliculas_rentadas]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No tienes películas rentadas']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Cliente no encontrado']);
}

$conexion->close();
?>
