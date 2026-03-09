<?php
/**
 * Controlador: catalogo.php
 * Propósito: Gestionar el catálogo de videojuegos con búsqueda y filtros dinámicos.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Videojuego.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/imagen.php';
require_once __DIR__ . '/../helpers/videojuego_admin.php';

$id_usuario = requiereLogin($conexion);

$modelo_videojuego = new Videojuego($conexion);

// Recogemos los filtros activos de la URL
$busqueda       = trim($_GET['q']              ?? '');
$genero         = trim($_GET['genero']         ?? '');
$plataforma     = trim($_GET['plataforma']     ?? '');
$desarrolladora = trim($_GET['desarrolladora'] ?? '');
$tipo           = trim($_GET['tipo']           ?? '');

// Construimos la consulta dinámicamente según los filtros que estén activos
$sql    = "SELECT * FROM videojuegos WHERE 1=1";
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
if ($tipo !== '') {
    // El tipo usa = en vez de LIKE porque los valores son fijos y exactos
    $sql .= " AND tipo = :tipo";
    $params[':tipo'] = $tipo;
}

$sql .= " ORDER BY fecha_lanzamiento DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$videojuegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Añadimos la URL de portada resuelta a cada juego
procesarImagenesJuegos($videojuegos);

// Título dinámico según el filtro activo
if ($busqueda !== '') {
    $titulo_pagina = "Resultados para '" . htmlspecialchars($busqueda) . "' | GameSocial";
} elseif ($genero !== '') {
    $titulo_pagina = "Juegos de " . htmlspecialchars($genero) . " | GameSocial";
} elseif ($plataforma !== '') {
    $titulo_pagina = "Juegos en " . htmlspecialchars($plataforma) . " | GameSocial";
} elseif ($desarrolladora !== '') {
    $titulo_pagina = "Juegos de " . htmlspecialchars($desarrolladora) . " | GameSocial";
} elseif ($tipo !== '') {
    // Usamos la etiqueta legible de TIPOS_VIDEOJUEGO_VALIDOS en vez de mostrar la clave interna
    $etiqueta_tipo = TIPOS_VIDEOJUEGO_VALIDOS[$tipo] ?? ucfirst($tipo);
    $titulo_pagina = $etiqueta_tipo . " | GameSocial";
} else {
    $titulo_pagina = "Catálogo de Videojuegos | GameSocial";
}

require_once __DIR__ . '/../../frontend/vistas/catalogo.php';
