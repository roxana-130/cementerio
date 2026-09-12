@props(['estado'])

@php
    $clase = match ($estado) {
        'Pendiente' => 'bg-warning text-dark',
        'Realizado' => 'bg-success',
        'Cancelado' => 'bg-danger',
        default     => 'bg-secondary',
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $clase]) }}>{{ $estado }}</span>
