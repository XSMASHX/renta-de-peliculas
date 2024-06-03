<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "renta_peliculas";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL para obtener películas
$sql = "SELECT * FROM pelicula";
$result = $conn->query($sql);

// Crear un array para almacenar los datos de las películas
$peliculas = array();

// Verificar si hay resultados y agregarlos al array de películas
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $peliculas[] = $row;
    }
}

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($peliculas);

// Cerrar la conexión
$conn->close();
?>
