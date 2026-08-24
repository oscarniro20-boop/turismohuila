#!/usr/bin/env pwsh
# build-package.ps1 - Script para empaquetar el proyecto para despliegue en InfinityFree
# Genera un ZIP optimizado excluyendo archivos innecesarios

# Configuración
$PROJECT_ROOT = $PWD.Path
$DIST_DIR = Join-Path $PROJECT_ROOT "dist"
$ZIP_NAME = "turismo-huila-deploy.zip"
$ZIP_PATH = Join-Path $DIST_DIR $ZIP_NAME

# Exclusiones del paquete
$EXCLUDE_PATTERNS = @(
    "node_modules/*",
    "tests/*",
    ".git/*",
    ".env",
    "storage/logs/*.log",
    "database/seeders/*",
    "*.md"
)

# Crear directorio dist/ si no existe
if (-not (Test-Path $DIST_DIR)) {
    New-Item -ItemType Directory -Path $DIST_DIR -Force | Out-Null
    Write-Host "✓ Directorio dist/ creado"
}

# Limpiar ZIP anterior si existe
if (Test-Path $ZIP_PATH) {
    Remove-Item $ZIP_PATH -Force
    Write-Host "✓ ZIP anterior eliminado"
}

# Cambiar al directorio del proyecto para los comandos
Push-Location $PROJECT_ROOT

Write-Host "=== Paso 1: Ejecutando composer install --no-dev --optimize-autoloader ==="
composer install --no-dev --optimize-autoloader
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Error en composer install"
    Pop-Location
    exit 1
}
Write-Host "✓ composer install completado"

Write-Host "=== Paso 2: Ejecutando npm run build ==="
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Error en npm run build"
    Pop-Location
    exit 1
}
Write-Host "✓ npm run build completado"

Write-Host "=== Paso 3: Generando ZIP optimizado ==="
# Usamos -r para recursivo y --exclude para los patrones
# Cambiamos a la estructura: todo el proyecto menos las carpetas excluidas
# Pero queremos incluir vendor/ (sin dev) y public/

# Crear el ZIP con exclusiones
$excludeArgs = $EXCLUDE_PATTERNS -join ","
Write-Host "Patrones de exclusión: $excludeArgs"

# Usamos Compress-Archive con -Path y -LiteralPath
# El ZIP se crea desde el project root pero excluyendo los patrones
# Nota: En Windows PowerShell, el parámetro -Exclude funciona con Proveider de proveedor de archivos
# Usaremos un enfoque con robocopy o el proveedor de archivo directo

# Enfoque: crear ZIP excluyendo patrones usando la sintaxis de zip
# Usamos FileSystemInfo para iterar y crear el archive

# Método: Usar Compress-Archive con -LiteralPath y filtrar
$filesToInclude = Get-ChildItem -Path $PROJECT_ROOT -Recurse -Force |
    Where-Object {
        $_.PSIsContainer ? 
            # Para directorios: verificar que no coincidan con patrones de exclusión
            $EXCLUDE_PATTERNS | ForEach-Object {
                if ($_ -like "$($_.FullName)*" -or $_.Name -like $_) { $false }
                else { $true }
            } -contains $false : $false
        : ($_.FullName -notlike $EXCLUDE_PATTERNS -and $_.Extension -ne ".md")
    } | Select-Object -ExpandProperty FullName

# Alternativa más robusta: usar el proveedor de ZIP directamente
# Pero PowerShell no tiene built-in fácil, así que usaremos el enfoque de .NET

# Vamos a crear el ZIP usando la clase .NET System.IO.Compression.ZipFile
Add-Type -AssemblyName System.IO.Compression.FileSystem

# Limpiamos la lista y creamos el ZIP
$excludePatternsArray = $EXCLUDE_PATTERNS

# Usamos un enfoque más simple: generar ZIP desde el root y excluir después
# O mejor: usar el provider de archivo con -Exclude

Write-Host "Generando ZIP en: $ZIP_PATH"

# Usar Compress-Archive con patrón de exclude
# El -Path es el directorio base, -DestinationPath es donde guardar
# Usaremos el truco de crear un temp dir, copiar lo necesario y luego zip

# Enfoque alternativo: usar robocopy para copiar lo necesario a dist/, luego zip
$tempDir = Join-Path $DIST_DIR "temp-deploy"
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir -Force | Out-Null

Write-Host "Copiando archivos necesarios a directorio temporal..."

# Copiar todo excepto los excluidos
Get-ChildItem -Path $PROJECT_ROOT -Recurse -Force | ForEach-Object {
    $relativePath = $_.FullName.Substring($PROJECT_ROOT.Length + 1)
    
    # Verificar si debe ser excluido
    $shouldExclude = $false
    foreach ($pattern in $excludePatternsArray) {
        # Convertir patrón a regex simple
        $regexPattern = $pattern -replace '\*', '.*' -replace '\?', '.'
        if ($relativePath -match "^$regexPattern$") {
            $shouldExclude = $true
            break
        }
        # También verificar nombre de directorio/archivo
        if ($_.Name -like $pattern -or $relativePath -like $pattern) {
            $shouldExclude = $true
            break
        }
    }
    
    if (-not $shouldExclude) {
        $dest = Join-Path $tempDir $relativePath
        if ($_.PSIsContainer) {
            if (-not (Test-Path $dest)) {
                New-Item -ItemType Directory -Path $dest -Force | Out-Null
            }
        } else {
            Copy-Item -Path $_.FullName -Destination $dest -Force
        }
    }
}

# Ahora generar el ZIP desde el directorio temporal
$zipFor = Join-Path $tempDir "."
if (Test-Path $ZIP_PATH) {
    Remove-Item $ZIP_PATH -Force
}

[System.IO.Compression.ZipFile]::CreateFromDirectory($zipFor, $ZIP_PATH)
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Error al generar ZIP"
    Pop-Location
    Remove-Item $tempDir -Recurse -Force
    exit 1
}
Write-Host "✓ ZIP generado correctamente"

# Obtener tamaño del ZIP en MB
$zipSizeBytes = (Get-Item $ZIP_PATH).Length
$zipSizeMB = [Math]::Round($zipSizeBytes / 1MB, 2)
Write-Host "=== Tamaño del ZIP: $zipSizeMB MB ==="

# Limpiar directorio temporal
Remove-Item $tempDir -Recurse -Force

Pop-Location
Write-Host "=== Script completado ==="