@extends('layouts.app')

@section('title', 'Importación masiva CSV')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="d-block mb-1 fw-semibold small" style="color: var(--color-primary); letter-spacing: 0.05em;">ADMINISTRACIÓN</span>
            <h1 class="mb-0" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.9rem;">Importación masiva CSV</h1>
        </div>
        <a href="{{ route('admin.importacion.plantilla', $categoria) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">⬇️ Descargar plantilla CSV</a>
    </div>

    {{-- ── Categorías importables (lista permitida) ─────────────────────── --}}
    <ul class="nav flex-wrap gap-1 mb-4">
        @foreach($categorias as $clave => $label)
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.importacion.index', ['categoria' => $clave]) }}"
                   class="nav-link rounded-pill px-3 {{ $clave === $categoria ? 'active' : '' }}"
                   @if($clave === $categoria) style="background: var(--color-primary); color: #fff;" @else style="border: 1px solid var(--color-border); color: var(--color-fg);" @endif>{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    @php($resumen = session('importacion_resumen'))

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- ── Columnas reales de la categoría ───────────────────────── --}}
            <div class="rounded-4 p-4 mb-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <h2 class="h5 mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Columnas de la plantilla · {{ $definicion['label'] }}</h2>
                <p class="small mb-3" style="color: var(--color-muted-fg);">Los encabezados deben coincidir exactamente con estos nombres de columna. Las filas se validan con las mismas reglas del formulario web, incluido el catálogo geográfico oficial (DIVIPOLA).</p>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small" style="--bs-table-hover-bg: var(--color-border);">
                        <thead>
                            <tr>
                                <th scope="col">Columna</th>
                                <th scope="col">Obligatoria</th>
                                <th scope="col">Ejemplo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($definicion['columnas'] as $columna => $meta)
                                <tr>
                                    <td class="fw-semibold" style="color: var(--color-fg);">{{ $columna }}</td>
                                    <td>
                                        @if($meta['requerido'])
                                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,167,69,.16); color: var(--color-fg);">Sí</span>
                                        @else
                                            <span style="color: var(--color-muted-fg);">No</span>
                                        @endif
                                    </td>
                                    <td style="color: var(--color-muted-fg);">{{ $meta['ejemplo'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Subida del archivo ──────────────────────────────────── --}}
            <div class="rounded-4 p-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <h2 class="h5 mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Subir archivo CSV</h2>

                @if ($errors->has('archivo'))
                    <div class="alert alert-danger small py-2 mb-3" role="alert">{{ $errors->first('archivo') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.importacion.procesar') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="categoria" value="{{ $categoria }}">

                    <div class="mb-3">
                        <label for="archivo" class="form-label small fw-semibold" style="color: var(--color-fg);">
                            Archivo .csv o .txt (UTF-8 · máx. {{ round(config('importacion.max_peso_kb') / 1024) }} MB · máx. {{ config('importacion.max_filas') }} filas · separadores , ; o tabulador)
                        </label>
                        <input type="file" class="form-control" id="archivo" name="archivo" accept=".csv,.txt" required>
                    </div>

                    <button type="submit" class="btn rounded-pill px-4" style="background: var(--color-primary); color: #fff;">📤 Validar e importar</button>

                    <p class="small mt-3 mb-0" style="color: var(--color-muted-fg);">Los registros existentes no se modifican ni se borran: solo se insertan filas nuevas. Las filas inválidas se rechazan sin interrumpir la importación del resto.</p>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            {{-- ── Instrucciones ─────────────────────────────────────────── --}}
            <div class="rounded-4 p-4 mb-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <h2 class="h5 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Cómo importar</h2>
                <ol class="small mb-0 ps-3" style="color: var(--color-muted-fg);">
                    <li class="mb-2">Descarga la plantilla de la categoría seleccionada (arriba a la derecha).</li>
                    <li class="mb-2">Completa una fila por registro, sin cambiar los encabezados. Usa los nombres exactos de departamento y municipio del catálogo oficial.</li>
                    <li class="mb-2">Sube el archivo: el panel valida cada fila y muestra cuántas se leyeron, importaron y rechazaron.</li>
                </ol>
            </div>

            {{-- ── Resultado de la última importación ───────────────────── --}}
            @if ($resumen)
                <div class="rounded-4 p-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                    <h2 class="h5 mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Resultado de la última importación</h2>
                    <p class="small mb-3" style="color: var(--color-muted-fg);">
                        @if($resumen['resultado'] === 'completa')
                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,167,69,.16); color: var(--color-fg);">Importación completa</span>
                        @elseif($resumen['resultado'] === 'parcial')
                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(255,193,7,.2); color: var(--color-fg);">Importación parcial</span>
                        @else
                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(220,53,69,.18); color: var(--color-fg);">Sin registros importados</span>
                        @endif
                    </p>

                    <div class="row row-cols-2 g-3 mb-3">
                        <div class="col">
                            <div class="small" style="color: var(--color-muted-fg);">Filas leídas</div>
                            <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.6rem;">{{ $resumen['leidas'] }}</div>
                        </div>
                        <div class="col">
                            <div class="small" style="color: var(--color-muted-fg);">Importadas</div>
                            <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.6rem; color: var(--color-primary);">{{ $resumen['importadas'] }}</div>
                        </div>
                        <div class="col">
                            <div class="small" style="color: var(--color-muted-fg);">Rechazadas</div>
                            <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.6rem;">{{ $resumen['rechazadas'] }}</div>
                        </div>
                        <div class="col">
                            <div class="small" style="color: var(--color-muted-fg);">Duplicados omitidos</div>
                            <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.6rem;">{{ $resumen['duplicados'] }}</div>
                        </div>
                    </div>

                    @if (count($resumen['errores']))
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-sm align-middle mb-0 small">
                                <thead>
                                    <tr>
                                        <th scope="col">Fila</th>
                                        <th scope="col">Motivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resumen['errores'] as $error)
                                        <tr>
                                            <td style="white-space: nowrap; color: var(--color-muted-fg);">{{ $error['fila'] }}</td>
                                            <td style="color: var(--color-fg);">{{ $error['mensaje'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
@endsection
