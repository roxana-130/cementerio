# CLAUDE.md

## 1. Propósito del proyecto

Este proyecto es una plataforma web para la gestión administrativa del Cementerio General de Sacaba mediante mapas interactivos y servicios digitales.

El sistema debe facilitar la gestión de difuntos, inhumaciones, exhumaciones, cremaciones, anexiones, panteoneros, agenda, ubicaciones e historial, además de ofrecer una interfaz pública para la consulta de difuntos y ubicación.

## 2. Stack tecnológico obligatorio

- Laravel: última versión estable disponible al iniciar el desarrollo.
- PHP: última versión estable compatible con Laravel.
- PostgreSQL: última versión estable disponible.
- Laravel Sail.
- Docker Desktop.
- Docker Compose.
- Composer 2.x.
- Node.js 22 LTS.
- NPM 10.x.
- Blade.
- Bootstrap 5.
- AdminLTE 4.x como plantilla del panel administrativo.
- JavaScript ES6+ básico.
- Leaflet 1.9.x + OpenStreetMap para mapas.
- Laravel Breeze para autenticación.
- Git 2.x.

### Importante sobre la interfaz

El panel administrativo debe construirse con **AdminLTE + Bootstrap 5**, no con Tailwind CSS.

No agregar frameworks JavaScript adicionales. Usar JavaScript básico y, únicamente cuando sea realmente necesario y aprobado, herramientas ligeras compatibles con la solución.

## 3. Principio fundamental: código fácil de entender

Todo el proyecto debe desarrollarse con código claro, sencillo y fácil de explicar en una defensa de grado.

Prioridades:

1. Código funcional.
2. Código legible.
3. Código fácil de mantener.
4. Código fácil de explicar.
5. Simplicidad antes que sofisticación.

Usar Laravel convencional y MVC.

Preferir:

- Modelos Eloquent simples.
- Controladores claros.
- Migraciones fáciles de leer.
- Relaciones Eloquent básicas.
- Validaciones claras.
- Blade y Bootstrap 5.
- Componentes Blade solo cuando realmente mejoren la reutilización.
- Form Requests cuando aporten claridad real.
- Nombres descriptivos.

No usar:

- Repository Pattern.
- Service Layer innecesaria.
- DTOs.
- Microservicios.
- API independiente.
- Arquitecturas excesivamente complejas.
- Abstracciones innecesarias.
- Código excesivamente abreviado o “mágico”.

Ante dos soluciones válidas, elegir la más sencilla de explicar.

## 4. Reglas de trabajo para Claude

Antes de realizar cambios importantes:

1. Revisar este archivo y los documentos de contexto relacionados.
2. Revisar el código existente.
3. Explicar brevemente qué se va a modificar y por qué.
4. No modificar varias funcionalidades importantes al mismo tiempo.
5. No instalar paquetes adicionales sin aprobación previa.
6. No cambiar la estructura de la base de datos sin explicar primero el cambio.
7. No inventar requisitos, datos físicos ni numeración del cementerio.
8. No eliminar información histórica.
9. Usar `activo` para desactivar registros importantes en lugar de borrarlos físicamente.
10. Después de completar una tarea, indicar:
   - archivos creados o modificados;
   - qué se hizo;
   - cómo probarlo;
   - cualquier pendiente o advertencia.

Ante un error, investigar primero la causa y evitar cambios aleatorios en múltiples archivos.

## 5. Usuarios y acceso

Existen dos usuarios administrativos:

- `administrador`: acceso completo.
- `personal`: acceso administrativo limitado.

El visitante no inicia sesión y utiliza la interfaz pública.

El panteonero no es un usuario del sistema: es un registro operativo que puede ser asignado a actividades.

No existe registro público de usuarios.

No existe recuperación automática de contraseña por correo. El Administrador restablece manualmente las contraseñas desde el CRUD de usuarios.

## 6. Reglas físicas del cementerio

La estructura física no utiliza sectores.

Una ubicación se identifica por:

**Bloque + Lado + Columna + Fila + Tipo + Número**

Lados válidos:

- Norte
- Sur
- Este
- Oeste

Tipos:

- Nicho
- Mausoleo

Distribución:

- Norte y Sur: únicamente Nichos.
- Este y Oeste: únicamente Mausoleos.

Capacidad:

- Nicho: 1.
- Mausoleo: 5.

La estructura física real se carga mediante seeders. No se deben inventar números.

Los bloques y ubicaciones existentes no se crean, editan ni eliminan libremente desde la interfaz.

## 7. Estado de las ubicaciones

No guardar un campo físico `estado`.

El estado se calcula al consultar la ubicación:

- Si existe un registro activo en `ubicacion_difunto`: `Ocupado`.
- Si no existe registro activo y el último registro inactivo tiene `fecha_salida` de menos de 14 días atrás: `Mantenimiento`.
- En cualquier otro caso: `Disponible`.

No existe estado `Reservado`.

## 8. Módulos

El sistema debe contemplar:

1. Autenticación y usuarios.
2. Gestión de difuntos.
3. Mapa y ubicaciones.
4. Panteoneros.
5. Agenda.
6. Historial.
7. Dashboard.
8. Interfaz pública.

## 9. Información pública

La interfaz pública debe permitir:

- inicio;
- información básica del cementerio;
- búsqueda de difuntos;
- mapa público;
- ficha pública de ubicación.

La ficha pública muestra solamente:

- nombre;
- ubicación: bloque, lado, columna, fila, tipo y número.

Nunca mostrar públicamente:

- CI;
- fecha de fallecimiento;
- causa de muerte;
- disponibilidad de la ubicación;
- historial.

## 10. Mapa

Usar Leaflet + OpenStreetMap.

Distinguir claramente entre:

- mapa general/geográfico del cementerio;
- representación interactiva de las ubicaciones internas.

La gestión administrativa de la geometría puede editar únicamente:

- `geom_geojson`;
- `centro_lat`;
- `centro_lng`;
- `activo`.

No permitir modificar desde la interfaz:

- bloque;
- lado;
- columna;
- fila;
- tipo;
- número;
- capacidad.

## 11. Agenda

Tipos permitidos:

- Inhumación.
- Exhumación.
- Cremación.
- Anexión.

Estados:

- Pendiente.
- Realizado.
- Cancelado.

No agregar `Traslado` ni `En proceso`.

Al marcar una actividad como Realizado:

- Inhumación: crea asociación activa entre difunto y ubicación.
- Exhumación: cierra la asociación activa y registra fecha de salida.
- Anexión: crea una asociación nueva, pudiendo existir otros registros activos en un mausoleo.
- Cremación: si tiene ubicación, crea asociación; si no tiene ubicación, no crea asociación.

La ubicación es obligatoria para Inhumación, Exhumación y Anexión, y opcional para Cremación.

## 12. Base de datos

Usar PostgreSQL y Eloquent.

Tablas principales:

- users
- panteoneros
- bloques
- ubicaciones
- ubicacion_difunto
- difuntos
- agendas
- historial

Mantener las relaciones y restricciones definidas en `REQUISITOS.md`.

## 13. Diseño visual

El panel administrativo debe usar AdminLTE + Bootstrap 5.

Características:

- diseño institucional;
- limpio;
- responsive;
- navegación lateral;
- tablas claras;
- formularios sencillos;
- alertas y estados visibles;
- sin animaciones excesivas.

Paleta institucional:

- verde oscuro como principal;
- verde claro como apoyo;
- blanco;
- gris claro;
- gris oscuro;
- rojo para acciones destructivas/alertas;
- amarillo para pendientes;
- verde para disponibles/completados.

## 14. Alcance

Incluido:

- autenticación administrativa;
- gestión de difuntos;
- ubicaciones existentes;
- estados calculados;
- panteoneros;
- agenda;
- historial;
- mapa interactivo;
- búsqueda pública;
- ubicación y ruta interna.

Excluido:

- pagos;
- caja;
- contabilidad;
- facturación;
- gestión financiera;
- carga de documentos;
- cuentas de visitantes;
- creación libre de bloques/nichos desde la app;
- bloque municipal aún no construido;
- disponibilidad pública;
- CI público;
- fecha de fallecimiento pública;
- historial público.

## 15. Forma de trabajar

Trabajar por fases pequeñas.

No intentar desarrollar todo el sistema en una sola tarea.

Orden recomendado:

1. Analizar el proyecto existente.
2. Revisar/configurar Laravel, Sail, Docker y PostgreSQL.
3. Planificar migraciones y modelos.
4. Implementar migraciones y modelos.
5. Configurar autenticación y roles.
6. Implementar dashboard.
7. Implementar difuntos.
8. Implementar panteoneros.
9. Implementar mapa.
10. Implementar agenda y automatismos.
11. Implementar historial.
12. Implementar interfaz pública.
13. Realizar pruebas y correcciones.

Cada fase debe terminar funcionando antes de pasar a la siguiente.

## 16. Regla de oro

No inventar.

Si una decisión no está definida en `REQUISITOS.md` o `MAPA.md`, informar que está pendiente y pedir autorización antes de convertirla en una regla del sistema.
