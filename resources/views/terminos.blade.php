@extends('layouts.app')

@section('title', __('ui.legal.terms_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">{{ __('ui.legal.terms_title') }}</h1>

            <p class="text-muted mb-4">{{ __('ui.legal.last_update', ['date' => date('Y-m-d')]) }}</p>

            <h2 class="h4 mt-4 mb-3">1. {{ __('ui.legal.terms_acceptance') }}</h2>
            <p>{{ __('ui.legal.terms_intro') }}</p>

            <h2 class="h4 mt-4 mb-3">2. {{ __('ui.legal.service_description') }}</h2>
            <p>{{ __('ui.legal.service_intro') }}</p>
            <ul>
                @foreach(explode('||', __('ui.legal.service_items')) as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
            <p>{{ __('ui.legal.service_limit') }}</p>

            <h2 class="h4 mt-4 mb-3">3. {{ __('ui.legal.user_responsibilities') }}</h2>
            <p>{{ __('ui.legal.responsibility_intro') }}</p>
            <p>{{ __('ui.legal.responsibility_limit') }}</p>

            <h2 class="h4 mt-4 mb-3">4. {{ __('ui.legal.liability') }}</h2>
            <p>{{ __('ui.legal.liability_intro') }}</p>

            <h2 class="h4 mt-4 mb-3">5. {{ __('ui.legal.ai_title') }}</h2>
            <p>{{ __('ui.legal.ai_body') }}</p>

            <h2 class="h4 mt-4 mb-3">6. {{ __('ui.legal.terms_changes') }}</h2>
            <p>{{ __('ui.legal.changes_body') }}</p>

            <h2 class="h4 mt-4 mb-3">7. {{ __('ui.legal.contact') }}</h2>
            <p>{{ __('ui.legal.contact_body') }}</p>
        </div>
    </div>
</div>
@endsection