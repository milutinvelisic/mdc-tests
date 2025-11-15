<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserManagementPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'user-management',
                'description' => 'Access User Management tab',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'data-import',
                'description' => 'Access Data Import tab',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'import-orders',
                'description' => 'Import Orders functionality',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'import-inventory',
                'description' => 'Import Inventory functionality',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'import-shipments',
                'description' => 'Import Shipments functionality',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }

        $user = User::first();
        if (!$user) {
            $this->command->info('No users found in the database.');
            return;
        }

        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')->where('name', $permission['name'])->value('id');

            if (!DB::table('user_permission')->where('user_id', $user->id)->where('permission_id', $permissionId)->exists()) {
                DB::table('user_permission')->insert([
                    'user_id' => $user->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
