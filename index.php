<?php
/**
 * Router central — GameSocial
 */

$ruta = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$ruta = preg_replace('#^gamesocial/?#', '', $ruta);

switch ($ruta) {

    case '':
    case 'inicio':
        require __DIR__ . '/backend/controladores/feed.php';
        break;

    case 'catalogo':
        require __DIR__ . '/backend/controladores/catalogo.php';
        break;

    case 'mis-juegos':
        require __DIR__ . '/backend/controladores/mis_juegos.php';
        break;

    case 'listas':
        require __DIR__ . '/backend/controladores/listas.php';
        break;

    case 'perfil':
        require __DIR__ . '/backend/controladores/perfil.php';
        break;

    case 'buscar-usuarios':
        require __DIR__ . '/backend/controladores/buscar_usuarios.php';
        break;

    case 'login':
        require __DIR__ . '/backend/controladores/login.php';
        break;

    case 'registro':
        require __DIR__ . '/backend/controladores/registro.php';
        break;

    case 'logout':
        require __DIR__ . '/backend/controladores/logout.php';
        break;

    case 'like':
        require __DIR__ . '/backend/controladores/like.php';
        break;

    case 'follow':
        require __DIR__ . '/backend/controladores/follow.php';
        break;

    case 'notificaciones':
        require __DIR__ . '/backend/controladores/notificaciones.php';
        break;

    case 'contacto':
        require __DIR__ . '/backend/controladores/contacto.php';
        break;

    case 'admin':
        require __DIR__ . '/backend/controladores/admin/admin.php';
        break;

    case 'admin/videojuegos':
        require __DIR__ . '/backend/controladores/admin/videojuegos.php';
        break;

    case 'admin/videojuego/crear':
        require __DIR__ . '/backend/controladores/admin/videojuego_crear.php';
        break;

    default:
        // /juego/the-witcher-3
        if (preg_match('#^juego/([a-z0-9-]+)$#', $ruta, $m)) {
            $_GET['slug'] = $m[1];
            require __DIR__ . '/backend/controladores/detalle.php';

        // /usuario/123 (ID numérico)
        } elseif (preg_match('#^usuario/(\d+)$#', $ruta, $m)) {
            $_GET['id'] = (int)$m[1];
            require __DIR__ . '/backend/controladores/perfil_publico.php';

        // /usuario/nombre_usuario (cualquier nombre, sin barras)
        } elseif (preg_match('#^usuario/([^/]+)$#u', $ruta, $m)) {
            $_GET['nombre_usuario'] = urldecode($m[1]);
            require __DIR__ . '/backend/controladores/perfil_publico.php';

        // /lista/3-mi-lista-favorita (ID numérico + slug separado por primer guión)
        } elseif (preg_match('#^lista/(\\d+)-([a-z0-9-]*)$#', $ruta, $m)) {
            $_GET['ver'] = (int)$m[1];  // Usamos el ID numérico, el slug es solo decorativo
            require __DIR__ . '/backend/controladores/listas.php';

        // /admin/videojuego/editar/nombre-juego
        } elseif (preg_match('#^admin/videojuego/editar/([a-z0-9-]+)$#', $ruta, $m)) {
            $_GET['id'] = $m[1];
            require __DIR__ . '/backend/controladores/admin/videojuego_editar.php';

        // /admin/videojuego/eliminar/nombre-juego
        } elseif (preg_match('#^admin/videojuego/eliminar/([a-z0-9-]+)$#', $ruta, $m)) {
            $_GET['id'] = $m[1];
            require __DIR__ . '/backend/controladores/admin/videojuego_eliminar.php';

        } else {
            http_response_code(404);
            echo "<h1>404 - Página no encontrada</h1>";
        }
        break;
}
