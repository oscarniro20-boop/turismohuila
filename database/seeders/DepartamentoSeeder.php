<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    private const DEPTS = [
        ['91','Amazonas','amazonas','Amazonía','Leticia',0,30],
        ['05','Antioquia','antioquia','Andina','Medellín',1,1],
        ['81','Arauca','arauca','Orinoquía','Arauca',0,28],
        ['08','Atlántico','atlantico','Caribe','Barranquilla',1,4],
        ['13','Bolívar','bolivar','Caribe','Cartagena',1,3],
        ['15','Boyacá','boyaca','Andina','Tunja',1,6],
        ['17','Caldas','caldas','Andina','Manizales',0,7],
        ['18','Caquetá','caqueta','Amazonía','Florencia',0,31],
        ['19','Cauca','cauca','Andina','Popayán',0,8],
        ['20','Cesar','cesar','Caribe','Valledupar',0,5],
        ['27','Chocó','choco','Pacífica','Quibdó',0,17],
        ['23','Córdoba','cordoba','Caribe','Montería',0,16],
        ['25','Cundinamarca','cundinamarca','Andina','Bogotá',0,13],
        ['85','Casanare','casanare','Orinoquía','Yopal',0,29],
        ['94','Guainía','guainia','Amazonía','Inírida',0,32],
        ['95','Guaviare','guaviare','Amazonía','San José del Guaviare',0,33],
        ['41','Huila','huila','Andina','Neiva',1,2],
        ['44','La Guajira','la-guajira','Caribe','Riohacha',0,14],
        ['47','Magdalena','magdalena','Caribe','Santa Marta',0,15],
        ['50','Meta','meta','Orinoquía','Villavicencio',0,25],
        ['52','Nariño','narino','Andina','Pasto',0,9],
        ['54','Norte de Santander','norte-de-santander','Andina','Cúcuta',0,10],
        ['86','Putumayo','putumayo','Amazonía','Mocoa',0,34],
        ['63','Quindío','quindio','Andina','Armenia',0,11],
        ['66','Risaralda','risaralda','Andina','Pereira',0,12],
        ['88','San Andrés y Providencia','san-andres-y-providencia','Insular','San Andrés',0,35],
        ['68','Santander','santander','Andina','Bucaramanga',0,18],
        ['70','Sucre','sucre','Caribe','Sincelejo',0,19],
        ['73','Tolima','tolima','Andina','Ibagué',0,20],
        ['76','Valle del Cauca','valle-del-cauca','Pacífica','Cali',0,21],
        ['97','Vaupés','vaupes','Amazonía','Mitú',0,36],
        ['99','Vichada','vichada','Orinoquía','Puerto Carreño',0,27],
        ['11','Bogotá D.C.','bogota-dc','Andina','Bogotá',1,0],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            foreach (self::DEPTS as [$cod, $nom, $slug, $reg, $cap, $dest, $ord]) {
                DB::table('departamentos')->updateOrInsert(
                    ['cod_dpto' => $cod],
                    [
                        'nombre' => $nom,
                        'slug' => $slug,
                        'region' => $reg,
                        'capital' => $cap,
                        'destacado' => (bool) $dest,
                        'orden' => $ord,
                        'descripcion' => null,
                        'imagen' => null,
                        'latitud' => null,
                        'longitud' => null,
                    ]
                );
            }
        });

        $this->command?->info('Departamentos importados: ' . DB::table('departamentos')->count());
    }
}