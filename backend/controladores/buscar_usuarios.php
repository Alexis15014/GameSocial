<?php
/**
 * Controlador: buscar_usuarios.php
 * Propósito: Gestionar la búsqueda de otros usuarios dentro de la plataforma.
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Usuario.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verificación de Seguridad
$id_usuario_sesion = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario_sesion) {
    header("Location: /gamesocial/backend/controladores/login.php");
    exit;
}

$modelo_usuario = new Usuario($conexion);

// 2. Captura del término de búsqueda
$termino = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados = [];

// 3. Lógica de búsqueda
if ($termino !== '') {
    // Buscamos usuarios que coincidan con el nombre
    $resultados = $modelo_usuario->buscarPorNombre($termino);
    
    // Opcional: Podrías filtrar aquí para que el usuario no se encuentre a sí mismo
    $resultados = array_filter($resultados, function($u) use ($id_usuario_sesion) {
        return $u['id_usuario'] != $id_usuario_sesion;
    });
}

// 4. Preparación de Metadatos para la Vista
if ($termino !== '') {
    $titulo_pagina = "Resultados para '" . htmlspecialchars($termino) . "' - GameSocial";
} else {
    $titulo_pagina = "Buscar Amigos - GameSocial";
}

// 5. Carga de la Vista
require_once __DIR__ . '/../../frontend/vistas/buscar_usuarios.php';