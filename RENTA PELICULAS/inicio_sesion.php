<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['correo_electronico'])) {
    // Redirigir a la página de inicio de sesión si el usuario no está autenticado
    header('Location: login_cliente.php');
    exit();
}

// Obtener el correo electrónico del usuario actual
$correo_electronico_actual = $_SESSION['correo_electronico'];

echo $correo_electronico_actual;


// Aquí puedes realizar cualquier operación adicional con la sesión del usuario, como recuperar más información del usuario de la base de datos

?>