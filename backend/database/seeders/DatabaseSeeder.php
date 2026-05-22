<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['code' => 'SEC-PRD-R', 'name' => 'Consulta productos', 'module' => 'productos', 'can_write' => false],
            ['code' => 'SEC-PRD-W', 'name' => 'Gestión productos', 'module' => 'productos', 'can_write' => true],
            ['code' => 'SEC-USR-R', 'name' => 'Consulta usuarios', 'module' => 'usuarios', 'can_write' => false],
            ['code' => 'SEC-USR-W', 'name' => 'Gestión usuarios', 'module' => 'usuarios', 'can_write' => true],
            ['code' => 'SEC-PFL-R', 'name' => 'Consulta perfiles', 'module' => 'perfiles', 'can_write' => false],
            ['code' => 'SEC-PFL-W', 'name' => 'Gestión perfiles', 'module' => 'perfiles', 'can_write' => true],
        ];

        $sectionIds = [];
        foreach ($sections as $section) {
            $model = Section::updateOrCreate(['code' => $section['code']], $section);
            $sectionIds[] = (string) $model->_id;
        }

        $adminProfile = Profile::updateOrCreate(
            ['code' => 'PFL000001'],
            ['name' => 'Administrador', 'section_ids' => $sectionIds]
        );

        User::updateOrCreate(
            ['username' => 'admin@tapterminal.com'],
            [
                'code' => 'USR000001',
                'name' => 'Administrador Tap Terminal',
                'phone' => '+523141234567',
                'profile_photo' => 'https://ui-avatars.com/api/?name=Admin+TT',
                'password' => Hash::make('Admin123!'),
                'profile_ids' => [(string) $adminProfile->_id],
                'is_admin' => true,
            ]
        );
    }
}
