<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission; // Ditambahkan
use Illuminate\Http\Request;
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
        //
    }

    public function update($id, Request $request)
    {
        //
    }

    public function delete($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
