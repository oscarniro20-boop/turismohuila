@extends('layouts.app')

@section('title', __('ui.contact_page.title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">{{ __('ui.contact_page.title') }}</h1>

            @if(session('exito'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('exito') ?: __('ui.contact_page.success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <form method="POST" action="{{ route('contacto.enviar') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">{{ __('ui.contact_page.name') }}</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('ui.contact_page.email') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="asunto" class="form-label">{{ __('ui.contact_page.subject') }}</label>
                                    <input type="text" class="form-control @error('asunto') is-invalid @enderror" id="asunto" name="asunto" value="{{ old('asunto') }}" required>
                                    @error('asunto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="mensaje" class="form-label">{{ __('ui.contact_page.message') }}</label>
                                    <textarea class="form-control @error('mensaje') is-invalid @enderror" id="mensaje" name="mensaje" rows="5" required>{{ old('mensaje') }}</textarea>
                                    @error('mensaje')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success">{{ __('ui.contact_page.send') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('ui.contact_page.info') }}</h5>
                            <p class="text-muted mb-2">📍 Huila, Colombia</p>
                            <p class="text-muted mb-2">📧 info@turismohuila.com</p>
                            <p class="text-muted mb-2">📞 +57 300 123 4567</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection