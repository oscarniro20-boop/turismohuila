@extends('layouts.app')

@section('title', 'Mapa - TurismoHuila')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container py-4">
    <div class="mb-4">
        <h1 class="fw-bold" style="color: var(--color-primary);">Mapa de Lugares Turísticos</h1>
        <p class="text-muted">Ubicación de los lugares turísticos del Huila registrados con coordenadas.</p>
    </div>

    @if($lugaresMapa->isEmpty())
        <div class="alert alert-warning">
            Todavía no hay lugares con coordenadas registradas. Agrega latitud y longitud al crear o editar un lugar para que aparezca aquí.
        </div>
    @else
        <div id="mapa-huila" style="height: 560px; border-radius: 12px;"></div>
    @endif
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lugares = @json($lugaresMapa);

    if (lugares.length) {
        const centro = [
            lugares.reduce((s, l) => s + l.lat, 0) / lugares.length,
            lugares.reduce((s, l) => s + l.lng, 0) / lugares.length,
        ];

        const mapa = L.map('mapa-huila').setView(centro, 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapa);

        lugares.forEach(function (l) {
            L.marker([l.lat, l.lng]).addTo(mapa)
                .bindPopup('<strong>' + l.nombre + '</strong><br>' + l.municipio + '<br><a href="' + l.url + '">Ver más</a>');
        });
    }
</script>
@endsection