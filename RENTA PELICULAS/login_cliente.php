<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "renta_peliculas");

// Comprobar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los datos del formulario
$correo_electronico = $_POST['correo_electronico'];
$contrasena = $_POST['contraseña'];

// Consulta SQL para verificar las credenciales
$sql = "SELECT * FROM cliente WHERE correo_electronico = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo_electronico);
$stmt->execute();
$resultado = $stmt->get_result();

$response = array();

if ($resultado->num_rows > 0) {
    $cliente = $resultado->fetch_assoc();
    // Verificar si la contraseña coincide
    if ($contrasena == $cliente['contraseña']) {
        // Inicio de sesión exitoso
        session_start();
        $_SESSION['correo_electronico'] = $correo_electronico;
        $_SESSION['nombre'] = $cliente['nombre']; // Asegúrate de que el campo 'nombre' existe en la tabla
        $_SESSION['apellido'] = $cliente['apellido']; // Asegúrate de que el campo 'apellido' existe en la tabla

        $response['status'] = 'success';
        $response['message'] = 'Inicio de sesión exitoso';
        $response['nombre_usuario'] = $cliente['nombre'];

    } else {
        // Contraseña incorrecta
        $response['status'] = 'error';
        $response['message'] = 'Contraseña incorrecta';
    }
} else {
    // Correo electrónico incorrecto
    $response['status'] = 'error';
    $response['message'] = 'Correo electrónico no encontrado';
}

// Cerrar la conexión a la base de datos
$conexion->close();

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
