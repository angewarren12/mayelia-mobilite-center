<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Créer un Super Admin
        User::create([
            'nom' => 'Super Admin',
            'email' => 'admin@mayelia.com',
            'password' => Hash::make('password123'),
        ]);

        // Créer un Admin Centre
        User::create([
            'nom' => 'Admin Centre',
            'email' => 'admin.centre@mayelia.com',
            'password' => Hash::make('password123'),
        ]);

        $this->command->info('Utilisateurs admin créés avec succès !');
        $this->command->info('Super Admin: admin@mayelia.com / password123');
        $this->command->info('Admin Centre: admin.centre@mayelia.com / password123');
    }
}