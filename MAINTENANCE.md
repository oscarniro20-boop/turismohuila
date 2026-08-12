# 🛠️ Guía de Mantenimiento - TurismoHuila

## Agregar Nuevos Lugares Turísticos

1. Acceder al panel de administración
2. Ir a **Lugares** → **Nuevo Lugar**
3. Completar los campos requeridos
4. Subir imagen principal y galería
5. Guardar

## Manejo de Imágenes

- **Imagen principal**: Se almacena en `public/imagenes/{tipo}/`
- **Galería**: Se almacena en `public/galeria/{tipo}/`
- **Formatos**: Solo JPG, JPEG, PNG
- **Tamaño máximo**: 2 MB por imagen
- Las imágenes se eliminan automáticamente al eliminar el registro

## Funcionamiento de Favoritos

- Los favoritos usan relaciones polimórficas
- Cada favorito referencia un `tipo` (lugar, hotel, restaurante, tour, guia, agencia)
- La tabla `favoritos` tiene: `user_id`, `tipo`, `referencia_id`
- El accessor `getServicioAttribute()` resuelve el modelo relacionado

## Funcionamiento de Reseñas

- Las reseñas usan relaciones polimórficas
- Cada reseña tiene: `user_id`, `tipo`, `referencia_id`, `comentario`, `calificacion`
- Solo el autor puede editar/eliminar su reseña (Policy `ResenaPolicy`)
- Los administradores pueden gestionar todas las reseñas

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

No hay tests automatizados completos aún. Se recomienda agregarlos progresivamente.

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