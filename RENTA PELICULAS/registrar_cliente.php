<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "renta_peliculas");

// Comprobar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Definir consulta SQL para insertar datos en la tabla cliente
$sql = "INSERT INTO cliente (nombre, apellido, telefono, correo_electronico, direccion, contraseña) VALUES (?, ?, ?, ?, ?, ?)";

// Preparar la consulta
$stmt = $conexion->prepare($sql);

// Vincular parámetros
$stmt->bind_param("ssssss", $nombre, $apellido, $telefono, $correo, $direccion, $contraseña);

// Asignar valores a los parámetros
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo_electronico'];
$direccion = $_POST['direccion'];
$contraseña = $_POST['contraseña'];

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Registro exitoso";
} else {
    echo "Error al registrar: " . $stmt->error;
}

// Cerrar la conexión
$stmt->close();
$conexion->close();
?>
