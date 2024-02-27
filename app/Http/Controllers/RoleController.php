<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Task;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $pageTitle = 'Role Lists';
        $roles = Role::all();

        return view('roles.index', [
            'pageTitle' => $pageTitle,
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $pageTitle = 'Add Role';
        $permissions = Permission::all();
        return view('roles.create', [
            'pageTitle' => $pageTitle,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'permissionIds' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
            ]);

            $role->permissions()->sync($request->permissionIds);

            DB::commit();

            return redirect()->route('roles.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Role';
        $role = Role::findOrFail($id);

        if (Gate::denies('performAsTaskOwner', $role)) {
            Gate::authorize('updateAnyRoles', Role::class);
        }

        $permissions = Permission::all();
        return view('roles.edit', [
            'pageTitle' => $pageTitle,
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name
            ]);

            $role->permissions()->sync($request->permissionIds);

            DB::commit();

            return redirect()->route('roles.index');

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function delete($id)
    {
        $pageTitle = 'Delete Role';
        $role = Role::findOrFail($id);

        if (Gate::denies('performAsTaskOwner', $role)) {
            Gate::authorize('deleteAnyRoles', Role::class);
        }

        return view('roles.delete', [
            'pageTitle' => $pageTitle,
            'role' => $role,
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect()->route('roles.index');
    }
}
