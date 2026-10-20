@extends('layouts.app')

@section('title', __('ui.legal.privacy_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">{{ __('ui.legal.privacy_title') }}</h1>

            <p class="text-muted mb-4">{{ __('ui.legal.last_update', ['date' => date('Y-m-d')]) }}</p>

            <h2 class="h4 mt-4 mb-3">1. {{ __('ui.legal.privacy_collect') }}</h2>
            <p>{{ __('ui.legal.privacy_intro') }}</p>

            <h2 class="h4 mt-4 mb-3">2. {{ __('ui.legal.privacy_use') }}</h2>
            <p>{{ __('ui.legal.privacy_use_body') }}</p>

            <h2 class="h4 mt-4 mb-3">3. {{ __('ui.legal.privacy_security') }}</h2>
            <p>{{ __('ui.legal.privacy_security_body') }}</p>

            <h2 class="h4 mt-4 mb-3">4. {{ __('ui.legal.cookies') }}</h2>
            <p>{{ __('ui.legal.cookies_body') }}</p>

            <h2 class="h4 mt-4 mb-3">5. {{ __('ui.legal.rights') }}</h2>
            <p>{{ __('ui.legal.rights_body') }}</p>

            <h2 class="h4 mt-4 mb-3">6. {{ __('ui.legal.contact') }}</h2>
            <p>{{ __('ui.legal.contact_body') }}</p>
        </div>
    </div>
</div>
@endsection