<x-guest-layout>
    <div class="text-center mb-4">
        <h5 class="fw-bold text-dark mb-1">Iniciar Sesión</h5>
        <p class="text-muted small">Ingrese sus credenciales para acceder al panel administrativo</p>
    </div>

    {{-- Estado de Sesión / Mensaje de Alerta --}}
    @if (session('status'))
        <div class="alert alert-info alert-dismissible fade show border-0 small" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Errores de Validación --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 small mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Correo Electrónico --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-medium text-dark small">Correo electrónico</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@cementerio.test">
            </div>
        </div>

        {{-- Contraseña --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-medium text-dark small">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="password" required autocomplete="current-password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
            </div>
        </div>

        {{-- Recordarme --}}
        <div class="form-check mb-4">
            <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
            <label for="remember_me" class="form-check-label text-muted small">Recordar sesión en este equipo</label>
        </div>

        {{-- Botón de Ingreso --}}
        <div class="d-grid">
            <button type="submit" class="btn btn-sacaba shadow-sm rounded-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Acceder al Sistema
            </button>
        </div>
    </form>
</x-guest-layout>
