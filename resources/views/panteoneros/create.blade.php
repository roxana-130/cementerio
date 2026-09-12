<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('panteoneros.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-1 px-2" title="Volver">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0 fw-bold text-dark">Registrar Nuevo Panteonero</h1>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0">Formulario de Registro de Panteonero</h5>
                    <p class="text-muted small mb-0">Complete la información operativa para dar de alta al panteonero en el sistema.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('panteoneros.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            {{-- Nombre --}}
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label fw-medium text-dark">
                                    Nombre(s) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    placeholder="Ej. Mario">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CI --}}
                            <div class="col-md-6 mb-3">
                                <label for="ci" class="form-label fw-medium text-dark">
                                    Cédula de Identidad (CI) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="ci" id="ci" value="{{ old('ci') }}" required
                                    class="form-control @error('ci') is-invalid @enderror"
                                    placeholder="Ej. 6543210">
                                @error('ci')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Apellido Paterno --}}
                            <div class="col-md-6 mb-3">
                                <label for="apellido_paterno" class="form-label fw-medium text-dark">
                                    Apellido Paterno <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno') }}" required
                                    class="form-control @error('apellido_paterno') is-invalid @enderror"
                                    placeholder="Ej. Flores">
                                @error('apellido_paterno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Apellido Materno --}}
                            <div class="col-md-6 mb-4">
                                <label for="apellido_materno" class="form-label fw-medium text-dark">
                                    Apellido Materno <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno') }}" required
                                    class="form-control @error('apellido_materno') is-invalid @enderror"
                                    placeholder="Ej. Mamani">
                                @error('apellido_materno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('panteoneros.index') }}" class="btn btn-light border px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-success px-4 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i>Guardar Panteonero
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

