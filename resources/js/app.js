import * as bootstrap from 'bootstrap';
import 'admin-lte';

// Expuesto como global para las vistas Blade que instancian componentes de
// Bootstrap por JS (ej. `new bootstrap.Modal(...)` en el mapa administrativo).
window.bootstrap = bootstrap;
