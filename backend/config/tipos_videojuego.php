<?php
/**
 * Configuración centralizada de tipos de videojuego.
 * Incluir con require_once en todas las vistas que usen $tipo_etiquetas / $tipo_colores.
 */

$tipo_etiquetas = [
    'juego_base'       => 'Juego base',
    'dlc'              => 'DLC',
    'expansion'        => 'Expansión',
    'edicion_especial' => 'Edición especial',
    'remake'           => 'Remake',
    'remaster'         => 'Remaster',
];

$tipo_colores = [
    'juego_base'       => 'badge-tipo-base',
    'dlc'              => 'badge-tipo-dlc',
    'expansion'        => 'badge-tipo-expansion',
    'edicion_especial' => 'badge-tipo-especial',
    'remake'           => 'badge-tipo-remake',
    'remaster'         => 'badge-tipo-remaster',
];
