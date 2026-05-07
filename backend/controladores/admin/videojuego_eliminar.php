<?php
/**
 * Controlador: admin/videojuego_eliminar.php
 * Propósito: Eliminar un videojuego del catálogo desde el panel de administración.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/slug.php';

requiereAdmin($conexion);

$param = $_GET['id'] ?? null;
if (!$param) { die("Videojuego no especificado."); }

if (is_numeric($param)) {
    $id_videojuego = (int)$param;
} else {
    $stmt_t = $conexion->query("SELECT id_videojuego, titulo FROM videojuegos");
    $id_videojuego = null;
    foreach ($stmt_t->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (generarSlug($row['titulo']) === $param) {
            $id_videojuego = (int)$row['id_videojuego'];
            break;
        }
    }
    if (!$id_videojuego) { http_response_code(404); die("<h1>Videojuego no encontrado.</h1>"); }
}

try {
    $stmt = $conexion->prepare("DELETE FROM videojuegos WHERE id_videojuego = ?");
    $stmt->execute([$id_videojuego]);

    header("Location: /gamesocial/admin/videojuegos?mensaje=eliminado");
    exit;

} catch (PDOException $error_bd) {
    // Puede fallar si el videojuego tiene claves foráneas activas en otras tablas
    die("Error al eliminar el videojuego: " . $error_bd->getMessage());
}
