<?php
/**
 * Controlador: listas.php
 * Propósito: Gestionar las listas personalizadas de videojuegos del usuario.
 * Proyecto: GameSocial
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/Lista.php';
require_once __DIR__ . '/../modelos/Videojuego.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/imagen.php';

$id_usuario = requiereLogin($conexion);

$modelo_lista      = new Lista($conexion);
$modelo_videojuego = new Videojuego($conexion);

// --- ACCIONES POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    if ($_POST['accion'] === 'crear') {
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $es_publica  = isset($_POST['es_publica']) ? 1 : 0;
        if ($nombre !== '') {
            $modelo_lista->crear($id_usuario, $nombre, $descripcion, $es_publica);
        }
        header("Location: listas.php?ok=creada");
        exit;
    }

    if ($_POST['accion'] === 'eliminar') {
        $id_lista = (int)($_POST['id_lista'] ?? 0);
        if ($id_lista) {
            $modelo_lista->eliminar($id_lista, $id_usuario);
        }
        header("Location: listas.php?ok=eliminada");
        exit;
    }

    if ($_POST['accion'] === 'toggle_visibilidad') {
        $id_lista = (int)($_POST['id_lista'] ?? 0);
        if ($id_lista) {
            $modelo_lista->toggleVisibilidad($id_lista, $id_usuario);
        }
        header("Location: listas.php");
        exit;
    }

    if ($_POST['accion'] === 'agregar_juego') {
        $id_lista      = (int)($_POST['id_lista']      ?? 0);
        $id_videojuego = (int)($_POST['id_videojuego'] ?? 0);
        if ($id_lista && $id_videojuego) {
            $lista = $modelo_lista->obtenerPorId($id_lista, $id_usuario);
            if ($lista) {
                $modelo_lista->agregarJuego($id_lista, $id_videojuego);
            }
        }
        $redirect = $_POST['redirect'] ?? 'listas.php';
        header("Location: " . $redirect);
        exit;
    }

    if ($_POST['accion'] === 'quitar_juego') {
        $id_lista      = (int)($_POST['id_lista']      ?? 0);
        $id_videojuego = (int)($_POST['id_videojuego'] ?? 0);
        if ($id_lista && $id_videojuego) {
            $lista = $modelo_lista->obtenerPorId($id_lista, $id_usuario);
            if ($lista) {
                $modelo_lista->quitarJuego($id_lista, $id_videojuego);
            }
        }
        header("Location: listas.php?ver=" . $id_lista);
        exit;
    }
}

// --- VISTA: Detalle de lista (propia o pública ajena) ---
if (isset($_GET['ver'])) {
    $id_lista = (int)$_GET['ver'];

    // Intentar obtener como lista propia
    $lista = $modelo_lista->obtenerPorId($id_lista, $id_usuario);
    $es_propietario = ($lista !== false && $lista !== null);

    // Si no es propia, intentar como lista pública ajena
    if (!$lista) {
        $lista = $modelo_lista->obtenerPublicaPorId($id_lista);
        if (!$lista) {
            header("Location: listas.php");
            exit;
        }
    }

    $juegos_lista = $modelo_lista->obtenerJuegosLista($id_lista);
    foreach ($juegos_lista as &$j) {
        $j['imagen_url'] = resolverPortada($j['imagen_portada'] ?? null);
    }
    unset($j);

    // Catálogo para agregar (solo si es el propietario)
    $todos_juegos = [];
    if ($es_propietario) {
        $todos_juegos = $modelo_videojuego->obtenerTodos();
        $ids_en_lista = array_column($juegos_lista, 'id_videojuego');
        foreach ($todos_juegos as &$v) {
            $v['en_lista']   = in_array($v['id_videojuego'], $ids_en_lista);
            $v['imagen_url'] = resolverPortada($v['imagen_portada'] ?? null);
        }
        unset($v);
    }

    $titulo_pagina = htmlspecialchars($lista['nombre']) . " | Listas | GameSocial";
    require_once __DIR__ . '/../../frontend/vistas/lista_detalle.php';
    exit;
}

// --- VISTA: Mis listas ---
$listas = $modelo_lista->obtenerListasUsuario($id_usuario);

// Resolver portadas
foreach ($listas as &$l) {
    $l['portada_url'] = resolverPortada($l['portada_lista'] ?? null);
}
unset($l);

$titulo_pagina = "Mis Listas | GameSocial";
require_once __DIR__ . '/../../frontend/vistas/listas.php';
