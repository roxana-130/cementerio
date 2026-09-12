# MAPA.md

# Mapa y estructura física del Cementerio General de Sacaba

## 1. Objetivo del módulo

El sistema debe representar y consultar la estructura física real del Cementerio General de Sacaba de manera interactiva.

Se utilizará:

- Leaflet 1.9.x.
- OpenStreetMap.

No se deben inventar ubicaciones, números o estructuras que no estén confirmadas con los datos reales del cementerio.

## 2. Dos niveles del mapa

Debe distinguirse entre:

### 2.1 Mapa geográfico general

OpenStreetMap proporciona el mapa base.

Leaflet permite:

- mostrar el mapa;
- navegar;
- hacer zoom;
- colocar referencias geográficas;
- mostrar la ubicación general del cementerio.

### 2.2 Mapa interno del cementerio

Representa las ubicaciones físicas reales:

- bloques;
- lados;
- columnas;
- filas;
- nichos;
- mausoleos.

Las ubicaciones internas pueden representarse mediante geometrías GeoJSON y/o coordenadas de centro.

## 3. Estructura física

El cementerio NO utiliza sectores.

Una ubicación se identifica mediante:

**Bloque + Lado + Columna + Fila + Tipo + Número**

Esta combinación debe ser única.

## 4. Lados

Los únicos lados válidos son:

- Norte.
- Sur.
- Este.
- Oeste.

## 5. Distribución por lado

Dentro de cada bloque:

| Lado | Tipo permitido |
|---|---|
| Norte | Nicho |
| Sur | Nicho |
| Este | Mausoleo |
| Oeste | Mausoleo |

Esta regla debe garantizarse al cargar los datos reales mediante seeder.

No inventar ubicaciones para completar bloques.

## 6. Tipos y capacidad

### Nicho

- Tipo: Nicho.
- Capacidad fija: 1.

### Mausoleo

- Tipo: Mausoleo.
- Capacidad fija: 5.

La capacidad se genera automáticamente según el tipo y no puede editarse manualmente.

## 7. Numeración

La numeración utilizada debe ser la numeración oficial real del cementerio.

No generar números ficticios.

Si todavía no se dispone de los datos oficiales de una ubicación, dejarla pendiente en lugar de inventarla.

## 8. Carga de datos físicos

### Bloques

Los bloques reales se cargan una única vez mediante seeder.

No existe CRUD de bloques.

No se deben crear, editar ni eliminar bloques desde la interfaz administrativa.

### Ubicaciones

Las ubicaciones reales se cargan mediante seeder.

No existe un CRUD independiente llamado “Espacios”.

No se deben crear nuevas ubicaciones desde la interfaz.

## 9. Campos de ubicación

La tabla `ubicaciones` debe manejar:

- `id`
- `bloque_id`
- `lado`
- `columna`
- `fila`
- `tipo`
- `numero`
- `capacidad`
- `geom_geojson`
- `centro_lat`
- `centro_lng`
- `activo`
- timestamps

La combinación siguiente es única:

`bloque_id + lado + columna + fila + tipo + numero`

## 10. Campos editables desde el mapa

Una ubicación existente puede ser editada por el Administrador únicamente en:

- `geom_geojson`;
- `centro_lat`;
- `centro_lng`;
- `activo`.

No se pueden modificar desde la interfaz:

- bloque;
- lado;
- columna;
- fila;
- tipo;
- número;
- capacidad.

El Personal administrativo solamente puede consultar el mapa.

## 11. Estado de una ubicación

El estado no se almacena en la tabla `ubicaciones`.

Se calcula en el momento de la consulta.

### Ocupado

Si existe un registro activo en `ubicacion_difunto` para esa ubicación.

### Mantenimiento

Si no existe registro activo, pero el último registro inactivo tiene una `fecha_salida` de menos de 14 días desde la fecha actual.

### Disponible

En cualquier otro caso.

No existe estado “Reservado”.

No utilizar Scheduler para actualizar estados.

## 12. Relación con difuntos

La tabla intermedia `ubicacion_difunto` permite relacionar difuntos con ubicaciones.

Campos principales:

- `ubicacion_id`
- `difunto_id`
- `fecha_ingreso`
- `fecha_salida`
- `activo`

Un difunto no puede tener más de una asociación activa al mismo tiempo.

Un mausoleo puede tener varios registros activos porque su capacidad es 5.

## 13. Integración con Agenda

El campo `ubicacion_id` de `agendas` puede ser nulo únicamente cuando corresponda a una Cremación sin anexión.

### Inhumación

Ubicación obligatoria.

Al realizarse:

- crear asociación en `ubicacion_difunto`;
- `fecha_ingreso` = fecha de agenda;
- `activo` = true.

### Exhumación

Ubicación obligatoria.

Al realizarse:

- localizar la asociación activa;
- colocar `fecha_salida` = fecha de agenda;
- cambiar `activo` a false.

Después comenzará el periodo de mantenimiento de 14 días.

### Anexión

Ubicación obligatoria.

Al realizarse:

- crear asociación nueva;
- permitir que existan otros registros activos cuando sea un mausoleo.

### Cremación

Ubicación opcional.

Si se selecciona ubicación:

- crear asociación como en una inhumación.

Si no se selecciona ubicación:

- no crear asociación.

## 14. Interacción pública

El visitante puede:

- navegar el mapa;
- seleccionar manualmente una ubicación;
- consultar la ficha pública correspondiente.

La información pública de una ubicación/difunto no debe exponer datos internos.

La ficha pública de un difunto muestra:

- nombre;
- bloque;
- lado;
- columna;
- fila;
- tipo;
- número.

Nunca mostrar:

- CI;
- fecha de fallecimiento;
- causa de muerte;
- historial;
- disponibilidad interna.

## 15. Interacción administrativa

El Administrador puede:

- consultar ubicaciones;
- consultar estados;
- editar geometría;
- editar coordenadas;
- activar/desactivar una ubicación;
- asociar o retirar difuntos cuando corresponda;
- acceder a información administrativa.

El Personal administrativo puede consultar el mapa, pero no editar las ubicaciones.

## 16. Reglas de implementación

- Mantener la solución sencilla.
- Evitar arquitecturas complejas.
- Usar controladores, modelos y vistas Laravel convencionales.
- Usar Blade + Bootstrap 5.
- Usar Leaflet de forma directa y comprensible.
- No introducir una API independiente.
- No agregar librerías de mapas innecesarias.
- No inventar geometrías oficiales.

## 17. Datos pendientes

La geometría real y la numeración oficial deben provenir de los datos proporcionados por el Cementerio General de Sacaba.

Mientras no estén disponibles:

- no inventar coordenadas;
- no inventar bloques;
- no inventar columnas;
- no inventar filas;
- no inventar números.

La estructura del sistema debe quedar preparada para cargar posteriormente los datos reales mediante seeders y GeoJSON.
