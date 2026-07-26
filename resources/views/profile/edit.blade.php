@extends('layouts.app')

@section('title', 'Mi Perfil - TurismoHuila')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Mi Perfil</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">Información del Perfil</div>
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">Cambiar Contraseña</div>
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="card shadow-sm border-danger mb-4">
        <div class="card-header bg-danger text-white">Eliminar Cuenta</div>
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection