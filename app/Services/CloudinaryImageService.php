<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CloudinaryImageService
{
    /**
     * Sube un archivo a Cloudinary dentro de una carpeta (folder).
     *
     * @param string $rutaAbsoluta Ruta absoluta (real) del archivo a subir.
     * @param string $carpeta      Carpeta destino, ej. 'lugares', 'galeria/lugares'.
     *
     * @return string URL pública de Cloudinary o del almacenamiento local.
     */
    public function subir(string $rutaAbsoluta, string $carpeta): string
    {
        try {
            $cloudinary = new Cloudinary($this->configuracion());

            $resultado = $cloudinary->uploadApi()->upload($rutaAbsoluta, [
                'folder' => 'turismo-huila/' . $this->carpetaNormalizada($carpeta),
            ]);

            if (empty($resultado['secure_url'])) {
                throw new RuntimeException('Cloudinary no devolvió un secure_url válido.');
            }

            return $resultado['secure_url'];
        } catch (Throwable) {
            $carpetaLocal = $this->carpetaNormalizada($carpeta);
            $extension = pathinfo($rutaAbsoluta, PATHINFO_EXTENSION) ?: 'jpg';
            $nombre = Str::uuid() . '.' . strtolower($extension);
            $rutaLocal = trim($carpetaLocal, '/') . '/' . $nombre;

            $guardado = Storage::disk('public')->putFileAs(
                $carpetaLocal,
                new File($rutaAbsoluta),
                $nombre
            );

            if ($guardado === false) {
                throw new RuntimeException('No fue posible guardar la imagen localmente.');
            }

            /** @var FilesystemAdapter $discoPublico */
            $discoPublico = Storage::disk('public');

            return $discoPublico->url($rutaLocal);
        }
    }

    /**
     * Elimina un recurso de Cloudinary extrayendo el public_id de su URL.
     */
    public function eliminarPorUrl(?string $urlCloudinary): void
    {
        $publicId = $this->publicIdDeUrl($urlCloudinary);

        if ($publicId === null) {
            return;
        }

        $cloudinary = new Cloudinary($this->configuracion());
        $cloudinary->uploadApi()->destroy($publicId);
    }

    public function esUrlCloudinary(?string $ruta): bool
    {
        return is_string($ruta) && $ruta !== '' && str_contains($ruta, 'res.cloudinary.com');
    }

    /**
     * Extrae el public_id de una URL de Cloudinary.
     * Ej: https://res.cloudinary.com/nube/image/upload/v123/lugares/foto.jpg
     *     -> public_id: lugares/foto
     */
    public function publicIdDeUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        if (preg_match('#/upload/(?:v\d+/)?(.+)\.\w+$#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function carpetaNormalizada(string $carpeta): string
    {
        $carpeta = ltrim($carpeta, '/');

        // El prefijo 'imagenes/' solo pertenecía al disco local (public_path).
        if (str_starts_with($carpeta, 'imagenes/')) {
            $carpeta = substr($carpeta, strlen('imagenes/'));
        }

        return rtrim($carpeta, '/');
    }

    private function configuracion(): ?string
    {
        $url = config('services.cloudinary.url');

        return (is_string($url) && $url !== '') ? $url : null;
    }
}