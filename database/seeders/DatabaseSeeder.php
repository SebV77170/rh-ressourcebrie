<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin RH',
            'email' => 'admin@example.com',
            'status' => User::STATUS_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Employe RH',
            'email' => 'employe@example.com',
            'status' => User::STATUS_EMPLOYEE,
        ]);

        User::factory()->create([
            'name' => 'Gestionnaire Paie',
            'email' => 'paie@example.com',
            'status' => User::STATUS_PAYROLL_MANAGER,
        ]);
    }
}
