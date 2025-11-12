<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        Permission::create($data);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
        ]);

        $permission->update($data);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
