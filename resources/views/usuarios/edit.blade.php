<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-1 px-2" title="Volver">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0 fw-bold text-dark">Editar Usuario: {{ $usuario->name }}</h1>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0">Modificar Datos del Usuario</h5>
                    <p class="text-muted small mb-0">Actualice la información general del usuario o restablezca su contraseña manualmente.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nombre completo --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium text-dark">
                                Nombre completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" required
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Correo electrónico --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium text-dark">
                                Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" required
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Restablecer contraseña (opcional) --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium text-dark">
                                Restablecer contraseña <span class="text-muted font-normal fw-normal">(Opcional)</span>
                            </label>
                            <input type="password" name="password" id="password" minlength="8"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Dejar en blanco si no se desea modificar">
                            <div class="form-text">Proporcione una nueva contraseña solo si desea realizar un reseteo manual.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Rol de acceso --}}
                        <div class="mb-4">
                            <label for="rol" class="form-label fw-medium text-dark">
                                Rol de acceso <span class="text-danger">*</span>
                            </label>
                            <select name="rol" id="rol" required class="form-select @error('rol') is-invalid @enderror">
                                <option value="personal" {{ old('rol', $usuario->rol) === 'personal' ? 'selected' : '' }}>Personal administrativo</option>
                                <option value="administrador" {{ old('rol', $usuario->rol) === 'administrador' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-light border px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-success px-4 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i>Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
