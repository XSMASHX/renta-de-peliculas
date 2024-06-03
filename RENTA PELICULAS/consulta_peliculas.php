<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "renta_peliculas");

// Comprobar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta SQL para obtener los datos de las películas, incluida la ruta de la imagen
$sql = "SELECT id_pelicula, titulo, descripcion, fecha_emision, precio, pais, autor, genero, imagen_ruta FROM pelicula";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    // Crear un array para almacenar los datos de las películas
    $peliculas = array();

    // Obtener los datos de cada película
    while ($fila = $resultado->fetch_assoc()) {
        // Agregar los datos de la película al array
        $pelicula = array(
            "id_pelicula" => $fila["id_pelicula"],
            "titulo" => $fila["titulo"],
            "descripcion" => $fila["descripcion"],
            "fecha_emision" => $fila["fecha_emision"],
            "precio" => $fila["precio"],
            "pais" => $fila["pais"],
            "autor" => $fila["autor"],
            "genero" => $fila["genero"],
            "imagen_ruta" => $fila["imagen_ruta"] // Aquí se incluye la ruta de la imagen
        );

        // Agregar la película al array de películas
        $peliculas[] = $pelicula;
    }

    // Convertir el array de películas a formato JSON y devolverlo
    echo json_encode($peliculas);
} else {
    echo "No se encontraron películas";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
