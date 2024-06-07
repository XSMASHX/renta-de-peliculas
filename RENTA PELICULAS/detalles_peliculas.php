<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "renta_peliculas");

// Comprobar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el ID de la película desde la URL
$id_pelicula = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pelicula > 0) {
    // Preparar la consulta SQL
    $stmt = $conexion->prepare("SELECT id_pelicula, titulo, descripcion, fecha_emision, precio, pais, autor, genero, imagen_ruta FROM pelicula WHERE id_pelicula = ?");
    $stmt->bind_param("i", $id_pelicula);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Obtener los datos de la película
        $pelicula = $resultado->fetch_assoc();

        // Asegurémonos de que el precio esté disponible
        if (isset($pelicula['precio'])) {
            // Convertir el precio a formato decimal, si es necesario
            $precio = $pelicula['precio'];
        } else {
            // Establecer un valor predeterminado si el precio no está disponible
            $precio = 'Precio no disponible';
        }

        // Añadir el precio al array de la película
        $pelicula['precio'] = $precio;

        // Devolver los datos de la película como JSON
        echo json_encode($pelicula);
    } else {
        echo json_encode(array("error" => "No se encontró la película"));
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo json_encode(array("error" => "ID de película no válido"));
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
