<?php
session_start();

// 🔐 Seguridad: Solo administradores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';

// Obtener el ID desde la URL (GET)
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de videojuego no válido o no proporcionado.");
}

try {
    // 1. Opcional: Podrías eliminar también la imagen física del servidor si existiera
    // 2. Ejecutar la eliminación en la base de datos
    $stmt = $conexion->prepare("DELETE FROM videojuegos WHERE id_videojuego = ?");
    $stmt->execute([$id]);

    // Redirigir al listado principal de administración con éxito
    header("Location: videojuegos.php?mensaje=eliminado");
    exit;

} catch (PDOException $e) {
    // En caso de error (por ejemplo, si tiene claves foráneas activas)
    die("Error al eliminar el videojuego: " . $e->getMessage());
}