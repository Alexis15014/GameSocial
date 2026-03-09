<?php
/**
 * Helper: videojuego_admin.php
 * Propósito: Centralizar la validación de formularios de videojuegos compartida entre crear y editar.
 * Proyecto: GameSocial
 */

// Tipos de contenido válidos para un videojuego.
// Se define aquí una sola vez para evitar duplicarlo en crear, editar y catálogo.
const TIPOS_VIDEOJUEGO_VALIDOS = [
    'juego_base'       => 'Juego base',
    'dlc'              => 'DLC',
    'expansion'        => 'Expansión',
    'edicion_especial' => 'Edición especial',
    'remake'           => 'Remake',
    'remaster'         => 'Remaster',
];

// Extraemos, limpiamos y validamos los datos del formulario de videojuego (POST).
// Si el tipo enviado no está entre los válidos, usamos 'juego_base' por defecto.
// Devuelve un array con claves nombradas listo para ejecutar en PDO.
function obtenerDatosFormulario(): array {
    $tipo_enviado  = $_POST['tipo'] ?? '';
    // Validamos contra la constante para evitar valores arbitrarios en la BD
    $tipo_validado = array_key_exists($tipo_enviado, TIPOS_VIDEOJUEGO_VALIDOS)
        ? $tipo_enviado
        : 'juego_base';

    return [
        ':titulo'            => trim($_POST['titulo']            ?? ''),
        ':descripcion'       => trim($_POST['descripcion']       ?? ''),
        ':plataforma'        => trim($_POST['plataforma']        ?? ''),
        ':genero'            => trim($_POST['genero']            ?? ''),
        ':fecha_lanzamiento' => trim($_POST['fecha_lanzamiento'] ?? ''),
        ':desarrolladora'    => trim($_POST['desarrolladora']    ?? ''),
        ':tipo'              => $tipo_validado,
    ];
}
