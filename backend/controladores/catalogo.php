<?php
// --------------------------------------------
// Controlador: catálogo de videojuegos
// --------------------------------------------
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Videojuego.php';

// Asegurar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Seguridad: usuario logueado
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

$modelo_videojuego = new Videojuego($conexion);

// ===========================
// Recoger filtros
// ===========================
$busqueda       = trim($_GET['q'] ?? '');
$genero         = trim($_GET['genero'] ?? '');
$plataforma     = trim($_GET['plataforma'] ?? '');
$desarrolladora = trim($_GET['desarrolladora'] ?? '');

// ===========================
// Construir consulta dinámica
// ===========================
$sql = "SELECT * FROM videojuegos WHERE 1=1";
$params = [];

if ($busqueda !== '') {
    $sql .= " AND titulo LIKE :busqueda";
    $params[':busqueda'] = '%' . $busqueda . '%';
}
if ($genero !== '') {
    $sql .= " AND genero LIKE :genero";
    $params[':genero'] = '%' . $genero . '%';
}
if ($plataforma !== '') {
    $sql .= " AND plataforma LIKE :plataforma";
    $params[':plataforma'] = '%' . $plataforma . '%';
}
if ($desarrolladora !== '') {
    $sql .= " AND desarrolladora LIKE :desarrolladora";
    $params[':desarrolladora'] = '%' . $desarrolladora . '%';
}

$sql .= " ORDER BY fecha_lanzamiento DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$videojuegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===========================
// Preparar imágenes (Rutas relativas originales)
// ===========================
foreach ($videojuegos as &$juego) {
    // Usamos la ruta relativa que tenías: ../../ para subir desde backend/controladores
    $ruta_verificacion = __DIR__ . '/../../' . $juego['imagen_portada'];
    
    if (!empty($juego['imagen_portada']) && file_exists($ruta_verificacion)) {
        $juego['imagen_portada_url'] = '../../' . $juego['imagen_portada'];
    } else {
        $juego['imagen_portada_url'] = '../../frontend/assets/img_placeholder.png';
    }
}

// ===========================
// Título de la página
// ===========================
if ($busqueda !== '') {
    $titulo_pagina = "Resultados para '" . htmlspecialchars($busqueda) . "' | GameSocial";
} elseif ($genero !== '') {
    $titulo_pagina = "Juegos de " . htmlspecialchars($genero) . " | GameSocial";
} elseif($plataforma !== ''){
	$titulo_pagina = "Juegos de " . htmlspecialchars($plataforma) . " | GameSocial";
} elseif($desarrolladora !== ''){
	$titulo_pagina = "Juegos de " . htmlspecialchars($desarrolladora) . " | GameSocial";
}else {
    $titulo_pagina = "Catálogo de Videojuegos | GameSocial";
}

// ===========================
// Cargar vista (Ruta relativa original)
// ===========================
require_once __DIR__ . '/../../frontend/vistas/catalogo.php';