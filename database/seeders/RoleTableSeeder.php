<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'administrator'],
            ['name' => 'super_admin'],
            ['name' => 'admin',]
        ];
        Role::withoutEvents(function () use ($roles) {
            foreach ($roles as $role) {
                Role::query()->updateOrCreate(['name' => $role['name']],[
                    'guard_name' => 'web'
                ]);
            }
        });
    }
}
