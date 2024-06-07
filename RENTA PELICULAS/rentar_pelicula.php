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

// Obtener el ID del cliente usando el correo electrónico
$sql_cliente = "SELECT id_cliente FROM cliente WHERE correo_electronico = ?";
$stmt_cliente = $conexion->prepare($sql_cliente);
$stmt_cliente->bind_param("s", $correo_electronico);
$stmt_cliente->execute();
$resultado_cliente = $stmt_cliente->get_result();

if ($resultado_cliente->num_rows > 0) {
    $cliente = $resultado_cliente->fetch_assoc();
    $id_cliente = $cliente['id_cliente'];
    
    // Verificar si 'no_ejemplar' está presente y no está vacío en $_POST
    if(isset($_POST['no_ejemplar']) && !empty($_POST['no_ejemplar'])) {
        $no_ejemplar = $_POST['no_ejemplar'];

        // Insertar en la tabla de alquiler
        $sql_alquiler = "INSERT INTO alquiler (id_cliente, no_ejemplar, fecha_alquiler) VALUES (?, ?, NOW())";
        $stmt_alquiler = $conexion->prepare($sql_alquiler);
        $stmt_alquiler->bind_param("ii", $id_cliente, $no_ejemplar);

        if ($stmt_alquiler->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Película rentada con éxito']);
        } else {
            $error_message = 'Error al ejecutar la consulta de alquiler: ' . $stmt_alquiler->error;
            error_log($error_message); // Registrar el error en el archivo de registro del servidor
            echo json_encode(['status' => 'error', 'message' => 'Error al rentar la película. Por favor, intenta de nuevo más tarde.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'El número de ejemplar es necesario para rentar la película']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Cliente no encontrado']);
}

?>
