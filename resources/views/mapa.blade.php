@extends('layouts.app')
@section('title', 'Mapa - TurismoHuila')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<div class="container py-4">
    <h1 class="h3 mb-3" style="font-family:'Fraunces',serif;">🗺️ Mapa de Servicios Turísticos</h1>
    <div id="mapa-huila" style="height: 500px; border-radius: 1rem;"></div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const puntos = @json($lugaresMapa);

const colores = {
    lugar: '#2E7D32',
    hotel: '#1565C0',
    restaurante: '#C62828',
    tour: '#EF6C00',
    guia: '#6A1B9A',
    agencia: '#00838F',
};

function crearPin(color, emoji) {
    const svg = `<svg width="34" height="44" viewBox="0 0 34 44" xmlns="http://www.w3.org/2000/svg">
        <path d="M17 0C7.6 0 0 7.6 0 17c0 12.4 17 27 17 27s17-14.6 17-27C34 7.6 26.4 0 17 0z" fill="${color}"/>
        <circle cx="17" cy="16" r="11" fill="white"/>
        <text x="17" y="21" font-size="14" text-anchor="middle">${emoji}</text>
    </svg>`;
    return L.divIcon({
        html: svg,
        className: '',
        iconSize: [34, 44],
        iconAnchor: [17, 44],
        popupAnchor: [0, -40],
    });
}

function escaparHTML(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

const centro = puntos.length
    ? [puntos.reduce((s,p)=>s+p.lat,0)/puntos.length, puntos.reduce((s,p)=>s+p.lng,0)/puntos.length]
    : [2.5359, -75.5277];

const mapa = L.map('mapa-huila').setView(centro, 9);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(mapa);

puntos.forEach(p => {
    L.marker([p.lat, p.lng], { icon: crearPin(colores[p.tipo] || '#555', p.icono) })
        .addTo(mapa)
        .bindPopup(`<strong>${escaparHTML(p.nombre)}</strong><br>${escaparHTML(p.municipio)}<br><a href="${p.url}">Ver más</a>`);
});
</script>
@endsection
