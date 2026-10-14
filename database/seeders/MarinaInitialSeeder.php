<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\BoatDocument;
use App\Models\Slip;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarinaInitialSeeder extends Seeder
{
    public function run(): void
    {
// 1. Usuario Administrador Inicial
        User::updateOrCreate(
            ['email' => 'admin@marinacgyc.com'],
            [
                'name' => 'Capitanía Marina CGYC',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Definición de las 8 Zonas Cromáticas
        $zonesConfig = [
            ['name' => 'Verde',    'color_key' => 'green',  'hex_code' => '#10b981', 'order' => 1, 'range' => [1, 10]],
            ['name' => 'Naranja',  'color_key' => 'orange', 'hex_code' => '#f59e0b', 'order' => 2, 'range' => [11, 16]],
            ['name' => 'Rojo',     'color_key' => 'red',    'hex_code' => '#ef4444', 'order' => 3, 'range' => [17, 58]],
            ['name' => 'Amarillo', 'color_key' => 'yellow', 'hex_code' => '#eab308', 'order' => 4, 'range' => [59, 73]],
            ['name' => 'Azul',     'color_key' => 'blue',   'hex_code' => '#3b82f6', 'order' => 5, 'range' => [74, 79], 'special' => ['79A']],
            ['name' => 'Morado',   'color_key' => 'purple', 'hex_code' => '#8b5cf6', 'order' => 6, 'range' => [80, 90]],
            ['name' => 'Rosado',   'color_key' => 'pink',   'hex_code' => '#ec4899', 'order' => 7, 'range' => [92, 98]],
            ['name' => 'Gris',     'color_key' => 'gray',   'hex_code' => '#64748b', 'order' => 8, 'range' => [99, 104]],
        ];

        $sortCounter = 1;

        foreach ($zonesConfig as $z) {
            $zone = Zone::updateOrCreate(
                ['color_key' => $z['color_key']],
                [
                    'name' => $z['name'],
                    'hex_code' => $z['hex_code'],
                    'display_order' => $z['order'],
                ]
            );

            for ($i = $z['range'][0]; $i <= $z['range'][1]; $i++) {
                Slip::updateOrCreate(
                    ['code' => (string) $i],
                    [
                        'zone_id' => $zone->id,
                        'sort_order' => $sortCounter++,
                        'status' => 'free',
                    ]
                );
            }

            if (!empty($z['special'])) {
                foreach ($z['special'] as $specialCode) {
                    Slip::updateOrCreate(
                        ['code' => $specialCode],
                        [
                            'zone_id' => $zone->id,
                            'sort_order' => $sortCounter++,
                            'status' => 'free',
                        ]
                    );
                }
            }
        }

        // 3. Inventario Inicial de las 42 Embarcaciones
        $listadoPDF = [
            ['slip' => '1',   'name' => 'AVENTURA',         'aforo' => 100,  'pies' => 65,   'owner' => 'JOSE MARIA FRAGACHAN CERVI',     'matricula' => 'ARSH-D-656'],
            ['slip' => '2',   'name' => 'SOKOA',            'aforo' => 100,  'pies' => 53,   'owner' => 'CARLOS H. BLOHM F.',             'matricula' => 'AGSI-RE-0291'],
            ['slip' => '3',   'name' => 'GRAND CRU',        'aforo' => 105,  'pies' => null, 'owner' => 'JOHN HINES',                     'matricula' => 'AGSI-RE-0018'],
            ['slip' => '4',   'name' => 'SURIKATO',         'aforo' => 90,   'pies' => null, 'owner' => 'LUIS EDGARDO RINCONES MAYA',     'matricula' => 'AGSI-RE-1319'],
            ['slip' => '5',   'name' => 'DESTINY',          'aforo' => 85,   'pies' => null, 'owner' => 'RICARDO COHEN COHN',             'matricula' => 'AGSI-RE-0299'],
            ['slip' => '6',   'name' => 'HATT ENOUGH',      'aforo' => 85,   'pies' => null, 'owner' => 'GUSTAVO CONDE',                  'matricula' => 'AGSI-RE-1353'],
            ['slip' => '7',   'name' => 'MIRI MIRI',        'aforo' => 85,   'pies' => null, 'owner' => 'NICOLAS KOZMA',                  'matricula' => 'AGSI-RE-0091'],
            ['slip' => '9',   'name' => 'CRIMSON DAWN',     'aforo' => 80,   'pies' => null, 'owner' => 'GERARDO ALFREDO ACOSTA',         'matricula' => 'AGSI-RE-1808'],
            ['slip' => '10',  'name' => 'LUNA',             'aforo' => 80,   'pies' => 62,   'owner' => 'JUAN RAFAEL DELFINO MONZON',     'matricula' => 'AGSI-RE-0285'],
            ['slip' => '12',  'name' => 'MISS FUDGE',       'aforo' => 80,   'pies' => null, 'owner' => 'VICENTE EMILIO VELUTINI BENE',   'matricula' => 'AGSI-RE-1185'],
            ['slip' => '13',  'name' => 'KANBALIC',         'aforo' => 80,   'pies' => null, 'owner' => 'EMILIO CONDE',                   'matricula' => 'AGSI-RE-0638'],
            ['slip' => '14',  'name' => 'SANCTUARY',        'aforo' => 80,   'pies' => null, 'owner' => 'NESTOR ENRIQUE RAMIREZ COL',     'matricula' => 'AGSI-RE-1792'],
            ['slip' => '15',  'name' => 'SHAZZU II',        'aforo' => 80,   'pies' => 82,   'owner' => 'MIGUEL ELIAS TSOUKATOS ALTL',    'matricula' => 'AGSI-RE-1492'],
            ['slip' => '16',  'name' => 'TABOGA',           'aforo' => 80,   'pies' => null, 'owner' => 'FERNANDO TAMAYO DEGWITZ',        'matricula' => 'ARSH-RE-0169'],
            ['slip' => '59',  'name' => 'CARACOL',          'aforo' => 50,   'pies' => null, 'owner' => 'LEONOR MENDOZA DE GOMEZ',        'matricula' => 'AMMT-D-207'],
            ['slip' => '60',  'name' => 'GREEN LIGHTS',     'aforo' => 50,   'pies' => 46,   'owner' => 'FROILAN ANDRES ANZOLA CABRERA',  'matricula' => 'AGSI-RE-0979'],
            ['slip' => '61',  'name' => 'VENEZUELAN ROVER', 'aforo' => 42,   'pies' => 59,   'owner' => 'MIGUEL AGUILERA',                'matricula' => 'AGSI-RE-0026'],
            ['slip' => '65',  'name' => 'COQUI II',         'aforo' => 45,   'pies' => null, 'owner' => 'ENRIQUE CONDE DELFINO',          'matricula' => 'AGSI-RE-1422'],
            ['slip' => '66',  'name' => 'CARIBBEAN',        'aforo' => 45,   'pies' => null, 'owner' => 'CARLOS ENRIQUE MONTENEGRO',      'matricula' => 'En Trámite'],
            ['slip' => '67',  'name' => 'MARIMBA',          'aforo' => 45,   'pies' => null, 'owner' => 'WILLIAN DAVID PACANINS CLEARY',  'matricula' => 'AGSI-RE-0521'],
            ['slip' => '69',  'name' => 'PODEROSA',         'aforo' => 33,   'pies' => 44,   'owner' => 'GEORGES DOKMADJI BITTAR',        'matricula' => 'AGSI-RE-0828'],
            ['slip' => '70',  'name' => 'BLACK FIN',        'aforo' => 35,   'pies' => 35,   'owner' => 'LEONARDO DOKMADJI BITAR',        'matricula' => 'AGSI-RE-0207'],
            ['slip' => '74',  'name' => 'TINTORERA',        'aforo' => 40,   'pies' => null, 'owner' => 'MANUEL A. PEREZ LUNA',           'matricula' => 'ARSH-RE-0274'],
            ['slip' => '75',  'name' => 'TOTUMA II',        'aforo' => 35,   'pies' => 40,   'owner' => 'EILEEN BETANCOURT DE SILVA',     'matricula' => 'AGSI-D-5730'],
            ['slip' => '77',  'name' => 'MY TOY',           'aforo' => 33,   'pies' => null, 'owner' => 'SALOMON JAIME GALSKY',           'matricula' => 'AGSI-RE-0976'],
            ['slip' => '79',  'name' => 'POLARIS V',        'aforo' => 33,   'pies' => 50,   'owner' => 'TOMAS ALBERTI',                  'matricula' => 'AGSI-RE-1055'],
            ['slip' => '81',  'name' => 'MAKO II',          'aforo' => 40,   'pies' => null, 'owner' => 'MANUEL ALBERTO TANGIR PALA',     'matricula' => 'AGSI-RE-0755'],
            ['slip' => '82',  'name' => 'CAMAIGUANA',       'aforo' => 45,   'pies' => null, 'owner' => 'LUIS CARRILLO (HIJO)',           'matricula' => 'ARSH-RE-0253'],
            ['slip' => '84',  'name' => 'BOOMERANG',        'aforo' => 60,   'pies' => null, 'owner' => 'GUSTAVO ROMERO BLOHM',           'matricula' => 'ARSH-RE-0210'],
            ['slip' => '86',  'name' => 'PICORETA',         'aforo' => 60,   'pies' => null, 'owner' => 'JOSE V. PUIG',                   'matricula' => 'AGSP-RE-0681'],
            ['slip' => '87',  'name' => 'COCO',             'aforo' => 60,   'pies' => null, 'owner' => 'DAVID COBO DELGADO',             'matricula' => 'AGSI-RE-0233'],
            ['slip' => '87',  'name' => 'BREEZY',           'aforo' => 60,   'pies' => null, 'owner' => 'DAVID COBO DELGADO',             'matricula' => 'ARSH-D-1709'],
            ['slip' => '88',  'name' => 'CARACOL I',        'aforo' => 60,   'pies' => 24,   'owner' => 'ERIC BEHARD PODER',              'matricula' => 'AGSI-RE-0775'],
            ['slip' => '90',  'name' => 'ARI III',          'aforo' => 60,   'pies' => null, 'owner' => 'ALEXANDER GALSKY',               'matricula' => 'AGSI-RE-0975'],
            ['slip' => '92',  'name' => 'HOPI BLUE',        'aforo' => 47,   'pies' => null, 'owner' => 'JAVIER RODRIGUEZ GARCIA',        'matricula' => 'AGSI-RE-0750'],
            ['slip' => '93',  'name' => 'MARYPUCHY V',      'aforo' => 46,   'pies' => 45,   'owner' => 'AQUILES SIMON GARCIA JIMENEZ',   'matricula' => 'AGSI-RE-0443'],
            ['slip' => '95',  'name' => 'RUEDA LIBRE',      'aforo' => 45,   'pies' => null, 'owner' => 'ANGEL RAMON LA ROSA',            'matricula' => 'AGSI-RE-0485'],
            ['slip' => '97',  'name' => 'POR FIN',          'aforo' => 40,   'pies' => null, 'owner' => 'ALFREDO ELIAS BENZECRI PADILLA', 'matricula' => 'AGSI-RE-0669'],
            ['slip' => '98',  'name' => 'CONOMA-VARADERO',  'aforo' => 31,   'pies' => null, 'owner' => 'MANUEL A. MATOS ARREAZA',        'matricula' => 'AGSI-RE-0581'],
            ['slip' => '102', 'name' => 'KOROTO',           'aforo' => 35,   'pies' => 43,   'owner' => 'ARTURO BELLOSO',                 'matricula' => 'AGSP-D-3798'],
            ['slip' => '104', 'name' => 'DESIREE',          'aforo' => 80,   'pies' => 85,   'owner' => 'ALFREDO BERACASA BENZECRY',      'matricula' => 'AGSI-D-23151'],
            ['slip' => null,  'name' => 'MARCELLA',         'aforo' => null, 'pies' => null, 'owner' => 'No Registrado',                  'matricula' => 'En Trámite'],
        ];

        foreach ($listadoPDF as $item) {
            $slip = $item['slip'] ? Slip::where('code', $item['slip'])->first() : null;

            $boat = Boat::updateOrCreate(
                ['name' => $item['name'], 'matricula' => $item['matricula']],
                [
                    'owner_name' => $item['owner'],
                    'eslora_ft' => $item['pies'],
                    'aforo_ft' => $item['aforo'],
                    'slip_id' => $slip?->id,
                    'navigation_status' => 'en_marina',
                ]
            );

            if ($slip) {
                $slip->update([
                    'status' => 'occupied',
                    'max_aforo_ft' => $item['aforo'] ?? $slip->max_aforo_ft,
                ]);
            }
        }

        // Documento de prueba próximo a vencer para visualizar en el Dashboard
        $firstBoat = Boat::first();
        if ($firstBoat) {
            BoatDocument::updateOrCreate(
                ['boat_id' => $firstBoat->id, 'document_type' => 'Póliza de Responsabilidad Civil'],
                [
                    'document_number' => 'POL-2026-8891',
                    'issued_at' => now()->subMonths(11),
                    'expires_at' => now()->addDays(14),
                    'notes' => 'Renovación pendiente por consignar en Capitanía.',
                ]
            );
        }
    }
}
