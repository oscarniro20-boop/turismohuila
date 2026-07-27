<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VerificarRolBD extends Command
{
    protected $signature = 'verificar:rol-bd';
    protected $description = 'Verifica el valor real del rol en la base de datos';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🔍 VERIFICACIÓN DE ROL EN BASE DE DATOS');
        $this->info('═══════════════════════════════════════════════════════');
        
        // Verificar si hay usuario autenticado
        if (Auth::check()) {
            $user = Auth::user();
            $this->warn('⚠️  Hay un usuario autenticado: ' . $user->email);
            $this->warn('   Rol desde Auth: ' . $user->rol);
            $this->warn('   esAdmin(): ' . ($user->esAdmin() ? 'true' : 'false'));
            $this->line('');
        }

        // Consultar TODOS los usuarios de la base de datos
        $usuarios = DB::table('users')->select('id', 'name', 'email', 'rol')->get();

        $this->info('📋 TODOS LOS USUARIOS EN LA BASE DE DATOS:');
        $this->info('');

        if ($usuarios->isEmpty()) {
            $this->error('❌ No hay usuarios en la base de datos');
            return 1;
        }

        $tabla = [];
        foreach ($usuarios as $u) {
            $esAdmin = $u->rol === 'admin';
            $esSuperAdmin = $u->rol === 'superadmin';
            $esTurista = $u->rol === 'turista';
            
            $tabla[] = [
                'ID' => $u->id,
                'Nombre' => $u->name,
                'Email' => $u->email,
                'Rol (BD)' => $u->rol,
                '¿Admin?' => $esAdmin ? '✅ SÍ' : '❌ NO',
                '¿Superadmin?' => $esSuperAdmin ? '✅ SÍ' : '❌ NO',
                '¿Turista?' => $esTurista ? '✅ SÍ' : '❌ NO',
            ];
        }

        $this->table(
            ['ID', 'Nombre', 'Email', 'Rol (BD)', '¿Admin?', '¿Superadmin?', '¿Turista?'],
            $tabla
        );

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🔍 ANÁLISIS DE PROBLEMAS');
        $this->info('═══════════════════════════════════════════════════════');

        $problemas = [];
        
        foreach ($usuarios as $u) {
            // Verificar si el rol tiene espacios
            if ($u->rol !== trim($u->rol)) {
                $problemas[] = "❌ Usuario ID {$u->id} ({$u->email}): El rol tiene espacios: '{$u->rol}'";
            }
            
            // Verificar si el rol tiene mayúsculas
            if ($u->rol !== strtolower($u->rol)) {
                $problemas[] = "❌ Usuario ID {$u->id} ({$u->email}): El rol tiene mayúsculas: '{$u->rol}' (debería ser 'admin')";
            }
            
            // Verificar valores diferentes a los esperados
            if (!in_array($u->rol, ['admin', 'superadmin', 'turista'])) {
                $problemas[] = "❌ Usuario ID {$u->id} ({$u->email}): Rol inesperado: '{$u->rol}'";
            }
        }

        if (empty($problemas)) {
            $this->info('✅ No se detectaron problemas con los valores de rol');
        } else {
            foreach ($problemas as $problema) {
                $this->error($problema);
            }
        }

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('📊 ESTADÍSTICAS');
        $this->info('═══════════════════════════════════════════════════════');
        
        $total = $usuarios->count();
        $admins = $usuarios->where('rol', 'admin')->count();
        $superadmins = $usuarios->where('rol', 'superadmin')->count();
        $turistas = $usuarios->where('rol', 'turista')->count();
        
        $this->line("Total de usuarios: {$total}");
        $this->line("Admins: {$admins}");
        $this->line("Superadmins: {$superadmins}");
        $this->line("Turistas: {$turistas}");

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('✅ CONCLUSIÓN');
        $this->info('═══════════════════════════════════════════════════════');
        
        if ($admins > 0) {
            $this->info("✅ Hay {$admins} usuario(s) con rol 'admin'");
            $this->info("✅ Estos usuarios deberían ver los botones:");
            $this->line("   • + Nuevo Hotel");
            $this->line("   • + Nuevo Restaurante");
            $this->line("   • + Nuevo Tour");
        } else {
            $this->error('❌ NO hay usuarios con rol "admin"');
            $this->warn('   Esto explicaría por qué no se ven los botones');
            $this->warn('   Solución: Crear un usuario con rol "admin"');
        }

        return 0;
    }
}