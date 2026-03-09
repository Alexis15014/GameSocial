<?php
/**
 * Controlador: admin/videojuego_eliminar.php
 * Propósito: Eliminar un videojuego del catálogo desde el panel de administración.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/auth.php';

requiereAdmin($conexion);

$id_videojuego = $_GET['id'] ?? null;

if (!$id_videojuego) {
    die("ID de videojuego no válido o no proporcionado.");
}

try {
    $stmt = $conexion->prepare("DELETE FROM videojuegos WHERE id_videojuego = ?");
    $stmt->execute([$id_videojuego]);

    header("Location: videojuegos.php?mensaje=eliminado");
    exit;

} catch (PDOException $error_bd) {
    // Puede fallar si el videojuego tiene claves foráneas activas en otras tablas
    die("Error al eliminar el videojuego: " . $error_bd->getMessage());
}
