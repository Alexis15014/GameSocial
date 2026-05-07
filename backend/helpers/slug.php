<?php
/**
 * Helper: slug.php
 * Propósito: Generar slugs URL amigables a partir de texto.
 * Proyecto: GameSocial
 */

function generarSlug(string $texto): string {
    // Convertimos caracteres especiales del español y otros idiomas
    $mapa = [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
        'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
        'ä'=>'a','ë'=>'e','ï'=>'i','ö'=>'o','ü'=>'u',
        'â'=>'a','ê'=>'e','î'=>'i','ô'=>'o','û'=>'u',
        'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u',
        'À'=>'a','È'=>'e','Ì'=>'i','Ò'=>'o','Ù'=>'u',
        'Ä'=>'a','Ë'=>'e','Ï'=>'i','Ö'=>'o','Ü'=>'u',
        'Â'=>'a','Ê'=>'e','Î'=>'i','Ô'=>'o','Û'=>'u',
        'ñ'=>'n','Ñ'=>'n','ç'=>'c','Ç'=>'c',
        ':'=>'','\''=>'','\"'=>'','!'=>'','?'=>'','('=>'',')'=> '',
        '&'=>'y','@'=>'','.'=>'','·'=>'-',','=>'',';'=>'',
    ];
    $texto = strtr($texto, $mapa);
    $texto = strtolower($texto);
    // Reemplazamos cualquier carácter que no sea letra, número o guion por guion
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    // Quitamos guiones al inicio y al final
    return trim($texto, '-');
}
