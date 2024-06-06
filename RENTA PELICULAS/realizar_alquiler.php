<?php
// Archivo realizar_alquiler.php

// Verificar si se recibió el id del cliente, el no_ejemplar de la película y la acción
if (isset($_POST['id_cliente']) && isset($_POST['no_ejemplar']) && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Conectar a la base de datos
    $conexion = new mysqli('localhost', 'root', '', 'renta_peliculas');

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Manejar diferentes acciones
    switch ($action) {
        case 'alquilar':
            // Obtener el id del cliente y el no_ejemplar de la película del formulario
            $id_cliente = $_POST['id_cliente'];
            $no_ejemplar = $_POST['no_ejemplar'];

            // Verificar si los datos son enteros válidos
            if (!is_numeric($id_cliente) || !is_numeric($no_ejemplar)) {
                echo json_encode(['status' => 'error', 'message' => 'Los datos proporcionados no son válidos.']);
                break;
            }

            // Preparar la consulta para obtener el precio de la película
            $consulta_precio = $conexion->prepare("SELECT precio FROM pelicula WHERE no_ejemplar = ?");
            $consulta_precio->bind_param("i", $no_ejemplar);
            $consulta_precio->execute();
            $resultado_precio = $consulta_precio->get_result();

            if ($resultado_precio->num_rows > 0) {
                $fila_precio = $resultado_precio->fetch_assoc();
                $precio_alquiler = $fila_precio['precio'];

                // Preparar la consulta para insertar los datos del alquiler
                $consulta_insertar = $conexion->prepare("INSERT INTO alquiler (fecha_alquiler, fecha_devolucion, pago_alquiler, id_cliente, no_ejemplar) 
                                                          VALUES (?, ?, ?, ?, ?)");
                $fecha_alquiler = date("Y-m-d");
                $fecha_devolucion = date("Y-m-d", strtotime($fecha_alquiler . "+ 2 days"));
                $consulta_insertar->bind_param("ssiii", $fecha_alquiler, $fecha_devolucion, $precio_alquiler, $id_cliente, $no_ejemplar);

                if ($consulta_insertar->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Alquiler registrado exitosamente.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al registrar alquiler: ' . $conexion->error]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontró el precio de la película.']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
            break;
    }

    // Cerrar conexión
    $conexion->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron los datos necesarios para realizar la acción.']);
}
?>
