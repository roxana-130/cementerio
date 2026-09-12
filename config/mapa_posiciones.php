<?php

/**
 * Posiciones de los rectangulos de bloque sobre la imagen del plano
 * (public/images/plano_ cementerio2.png), usadas por el mapa administrativo
 * (Modulo 3 - Nivel 1: vista general del plano).
 *
 * Cada entrada esta en PORCENTAJE del tamano de la imagen (no en pixeles
 * fijos), para que el rectangulo se mantenga en su lugar sin importar el
 * tamano de pantalla:
 *
 *   'top'    => distancia desde el borde superior de la imagen, en %.
 *   'left'   => distancia desde el borde izquierdo de la imagen, en %.
 *   'width'  => ancho del rectangulo, en %.
 *   'height' => alto del rectangulo, en %.
 *
 * Como ajustar estos valores a simple vista:
 * 1. Abrir la imagen del plano y ubicar el bloque deseado.
 * 2. Estimar en que porcentaje del ancho/alto total de la imagen empieza
 *    y cuanto ocupa el rectangulo (ej. si el bloque empieza a la mitad del
 *    ancho de la imagen, 'left' es aproximadamente 50).
 * 3. Guardar, recargar /mapa y comparar el rectangulo dibujado contra la
 *    imagen. Repetir hasta que quede alineado.
 *
 * Solo se dibuja el rectangulo de un bloque si ademas tiene al menos una
 * ubicacion sembrada en la base de datos (ver MapaController::index()).
 * No inventar posiciones de bloques sin datos reales (A3, A5, A6, etc.).
 */

return [

    // Estimado a partir de la etiqueta "A1" del plano (zona centro-izquierda,
    // fila superior de bloques A1/A2/A3). Ajustar a ojo si no coincide.
    'A1' => [
        'top'    => 61,
        'left'   => 10,
        'width'  => 7,
        'height' => 9,
    ],

    // A2 esta inmediatamente a la derecha de A1 en la misma fila.
    'A2' => [
        'top'    => 61,
        'left'   => 18,
        'width'  => 7,
        'height' => 9,
    ],

];
