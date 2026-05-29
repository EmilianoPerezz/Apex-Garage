<?php
// conexion.php
$conexion = new mysqli("localhost", "root", "X9mB4tQ7zR", "taller_mecanico");
if ($conexion->connect_error) {
    die(json_encode(['ok' => false, 'mensaje' => 'Error de conexión: ' . $conexion->connect_error]));
}
$conexion->set_charset("utf8mb4");
?>
