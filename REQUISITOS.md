# REQUISITOS.md

# Plataforma Web para la Gestión del Cementerio General de Sacaba

## 1. Objetivo

### Objetivo general

Desarrollar una plataforma web para la gestión administrativa del Cementerio General de Sacaba mediante mapas interactivos y servicios digitales, optimizando los procesos de consulta, control y asignación de espacios funerarios.

### Objetivos específicos

1. Implementar los módulos administrativos para la gestión de registros, inhumaciones, exhumaciones y servicios funerarios.
2. Implementar un módulo de mapas interactivos que facilite la localización y consulta de espacios funerarios mediante información georreferenciada.

## 2. Tecnologías

- Laravel, última versión estable disponible al iniciar.
- PHP, versión estable compatible.
- PostgreSQL.
- Laravel Sail.
- Docker Desktop.
- Docker Compose.
- Composer 2.x.
- Node.js 22 LTS.
- NPM 10.x.
- Blade.
- **Bootstrap 5.**
- **AdminLTE 3.x para el panel administrativo.**
- JavaScript ES6+ básico.
- Leaflet 1.9.x.
- OpenStreetMap.
- Laravel Breeze.
- Git 2.x.

### Criterio de desarrollo

El código debe ser sencillo, claro y fácil de entender. Se prioriza Laravel convencional y MVC sobre arquitecturas complejas.

No utilizar Repository Pattern, Service Layer innecesaria, DTOs, microservicios ni una API independiente.

No usar Tailwind CSS para el panel administrativo; el panel se implementará con AdminLTE + Bootstrap 5.

## 3. Usuarios y permisos

### Administrador

Puede:

- gestionar usuarios;
- gestionar panteoneros;
- gestionar difuntos;
- gestionar agenda;
- consultar y editar los campos permitidos del mapa;
- consultar historial;
- corregir datos con trazabilidad;
- consultar estados internos.

### Personal administrativo

Puede:

- gestionar difuntos;
- gestionar agenda;
- consultar el mapa administrativo.

No puede:

- gestionar usuarios;
- gestionar panteoneros;
- consultar historial;
- editar las ubicaciones del mapa.

### Visitante/familiar

No tiene cuenta.

Puede:

- consultar información básica;
- buscar difuntos;
- consultar el mapa público;
- consultar la ubicación pública de un difunto.

### Panteonero

No inicia sesión.

Es un registro operativo que puede asignarse a actividades de agenda.

## 4. Requisitos funcionales

### RF01 — Autenticación

El sistema debe permitir iniciar y cerrar sesión mediante Laravel Breeze.

### RF02 — Gestión de usuarios

El Administrador debe poder crear, editar, activar y desactivar usuarios.

No existe registro público de usuarios.

El restablecimiento de contraseña será manual por el Administrador.

### RF03 — Gestión de difuntos

El sistema debe permitir:

- registrar difuntos;
- editar difuntos;
- consultar difuntos;
- buscar por código;
- buscar por CI;
- buscar por nombre;
- buscar por apellidos;
- consultar su ubicación actual;
- consultar información relacionada con agenda;
- activar/desactivar difuntos.

El CI es exclusivamente para uso interno.

### RF04 — Gestión de panteoneros

El Administrador debe poder:

- registrar;
- editar;
- consultar;
- activar;
- desactivar panteoneros.

### RF05 — Mapa y ubicaciones

El sistema debe mostrar un mapa interactivo mediante Leaflet + OpenStreetMap.

Debe permitir consultar:

- bloque;
- lado;
- columna;
- fila;
- tipo;
- número;
- capacidad;
- estado calculado.

El Administrador puede modificar solamente la información geográfica permitida.

### RF06 — Agenda

El sistema debe permitir crear y programar:

- Inhumación;
- Exhumación;
- Cremación;
- Anexión.

Debe permitir registrar:

- difunto;
- ubicación cuando corresponda;
- panteonero;
- fecha;
- hora;
- observaciones;
- estado.

Estados:

- Pendiente;
- Realizado;
- Cancelado.

Debe existir vista de calendario por día, semana y mes.

### RF07 — Automatismos de agenda

Al marcar una actividad como Realizado:

**Inhumación**
- requiere ubicación;
- crea asociación activa en `ubicacion_difunto`.

**Exhumación**
- requiere ubicación;
- cierra la asociación activa;
- registra `fecha_salida`.

**Anexión**
- requiere ubicación;
- crea una nueva asociación activa.

**Cremación**
- ubicación opcional;
- si existe ubicación, crea asociación;
- si no existe, no crea asociación.

### RF08 — Historial

El sistema debe registrar cambios importantes:

- usuario;
- fecha/hora;
- tabla;
- registro afectado;
- acción;
- datos anteriores;
- datos nuevos;
- descripción.

Debe registrar operaciones de creación, edición, activación, desactivación, asociación, retiro y cancelación según corresponda.

No registrar búsquedas, filtros, paginación, apertura de pantallas ni clics sin modificación de información.

Solo el Administrador puede consultar el historial.

### RF09 — Dashboard

Después del login debe mostrarse un dashboard con:

- total de difuntos;
- total de ubicaciones;
- ubicaciones ocupadas;
- ubicaciones disponibles;
- actividades programadas para hoy;
- actividades pendientes.

No agregar gráficos innecesarios.

### RF10 — Interfaz pública

Debe incluir:

- inicio;
- información del cementerio;
- buscador de difuntos;
- mapa público;
- ficha pública.

La ficha pública muestra únicamente nombre y ubicación.

### RF11 — Cálculo de estado

El estado no se almacena en una columna.

Se calcula:

- Ocupado: existe asociación activa.
- Mantenimiento: última asociación inactiva con fecha de salida menor a 14 días.
- Disponible: cualquier otro caso.

No existe Reservado.

## 5. Requisitos no funcionales

### RNF01 — Usabilidad

Las interfaces deben ser claras y sencillas para personal administrativo.

### RNF02 — Legibilidad del código

El código debe ser fácil de comprender y explicar por un estudiante.

### RNF03 — Mantenibilidad

Usar Laravel convencional, MVC y Eloquent.

### RNF04 — Responsive

El panel y la interfaz pública deben adaptarse a escritorio, tablet y móvil.

### RNF05 — Seguridad

Los datos internos no deben exponerse públicamente.

El CI y los datos privados de los difuntos no deben aparecer en la interfaz pública.

### RNF06 — Integridad de datos

Las relaciones y reglas definidas deben validarse antes de guardar información.

### RNF07 — Trazabilidad

Los cambios importantes deben conservarse en el historial.

### RNF08 — No borrado físico

Los registros importantes deben desactivarse mediante `activo` en lugar de eliminarse físicamente.

### RNF09 — Tecnologías definidas

No agregar paquetes o tecnologías adicionales sin aprobación previa.

## 6. Estructura del panel administrativo

Menú:

- Dashboard
- Difuntos
- Agenda
- Mapa
- Historial
- Panteoneros
- Usuarios
- Cerrar sesión

`Usuarios`, `Panteoneros` e `Historial` son visibles únicamente para Administrador.

## 7. Diseño visual

Usar AdminLTE + Bootstrap 5.

Características:

- institucional;
- limpio;
- responsive;
- sidebar;
- tablas;
- formularios;
- mensajes de éxito/error;
- estados visuales claros;
- pocas animaciones.

Paleta:

- verde oscuro;
- verde claro;
- blanco;
- gris claro;
- gris oscuro;
- rojo para alertas/acciones destructivas;
- amarillo para pendientes;
- verde para disponibles/completados.

## 8. Alcance incluido

- autenticación;
- usuarios;
- difuntos;
- ubicaciones existentes;
- cálculo de estados;
- panteoneros;
- agenda;
- historial;
- dashboard;
- mapa;
- búsqueda pública;
- ficha pública;
- ubicación/ruta interna.

## 9. Fuera de alcance

- pagos;
- caja;
- contabilidad;
- facturación;
- gestión financiera;
- carga de documentos;
- cuentas de visitantes;
- creación libre de bloques;
- creación libre de nichos;
- bloque municipal aún no construido;
- disponibilidad pública;
- CI público;
- fecha de fallecimiento pública;
- historial público.
