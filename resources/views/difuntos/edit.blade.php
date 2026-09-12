<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('difuntos.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-1 px-2" title="Volver al listado">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0 fw-bold text-dark">Editar Información del Difunto</h1>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0">Formulario de Edición</h5>
                    <p class="text-muted small mb-0">Modifique los datos del difunto <strong>{{ $difunto->codigo }}</strong> ({{ $difunto->nombre }} {{ $difunto->apellido_paterno }}).</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('difuntos.update', $difunto) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- Sección: Datos Personales --}}
                            <div class="col-12">
                                <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                                    <i class="bi bi-person-badge me-1"></i>Datos Personales
                                </h6>
                            </div>

                            {{-- Código --}}
                            <div class="col-md-4">
                                <label for="codigo" class="form-label fw-medium text-dark">
                                    Código
                                </label>
                                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $difunto->codigo) }}"
                                    class="form-control font-monospace @error('codigo') is-invalid @enderror"
                                    placeholder="Ej. DIF-0001">
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CI --}}
                            <div class="col-md-4">
                                <label for="ci" class="form-label fw-medium text-dark">
                                    Carnet de Identidad (CI) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="ci" id="ci" value="{{ old('ci', $difunto->ci) }}" required
                                    class="form-control @error('ci') is-invalid @enderror"
                                    placeholder="Ej. 1234567 CB">
                                @error('ci')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nombre --}}
                            <div class="col-md-4">
                                <label for="nombre" class="form-label fw-medium text-dark">
                                    Nombre(s) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $difunto->nombre) }}" required
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    placeholder="Ej. Juan Carlos">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Apellido Paterno --}}
                            <div class="col-md-4">
                                <label for="apellido_paterno" class="form-label fw-medium text-dark">
                                    Apellido Paterno <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno', $difunto->apellido_paterno) }}" required
                                    class="form-control @error('apellido_paterno') is-invalid @enderror"
                                    placeholder="Ej. Pérez">
                                @error('apellido_paterno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Apellido Materno --}}
                            <div class="col-md-4">
                                <label for="apellido_materno" class="form-label fw-medium text-dark">
                                    Apellido Materno <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno', $difunto->apellido_materno) }}" required
                                    class="form-control @error('apellido_materno') is-invalid @enderror"
                                    placeholder="Ej. Gómez">
                                @error('apellido_materno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Apellido de Casada --}}
                            <div class="col-md-4">
                                <label for="apellido_casada" class="form-label fw-medium text-dark">
                                    Apellido de Casada <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" name="apellido_casada" id="apellido_casada" value="{{ old('apellido_casada', $difunto->apellido_casada) }}"
                                    class="form-control @error('apellido_casada') is-invalid @enderror"
                                    placeholder="Ej. de Mendoza">
                                @error('apellido_casada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Edad --}}
                            <div class="col-md-4">
                                <label for="edad" class="form-label fw-medium text-dark">
                                    Edad al fallecer <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="number" name="edad" id="edad" value="{{ old('edad', $difunto->edad) }}" min="0" max="120"
                                    class="form-control @error('edad') is-invalid @enderror"
                                    placeholder="Ej. 75">
                                @error('edad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Profesión / Ocupación --}}
                            <div class="col-md-8">
                                <label for="profesion_ocupacion" class="form-label fw-medium text-dark">
                                    Profesión u Ocupación <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" name="profesion_ocupacion" id="profesion_ocupacion" value="{{ old('profesion_ocupacion', $difunto->profesion_ocupacion) }}"
                                    class="form-control @error('profesion_ocupacion') is-invalid @enderror"
                                    placeholder="Ej. Agricultor, Profesor, Comerciante...">
                                @error('profesion_ocupacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Sección: Datos de Defunción --}}
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                                    <i class="bi bi-journal-medical me-1"></i>Datos de Defunción
                                </h6>
                            </div>

                            {{-- Fecha de Fallecimiento --}}
                            <div class="col-md-4">
                                <label for="fecha_fallecimiento" class="form-label fw-medium text-dark">
                                    Fecha de Fallecimiento <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha_fallecimiento" id="fecha_fallecimiento" 
                                    value="{{ old('fecha_fallecimiento', $difunto->fecha_fallecimiento ? $difunto->fecha_fallecimiento->format('Y-m-d') : '') }}" 
                                    max="{{ date('Y-m-d') }}" required
                                    class="form-control @error('fecha_fallecimiento') is-invalid @enderror">
                                @error('fecha_fallecimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hora de Fallecimiento --}}
                            <div class="col-md-4">
                                <label for="hora_fallecimiento" class="form-label fw-medium text-dark">
                                    Hora de Fallecimiento <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="time" name="hora_fallecimiento" id="hora_fallecimiento" 
                                    value="{{ old('hora_fallecimiento', $difunto->hora_fallecimiento ? \Illuminate\Support\Carbon::parse($difunto->hora_fallecimiento)->format('H:i') : '') }}"
                                    class="form-control @error('hora_fallecimiento') is-invalid @enderror">
                                @error('hora_fallecimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Número Certificado de Defunción --}}
                            <div class="col-md-4">
                                <label for="numero_certificado_defuncion" class="form-label fw-medium text-dark">
                                    N° Certificado Defunción <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" name="numero_certificado_defuncion" id="numero_certificado_defuncion" 
                                    value="{{ old('numero_certificado_defuncion', $difunto->numero_certificado_defuncion) }}"
                                    class="form-control @error('numero_certificado_defuncion') is-invalid @enderror"
                                    placeholder="Ej. CERT-98765">
                                @error('numero_certificado_defuncion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Causa de Muerte --}}
                            <div class="col-12">
                                <label for="causa_muerte" class="form-label fw-medium text-dark">
                                    Causa de Muerte <span class="text-muted">(Opcional)</span>
                                </label>
                                <textarea name="causa_muerte" id="causa_muerte" rows="3"
                                    class="form-control @error('causa_muerte') is-invalid @enderror"
                                    placeholder="Descripción o causa médica según certificado de defunción...">{{ old('causa_muerte', $difunto->causa_muerte) }}</textarea>
                                @error('causa_muerte')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                            <a href="{{ route('difuntos.index') }}" class="btn btn-light border px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i>Actualizar Difunto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
