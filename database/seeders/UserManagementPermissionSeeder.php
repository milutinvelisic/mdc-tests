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
        $permission = [
            'name' => 'user-management',
            'description' => 'Access User Management tab',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert permission if it doesn't exist
        $permissionId = DB::table('permissions')->updateOrInsert(
            ['name' => $permission['name']],
            $permission
        );

        // Get the first user
        $user = User::first();
        if (!$user) {
            $this->command->info('No users found in the database.');
            return;
        }

        // Get permission id
        $permissionId = DB::table('permissions')->where('name', 'user-management')->value('id');

        // Assign permission to first user (insert into pivot table if not exists)
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
