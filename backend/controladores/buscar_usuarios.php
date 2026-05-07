<?php
/**
 * Controlador: buscar_usuarios.php
 * Propósito: Gestionar la búsqueda de otros usuarios dentro de la plataforma.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/imagen.php';

$id_usuario_sesion = requiereLogin($conexion);

$modelo_usuario = new Usuario($conexion);

$termino    = trim($_GET['q'] ?? '');
$resultados = [];

if ($termino !== '') {
    $resultados_raw = $modelo_usuario->buscarPorNombre($termino);

    // Filtramos al propio usuario para que no aparezca en sus resultados
    $resultados_filtrados = array_filter($resultados_raw, function($u) use ($id_usuario_sesion) {
        return $u['id_usuario'] != $id_usuario_sesion;
    });

    // Resolvemos el avatar de cada usuario encontrado
    $resultados = [];
    foreach ($resultados_filtrados as $u) {
        $u['avatar_url'] = resolverAvatar($u['foto_perfil'] ?? null);
        $resultados[] = $u;
    }
}

$titulo_pagina = ($termino !== '')
    ? "Resultados para '" . htmlspecialchars($termino) . "' - GameSocial"
    : "Buscar Amigos - GameSocial";

require __DIR__ . '/../../frontend/vistas/buscar_usuarios.php';
