<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use LaravelIdea\Helper\App\Models\_IH_User_C;

class UserService
{
    public function getAllUsers($perPage = 10): array|_IH_User_C|LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function createUser(array $data): User
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // Attach permissions
        if (!empty($permissions)) {
            $user->permissions()->sync($permissions);
        }

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Sync permissions
        $user->permissions()->sync($permissions);

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }
}
