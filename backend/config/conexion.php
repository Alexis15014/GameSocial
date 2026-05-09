<?php
// --------------------------------------------
// Archivo: conexion.php
// Descripción: Conexión a la base de datos MySQL
// Proyecto: GameSocial
// --------------------------------------------

// Datos de conexión
$servidor = "localhost";
$base_datos = "gamesocial";
$usuario = "gamesocialuser";
$contrasena = "GKKDQY#15a";

// Variable donde se guardará la conexión
$conexion = null;

try {

    // Creamos la cadena de conexión (DSN)
    $cadena_conexion = "mysql:host=" . $servidor . ";dbname=" . $base_datos . ";charset=utf8mb4";

    // Creamos el objeto PDO y lo guardamos en una variable
    $conexion = new PDO($cadena_conexion, $usuario, $contrasena);

    // Configuramos PDO para que lance excepciones si hay errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $error) {

    // Guardamos el mensaje de error en una variable
    $mensaje_error = "Error de conexión: " . $error->getMessage();

    // Mostramos el error
    echo $mensaje_error;

}
