<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
        $role = Role::find($id);
        $permissions = Permission::all();

        // Gate::authorize('update', $role);

        return view('roles.edit', ['pageTitle' => $pageTitle, 'role' => $role , 'permissions' => $permissions,]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        // Gate::authorize('update', $role);
        $role->update([
            'name' => $request->name,
        ]);

        $role->permissions()->sync($request->permissionIds);
        
        return redirect()->route('roles.index');
    }

    public function delete($id) {
        $pageTitle = 'delete role';
        $role = Role::find($id);

        // Gate::authorize('delete', $role);

        return view('roles.delete', ['pageTitle' => $pageTitle, 'role' => $role]);
    } 

    public function destroy($id)
    {
    $role = Role::find($id);
    $role->delete();

    // Gate::authorize('delete', $role);
    
    return redirect()->route('roles.index');
    }
}