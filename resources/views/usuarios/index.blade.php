<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 fw-bold text-dark">
                <i class="bi bi-people-fill text-success me-2"></i>Gestión de Usuarios
            </h1>
            <a href="{{ route('usuarios.create') }}" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-person-plus-fill"></i>
                <span>Registrar Usuario</span>
            </a>
        </div>
    </x-slot>

    {{-- Alertas de éxito o error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabla de Usuarios --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="card-title mb-0 fw-semibold text-dark">Listado de Usuarios Registrados</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Nombre Completo</th>
                            <th scope="col">Correo Electrónico</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr>
                                <td class="ps-4 font-medium text-dark fw-semibold">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $usuario->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $usuario->email }}</span>
                                </td>
                                <td>
                                    @if ($usuario->rol === 'administrador')
                                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2.5 py-1 text-uppercase" style="background-color: #f3e8ff; color: #6b21a8;">
                                            <i class="bi bi-shield-lock-fill me-1"></i>Administrador
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 text-uppercase">
                                            <i class="bi bi-person-badge me-1"></i>Personal
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($usuario->activo)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Editar</span>
                                        </a>

                                        @if ($usuario->id !== auth()->id())
                                            <form action="{{ route('usuarios.toggle-activo', $usuario) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $usuario->activo ? 'btn-outline-danger' : 'btn-outline-success' }} d-flex align-items-center gap-1">
                                                    <i class="bi {{ $usuario->activo ? 'bi-person-x-fill' : 'bi-person-check-fill' }}"></i>
                                                    <span>{{ $usuario->activo ? 'Desactivar' : 'Activar' }}</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1.5" title="No puedes desactivar tu propia cuenta">
                                                (Tu cuenta)
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($usuarios->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
