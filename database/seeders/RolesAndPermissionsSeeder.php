<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();

        if ($roles->count() <= 0) {
            foreach (config('roles') as $key => $role) {
                Role::firstOrCreate([
                    'name' => $key,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
