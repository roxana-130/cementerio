@props(['tipo'])

@php
    $info = match ($tipo) {
        'Inhumacion' => ['icono' => 'bi-arrow-down-circle', 'texto' => 'Inhumación'],
        'Exhumacion' => ['icono' => 'bi-arrow-up-circle', 'texto' => 'Exhumación'],
        'Cremacion'  => ['icono' => 'bi-fire', 'texto' => 'Cremación'],
        'Anexion'    => ['icono' => 'bi-plus-circle', 'texto' => 'Anexión'],
        default      => ['icono' => 'bi-calendar-event', 'texto' => $tipo],
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge bg-success-subtle text-success-emphasis border border-success-subtle']) }}>
    <i class="bi {{ $info['icono'] }} me-1"></i>{{ $info['texto'] }}
</span>
