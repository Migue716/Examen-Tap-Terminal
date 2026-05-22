<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Profile;
use App\Models\Section;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');
        $codes = app(CodeGeneratorService::class);

        $sections = Section::all();
        $sectionIds = $sections->pluck('_id')->map(fn ($id) => (string) $id)->all();
        $byModule = $sections->groupBy('module');

        $profileNames = [
            'Operador productos',
            'Supervisor productos',
            'Consultor usuarios',
            'Gestor usuarios',
            'Auditor perfiles',
            'Administrador regional',
            'Analista inventario',
            'Coordinador almacén',
            'Técnico terminal',
            'Encargado logística',
            'Auxiliar catálogo',
            'Jefe de turno',
            'Soporte operaciones',
            'Control de calidad',
            'Compras tap',
            'Ventas mostrador',
            'Recepción mercancía',
            'Planeación demanda',
            'Mantenimiento datos',
            'Capacitación personal',
        ];

        $profileIds = [];
        foreach ($profileNames as $index => $name) {
            $module = ['productos', 'usuarios', 'perfiles'][$index % 3];
            $moduleSections = $byModule->get($module, collect());
            $assigned = $moduleSections->take($index % 2 === 0 ? 2 : 1)
                ->pluck('_id')
                ->map(fn ($id) => (string) $id)
                ->all();

            if ($assigned === []) {
                $assigned = array_slice($sectionIds, 0, 2);
            }

            $profile = Profile::create([
                'code' => $codes->next('profiles', 'PFL'),
                'name' => $name,
                'section_ids' => $assigned,
            ]);
            $profileIds[] = (string) $profile->_id;
        }

        $brands = ['Tap', 'Terminal', 'MarcaPro', 'LogiPack', 'PuertoMX', 'CargoLine', 'SteelCo', 'FlexWare'];

        for ($i = 0; $i < 20; $i++) {
            Product::create([
                'code' => $codes->next('products', 'PRD'),
                'name' => $faker->words(3, true),
                'brand' => $faker->randomElement($brands),
                'price' => $faker->numberBetween(10, 999),
            ]);
        }

        for ($i = 1; $i <= 20; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $username = sprintf('usuario%02d@tapterminal.com', $i);

            User::updateOrCreate(
                ['username' => $username],
                [
                    'code' => $codes->next('users', 'USR'),
                    'name' => "{$firstName} {$lastName}",
                    'phone' => '+52'.$faker->numerify('##########'),
                    'profile_photo' => 'https://ui-avatars.com/api/?name='.urlencode("{$firstName}+{$lastName}"),
                    'password' => Hash::make('Test123!'),
                    'profile_ids' => [
                        $profileIds[$i - 1],
                        $profileIds[($i + 3) % count($profileIds)],
                    ],
                    'is_admin' => false,
                ]
            );
        }

        $this->command?->info('Creados: 20 productos, 20 perfiles y 20 usuarios de prueba.');
        $this->command?->info('Contraseña usuarios de prueba: Test123!');
    }
}
