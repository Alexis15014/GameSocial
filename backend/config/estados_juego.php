<?php
/**
 * Configuración centralizada de estados de juego.
 * Incluir con require_once en todas las vistas que usen $estados_etiquetas / $estados_colores_chart / $estados_clases.
 */

$estados_etiquetas = [
    'sin_iniciar' => 'Sin iniciar',
    'inacabado'   => 'Inacabado',
    'terminado'   => 'Terminado',
    'completado'  => 'Completado',
    'en_curso'    => 'En curso',
    'abandonado'  => 'Abandonado',
];

$estados_colores_chart = [
    'sin_iniciar' => '#00C8FF',
    'inacabado'   => '#FF8C00',
    'terminado'   => '#9D4EDD',
    'completado'  => '#00B37E',
    'en_curso'    => '#00C49A',
    'abandonado'  => '#FF3D5A',
];

$estados_clases = [
    'sin_iniciar' => 'bg-estado-sin_iniciar',
    'inacabado'   => 'bg-estado-inacabado',
    'terminado'   => 'bg-estado-terminado',
    'completado'  => 'bg-estado-completado',
    'en_curso'    => 'bg-estado-en_curso',
    'abandonado'  => 'bg-estado-abandonado',
];
