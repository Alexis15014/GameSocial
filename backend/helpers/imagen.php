<?php
/**
 * Helper: imagen.php
 * Propósito: Centralizar la resolución de rutas de portadas y avatares.
 * Proyecto: GameSocial
 */

// Imagen de respaldo cuando la portada de un juego no existe o no se encuentra
const PLACEHOLDER_PORTADA = '/gamesocial/frontend/assets/img/gamesocial.png';

// Imagen de respaldo cuando el usuario no tiene foto de perfil asignada
const PLACEHOLDER_AVATAR  = '/gamesocial/frontend/assets/img/gamesocial.png';

// Devolvemos la URL relativa de la portada de un juego.
// Verificamos que el archivo exista físicamente; si no, devolvemos el placeholder.
function resolverPortada(?string $ruta_relativa): string {
    if (!empty($ruta_relativa) && file_exists(__DIR__ . '/../../' . $ruta_relativa)) {
        return '/gamesocial/' . $ruta_relativa;
    }
    return PLACEHOLDER_PORTADA;
}

// Devolvemos la URL absoluta del avatar de un usuario.
// Si no tiene foto asignada, devolvemos el logo de GameSocial como fallback.
function resolverAvatar(?string $foto_perfil): string {
    if (!empty($foto_perfil)) {
        return '/gamesocial/' . ltrim($foto_perfil, '/');
    }
    return PLACEHOLDER_AVATAR;
}

// Añadimos la clave 'imagen_portada_url' a cada juego del array.
// Evita repetir el mismo bucle en los controladores que listen juegos.
function procesarImagenesJuegos(array &$videojuegos): array {
    foreach ($videojuegos as &$juego) {
        $juego['imagen_portada_url'] = resolverPortada($juego['imagen_portada'] ?? null);
    }
    unset($juego);
    return $videojuegos;
}
