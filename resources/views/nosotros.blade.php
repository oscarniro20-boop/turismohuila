@extends('layouts.app')

@section('title', __('ui.about.title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">{{ __('ui.about.title') }}</h1>
            <p class="lead text-muted mb-4">
                {{ __('ui.about.intro') }}
            </p>

            <div class="row g-4 my-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">📍 {{ __('ui.about.locality') }}</h5>
                            <p class="card-text text-muted">
                                {{ __('ui.about.locality_text') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🤝 {{ __('ui.about.trust') }}</h5>
                            <p class="card-text text-muted">
                                {{ __('ui.about.trust_text') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🔍 {{ __('ui.about.discovery') }}</h5>
                            <p class="card-text text-muted">
                                {{ __('ui.about.discovery_text') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🌱 {{ __('ui.about.sustainability') }}</h5>
                            <p class="card-text text-muted">
                                {{ __('ui.about.sustainability_text') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-success mt-4">
                <h5 class="alert-heading">{{ __('ui.about.operator_title') }}</h5>
                <p class="mb-0">
                    {{ __('ui.about.operator_text') }}
                    <a href="{{ route('contacto') }}" class="alert-link">{{ __('ui.about.write_us') }}</a>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection