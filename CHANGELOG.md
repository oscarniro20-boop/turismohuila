# 📝 Changelog - TurismoHuila

## [1.0.0] - 2026-08-09

### 🎯 Mejoras de Arquitectura

- Extracción de lógica duplicada a Traits (`ManagesImages`, `HasGaleriaRules`)
- Creación de Services para lógica de negocio (`MapaService`, `BusquedaService`)
- Refactorización de controladores para delegar a Services
- Implementación de Dependency Injection en `LugarController` y `BusquedaController`

### ⚡ Optimización de Rendimiento

- Eliminación de consultas N+1 en `Resena` y `Favorito` mediante relaciones `morphTo`
- Implementación de eager loading en listados de reseñas
- Optimización de consultas en `ResenaController::index()` y `misResenas()`
- Configuración de `morphMap` en `AppServiceProvider`

### 🔒 Seguridad

- Creación de `ResenaPolicy` para autorización de reseñas
- Eliminación de verificaciones manuales de autorización
- Implementación de rate limiting en búsquedas (`throttle:30,1`) y contacto (`throttle:5,1`)
- Eliminación de consultas a BD directamente en vistas Blade

### 🏗️ Laravel Best Practices

- Creación de Form Requests (`StoreAgenciaRequest`, `UpdateAgenciaRequest`)
- Mejora del cumplimiento MVC (consultas movidas de vistas a controladores)
- Registro de middleware `rol` en `bootstrap/app.php`

### 📚 Documentación

- Creación de `DEPLOY.md` con guía de despliegue en InfinityFree
- Creación de `PRODUCTION_CHECKLIST.md` para checklist de producción
- Creación de `MAINTENANCE.md` con guía de mantenimiento

### 🐛 Correcciones

- Corregido N+1 en `FavoritoController::index()` agregando `with('servicio')`
- Corregidas consultas Eloquent en vistas de agencias y tours