<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class VerificarAdmin extends Command
{
    protected $signature = 'verificar:admin';
    protected $description = 'Verifica el rol del usuario autenticado y el acceso a funcionalidades de admin';

    public function handle()
    {
        if (!Auth::check()) {
            $this->error('❌ No hay usuario autenticado.');
            $this->info('Debes iniciar sesión primero para ejecutar este comando.');
            return 1;
        }

        $user = Auth::user();
        
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('📋 INFORMACIÓN DEL USUARIO AUTENTICADO');
        $this->info('═══════════════════════════════════════════════════════');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID', $user->id],
                ['Nombre', $user->name],
                ['Email', $user->email],
                ['Rol', $user->rol],
            ]
        );

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🔍 VERIFICACIONES');
        $this->info('═══════════════════════════════════════════════════════');
        
        $rolActual = $user->rol;
        $esAdmin = $user->esAdmin();
        
        $this->line("auth()->user()->rol: <fg=cyan>{$rolActual}</>");
        $this->line("auth()->user()->esAdmin(): <fg=cyan>" . ($esAdmin ? 'true' : 'false') . "</>");
        
        if ($esAdmin) {
            $this->info('✅ <fg=green>El usuario ES admin (rol: ' . $rolActual . ')</>');
        } else {
            $this->error('❌ El usuario NO es admin (rol: ' . $rolActual . ')');
        }

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🔐 ACCESO A RUTAS');
        $this->info('═══════════════════════════════════════════════════════');
        
        if ($esAdmin) {
            $this->info('✅ <fg=green>Rutas de gestión ACCESIBLES:</>');
            $this->line('  • hoteles.create');
            $this->line('  • hoteles.edit');
            $this->line('  • hoteles.destroy');
            $this->line('  • restaurantes.create');
            $this->line('  • restaurantes.edit');
            $this->line('  • restaurantes.destroy');
            $this->line('  • tours.create');
            $this->line('  • tours.edit');
            $this->line('  • tours.destroy');
        } else {
            $this->error('❌ Rutas de gestión NO ACCESIBLES');
        }

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🖱️  BOTONES VISIBLES EN PANELES');
        $this->info('═══════════════════════════════════════════════════════');
        
        if ($esAdmin) {
            $this->info('✅ <fg=green>Botones VISIBLES:</>');
            $this->line('  • <fg=yellow>+ Nuevo Hotel</> (en hoteles/index.blade.php)');
            $this->line('  • <fg=yellow>+ Nuevo Restaurante</> (en restaurantes/index.blade.php)');
            $this->line('  • <fg=yellow>+ Nuevo Tour</> (en tours/index.blade.php)');
            $this->info('');
            $this->info('✅ <fg=green>Botones de edición/eliminación VISIBLES en cada tarjeta</>');
        } else {
            $this->error('❌ Botones NO VISIBLES');
        }

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('📊 RESUMEN');
        $this->info('═══════════════════════════════════════════════════════');
        
        if ($esAdmin) {
            $this->info('✅ <fg=green>CONFIRMADO: El usuario con rol "admin" tiene acceso completo</>');
            $this->info('✅ <fg=green>Los botones deberían ser visibles en el panel</>');
            return 0;
        } else {
            $this->error('❌ PROBLEMA: El usuario NO tiene rol "admin"');
            $this->warn('Roles permitidos: admin, superadmin');
            $this->warn('Rol actual: ' . $rolActual);
            return 1;
        }
    }
}