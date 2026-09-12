# Plan de Migraciones, Modelos y Seeders

Este documento complementa a `ESPECIFICACION_FINAL.md`. Define el orden exacto y el contenido esperado de cada migración, modelo y seeder. Debe seguirse tal cual, sin agregar tablas, campos o lógica no descrita aquí ni en la especificación final.

## Orden de migraciones (respetar estrictamente por dependencias de FK)

1. `create_users_table` — **no crear nueva**, se MODIFICA la migración que ya trae Laravel Breeze, agregando los campos `rol` (varchar 20, valores: `administrador`/`personal`) y `activo` (boolean, default true).
2. `create_panteoneros_table`
3. `create_bloques_table`
4. `create_difuntos_table`
5. `create_ubicaciones_table` (FK a `bloques`)
6. `create_ubicacion_difunto_table` (FK a `ubicaciones` y `difuntos`)
7. `create_agendas_table` (FK a `difuntos`, `ubicaciones`, `panteoneros`, `users`)
8. `create_historial_table` (FK a `users`)

No usar `Schema::create` para `users` de nuevo; usar `Schema::table` en una migración separada solo para el `ALTER` de los dos campos nuevos, o editar directamente la migración original de Breeze si el proyecto aún no se ha migrado.

## Estructura exacta de columnas por tabla

(Ver la sección 8 de `ESPECIFICACION_FINAL.md` para el detalle completo de tipos y restricciones de cada tabla. No repetir aquí para evitar desincronización entre documentos — siempre consultar la especificación final como fuente de verdad.)

Puntos que NO deben olvidarse porque no son obvios a simple vista:

- `ubicaciones`: restricción única compuesta sobre (`bloque_id`, `lado`, `columna`, `fila`, `tipo`, `numero`). NO tiene columna `estado` (se calcula, ver más abajo).
- `ubicaciones.capacidad`: se autogenera en el modelo (no en el formulario) según `tipo` (Nicho=1, Mausoleo=5). No debe quedar editable desde ningún formulario.
- `ubicacion_difunto`: un mismo `difunto_id` no puede tener más de un registro con `activo = true` simultáneamente. Implementar esta regla como validación a nivel de aplicación (Form Request o método del modelo), no como constraint de base de datos, para mantenerlo simple.
- `agendas.ubicacion_id`: nullable. Obligatorio en el formulario solo si `tipo` es Inhumación, Exhumación o Anexión (usar `required_if` en el Form Request).
- `historial`: NO lleva `updated_at`, solo `created_at` (es un log inmutable).

## Modelos Eloquent

| Modelo | Tabla | Relaciones |
|---|---|---|
| `User` | `users` | `hasMany(Agenda::class)`, `hasMany(Historial::class)` |
| `Panteonero` | `panteoneros` | `hasMany(Agenda::class)` |
| `Bloque` | `bloques` | `hasMany(Ubicacion::class)` |
| `Difunto` | `difuntos` | `belongsToMany(Ubicacion::class, 'ubicacion_difunto')`, `hasMany(Agenda::class)` |
| `Ubicacion` | `ubicaciones` | `belongsTo(Bloque::class)`, `belongsToMany(Difunto::class, 'ubicacion_difunto')`, `hasMany(Agenda::class)` — **incluye accessor `getEstadoActualAttribute()`** (ver lógica abajo) |
| `UbicacionDifunto` | `ubicacion_difunto` | `belongsTo(Ubicacion::class)`, `belongsTo(Difunto::class)` — modelo Eloquent explícito, NO relación pivote implícita con `withPivot` |
| `Agenda` | `agendas` | `belongsTo(Difunto::class)`, `belongsTo(Ubicacion::class)`, `belongsTo(Panteonero::class)`, `belongsTo(User::class)` |
| `Historial` | `historial` | `belongsTo(User::class)` |

### Lógica del accessor `getEstadoActualAttribute()` en `Ubicacion`

```
Si existe un registro en ubicacion_difunto con ubicacion_id = esta ubicación y activo = true
   → "Ocupado"

Si no existe ese registro activo, pero el último registro (activo = false)
   tiene fecha_salida de menos de 14 días atrás desde hoy
   → "Mantenimiento"

En cualquier otro caso
   → "Disponible"
```

Esta lógica va en el modelo `Ubicacion`, en PHP simple y comentado, sin librerías externas.

## Seeders

> **ACTUALIZACIÓN:** el Bloque 18 fue eliminado por completo del
> proyecto (ver `docs/datos-reales/OBSOLETO_bloque_18_lado_norte.md`).
> Los bloques activos actualmente son **A1 y A2**, con sus datos reales
> en `docs/datos-reales/bloques_a1_a2.md`.

| Orden | Seeder | Contenido |
|---|---|---|
| 1 | `UserSeeder` | Un usuario Administrador inicial (email y password de desarrollo, documentado en el propio seeder con un comentario) |
| 2 | `BloqueSeeder` | Carga los bloques A1 y A2 con datos reales (ver `datos-reales/bloques_a1_a2.md`). NO inventar bloques adicionales. |
| 3 | `UbicacionSeeder` | Carga las ubicaciones reales de A1 (160) y A2 (232), aplicando la fórmula de numeración ya verificada, con numeración independiente por lado. Debe generarse mediante un bucle simple, NO copiando líneas repetitivas a mano — pero el resultado final debe coincidir exactamente con los datos reales verificados en `datos-reales/bloques_a1_a2.md`. |

### Fórmula de numeración verificada (Bloque 18, Lado Norte)

```
numero = (total_filas - fila) * columnas_por_fila + columna
```

Donde para este bloque: `total_filas = 5`, `columnas_por_fila = 26`. La fila 5 contiene los números más bajos (1 a 26); la fila 1 contiene los más altos (105 a 130). Ver el detalle verificado en `datos-reales/bloque_18_lado_norte.md`.

**IMPORTANTE:** esta fórmula es específica de este bloque/lado. NO debe asumirse como regla general para todos los bloques, porque ya se confirmó que cada bloque puede tener distinta cantidad de filas y columnas. Cuando se agreguen más bloques al seeder, cada uno debe traer su propia cantidad de filas/columnas verificada, nunca inventada.

## Orden sugerido de ejecución para el agente

1. Crear las 8 migraciones (o 7 + 1 modificación de la de Breeze) en el orden indicado.
2. Ejecutar `sail artisan migrate` y verificar que no haya errores de FK.
3. Crear los 8 modelos con sus relaciones.
4. Crear los 3 seeders.
5. Registrar los seeders en `DatabaseSeeder.php` en orden: `UserSeeder`, `BloqueSeeder`, `UbicacionSeeder`.
6. Ejecutar `sail artisan migrate:fresh --seed` y confirmar que la tabla `ubicaciones` quede con exactamente 130 registros para el Bloque 18.

No avanzar a controladores, rutas ni vistas hasta que este paso de base de datos esté validado.
