# Datos reales — Bloques A1 y A2

Fuente: plano de emplazamiento actualizado (plano__cementerio2.png), que
identifica la ubicación y etiqueta de los bloques A1, A2, A3, A5, A6
dentro del predio. Datos de filas/columnas proporcionados directamente
por el usuario (no extraídos de una foto de numeración legada como en
el Bloque 18).

Estos son los únicos datos verificados de A1 y A2. NO se deben
inventar ni extrapolar datos de A3, A5 o A6 — esos bloques aparecen
etiquetados en el plano pero sin relevamiento de filas/columnas
todavía.

## Regla de numeración (misma fórmula verificada en Bloque 18)

```
numero = (total_filas_del_lado - fila) * columnas_del_lado + columna
```

La numeración **empieza en la última fila** (números más bajos) y
sube hacia la fila 1, igual que en Bloque 18.

## Regla de numeración por lado: INDEPENDIENTE

A diferencia de si fuera continua, aquí **cada lado empieza su propia
numeración en 1**. Es decir, A1-Sur tiene números 1 a 76, y A1-Norte
TAMBIÉN tiene números 1 a 76 (números repetidos entre lados, pero la
combinación Bloque+Lado+Columna+Fila+Tipo+Número sigue siendo única
porque el Lado es distinto).

## Bloque A1

| Lado | Columnas | Filas | Tipo | Total ubicaciones | Rango de número |
|---|---|---|---|---|---|
| Sur | 19 | 4 | Nicho | 76 | 1 – 76 |
| Norte | 19 | 4 | Nicho | 76 | 1 – 76 |
| Este | 4 | 2 | Mausoleo | 8 | 1 – 8 |
| Oeste | — | — | — | 0 | (vacío, no se cargan registros para este lado) |

Total de ubicaciones en A1: 160

## Bloque A2

| Lado | Columnas | Filas | Tipo | Total ubicaciones | Rango de número |
|---|---|---|---|---|---|
| Sur | 22 | 5 | Nicho | 110 | 1 – 110 |
| Norte | 22 | 5 | Nicho | 110 | 1 – 110 |
| Este | 4 | 2 | Mausoleo | 8 | 1 – 8 |
| Oeste | 2 | 2 | Mausoleo | 4 | 1 – 4 |

Total de ubicaciones en A2: 232

## Capacidad

Aplica la regla general ya definida: Nicho = capacidad 1, Mausoleo =
capacidad 5 (autogenerado según tipo, no es un dato de este archivo).

## Posición en el plano (para el mapa visual, no para la base de datos)

En la imagen plano__cementerio2.png, los bloques A1 y A2 están
ubicados en la zona centro-izquierda del predio, en la fila de
bloques rotulados A1, A2, A3 (fila superior) y A5, A6 (fila inferior,
justo debajo de A2 y A3). A1 está más al oeste (izquierda) que A2.

Esta posición es solo una referencia aproximada para colocar los
marcadores sobre la imagen en el mapa interactivo — NO se debe usar
para inventar coordenadas de píxel exactas; los marcadores se colocan
de forma aproximada dentro de esa zona general, tal como ya se hizo
con el Bloque 18.

## Pendiente (no inventar, completar cuando se tenga el relevamiento)

- Bloque A1, Lado Oeste (confirmado vacío por ahora, no hay datos)
- Bloques A3, A5, A6 (aparecen etiquetados en el plano pero sin datos
  de filas/columnas todavía)
- Resto de bloques del cementerio
