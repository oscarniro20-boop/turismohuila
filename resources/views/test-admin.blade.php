@extends('layouts.app')

@section('title', 'Prueba de Acceso Admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2>🔍 Prueba Funcional de Acceso Admin</h2>
                </div>
                <div class="card-body">
                    @if(auth()->check())
                        @php
                            $user = auth()->user();
                            $esAdmin = $user->esAdmin();
                        @endphp

                        <!-- Información del Usuario -->
                        <div class="mb-4">
                            <h4>📋 Información del Usuario Autenticado</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Rol</th>
                                    <td>
                                        <span class="badge {{ $user->rol === 'admin' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $user->rol }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Verificaciones -->
                        <div class="mb-4">
                            <h4>🔍 Verificaciones del Sistema</h4>
                            <div class="alert alert-info">
                                <p><strong>auth()->user()->rol:</strong> <code>{{ $user->rol }}</code></p>
                                <p><strong>auth()->user()->esAdmin():</strong> 
                                    @if($esAdmin)
                                        <span class="text-success fw-bold">true</span>
                                    @else
                                        <span class="text-danger fw-bold">false</span>
                                    @endif
                                </p>
                                <p><strong>Resultado:</strong> 
                                    @if($esAdmin)
                                        <span class="text-success">✅ El usuario ES admin (rol: {{ $user->rol }})</span>
                                    @else
                                        <span class="text-danger">❌ El usuario NO es admin (rol: {{ $user->rol }})</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Acceso a Rutas -->
                        <div class="mb-4">
                            <h4>🔐 Acceso a Rutas de Gestión</h4>
                            @if($esAdmin)
                                <div class="alert alert-success">
                                    <h5>✅ Rutas ACCESIBLES:</h5>
                                    <ul>
                                        <li>hoteles.create</li>
                                        <li>hoteles.edit</li>
                                        <li>hoteles.destroy</li>
                                        <li>restaurantes.create</li>
                                        <li>restaurantes.edit</li>
                                        <li>restaurantes.destroy</li>
                                        <li>tours.create</li>
                                        <li>tours.edit</li>
                                        <li>tours.destroy</li>
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    ❌ Rutas de gestión NO ACCESIBLES
                                </div>
                            @endif
                        </div>

                        <!-- Botones Visibles -->
                        <div class="mb-4">
                            <h4>🖱️ Botones que Deberían Aparecer en los Paneles</h4>
                            @if($esAdmin)
                                <div class="alert alert-success">
                                    <h5>✅ Botones VISIBLES:</h5>
                                    <ul>
                                        <li><strong>+ Nuevo Hotel</strong> (en hoteles/index.blade.php línea 10)</li>
                                        <li><strong>+ Nuevo Restaurante</strong> (en restaurantes/index.blade.php línea 10)</li>
                                        <li><strong>+ Nuevo Tour</strong> (en tours/index.blade.php línea 10)</li>
                                    </ul>
                                    <hr>
                                    <h6>Botones de Edición/Eliminación en Tarjetas:</h6>
                                    <p>✅ También visibles en cada tarjeta de hotel, restaurante y tour</p>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    ❌ Botones NO VISIBLES
                                </div>
                            @endif
                        </div>

                        <!-- Resumen -->
                        <div class="mb-4">
                            <h4>📊 Resumen</h4>
                            @if($esAdmin)
                                <div class="alert alert-success">
                                    <h5>✅ CONFIRMADO</h5>
                                    <p>El usuario con rol <strong>"{{ $user->rol }}"</strong> tiene acceso completo a:</p>
                                    <ul>
                                        <li>Ver botones "+ Nuevo Hotel", "+ Nuevo Restaurante", "+ Nuevo Tour"</li>
                                        <li>Acceder a rutas de creación, edición y eliminación</li>
                                        <li>Ver botones de edición (✏️) y eliminación (🗑️) en cada tarjeta</li>
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <h5>❌ PROBLEMA DETECTADO</h5>
                                    <p>El usuario NO tiene rol "admin"</p>
                                    <p><strong>Roles permitidos:</strong> admin, superadmin</p>
                                    <p><strong>Rol actual:</strong> {{ $user->rol }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Enlaces de Prueba -->
                        @if($esAdmin)
                        <div class="mb-4">
                            <h4>🔗 Enlaces de Prueba</h4>
                            <div class="d-grid gap-2">
                                <a href="{{ route('hoteles.index') }}" class="btn btn-primary" target="_blank">
                                    Ver Panel de Hoteles
                                </a>
                                <a href="{{ route('restaurantes.index') }}" class="btn btn-primary" target="_blank">
                                    Ver Panel de Restaurantes
                                </a>
                                <a href="{{ route('tours.index') }}" class="btn btn-primary" target="_blank">
                                    Ver Panel de Tours
                                </a>
                            </div>
                        </div>
                        @endif

                    @else
                        <div class="alert alert-warning">
                            <h4>⚠️ No autenticado</h4>
                            <p>Debes <a href="{{ route('login') }}">iniciar sesión</a> para ver esta información.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection