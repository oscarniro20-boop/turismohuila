# 🛠️ Guía de Mantenimiento - Turismo por Colombia

## Agregar Nuevos Lugares Turísticos

1. Acceder al panel de administración
2. Ir a **Lugares** → **Nuevo Lugar**
3. Completar los campos requeridos
4. Subir imagen principal y galería
5. Guardar

## Manejo de Imágenes

- **Cloudinary** es el proveedor principal de imágenes (subida, eliminación y URLs). Las credenciales viven solo en `.env` (`CLOUDINARY_URL`).
- Imagen principal y galería: JPG, JPEG, PNG; máximo 2 MB por imagen.
- Las imágenes se eliminan automáticamente (Cloudinary o local) al eliminar el registro.

## Funcionamiento de Favoritos

- Los favoritos usan relaciones polimórficas
- Cada favorito referencia un `tipo` (lugar, hotel, restaurante, tour, guia, agencia, transporte)
- La tabla `favoritos` tiene: `user_id`, `tipo`, `referencia_id`
- El accessor `getServicioAttribute()` resuelve el modelo relacionado

## Funcionamiento de Reseñas

- Las reseñas usan relaciones polimórficas
- Cada reseña tiene: `user_id`, `tipo`, `referencia_id`, `comentario`, `calificacion`
- Solo el autor puede editar/eliminar su reseña (Policy `ResenaPolicy`)
- Los administradores solo pueden editar/eliminar sus propias reseñas, igual que cualquier turista

## Funcionamiento de Búsquedas

- El `BusquedaService` busca en todos los modelos turísticos
- Limita a 4 resultados por tipo
- Usa `LIKE %query%` en el campo `nombre`
- Tiene rate limiting de 30 peticiones por minuto

## Revisar Logs

Los logs se almacenan en:
```
storage/logs/laravel.log
```

Para ver los últimos errores:
```bash
tail -100 storage/logs/laravel.log
```

## Ejecutar Pruebas

```bash
php artisan test
```

Suite completa automatizada en verde (**109 tests / 230 assertions**): autenticación, autorización, CRUD de los 7 módulos, reseñas, favoritos, tours/agencias, transporte, páginas públicas, seguridad y cabeceras HTTP. Antes de correr, ejecuta `php artisan optimize:clear` para evitar cachés obsoletas.

## Actualizar Dependencias de Forma Segura

```bash
# Ver qué hay desactualizado
composer outdated
npm outdated

# Actualizar dependencias de desarrollo
composer update --dev
npm update

# Actualizar dependencias de producción
composer update
npm install
```

**IMPORTANTE**: Siempre hacer backup antes de actualizar dependencias. Probar la aplicación después de cada actualización.

## Comandos Útiles

```bash
# Ver rutas registradas
php artisan route:list

# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Regenerar cachés (producción)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear un nuevo modelo
php artisan make:model NombreModelo

# Crear una nueva migración
php artisan make:migration nombre_migracion

# Ejecutar migraciones
php artisan migrate

# Compilar assets
npm run build
