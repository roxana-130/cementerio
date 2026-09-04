# Especificación Final del Proyecto
## Plataforma Web para la Gestión del Cementerio General de Sacaba mediante Mapas Interactivos y Servicios Digitales

**Documento de validación previo al desarrollo.**
Este documento consolida todas las definiciones y decisiones tomadas durante la etapa de análisis, incluyendo las aclaraciones e inconsistencias resueltas respecto al documento original de requisitos. Debe ser revisado y aprobado antes de iniciar la escritura de código, migraciones o archivos del proyecto.

---

## 1. Objetivo del proyecto

**Objetivo general:** Desarrollar una plataforma web para la gestión administrativa del Cementerio General de Sacaba mediante mapas interactivos y servicios digitales, optimizando los procesos de consulta, control y asignación de espacios funerarios.

**Objetivos específicos:**
1. Implementar los módulos administrativos para la gestión de registros, inhumaciones, exhumaciones y servicios funerarios.
2. Implementar un módulo de mapas interactivos que facilite la localización y consulta de espacios funerarios mediante información georreferenciada.

---

## 2. Stack tecnológico

| Tecnología | Versión acordada |
|---|---|
| Laravel | Última versión estable disponible al momento de implementar (verificar en el momento de iniciar) |
| PHP | Última versión estable compatible con esa versión de Laravel |
| PostgreSQL | Última versión estable disponible |
| Laravel Sail | Sí (obligatorio, sin dependencia de PHP/XAMPP local en Windows) |
| Docker Desktop | 29.x |
| Docker Compose | 5.x |
| Composer | 2.x |
| Node.js | 22 LTS |
| NPM | 10.x |
| Vistas | Blade |
| CSS | Tailwind CSS 3.x |
| JavaScript | ES6+ básico, sin frameworks adicionales |
| Mapas | Leaflet 1.9.x + OpenStreetMap |
| Autenticación | Laravel Breeze (Blade + Tailwind) |
| Control de versiones | Git 2.x |

> **Nota:** al momento de crear el proyecto se debe verificar cuáles son exactamente las versiones estables vigentes de Laravel/PHP/PostgreSQL, ya que este documento no fija un número exacto sino el criterio "última estable disponible".

---

## 3. Principios de desarrollo (nivel Junior)

- Laravel convencional, patrón MVC estándar.
- Modelos simples, controladores claros, migraciones fáciles de entender.
- Relaciones Eloquent básicas (belongsTo, hasMany, belongsToMany).
- Form Requests solo cuando aporten claridad real.
- Sin Repository Pattern, sin Service Layer innecesaria, sin DTOs, sin interfaces por clase, sin microservicios, sin API independiente.
- Sin borrado físico de registros importantes: se usa el campo `activo` para desactivar.
- Ante dos soluciones válidas, se elige siempre la más simple de explicar para un estudiante Junior en una defensa de grado.
- No se debe inventar numeración física, sectores, módulos ni reglas de negocio no definidas en este documento.

---

## 4. Usuarios y roles

| Rol | Accede al sistema | Permisos principales |
|---|---|---|
| **Administrador** | Sí | Gestiona usuarios y panteoneros, consulta estados internos e historial, corrige datos con trazabilidad, gestiona difuntos, agenda, mapa administrativo completo (consulta y edición de campos permitidos) |
| **Personal administrativo** | Sí | Gestiona difuntos, agenda, mapa administrativo **solo en modo consulta**. No gestiona usuarios, panteoneros, ni accede al historial |
| **Visitante/familiar** | No (interfaz pública, sin cuenta) | Consulta información del cementerio, busca difuntos, ve el mapa público |
| **Panteonero** | No es un rol de acceso | Persona registrada en el sistema para ser asignada a actividades de la agenda; no inicia sesión |

No existe registro público de usuarios. No existe tabla de roles: el rol se maneja como un campo simple (`administrador` / `personal`) en la tabla `users`.

**Recuperación de contraseña:** no existe flujo automático por correo. Si un usuario administrativo olvida su contraseña, el Administrador se la restablece manualmente desde el CRUD de usuarios.

---

## 5. Reglas de negocio físicas del cementerio

- La estructura física **no usa sectores**.
- Un espacio físico se identifica siempre mediante: **Bloque + Lado + Columna + Fila + Tipo + Número**. Esta combinación es **única sin excepción**, tanto para Nicho como para Mausoleo.
- Lados válidos: Norte, Sur, Este, Oeste.
- Tipos válidos: Nicho, Mausoleo.
- **Regla de distribución por lado:** en cada bloque, los lados **Norte y Sur contienen únicamente Nichos**; los lados **Este y Oeste contienen únicamente Mausoleos**. Esta regla se garantiza mediante el seeder de carga inicial de datos (no mediante restricción a nivel de base de datos).
- Capacidad fija según tipo: Nicho = 1, Mausoleo = 5. El campo `capacidad` se autogenera según el `tipo` seleccionado y **no es editable** por el usuario.
- La numeración oficial es la real del cementerio y **no debe inventarse**.
- El bloque de nichos municipales aún no construido queda **fuera del alcance**.

### Carga y edición de la estructura física
- **Bloques:** se cargan una única vez mediante seeder con datos reales. **Nunca se crean, editan ni eliminan desde la interfaz.**
- **Ubicaciones (nichos/mausoleos):** se cargan una única vez mediante seeder con los datos físicos reales (bloque, lado, columna, fila, tipo, número, capacidad). **Nunca se crean nuevas desde la interfaz.**
- Desde el módulo Mapa, el Administrador **solo puede editar** los siguientes campos de una ubicación ya existente:
  - `geom_geojson`
  - `centro_lat`
  - `centro_lng`
  - `activo` (por si el espacio queda físicamente inutilizable)
- Los campos `bloque_id`, `lado`, `columna`, `fila`, `tipo`, `numero` y `capacidad` **nunca se editan** desde la interfaz una vez cargados por el seeder.
- No existe un CRUD independiente llamado "Espacios"; toda la gestión de ubicaciones ocurre desde el módulo Mapa.

### Estado de una ubicación (calculado, no almacenado)
El estado de cada ubicación **no se guarda en base de datos**; se calcula siempre en el momento de la consulta mediante un accessor en el modelo `Ubicacion`, con la siguiente lógica:

```
Si existe un registro activo (activo = true) en ubicacion_difunto para esa ubicación
   → "Ocupado"

Si no existe registro activo, pero el último registro (activo = false)
   tiene fecha_salida de menos de 14 días atrás desde hoy
   → "Mantenimiento"

En cualquier otro caso
   → "Disponible"
```

- El periodo de mantenimiento tras una exhumación es de **14 días (2 semanas)**, contados desde la `fecha_salida` registrada en `ubicacion_difunto`.
- No existe el estado "Reservado".
- No hay tarea programada (Scheduler) ni columna física de estado: todo se resuelve en PHP al momento de mostrar la información (mapa, dashboard, ficha pública).

---

## 6. Módulos funcionales

### Módulo 1 — Autenticación y usuarios
- Login / Logout mediante Laravel Breeze.
- Control de acceso por rol (`administrador` / `personal`).
- CRUD completo de usuarios (crear, editar, activar/desactivar), **solo accesible por Administrador**.
- Reseteo de contraseña manual por el Administrador (sin flujo por correo).
- No existe registro público de usuarios.

### Módulo 2 — Gestión de difuntos
- Registrar, editar y consultar difuntos.
- Búsqueda por código, CI, nombre y apellidos.
- Ver la ubicación actual del difunto (si tiene una asociación activa en `ubicacion_difunto`).
- Consultar información relacionada con su agenda.
- Validación de datos antes de guardar.
- El CI se usa solo para búsqueda interna; **nunca se muestra en la interfaz pública**.
- Campo `activo` para desactivar un difunto (sin borrado físico), registrado en el historial.
- Acceso: Administrador y Personal administrativo.

### Módulo 3 — Mapa y ubicaciones
- Visualización del mapa interactivo (Leaflet + OpenStreetMap).
- Consulta de ubicaciones: bloque, lado, columna, fila, tipo, número, capacidad y estado calculado.
- Edición limitada de ubicaciones (ver sección 5: solo geolocalización y `activo`).
- Asociar / retirar difuntos de una ubicación (manual desde el mapa, y también automático desde la Agenda, ver Módulo 5).
- Consulta de capacidad y estado.
- Acceso: Administrador (consulta y edición permitida) y Personal administrativo (**solo consulta**).

### Módulo 4 — Panteoneros
- Registrar, editar, consultar y activar/desactivar panteoneros.
- Asignación de panteoneros a actividades de la agenda.
- El panteonero no inicia sesión; es solo un registro operativo.
- Acceso: **solo Administrador**.

### Módulo 5 — Agenda
- Crear y programar actividades (Inhumación, Exhumación, Cremación, Anexión).
- Seleccionar difunto, panteonero, fecha, hora y observaciones.
- Cambiar estado de la actividad: Pendiente / Realizado / Cancelado.
- Consulta por día, semana o mes; vista de calendario.
- El tipo de actividad es un campo de la propia tabla `agendas` (no existe tabla `servicios`).
- Campo `ubicacion_id` en `agendas` (nullable), usado para el automatismo descrito abajo.

**Automatismo al marcar una actividad como "Realizado":**

| Tipo de actividad | ¿Ubicación obligatoria? | Efecto automático en `ubicacion_difunto` |
|---|---|---|
| Inhumación | Sí | Crea registro nuevo: `ubicacion_id`, `difunto_id`, `fecha_ingreso = fecha de la agenda`, `activo = true` |
| Exhumación | Sí | Busca el registro activo de ese difunto en esa ubicación y lo cierra: `fecha_salida = fecha de la agenda`, `activo = false` (dispara el cálculo de "Mantenimiento") |
| Anexión | Sí | Crea registro nuevo (igual que Inhumación), sobre una ubicación que puede ya tener otros registros activos (típicamente un mausoleo) |
| Cremación | **Depende del caso** | Si se indica `ubicacion_id` (la familia decide anexar la urna a un mausoleo): igual que Inhumación. Si no se indica ubicación (la familia se lleva los restos): no se crea ningún registro en `ubicacion_difunto` |

En el formulario de Agenda, el campo "Ubicación" es obligatorio para Inhumación, Exhumación y Anexión, y opcional para Cremación (validado con Form Request, `required_if`).

Acceso: Administrador y Personal administrativo.

### Módulo 6 — Historial
- Consulta de cambios importantes del sistema: usuario, fecha/hora, tabla, registro afectado, acción, datos anteriores, datos nuevos, descripción.
- **Sí se registra:** crear/editar/desactivar difuntos; crear/editar/cambiar estado/asociar/retirar difunto en ubicaciones; crear/editar/activar/desactivar panteoneros; crear/editar/cambiar estado/cancelar agendas; crear/editar/activar/desactivar usuarios.
- **No se registra:** inicio/cierre de sesión, búsquedas, apertura de pantallas o del mapa, clics que no modifiquen información, filtros, paginación.
- Formato de registro de cambios: valor **antes** → valor **después**, por cada campo relevante modificado.
- Acceso: **solo Administrador** (solo lectura).

### Módulo 7 — Dashboard
- Pantalla inicial tras el login.
- Muestra: total de difuntos registrados, total de ubicaciones, ubicaciones ocupadas, ubicaciones disponibles, actividades programadas para hoy, actividades pendientes.
- Los conteos de ocupadas/disponibles usan el mismo cálculo de estado definido en la sección 5.
- Sin gráficos innecesarios.

### Módulo 8 — Interfaz pública
- Inicio, información básica del cementerio, buscador de difuntos, mapa público, ficha pública de ubicación.
- Búsqueda por: código/ID, CI, nombre, apellido paterno, apellido materno.
- La ficha pública muestra **únicamente**: nombre y ubicación (bloque, lado, columna, fila, tipo, número).
- **Nunca se muestra públicamente:** CI, fecha de fallecimiento, causa de muerte, disponibilidad de la ubicación, historial.
- No requiere autenticación.

---

## 7. Panel administrativo (estructura de navegación)

```
┌──────────────────────────────────────────────┐
│  Logo / Cementerio General de Sacaba          │
├──────────────┬───────────────────────────────┤
│ Dashboard    │                               │
│ Difuntos     │                               │
│ Agenda       │          Contenido            │
│ Mapa         │                               │
│ Historial    │                               │
│ Panteoneros  │                               │
│ Usuarios     │                               │
│ Cerrar sesión│                               │
└──────────────┴───────────────────────────────┘
```

*(Nota: "Usuarios" se agrega al menú original del documento de requisitos, como consecuencia directa de la decisión tomada en el Módulo 1 de crear un CRUD completo de usuarios, visible solo para Administrador. "Panteoneros" y "Historial" también visibles solo para Administrador.)*

**Paleta institucional:** verde oscuro (principal), verde claro (apoyo), blanco (fondo), gris claro (áreas secundarias), gris oscuro (texto), rojo (solo acciones destructivas/alertas), amarillo (estados pendientes), verde (estados disponibles/completados). Tipografía Inter. Diseño limpio, institucional, responsive, sin animaciones excesivas.

---

## 8. Diseño de base de datos (final)

### `users`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| name | varchar(150) | not null |
| email | varchar(150) | unique, not null |
| password | varchar(255) | not null |
| rol | varchar(20) | not null — valores: `administrador`, `personal` |
| activo | boolean | default true |
| created_at / updated_at | timestamp | |

### `panteoneros`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| nombre | varchar(100) | not null |
| apellido_paterno | varchar(100) | not null |
| apellido_materno | varchar(100) | not null |
| ci | varchar(20) | unique, not null |
| activo | boolean | default true |
| created_at / updated_at | timestamp | |

### `bloques`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| codigo | varchar(20) | unique, not null |
| nombre | varchar(100) | not null |
| descripcion | text | nullable |
| activo | boolean | default true |
| created_at / updated_at | timestamp | |

### `ubicaciones`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| bloque_id | bigint | FK → bloques.id |
| lado | varchar(10) | Norte / Sur / Este / Oeste |
| columna | integer | not null |
| fila | integer | not null |
| tipo | varchar(20) | Nicho / Mausoleo |
| numero | integer | not null |
| capacidad | integer | autogenerado (1 si Nicho, 5 si Mausoleo), no editable |
| geom_geojson | json | nullable |
| centro_lat | decimal(10,7) | nullable |
| centro_lng | decimal(10,7) | nullable |
| activo | boolean | default true |
| created_at / updated_at | timestamp | |
| **Restricción única** | (bloque_id, lado, columna, fila, tipo, numero) | unique compuesto |

*(No existe columna `estado`; se calcula mediante accessor, ver sección 5.)*

### `ubicacion_difunto`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| ubicacion_id | bigint | FK → ubicaciones.id |
| difunto_id | bigint | FK → difuntos.id |
| fecha_ingreso | date | not null |
| fecha_salida | date | nullable |
| activo | boolean | default true |
| created_at / updated_at | timestamp | |
| **Restricción** | un mismo `difunto_id` no puede tener más de un registro con `activo = true` a la vez | |

### `difuntos`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| codigo | varchar(30) | unique, not null |
| nombre | varchar(100) | not null |
| apellido_paterno | varchar(100) | not null |
| apellido_materno | varchar(100) | not null |
| apellido_casada | varchar(100) | nullable |
| ci | varchar(20) | not null (uso interno, nunca público) |
| edad | integer | nullable |
| profesion_ocupacion | varchar(150) | nullable |
| causa_muerte | text | nullable |
| fecha_fallecimiento | date | not null |
| hora_fallecimiento | time | nullable |
| numero_certificado_defuncion | varchar(50) | nullable |
| activo | boolean | default true |
| created_at / updated_at | timestamp | |

### `agendas`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| codigo | varchar(30) | unique, not null |
| tipo | varchar(20) | Inhumación / Exhumación / Cremación / Anexión |
| difunto_id | bigint | FK → difuntos.id |
| ubicacion_id | bigint | FK → ubicaciones.id, nullable |
| panteonero_id | bigint | FK → panteoneros.id, nullable |
| fecha | date | not null |
| hora | time | not null |
| estado | varchar(20) | Pendiente / Realizado / Cancelado |
| observaciones | text | nullable |
| usuario_id | bigint | FK → users.id |
| created_at / updated_at | timestamp | |

### `historial`
| Campo | Tipo | Restricciones |
|---|---|---|
| id | bigserial | PK |
| user_id | bigint | FK → users.id |
| tabla | varchar(50) | not null |
| registro_id | bigint | not null |
| accion | varchar(20) | Crear / Editar / Desactivar / Activar / Asociar / Retirar / Cancelar, etc. |
| descripcion | text | not null |
| datos_anteriores | json | nullable |
| datos_nuevos | json | nullable |
| created_at | timestamp | (sin `updated_at`: registro inmutable) |

### Relaciones Eloquent (resumen)
- `Bloque` → `hasMany(Ubicacion)`
- `Ubicacion` → `belongsTo(Bloque)`, `belongsToMany(Difunto)` a través de `ubicacion_difunto`
- `Difunto` → `belongsToMany(Ubicacion)` a través de `ubicacion_difunto`, `hasMany(Agenda)`
- `Panteonero` → `hasMany(Agenda)`
- `User` → `hasMany(Agenda)`, `hasMany(Historial)`
- `Agenda` → `belongsTo(Difunto)`, `belongsTo(Ubicacion)`, `belongsTo(Panteonero)`, `belongsTo(User)`

---

## 9. Rutas y controladores propuestos (resumen por módulo)

| Módulo | Controlador(es) | Acceso |
|---|---|---|
| Autenticación | Laravel Breeze (`Auth\...`) | Público (login) |
| Usuarios | `UsuarioController` | Administrador |
| Difuntos | `DifuntoController` | Administrador, Personal |
| Mapa / Ubicaciones | `MapaController`, `UbicacionController` | Administrador (edición), Personal (consulta) |
| Panteoneros | `PanteoneroController` | Administrador |
| Agenda | `AgendaController` | Administrador, Personal |
| Historial | `HistorialController` (solo lectura) | Administrador |
| Dashboard | `DashboardController` | Administrador, Personal |
| Interfaz pública | `PublicoController` | Público, sin autenticación |

---

## 10. Alcance

### Incluido
Autenticación administrativa, gestión de difuntos, gestión de ubicaciones existentes (bloques, lados, columnas, filas, nichos, mausoleos), estados internos (calculados), panteoneros, agenda, historial, mapa interactivo, búsqueda pública, ubicación y ruta interna.

### Excluido
Pagos, caja, contabilidad, facturación, gestión financiera, carga de documentos, cuentas de visitantes, creación libre de nichos/bloques desde la app, bloque nuevo de nichos municipales (aún no construido), disponibilidad pública, CI público, fecha de fallecimiento pública, historial público.

---

## 11. Restricciones de desarrollo (recordatorio para todo el proyecto)

- No usar arquitectura innecesariamente compleja ni patrones de diseño no solicitados.
- No crear capas, servicios o repositorios "por buenas prácticas" si Laravel convencional resuelve el problema de forma simple.
- No usar microservicios ni API independiente.
- No instalar paquetes adicionales sin aprobación previa, salvo Laravel Breeze (ya aprobado).
- No modificar varias funcionalidades importantes al mismo tiempo.
- No cambiar la estructura de base de datos sin explicar antes qué se modifica y por qué.
- No inventar información física del cementerio ni numeración oficial.
- No mostrar el CI ni datos sensibles en la interfaz pública.
- No eliminar información histórica; no borrar físicamente registros importantes (se usa `activo`).
- No implementar funcionalidades no contempladas en este documento sin consultarlo antes.
- Antes de cambios importantes, explicar brevemente qué se va a modificar.
- Al terminar una tarea, indicar: archivos modificados, qué se hizo, cómo probarlo.
- Ante un error, investigar la causa antes de cambiar varias cosas al azar.
- Priorizar código explicable por un estudiante Junior; evitar código "mágico" o excesivamente abreviado.
- No optimizar prematuramente: primero correcto, claro y funcional.

---

## 12. Historial de decisiones y aclaraciones tomadas en esta sesión

Este apartado documenta las inconsistencias detectadas en el documento original de requisitos y cómo fueron resueltas, para que quede trazabilidad de las decisiones:

1. **Versiones de stack:** se usará la última versión estable disponible al momento de implementar, no una versión fija predefinida.
2. **Creación de ubicaciones:** toda la estructura física (bloques y ubicaciones) se carga una única vez por seeder; nunca se crean nuevas desde la interfaz.
3. **Gestión de bloques:** no existe interfaz para bloques; se cargan y mantienen únicamente por seeder.
4. **Gestión de usuarios:** se agregó un CRUD completo (no solo activar/desactivar), para poder cumplir con lo pedido en el módulo de Historial.
5. **Campo `estado` de ubicaciones:** se eliminó como columna física; se calcula automáticamente (Disponible / Ocupado / Mantenimiento), sin el estado "Reservado".
6. **Campo `capacidad`:** autogenerado según tipo, no editable.
7. **Unicidad de ubicación física:** Bloque+Lado+Columna+Fila+Tipo+Número es único siempre, sin excepción para mausoleos.
8. **Regla física de distribución:** Norte/Sur = Nichos, Este/Oeste = Mausoleos (validado en seeder, no en base de datos).
9. **Mantenimiento tras exhumación:** periodo fijo de 14 días, calculado automáticamente desde `fecha_salida`.
10. **Campo `activo` en `difuntos`:** se agregó, ya que el Historial requiere poder registrar la acción "Desactivar un difunto".
11. **Vínculo Agenda–Ubicación:** se agregó el campo `ubicacion_id` (nullable) a `agendas`, necesario para automatizar la asociación/retiro de difuntos según el tipo de actividad realizada.
12. **Automatismo de agenda:** al marcar una actividad como "Realizado", el sistema actualiza automáticamente `ubicacion_difunto` según el tipo (Inhumación, Exhumación, Anexión siempre; Cremación solo si se indica una ubicación).
13. **Autenticación:** se usará Laravel Breeze (Blade + Tailwind) como scaffolding de login.
14. **Recuperación de contraseña:** manual, por el Administrador, sin flujo automático por correo.

---

## 13. Próximo paso

Una vez validado y aprobado este documento, el siguiente paso será elaborar el **plan de migraciones y modelos Eloquent** (orden de creación según dependencias de llaves foráneas, nombres exactos de archivos y relaciones), sin escribir todavía código de controladores ni vistas.

**Este documento no incluye código, migraciones ni archivos de configuración — es exclusivamente la especificación validada para iniciar el desarrollo.**
