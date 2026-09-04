# Datos reales — Bloque 18, Lado Norte

Fuente: relevamiento fotográfico del sistema legado de consulta de sepulturas del Cementerio General de Sacaba (sección "Semi Antiguos").

Estos son los únicos datos verificados hasta el momento. No se debe inventar ni extrapolar información de otros bloques o lados a partir de este archivo.

## Datos del bloque

| Campo | Valor |
|---|---|
| Bloque | 18 |
| Lado | Norte |
| Tipo de espacio | Nicho (todos, por regla: Norte/Sur = Nicho) |
| Capacidad por espacio | 1 (regla general de Nicho) |
| Columnas | 26 (columnas 1 a 26) |
| Filas | 5 (filas 1 a 5) |
| Total de nichos en este lado | 130 |

## Regla de numeración verificada

La numeración comienza en la última fila (fila 5) y decrece hacia la fila 1:

```
numero = (5 - fila) * 26 + columna
```

| Fila | Rango de números resultante |
|---|---|
| 5 | 1 – 26 |
| 4 | 27 – 52 |
| 3 | 53 – 78 |
| 2 | 79 – 104 |
| 1 | 105 – 130 |

Verificado contra la imagen original en varios puntos de control:
- Fila 5, columna 1 → Nro. 1 ✅
- Fila 5, columna 9 → Nro. 9 ✅
- Fila 4, columna 2 → Nro. 28 ✅
- Fila 3, columna 9 → Nro. 61 ✅
- Fila 2, columna 26 → Nro. 104 ✅
- Fila 1, columna 26 → Nro. 130 ✅

## Nota sobre disponibilidad

Las celdas marcadas como "Disponible" en el sistema legado (ej. fila 5, columnas 2, 14 y 19) **no significan ausencia de número** — ese nicho sí tiene número asignado (2, 14, 19 respectivamente), solo que actualmente no tiene un difunto asociado. Esto **no debe reflejarse en el seeder** de ninguna forma especial: todos los 130 nichos se crean igual en la tabla `ubicaciones`; la disponibilidad se calcula después mediante el accessor de estado (ver `PLAN_MIGRACIONES.md`), no se carga como dato fijo del seeder.

## Pendiente (no inventar, completar cuando se tenga el relevamiento real)

- Bloque 18, Lado Sur (nichos)
- Bloque 18, Lado Este (mausoleos)
- Bloque 18, Lado Oeste (mausoleos)
- Resto de bloques del cementerio
